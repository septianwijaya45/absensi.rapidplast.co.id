<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\AbsenLog;
use App\Models\AbsenMentah;
use App\Models\HariKerja;
use App\Models\Jadwal;
use App\Models\Mesin;
use App\Models\Pegawai;
use App\Models\ReferensiKerja;
use App\Models\ReguKerja;
use App\Models\ShiftKerja;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use ZKLibrary;

class AbsensiController extends Controller
{
    function index(Request $request){
        if($request->method() == 'GET'){
            $date = Carbon::now()->format('Y-m-d');
            $tanggal = Carbon::now()->format('d F Y');
            $tanggalCetak = Carbon::now()->format('Y-m-d');
            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');
            $dbName = $year.''.$month.'HISTORY';
            
            if(!Schema::connection('mysql2')->hasTable($dbName)){
                Schema::connection('mysql2')->create($dbName, function(Blueprint $table){
                    $table->id();
                    $table->unsignedBigInteger('pid');
                    $table->unsignedBigInteger('sap')->nullable();
                    $table->time('check_in')->nullable();
                    $table->time('check_out')->nullable();
                    $table->time('telat')->nullable();
                    $table->time('check_in1')->nullable();
                    $table->time('check_out1')->nullable();
                    $table->time('check_in2')->nullable();
                    $table->time('check_out2')->nullable();
                    $table->time('check_in3')->nullable();
                    $table->time('check_out3')->nullable();
                    $table->time('absen1')->nullable();
                    $table->time('absen2')->nullable();
                    $table->time('izin')->nullable();
                    $table->timestamp('sync_date')->nullable();
                    $table->timestamp('updated_at')->nullable();
                });
            }
            // $absensi = Absen::all();
            $date = Carbon::now()->format('Y-m-d');
            $absensi = DB::select(
                "SELECT afh.pid, p.nama, p.departement, afh.check_in, afh.check_out, afh.telat, afh.izin, afh.check_in1, afh.check_out1, afh.check_in2, afh.check_out2, afh.check_in3, afh.check_out3, afh.sync_date
                FROM absensi_fingerprint.pegawais p, absensi_frhistory.$dbName afh
                WHERE p.pid = afh.pid AND DATE(afh.sync_date) = '$date'
                ORDER BY afh.id DESC"
            );

            return view('admin.absensi.index', compact(['absensi', 'tanggal', 'date', 'tanggalCetak', 'dbName']));
        } else{
            $date = Carbon::now()->format('Y-m-d');
            $year = date('Y', strtotime($request->tanggal));
            $month = date('m', strtotime($request->tanggal));
            $dbName = $year.''.$month.'HISTORY';
            $tanggal = date('Y-m-d', strtotime($request->tanggal));
            $tanggal2 = date('Y-m-d', strtotime($request->tanggal2));
            $tanggalCetak = date('Y-m-d', strtotime($request->tanggal));
            // $absensi = Absen::all();
            $absensi = DB::select(
                "SELECT afh.pid, p.nama, p.departement, afh.check_in, afh.check_out, afh.telat, afh.izin, afh.check_in1, afh.check_out1, afh.check_in2, afh.check_out2, afh.check_in3, afh.check_out3, afh.sync_date
                FROM absensi_fingerprint.pegawais p, absensi_frhistory.$dbName afh
                WHERE p.pid = afh.pid AND DATE(sync_date) >= '$tanggal' AND DATE(sync_date) <= '$tanggal2'
                ORDER BY afh.id DESC"
            );

            return view('admin.absensi.index', compact(['absensi', 'tanggal', 'date', 'tanggal2', 'tanggalCetak', 'dbName']));
        }
    }

