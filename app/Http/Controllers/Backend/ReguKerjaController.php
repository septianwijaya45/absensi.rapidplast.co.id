<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ReferensiKerja;
use App\Models\ReguKerja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ReguKerjaController extends Controller
{
    function index(){
        $reguKerja = ReguKerja::all();
        return view('admin.regukerja.index', compact(['reguKerja']));
    }
    
    function insert(){
        $refKerja = ReferensiKerja::all();
        return view('admin.regukerja.insert', compact(['refKerja']));
    }

    function store(Request $request){
        $validate = Validator::make($request->all(),[
            'kode'          => 'required',
            'nama'          => 'required',
            'tgl_start'     => 'required',
            'hari'          => 'required',
            'jadwal'        => 'required'
        ], [
            'kode.required'        => 'Kode Harus Diisi!',
            'nama.required'        => 'Nama Harus Diisi!',
            'tgl_start.required'   => 'Tanggal Start Harus Diisi!',
            'hari.required'        => 'Hari Harus Diisi!',
            'jadwal.required'      => 'Jadwal Harus Diisi!',
        ]);
        
        if($validate->fails()){
            Session::put('sweetalert', 'warning');
            return redirect()->back()->with('alert', 'Gagal Menambahkan Regu Kerja!')->withErrors($validate);
        }

        ReguKerja::insert([
            'kode'          => $request->kode,
            'nama'          => $request->nama,
            'tgl_start'     => $request->tgl_start,
            'hari'          => $request->hari,
            'jadwal'        => $request->jadwal,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now()
        ]);

        Session::put('sweetalert', 'success');
        return redirect()->route('reguKerja')->with('alert', 'Sukses Menambahkan Data');
    }

    function edit($id){
        $reguKerja = ReguKerja::find($id);
        return view('admin.regukerja.edit', compact(['reguKerja']));
    }

    function update(Request $request, $id){
        $validate = Validator::make($request->all(),[
            'kode'          => 'required',
            'nama'          => 'required',
            'tgl_start'     => 'required',
            'hari'          => 'required',
            'jadwal'        => 'required'
        ], [
            'kode.required'        => 'Kode Harus Diisi!',
            'nama.required'        => 'Nama Harus Diisi!',
            'tgl_start.required'   => 'Tanggal Start Harus Diisi!',
            'hari.required'        => 'Hari Harus Diisi!',
            'jadwal.required'      => 'Jadwal Harus Diisi!',
        ]);
        
        if($validate->fails()){
            Session::put('sweetalert', 'warning');
            return redirect()->back()->with('alert', 'Gagal Menambahkan Regu Kerja!')->withErrors($validate);
        }

        ReguKerja::where('id', $id)->update([
            'kode'          => $request->kode,
            'nama'          => $request->nama,
            'tgl_start'     => $request->tgl_start,
            'hari'          => $request->hari,
            'jadwal'        => $request->jadwal,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now()
        ]);

        Session::put('sweetalert', 'success');
        return redirect()->back()->with('alert', 'Sukses Mengedit Data');
    }

    function destroy($id){
        $reguKerja = ReguKerja::find($id);
        if($reguKerja){
            ReguKerja::where('id', $id)->delete();
        }
    }
}
