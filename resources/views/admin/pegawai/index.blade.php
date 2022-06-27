@extends('layouts.index', [$title = 'Pegawai'])

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
                <h1 class="m-0">Pegawai</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Pegawai</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Pegawai</h3>
                    <a href="{{route('syncPegawai')}}" class="text-decoration-none">
                        <button type="button" class="btn btn-sm btn-primary float-right ml-2">
                            Sync Pegawai
                        </button>
                    </a>
                    <a href="{{route('addPegawai')}}" class="text-decoration-none">
                        <button type="button" class="btn btn-sm btn-success float-right">
                            Tambah Data
                        </button>
                    </a>

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline collapsed" aria-describedby="example1_info">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>PID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>KTP</th>
                        <th>Regu</th>
                        <th>SAP</th>
                        <th>Alamat</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php

use App\Models\User;

                            $no = 1;
                        ?>
                        @foreach($pegawai as $data)
                        <tr>
                            <td>{{$no++}}</td>
                            <td>{{$data->pid}}</td>
                            <td>{{$data->nama}}</td>
                            <td>{{$data->email}}</td>
                            <td>{{$data->no_ktp}}</td>
                            <td>{{$data->regukerja_id}}</td>
                            <td>{{$data->sap}}</td>
                            <td>{{$data->alamat}}</td>
                            <td>
                                <a href="{{route('editPegawai', $data->id)}}" class="text-decoration-none">
                                    <button class="btn btn-warning btn-sm">Ubah</button>
                                </a>
                                <button class="btn btn-danger btn-sm" onClick="destroy('{{$data->id}}')">Hapus</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>PID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>KTP</th>
                            <th>Regu</th>
                            <th>SAP</th>
                            <th>Alamat</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
</section>

@stop


@section('footer')
<script type="text/javascript">
    function destroy(id){
        swal({
            title: "Anda Yakin?",
            text: "Untuk menghapus data ini? Data yang terhapus TIDAK DAPAT DIKEMBALIKAN!",
            icon: 'warning',
            buttons: true,
            dangerMode: true
        })
        .then((willDelete) => {
            if(willDelete) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                        url: "{{url('Admin/Pegawai/Delete')}}/"+id,
                        method: 'DELETE',
                        success: function (results) {
                            swal("Berhasil!", "Data Berhasil Dihapus!", "success");
                            window.location.reload();
                        },
                        error: function (results) {
                            swal("GAGAL!", "Gagal Menghapus Data!", "error");
                        }
                    });
            }else{
                swal("Data Batal Dihapus", "", "info")
            }
        })
    }
</script>
@stop