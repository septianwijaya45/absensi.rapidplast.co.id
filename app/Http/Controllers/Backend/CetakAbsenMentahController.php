<?php

namespace App\Http\Controllers\Backend;

use App\Exports\AbsenMentahExport;
use App\Exports\AbsenMentahExportSearch;
use App\Http\Controllers\Controller;
use App\Models\AbsenMentah;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\DB;

class CetakAbsenMentahController extends Controller
{
    // public function cetakTXT($tanggal, $dbName)
    // {
    //     $tanggalName = date('d F Y', strtotime($tanggal));
    //     return Excel::download(new AbsenMentahExport($tanggal, $dbName), 'Tarik Absen '.$tanggalName.'.txt');
    // }

    public function cetakTXT($tanggal, $dbName)
    {
        $tanggal = date('Y-m-d', strtotime($tanggal));
        $absen = DB::connection('mysql2')->table($dbName)
                ->select('pid', DB::raw("DATE_FORMAT(sync_date, '%d.%m.%Y%H:%i:%s') as sync_date"), 'check_in', 'check_out', 'sap')
                ->where(DB::raw('DATE(sync_date)'), $tanggal)
                ->get();

                $txt = "\tNO.IDTgl/Waktu\t  Status";
                $txt .= "\r\n";
                foreach($absen as $data){
                    if(!empty($data->check_in)){
                        if(strlen($data->sap) === 0){
                            $txt .="\r\n        ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 1){
                            $txt .="\r\n       ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 2){
                            $txt .="\r\n      ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 3){
                            $txt .="\r\n     ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 4){
                            $txt .="\r\n    ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 5){
                            $txt .="\r\n   ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 6){
                            $txt .="\r\n  ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 7){
                            $txt .="\r\n ".$data->sap.''.$data->sync_date.'P10';
                        }else{
                            $txt .="\r\n".$data->sap.''.$data->sync_date.'P10';
                        }
                    }else{
                        if(strlen($data->sap) === 0){
                            $txt .="\r\n        ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 1){
                            $txt .="\r\n       ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 2){
                            $txt .="\r\n      ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 3){
                            $txt .="\r\n     ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 4){
                            $txt .="\r\n    ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 5){
                            $txt .="\r\n   ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 6){
                            $txt .="\r\n  ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 7){
                            $txt .="\r\n ".$data->sap.''.$data->sync_date.'P20';
                        }else{
                            $txt .="\r\n".$data->sap.''.$data->sync_date.'P20';
                        }
                    }
                }
        return response($txt)
        ->withHeaders([
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => 'attachment; filename="Tarik Absen '.$tanggal.'.txt"',
        ]);
    }

    public function cetakTXTSearch($tanggal, $tanggal2, $dbName)
    {

        $absen = DB::connection('mysql2')->table($dbName)
                ->select('pid', DB::raw("DATE_FORMAT(sync_date, '%d.%m.%Y%H:%i:%s') as sync_date"), 'check_in', 'check_out', 'sap')
                ->whereDate(DB::raw('DATE(sync_date)'), '>=',$tanggal)   
                ->whereDate(DB::raw('DATE(sync_date)'), '<=',$tanggal2)   
                ->get();

                $txt = "\tNO.IDTgl/Waktu\t  Status";
                $txt .= "\r\n";
                foreach($absen as $data){
                    if(!empty($data->check_in)){
                        if(strlen($data->sap) === 0){
                            $txt .="\r\n        ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 1){
                            $txt .="\r\n       ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 2){
                            $txt .="\r\n      ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 3){
                            $txt .="\r\n     ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 4){
                            $txt .="\r\n    ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 5){
                            $txt .="\r\n   ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 6){
                            $txt .="\r\n  ".$data->sap.''.$data->sync_date.'P10';
                        }elseif(strlen($data->sap) == 7){
                            $txt .="\r\n ".$data->sap.''.$data->sync_date.'P10';
                        }else{
                            $txt .="\r\n".$data->sap.''.$data->sync_date.'P10';
                        }
                    }else{
                        if(strlen($data->sap) === 0){
                            $txt .="\r\n        ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 1){
                            $txt .="\r\n       ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 2){
                            $txt .="\r\n      ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 3){
                            $txt .="\r\n     ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 4){
                            $txt .="\r\n    ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 5){
                            $txt .="\r\n   ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 6){
                            $txt .="\r\n  ".$data->sap.''.$data->sync_date.'P20';
                        }elseif(strlen($data->sap) == 7){
                            $txt .="\r\n ".$data->sap.''.$data->sync_date.'P20';
                        }else{
                            $txt .="\r\n".$data->sap.''.$data->sync_date.'P20';
                        }
                    }
                }
        return response($txt)
        ->withHeaders([
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-store, no-cache',
            'Content-Disposition' => 'attachment; filename="Tarik Absen '.$tanggal.' sampai '.$tanggal2.'.txt"',
        ]);
    }
}
