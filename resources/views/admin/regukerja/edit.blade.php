@extends('layouts.index', ['title' => 'Edit Regu Kerja'])

@section('content')
@if(Session::has('alert'))
  @if(Session::get('sweetalert')=='success')
    <div class="swalDefaultSuccess">
    </div>
  @elseif(Session::get('sweetalert')=='error')
    <div class="swalDefaultError">
    </div>
    @elseif(Session::get('sweetalert')=='warning')
    <div class="swalDefaultWarning">
    </div>
  @endif
@endif

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Regu Kerja</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item">Regu Kerja</li>
                    <li class="breadcrumb-item active">Edit Data</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit Regu Kerja {{ $reguKerja->nama }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{route('updateReguKerja', $reguKerja->id)}}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="kode">Kode</label>
                            <input type="text" name="kode" id="kode" class="form-control @error('kode') is-invalid @enderror" placeholder="Ketik Kode" value="{{ $reguKerja->kode }}">

                            @error('kode')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Ketik Nama" value="{{ $reguKerja->nama }}">

                            @error('nama')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="tgl_start">Tanggal Start</label>
                            <input type="date" name="tgl_start" id="tgl_start" class="form-control @error('tgl_start') is-invalid @enderror" placeholder="Pilih Tanggal Start" value="{{ $reguKerja->tgl_start }}">

                            @error('tgl_start')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="hari">Hari</label>
                            <input type="number" name="hari" id="hari" class="form-control @error('hari') is-invalid @enderror" placeholder="Ketik Hari" value="{{ $reguKerja->hari }}">

                            @error('hari')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="jadwal">Jadwal</label>
                            <input type="text" name="jadwal" id="jadwal" class="form-control @error('jadwal') is-invalid @enderror" placeholder="Ketik Jadwal" value="{{ $reguKerja->jadwal }}">

                            @error('jadwal')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop