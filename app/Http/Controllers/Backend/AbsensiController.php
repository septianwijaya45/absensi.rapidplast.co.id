<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\AbsenMentah;
use App\Models\HariKerja;
use App\Models\Mesin;
use App\Models\Pegawai;
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
                    $table->time('izin')->nullable();
                    $table->timestamp('sync_date')->nullable();
                    $table->timestamp('updated_at')->nullable();
                });
            }
            // $absensi = Absen::all();
            $date = Carbon::now()->format('Y-m-d');
            $absensi = DB::select(
                "SELECT afh.pid, p.nama, p.departement, afh.check_in, afh.check_out, afh.telat, afh.izin, afh.check_in1, afh.check_out1, afh.check_in2, afh.check_out2, afh.check_in3, afh.check_out3
                FROM absensi_fingerprint.pegawais p, absensi_frhistory.$dbName afh
                WHERE p.pid = afh.pid AND DATE(afh.sync_date) = '$date'"
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
                "SELECT afh.pid, p.nama, p.departement, afh.check_in, afh.check_out, afh.telat, afh.izin, afh.check_in1, afh.check_out1, afh.check_in2, afh.check_out2, afh.check_in3, afh.check_out3
                FROM absensi_fingerprint.pegawais p, absensi_frhistory.$dbName afh
                WHERE p.pid = afh.pid AND DATE(sync_date) >= '$tanggal' AND DATE(sync_date) <= '$tanggal2'"
            );

            return view('admin.absensi.index', compact(['absensi', 'tanggal', 'date', 'tanggal2', 'tanggalCetak', 'dbName']));
        }
    }

    function syncData(Request $request){
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

            $absenMentah = AbsenMentah::where(DB::raw('DATE(date)'), $request->tanggal)->get();

            if(!is_null($absenMentah)){
                foreach($absenMentah as $row){
                    if($row->status === 0){
                        $pegawai = Pegawai::where('pid', $row->pid)->first();
                        $checkPegawai = DB::connection('mysql2')->table($dbName)->where([
                             ['pid', $row->pid],
                             [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                         ])->first();

                        if($checkPegawai === null || !empty($checkPegawai)){
                            DB::connection('mysql2')->table($dbName)->insert([
                                'pid'       => $row->pid,
                                'sap'       => $pegawai->sap,
                                'check_in'  => $row->date,
                                'sync_date'=>   $row->date,
                                'updated_at'=> Carbon::now()
                            ]);
                        }
                    }elseif($row->status === 1){
                        $pegawai = Pegawai::where('pid', $row->pid)->value('sap');
                         $checkPegawai = DB::connection('mysql2')->table($dbName)->where([
                             ['pid', $row->pid],
                             [DB::raw('DATE(sync_date)'), date('Y-m-d', strtotime($row->date))] 
                         ])->first();
                        
                         if(is_null($checkPegawai) || !$checkPegawai){
                            DB::connection('mysql2')->table($dbName)->insert([
                                'pid'       => $row->pid,
                                'sap'       => $pegawai,
                                'check_out'  => $row->date,
                                'sync_date'=> $row->date,
                                'updated_at'=> Carbon::now()
                            ]);
                         }else{
                             DB::connection('mysql2')->table($dbName)->where([
                                 ['pid', $row->pid],
                                 ['sync_date', $row->date] 
                             ])->update([
                                'check_out'  => $row->date,
                                'updated_at'=> Carbon::now()
                             ]);
                         }
                    }
                }

                if(Carbon::now()->format('Y-m-d') === $request->tanggal){
                    AbsenMentah::where(DB::raw('DATE(date)'), $request->tanggal)->delete();
                }else{
                    AbsenMentah::where(DB::raw('DATE(created_at)'), $request->tanggal)->delete();
                }
    
                Session::put('sweetalert', 'success');
                return redirect()->route('absensi')->with('alert', 'Sukses Import Data Absensi Tertanggal '.$request->tanggal.' !');
            }else{
                Session::put('sweetalert', 'error');
                return redirect()->route('absensi')->with('alert', 'Gagal Import Data Absensi! Data Tidak Ada!');
            }
        }else{
            Session::put('sweetalert', 'error');
            return redirect()->route('absensi')->with('alert', 'Gagal Import Data Absensi! Mungkin data sudah terhapus!');
        }
    }
}
