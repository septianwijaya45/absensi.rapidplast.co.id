<?php

use App\Models\AbsensiWfh;
use App\Models\Mesin;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

    function totPegawai(){
        return count(Pegawai::all());
    }

    function totMesin(){
        return count(Mesin::all());
    }

    function totAbsenBackup(){
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $dbName = $year.''.$month.'HISTORY';

        return count(DB::select(
            "SELECT afh.pid, p.nama, p.departement, afh.check_in, afh.check_out, afh.telat, afh.izin, afh.check_in1, afh.check_out1, afh.check_in2, afh.check_out2, afh.check_in3, afh.check_out3
            FROM absensi_fingerprint.pegawais p, absensi_frhistory.$dbName afh
            WHERE p.pid = afh.pid AND MONTH(afh.sync_date) = '$month'
            ORDER BY afh.id DESC"
        ));
    }

    function absensiWFH()
    {
        return count(AbsensiWfh::all());
    }

    function clockMin($num){
        if($num < 10){
            return '0'.$num;
        }
        return $num;
    }

    function clockCount($num){
        $tot = 0;
        $tot += $num;
        return $tot;
    }
?>