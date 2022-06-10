@extends('layouts.index', ['title' => 'Tambah Regu Kerja'])

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
                    <li class="breadcrumb-item active">Tambah Data</li>
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
                    <h3 class="card-title">Tambah Regu Kerja</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{route('storeReguKerja')}}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="kode">Kode</label>
                            <input type="text" name="kode" id="kode" class="form-control @error('kode') is-invalid @enderror" placeholder="Ketik Kode" value="{{ old('kode') }}">

                            @error('kode')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Ketik Nama" value="{{ old('nama') }}">

                            @error('nama')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="tgl_start">Tanggal Start</label>
                            <input type="date" name="tgl_start" id="tgl_start" class="form-control @error('tgl_start') is-invalid @enderror" placeholder="Pilih Tanggal Start" value="{{ old('tgl_start') }}">

                            @error('tgl_start')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="hari">Hari</label>
                            <input type="number" name="hari" id="hari" class="form-control @error('hari') is-invalid @enderror" placeholder="Ketik Hari" value="{{ old('hari') }}">

                            @error('hari')
                                <div class="invalid-feedback">
                                    {{$message}}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="jadwal">Jadwal</label>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="1" class="refKerja form-control" style="width: 200px;" >
                                                <option value="" selected disabled>1</option>
                                                @foreach($refKerja as $data)
                                                    <option value="{{$data->kode}}">{{$data->kode}} | {{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="2" class="refKerja form-control" style="width: 200px;" >
                                                <option value="" selected disabled>2</option>
                                                @foreach($refKerja as $data)
                                                    <option value="{{$data->kode}}">{{$data->kode}} | {{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="3" class="refKerja form-control" style="width: 200px;" >
                                                <option value="" selected disabled>3</option>
                                                @foreach($refKerja as $data)
                                                    <option value="{{$data->kode}}">{{$data->kode}} | {{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="4" class="refKerja form-control" style="width: 200px;" >
                                                <option value="" selected disabled>4</option>
                                                @foreach($refKerja as $data)
                                                    <option value="{{$data->kode}}">{{$data->kode}} | {{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="5" class="refKerja form-control" style="width: 200px;" >
                                                <option value="" selected disabled>5</option>
                                                @foreach($refKerja as $data)
                                                    <option value="{{$data->kode}}">{{$data->kode}} | {{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select name="6" class="refKerja form-control" style="width: 200px;" >
                                                <option value="" selected disabled>6</option>
                                                @foreach($refKerja as $data)
                                                    <option value="{{$data->kode}}">{{$data->kode}} | {{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="7" class="refKerja form-control" style="width: 200px;" >
                                                <option value="" selected disabled>7</option>
                                                @foreach($refKerja as $data)
                                                    <option value="{{$data->kode}}">{{$data->kode}} | {{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="8" class="refKerja form-control" style="width: 200px;" >
                                                <option value="" selected disabled>8</option>
                                                @foreach($refKerja as $data)
                                                    <option value="{{$data->kode}}">{{$data->kode}} | {{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="9" class="refKerja form-control" style="width: 200px;" >
                                                <option value="" selected disabled>9</option>
                                                @foreach($refKerja as $data)
                                                    <option value="{{$data->kode}}">{{$data->kode}} | {{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="10" class="refKerja form-control" style="width: 200px;" >
                                                <option value="" selected disabled>10</option>
                                                @foreach($refKerja as $data)
                                                    <option value="{{$data->kode}}">{{$data->kode}} | {{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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