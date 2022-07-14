@extends('layouts.index', [$title = 'Absensi Pegawai'])

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
                <h1 class="m-0">Data Absensi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">Absensi</li>
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
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Search Data</h3>
                    </div>
                    <form action="{{route('searchAbsensi')}}" method="POST" enctype="multipart/form-data" id="form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <span>Dari Tanggal</span>
                                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{$tanggalCetak}}" required>
                                </div>
                                <div class="col-md-4">
                                    <span>Ke Tanggal</span>
                                    @if(Route::is('searchAbsensi'))
                                    <input type="date" id="tanggal2" name="tanggal2" class="form-control" value="{{ $tanggal2}}" required>
                                    @else
                                    <input type="date" id="tanggal2" name="tanggal2" class="form-control" value="{{$tanggalCetak}}" required>
                                    @endif
                                </div>
                                <div class="col-md-2 text-center">
                                    <button type="button" id="sync-absensi" class="btn btn-warning mt-4">
                                        Tarik Absensi
                                    </button>
                                </div>
                                <div class="col-md-2 text-center">
                                    <button type="submit" class="btn btn-success mt-4">Cari Data</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Absensi</h3>
                    @if(Route::is('searchAbsensi'))
                        <a href="{{route('cetakSearch.TXT', ['tanggal' => $tanggal, 'tanggal2' => $tanggal2, 'dbName' => $dbName])}}">
                            <button type="button" class="btn btn-warning btn-sm float-right mr-2" >
                                Cetak .TXT
                            </button>
                        </a>
                    @else
                        <a href="{{route('cetak.TXT', ['tanggal' => $tanggal, 'dbName' => $dbName])}}">
                            <button type="button" class="btn btn-warning btn-sm float-right mr-2" >
                                Cetak .TXT
                            </button>
                        </a>
                    @endif

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped dataTable" aria-describedby="example1_info">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Tanggal Absen</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Telat</th>
                        <th>Check In 1</th>
                        <th>Check Out 1</th>
                        <th>Check In 2</th>
                        <th>Check Out 2</th>
                        <th>Check In 3</th>
                        <th>Check Out 3</th>
                        <th>Izin</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $no = 1;
                        ?>
                        @foreach($absensi as $data)
                        <tr>
                            <td>{{$no++}}</td>
                            <td>{{$data->pid}}</td>
                            <td>{{$data->nama}}</td>
                            <td>{{date('d F Y', strtotime($data->sync_date))}}</td>
                            <td>{{$data->check_in}}</td>
                            <td>{{$data->check_out}}</td>
                            <td>{{$data->telat}}</td>
                            <td>{{$data->check_in1}}</td>
                            <td>{{$data->check_out1}}</td>
                            <td>{{$data->check_in2}}</td>
                            <td>{{$data->check_out2}}</td>
                            <td>{{$data->check_in3}}</td>
                            <td>{{$data->check_out3}}</td>
                            <td>{{$data->izin}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Tanggal Absen</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Telat</th>
                            <th>Check In 1</th>
                            <th>Check Out 1</th>
                            <th>Check In 2</th>
                            <th>Check Out 2</th>
                            <th>Check In 3</th>
                            <th>Check Out 3</th>
                            <th>Izin</th>
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

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Pilih Tanggal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('syncDataAbsensi') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="tanggal">Tanggal <span class="text-danger">* Harus Diisi</span></label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ $date }}">

                    @error('tanggal')
                        <div class="is-invalid">
                            {{  $message }}
                        </div>
                    @enderror
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Sync Data</button>
          </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('footer')
<script type="text/javascript">
    $(document).ready(function(){
        $('#sync-absensi').click(function(e){
            e.preventDefault();

            let tanggal = $('#tanggal').val();
            let tanggal2 = $('#tanggal2').val();

            $.ajaxSetup({
                headers : {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            })

            $.ajax({
                url     : "{{url('Admin/Absensi/Data-Synchronous-Absensi')}}",
                method  : "POST",
                data    : {tanggal:tanggal, tanggal2:tanggal2},
                success : function(success){
                    swal("Sukses!", "Berhasil Sync Data Absensi!", "success");
                    setInterval(() => {
                        window.location.reload();
                    }, 2000);
                },
                error : function(error){
                    console.log(error);
                    swal("Gagal!", "Gagal Sync Data Absensi!\n Periksa Jaringan Anda!", "error");
                }
            });
        })
    })
</script>
@stop