    function syncData(Request $request){
        $tanggal = $request->tanggal;
        $tanggal2 = $request->tanggal2;

        $mesin  = Mesin::where('is_default', 1)->first();
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $dbName = $year.''.$month.'HISTORY';
        $port = 4370;

        $zk = new ZKLibrary($mesin->tcpip, $port);
        $zk->connect();
        $log_kehadiran = $zk->getAttendance();



        if(!empty($log_kehadiran) == true){
            foreach($log_kehadiran as $data){
                $countData = count($log_kehadiran) - 1;
                $checkAbsen = COUNT(AbsenMentah::where(DB::raw('DATE(date)'), date('Y-m-d', strtotime($data[3])))->get());

                if($checkAbsen === 0 || is_null($checkAbsen)){
                    for($i = 0; $i <= $countData; $i++){
                        AbsenMentah::insert([
                            'pid'           => $log_kehadiran[$i][1],
                            'status'        => $log_kehadiran[$i][2],
                            'date'          => $log_kehadiran[$i][3],
                            'created_at'    => Carbon::now(),
                            'updated_at'    => Carbon::now()
                        ]);
                    }
                }
            }

            if(strtotime($tanggal) === strtotime($tanggal2)){
                $absenMentah = AbsenMentah::where(DB::raw('DATE(date)'), $tanggal)->get();
            }else{
                $absenMentah = AbsenMentah::whereBetween(DB::raw('DATE(date)'), [$tanggal, $tanggal2])->get();
            }

            if(!is_null($absenMentah)){
                foreach($absenMentah as $row){
                    // $checkPegawai = DB::connection('mysql2')->table($dbName)->where([
                    //     ['pid', $row->pid],
                    //     [DB::raw('DATE(sync_date)'), ''.date('Y-m-d', strtotime($row->date).'')]
                    // ])->get();

                    $checkDate = date('Y-m-d', strtotime($row->date));

                    if(strtotime($tanggal) === strtotime($tanggal2)){
                        $checkPegawai = DB::select("
                            SELECT db.* 
                            FROM absensi_frhistory.$dbName db
                            WHERE db.pid = '$row->pid' AND DATE(db.sync_date) = '$checkDate'
                        ");
                    }else{
                        $checkPegawai = DB::select("
                            SELECT db.* 
                            FROM absensi_frhistory.$dbName db
                            WHERE db.pid = '$row->pid' AND DATE(db.sync_date) BETWEEN '$tanggal' AND '$tanggal2'
                        ");
                    }


                    if($checkPegawai === null || empty($checkPegawai)){
                        $pegawai = Pegawai::where('pid', $row->pid)->first();
                        if(!is_null($pegawai)){
                            // Check regukerja_id is not null
                            if(!empty($pegawai->regukerja_id) || $pegawai->regukerja_id != 'null' || $pegawai->regukerja_id != null){
                                $reguKerja = ReguKerja::where('kode', $pegawai->regukerja_id)->first();
                                if($reguKerja != null){
                                    if($reguKerja->kode === 'Default' || $reguKerja->kode === 'DEFAULT'){
                                        $clock = date('H:i:s', strtotime($row->date));
                                        DB::connection('mysql2')->table($dbName)->insert([
                                            'pid'       => $row->pid,
                                            'sap'       => $pegawai->sap,
                                            'absen1'    => $clock,
                                            'telat'     => '00:00:00',
                                            'sync_date'=>   $row->date,
                                            'updated_at'=> Carbon::now()
                                        ]);
                                    }else{

                                        $awal  = date_create($reguKerja->tgl_start);
                                        $akhir = date_create($row->date); // waktu sekarang
                                        $diff  = date_diff( $awal, $akhir );
                                        $hari = $diff->days % $reguKerja->hari;
                                        if($hari === 0){
                                            $hari = $reguKerja->hari;
                                        }
                                        // Get Jadwals
                                        $jadwal = Jadwal::where('id', $reguKerja->jadwal_id)->first();
                                        // Get Ref Kerja
                                        $refKerja = ReferensiKerja::where('kode', $jadwal[$hari])->first();
                                        // Get Time in row
                                        $clock = date('H:i:s', strtotime($row->date));
                                        // Get Time - 1 hour before workin
                                        $workInBefore = date('H:i:s', (strtotime($refKerja->workin) - strtotime('01:00:00')));
                                        $workOutBefore = date('H:i:s', (strtotime($refKerja->workout) - strtotime('01:00:00')));
                                        // dd($clock >= $refKerja->workout && $clock >= $workOutBefore);
                                        if($clock <= $refKerja->workout && $clock >= $workInBefore){
                                            if($clock >= $refKerja->workin){                                // When Late Work in
                                                $timeLate = strtotime($clock) - strtotime($refKerja->workin);
                                                $late = date('H:i:s', $timeLate);
                                                DB::connection('mysql2')->table($dbName)->insert([
                                                    'pid'       => $row->pid,
                                                    'sap'       => $pegawai->sap,
                                                    'check_in'  => $clock,
                                                    'telat'     => $late,
                                                    'sync_date'=>   $row->date,
                                                    'updated_at'=> Carbon::now()
                                                ]);
                                            }else{                                                          // When Not Late
                                                DB::connection('mysql2')->table($dbName)->insert([
                                                    'pid'       => $row->pid,
                                                    'sap'       => $pegawai->sap,
                                                    'check_in'  => $clock,
                                                    'telat'     => '00:00:00',
                                                    'sync_date'=>   $row->date,
                                                    'updated_at'=> Carbon::now()
                                                ]);
                                            }
                                        }elseif($clock >= $refKerja->workout && $clock >= $workOutBefore){
                                            DB::connection('mysql2')->table($dbName)->insert([ 
                                                'pid'       => $row->pid,
                                                'sap'       => $pegawai->sap,
                                                'check_out'  => $clock,
                                                'telat'     => '00:00:00',
                                                'sync_date'=>   $row->date,
                                                'updated_at'=> Carbon::now()
                                            ]);
                                        }
                                    }
                                }
        
                            }
                        }

                    }else{
                        $pegawai = Pegawai::where('pid', $row->pid)->first();

                        if(!is_null($pegawai)){
                            if(!empty($pegawai->regukerja_id) || $pegawai->regukerja_id != 'null' || $pegawai->regukerja_id != null){
                                $reguKerja = ReguKerja::where('kode', $pegawai->regukerja_id)->first();
                                
                                // Range date start and date request in machine
                                $tglStart = strtotime($reguKerja->tgl_start);
                                $tglReq = strtotime($row->date);
                                $range = $tglReq - $tglStart;
                                $range = $range / 60 /60 /24;
                                $hari  = $range%$reguKerja->hari;
                                if($hari === 0){
                                    $hari = $reguKerja->hari;
                                }
        
                                // Get Jadwals
                                $jadwal = Jadwal::where('id', $reguKerja->jadwal_id)->first();
                                // Get Ref Kerja
                                $refKerja = ReferensiKerja::where('kode', $jadwal[$hari])->first();
                                // Get Time in row
                                $clock = date('H:i:s', strtotime($row->date));
                                // Get Time - 1 hour before workin
                                $workInBefore = date('H:i:s', (strtotime($refKerja->workin) - strtotime('01:00:00')));
                                $workOutBefore = date('H:i:s', (strtotime($refKerja->workout) - strtotime('01:00:00')));
                                $checkAbsen = DB::connection('mysql2')->table($dbName)->where([
                                    ['pid', $row->pid],
                                    [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                                ])->first();
    
                                if(!empty($checkAbsen->check_in) && $clock >= $refKerja->workout && $clock >= $workOutBefore){
                                    DB::connection('mysql2')->table($dbName)->where([
                                        ['pid', $row->pid],
                                        [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                                    ])->update([
                                        'check_out'  => $clock,
                                        'sync_date'=>   $row->date,
                                        'updated_at'=> Carbon::now()
                                    ]);
                                }elseif(!empty($checkAbsen->check_in) && !empty($checkAbsen->check_out)){
                                    DB::connection('mysql2')->table($dbName)->where([
                                        ['pid', $row->pid],
                                        [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                                    ])->update([
                                        'check_in1'  => $clock,
                                        'sync_date'=>   $row->date,
                                        'updated_at'=> Carbon::now()
                                    ]);
                                }elseif(!empty($checkAbsen->check_in) && !empty($checkAbsen->check_out) && !empty($checkAbsen->check_in1) && $clock >= $refKerja->workout && $clock >= $workOutBefore){
                                    DB::connection('mysql2')->table($dbName)->where([
                                        ['pid', $row->pid],
                                        [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                                    ])->update([
                                        'check_out1'  => $clock,
                                        'sync_date'=>   $row->date,
                                        'updated_at'=> Carbon::now()
                                    ]);
                                }elseif(!empty($checkAbsen->check_in) && !empty($checkAbsen->check_out) && !empty($checkAbsen->check_in1) && !empty($checkAbsen->check_out1)){
                                    DB::connection('mysql2')->table($dbName)->where([
                                        ['pid', $row->pid],
                                        [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                                    ])->update([
                                        'check_in2'  => $clock,
                                        'sync_date'=>   $row->date,
                                        'updated_at'=> Carbon::now()
                                    ]);
                                }elseif(!empty($checkAbsen->check_in) && !empty($checkAbsen->check_out) && !empty($checkAbsen->check_in1) && !empty($checkAbsen->check_out1) && !empty($checkAbsen->check_in2) && $clock >= $refKerja->workout && $clock >= $workOutBefore){
                                    DB::connection('mysql2')->table($dbName)->where([
                                        ['pid', $row->pid],
                                        [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                                    ])->update([
                                        'check_out2'  => $clock,
                                        'sync_date'=>   $row->date,
                                        'updated_at'=> Carbon::now()
                                    ]);
                                }elseif(!empty($checkAbsen->check_in) && !empty($checkAbsen->check_out) && !empty($checkAbsen->check_in1) && !empty($checkAbsen->check_out1) && !empty($checkAbsen->check_in2) && !empty($checkAbsen->check_out2)){
                                    DB::connection('mysql2')->table($dbName)->where([
                                        ['pid', $row->pid],
                                        [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                                    ])->update([
                                        'check_in3'  => $clock,
                                        'sync_date'=>   $row->date,
                                        'updated_at'=> Carbon::now()
                                    ]);
                                }else{
                                    if($clock >= $refKerja->workout && $clock >= $workOutBefore){
                                        DB::connection('mysql2')->table($dbName)->where([
                                            ['pid', $row->pid],
                                            [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                                        ])->update([
                                            'check_out3'  => $clock,
                                            'sync_date'=>   $row->date,
                                            'updated_at'=> Carbon::now()
                                        ]);
                                    }else{
                                        DB::connection('mysql2')->table($dbName)->where([
                                            ['pid', $row->pid],
                                            [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                                        ])->update([
                                            'pid'       => $row->pid,
                                            'sap'       => $pegawai->sap,
                                            'absen2'    => $clock,
                                            'telat'     => '00:00:00',
                                            'sync_date'=>   $row->date,
                                            'updated_at'=> Carbon::now()
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $absenMentah = DB::select("
                DELETE FROM absen_mentahs
            ");

            AbsenLog::insert([
                'mesin_id'      => $mesin->id,
                'status_absen'  => 'Tarik Absen',
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
        
        }else{
            Session::put('sweetalert', 'error');
            return response()->json(['errors' => 'Gagal Import Data Absensi! Mungkin data sudah terhapus!']);
        }
    }
}
