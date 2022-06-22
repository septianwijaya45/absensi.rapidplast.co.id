@extends('layouts.index', [$title = 'Mesin Fingerprint'])

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
                <h1 class="m-0">Data Mesin</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Mesin</li>
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
                    <h3 class="card-title">Data Mesin</h3>
                    <a href="{{route('addMesin')}}" class="text-decoration-none">
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
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>TCPIP</th>
                        <th>SN</th>
                        <th>Default</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $no = 1;
                        ?>
                        @foreach($mesin as $data)
                        <tr>
                            <td>{{$no++}}</td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->tipe}}</td>
                            <td>{{$data->tcpip}}</td>
                            <td>{{$data->serial_number}}</td>
                            <td>
                                @if($data->is_default == 0)
                                    <button class="btn btn-light" onclick="defaultMesin('{{ $data->id }}')">Off</button>
                                @else
                                    <button class="btn btn-primary" onclick="defaultMesin('{{ $data->id }}')">On</button>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('editMesin', $data->id) }}" class="text-decoration-none">
                                    <button class="btn btn-warning btn-sm">Ubah</button>
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="destroy('{{$data->id}}')">Hapus</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>TCPIP</th>
                            <th>SN</th>
                            <th>Default</th>
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
    function defaultMesin(id){
        swal({
            title: "Anda Yakin?",
            text: "Untuk Default Mesin Ini?",
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
                        url: "{{url('Admin/Mesin/Default')}}/"+id,
                        method: 'GET',
                        success: function (results) {
                            swal("Berhasil!", "Mesin Berhasil Dihapus!", "success");
                            setInterval(() => {
                                window.location.reload();
                            }, 2000);
                        },
                        error: function (results) {
                            swal("GAGAL!", "Gagal Menghapus Data!", "error");
                        }
                    });
            }else{
                swal("Mesin Batal Default", "", "info")
            }
        })
    }
</script>

<script type="text/javascript">
    function destroy(id){
        swal({
            title: "Anda Yakin?",
            text: "Untuk menghapus mesin ini?",
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
                        url: "{{url('Admin/Mesin/Delete')}}/"+id,
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
                swal("Mesin Batal Dihapus", "", "info")
            }
        })
    }
</script>
@stop