<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class DashboardController extends Controller
{
    public function index()
    {
        // $tgl1 = strtotime("2015-06-01"); 
        // $tgl2 = strtotime("2020-01-20"); 

        // $jarak = $tgl2 - $tgl1;

        // $hari = $jarak / 60 / 60 / 24;
        // $hari = $hari%21;
        // dd($hari);
        return view('admin.dashboard.index');
    }
}
