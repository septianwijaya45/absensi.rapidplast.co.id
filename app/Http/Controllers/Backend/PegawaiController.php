<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Departement;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Mesin;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\ReferensiKerja;
use App\Models\ReguKerja;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use ZKLibrary;

class PegawaiController extends Controller
{
    function index(){
        $pegawai = DB::select("
            SELECT p.id, p.user_id, p.pid, p.nama, p.no_ktp, p.alamat, p.sap, p.email, p.departement_id, p.regukerja_id
            FROM pegawais p
            ORDER BY p.id ASC
        ");

        return view('admin.pegawai.index', compact(['pegawai']));
    }

    function create(){
        $jabatan = Jabatan::all();
        $departement = Departement::all();
        $divisi      = Divisi::all();
        $reguKerja = ReguKerja::all();
        return view('admin.pegawai.create', compact(['jabatan', 'departement', 'divisi', 'reguKerja']));
    }

    function store(Request $request){
        $this->validate($request, [
            'nama'          => 'required',
            'jabatan_id'    => 'required',
            'no_ktp'        => 'required|unique:pegawais',
            'departement'   => 'required',
            'email'         => 'required|unique:users',
            'sap'           => 'required',
            'pid'           => 'required'
        ], [
            'nama.required'         =>  'Nama Harus Diisi!',
            'jabatan_id.required'   =>  'Jabatan Harus Diisi!',
            'no_ktp.required'       =>  'Nomor KTP Harus Diisi!',
            'no_ktp.unique'         =>  'Nomor KTP Harus Berbeda!',
            'departement.required'  =>  'Departement Harus Diisi!',
            'email.required'        =>  'Email Harus Diisi!',
            'sap.required'          =>  'SAP Harus Diisi!',
            'email.unique'          =>  'Email Harus Berbeda!',
            'pid.required'          =>  'PID Harus Diisi!'
        ]);

        $user               = new User();
        $user->role_id      = 2;
        $user->name         = $request->nama;
        $user->email        = $request->email;
        $user->password     = bcrypt('pegawai');
        $user->created_at   = Carbon::now();
        $user->updated_at   = Carbon::now();
        $user->save();

        $pegawai = Pegawai::findOrCreate([
            'user_id'           => $user->id,
            'jabatan_id'        => $request->jabatan_id,
            'departement_id'    => $request->departement,
            'divisi_id'         => $request->divisi,
            'regukerja_id'      => $request->regukerja_id,
            'pid'               => $request->pid,
            'nama'              => $request->nama,
            'no_ktp'            => $request->no_ktp,
            'email'             => $request->email,
            'sap'               => $request->sap,
            'alamat'            => $request->alamat,
        ]);
        
        Session::put('sweetalert', 'success');
        return redirect()->route('pegawai')->with('alert', 'Sukses Menambahkan Data');
    }

    function edit($id){
        $pegawai = Pegawai::where('id', $id)->first();

        // $pegawai = Pegawai::find($id);
        $jabatan = Jabatan::all();
        $divisi = Divisi::all();
        $departement = Departement::all();
        $reguKerja = ReguKerja::all();
        return view('admin.pegawai.edit', compact(['pegawai', 'id', 'jabatan', 'divisi', 'departement', 'reguKerja']));
    }

    function update(Request $request, $id){
        $this->validate($request, [
            'nama'          => 'required',
            'email'         => 'required'
        ], [
            'nama.required'         =>  'Nama Harus Diisi!',
            'email.required'        =>  'Email Harus Diisi!'
        ]);
        $pegawai = Pegawai::where('id', $id)->first();

        User::where('id', $pegawai->user_id)->update([
            'email'         => $request->email,
            'updated_at'    => Carbon::now()
        ]);

        Pegawai::where('id', $id)->update([
            'jabatan_id'        => $request->jabatan_id,
            'departement_id'    => $request->departement,
            'divisi_id'         => $request->divisi,
            'regukerja_id'      => $request->regukerja_id,
            'pid'               => $request->pid,
            'nama'              => $request->nama,
            'no_ktp'            => $request->no_ktp,
            'departement'       => $request->departement,
            'email'             => $request->email,
            'sap'               => $request->sap,
            'alamat'            => $request->alamat,
        ]);

        Session::put('sweetalert', 'success');
        return redirect()->route('editPegawai', $id)->with('alert', 'Sukses Mengubah '.$pegawai->nama);
    }

    function destroy($id){
        $pegawai = Pegawai::where('id', $id)->first();
        if(!empty($pegawai)){
            User::where('id', $pegawai->user_id)->delete();
            Pegawai::where('id', $id)->delete();
        }
    }

    function syncPegawai(){
        $mesin  = Mesin::where('is_default', 1)->first();
        $port = 4370;

        $zk = new ZKLibrary($mesin->tcpip, $port);
        $zk->connect();
        $pegawai = $zk->getUser();

        foreach($pegawai as $data){
            $check = Pegawai::where('pid', $data[0])->first();
            $email = User::where('email', $data[1].'@gmail.com')->first();
            if(empty($check)){
                $user               = new User();
                $user->role_id      = 2;
                $user->name         = $data[1];
                if($email){
                    $user->email        = $data[1].''.rand(10,100).'2@gmail.com';
                }else{
                    $user->email        = $data[1].'@gmail.com';
                }
                $user->password     = bcrypt('pegawai');
                $user->created_at   = Carbon::now();
                $user->updated_at   = Carbon::now();
                $user->save();

                Pegawai::insert([
                    'user_id'       => $user->id,
                    'jabatan_id'    => 61,
                    'pid'           => $data[0],
                    'nama'          => $data[1],
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now()
                ]);
            }
        }
        Session::put('sweetalert', 'success');
        return redirect()->back()->with('alert', 'Sukses Menambahkan pegawai dari mesin fingerprint '.$mesin->ip);
    }
}
