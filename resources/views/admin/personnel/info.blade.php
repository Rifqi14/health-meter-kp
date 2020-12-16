@extends('admin.layouts.app')

@section('title', 'Detail Pegawai')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<style type="text/css">
    #map {
        height: 370px;
        border: 1px solid #CCCCCC;
    }
    .overlay-wrapper{
      position:relative;
    }
</style>
@endsection
@push('breadcrump')
<li class="active">Info Pegawai</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Detail Pegawai</h3>
                <div class="pull-right box-tools">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                </div>
            </div>
            <div class="box-body box-profile">
                <table class="table">
                    <tr>
                        <td width="25%"><strong>Jabatan</strong></td>
                        <td width="25%" class="text-right">{{$employee->movement[0]->title->name?$employee->movement[0]->title->name:'Tidak Ada'}}</td>
                        <td rowspan="9"><div id="map"></div></td>
                    </tr>
                    <tr>
                        <td><strong>NID</strong></td>
                        <td class="text-right">{{$employee->nid}}</td>
                    </tr>
                    <tr>
                        <td width="100"><strong>Nama</strong></td>
                        <td width="150" class="text-right">{{$employee->name}}</td>
                    </tr>
                    <tr>
                        <td><strong>Tipe</strong></td>
                        <td class="text-right">{{$employee->type}}</td>
                    </tr>
                    <tr>
                        <td><strong>Tempat & Tgl Lahir</strong></td>
                        <td class="text-right">{{$employee->region->name}} , {{ Carbon\Carbon::parse($employee->birth_date)->format('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Umur</strong></td>
                        <td class="text-right"><span class="label label-success">{{ Carbon\Carbon::parse($employee->birth_date)->age }} Tahun </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Hak Rawat Inap</strong></td>
                        <td class="text-right" id="inpatient">{{isset($employee->movement[0]->title->grade->name)?$employee->movement[0]->title->grade->name:'Tidak Ada'}}</td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td class="text-right" id="address">{{$employee->address}}</td>
                    </tr>
                    <tr>
                        <td><strong>Latitude</strong></td>
                        <td class="text-right" id="lat">{{$employee->latitude}}</td>
                    </tr>
                    <tr>
                        <td><strong>Longitude</strong></td>
                        <td class="text-right" id="long">{{$employee->longitude}}</td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="nav-tabs-custom tab-primary">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#employeefamily" data-toggle="tab">Tanggungan</a></li>
              <li><a href="#employeehistory" data-toggle="tab">Riwayat</a></li>
              <li><a href="#employeemedis" data-toggle="tab">Medis</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="employeefamily">
                <div class="overlay-wrapper">
                <table  class="table table-bordered table-striped" id="table-family" style="width:100%"style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align:center" width="10">#</th>
                            <th width="100" >Tipe</th>
                            <th width="250" >Nama</th>
                            <th width="100" >Tgl Lahir</th>
                            <th width="10" >#</th>
                        </tr>
                    </thead>
                </table>
                <div class="overlay hidden">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
                </div>
              </div>
              <div class="tab-pane" id="employeehistory">
                  <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Riwayat Pengisian</h3>
                            </div>
                            <div class="box-body">
                                <table  class="table table-bordered table-striped" style="width:100%" id="table-history">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right" width="10">#</th>
                                            <th style="text-align:center" width="100" >Tanggal</th>
                                            <th style="text-align:center" width="50" >Apakah Sehat?</th>
                                            <th style="text-align:center" width="50" >	Suhu Badan</th>
                                            <th style="text-align:center" width="50" >Saturasi</th>
                                            <th style="text-align:center" width="50" >Resiko Covid-19 (Hasil Presensi)</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="row">
                        <div class="col-md-6">
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">Temperatur Karyawan</h3>
                                </div>
                                <div class="box-body">
                                    <div id="chart-temperature">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">Saturasi Oksigen</h3>
                                </div>
                                <div class="box-body">
                                    <div id="chart-saturasi">
                                    </div>
                                </div>
                            </div>
                        </div>
                  </div>
              </div>
              <div class="tab-pane" id="employeemedis">
                <div class="overlay-wrapper">
                <a href="#" class="btn btn-primary pull-right btn-sm export-file" title="Example"><i class="fa fa-download"></i></a>
                <table  class="table table-bordered table-striped" id="table-medis" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align:center" width="10">#</th>
                            <th width="100" >No Dokumen</th>
                            <th width="250" >Nama</th>
                            <th width="100" >Hasil</th>
                            <th width="10" >#</th>
                        </tr>
                    </thead>
                </table>
                <div class="overlay hidden">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
                </div>
              </div>
            </div>
          </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript"
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk0A3uPdfOld8ZG1ibIZRaEktd-2Kv33E"></script>
<script>
    function adddetail(){
        $('#add-detail .modal-title').html('Tambah Tangungan');
        $('#add-detail').modal('show');
        $('#form')[0].reset();
        $('#form').attr('action','{{route('employeefamily.store')}}');
        $('#form input[name=_method]').attr('value','POST');
        $('#form .invalid-feedback').each(function () { $(this).remove(); });
        $('#form').find('.form-group').removeClass('has-error').removeClass('has-success');
        $('#form').find('select[name=type]').select2('val','');
        $('#form input[name=name]').attr('value','');
    }
    var map, geocoder, marker, infowindow;
    $(document).ready(function () {
        var lat = $("#lat").html()||-7.217416,
            long = $("#long").html()||112.72990470000002,
            address = $("#address").html(),

            options = {
                zoom: 15,
                center: new google.maps.LatLng(lat, long),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

        map = new google.maps.Map(document.getElementById('map'), options);

        setCoordinates(address,lat,long);

        $('.select2').select2();
        $('input[name=birth_date]').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        })
        $('input[name=birth_date]').on('change', function(){
            if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
            $(this).closest("form").validate().form();
            }
        });
        $.ajax({
            url: "{{route('personnel.temperature')}}",
            method: 'GET',
            dataType: 'json',
            data :{
                employee_id : {{$employee->id}}
            },
            beforeSend:function() {
                $('#chart-temperature').removeClass('no-after');
            },
            success:function(response) {
                $('#chart-temperature').addClass('no-after');
                Highcharts.chart('chart-temperature', {
                    title:{
                        text:response.date,
                    },
                    chart: {
                        type: 'column'
                    },
                    xAxis: {
                        categories: response.categories,
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '°C'
                        }
                    },
                    legend: {
                        enabled:false,
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="padding:0"><b>{point.y:.1f} °C</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    colors: ['#3c8dbc'],
                    credits: false,
                    series: [{
                        data: response.series
                    }]
                });
            }
        });

        $.ajax({
            url: "{{route('personnel.saturasi')}}",
            method: 'GET',
            dataType: 'json',
            data :{
                employee_id : {{$employee->id}}
            },
            beforeSend:function() {
                $('#chart-saturasi').removeClass('no-after');
            },
            success:function(response) {
                $('#chart-saturasi').addClass('no-after');
                Highcharts.chart('chart-saturasi', {
                    title:{
                        text:response.date,
                    },
                    chart: {
                        type: 'column'
                    },
                    xAxis: {
                        categories: response.categories,
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '%'
                        }
                    },
                    legend: {
                        enabled:false,
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    colors: ['#3c8dbc'],
                    credits: false,
                    series: [{
                        data: response.series
                    }]
                });
            }
        });
        dataTableFamily = $('#table-family').DataTable( {
            stateSave:true,
            processing: true,
            serverSide: true,
            filter:false,
            info:false,
            lengthChange:false,
            responsive: true,
            order: [[4, "asc" ]],
            ajax: {
                url: "{{url('admin/employeefamily/read')}}",
                type: "GET",
                data:function(data){
                    data.employee_id = {{$employee->id}};
                }
            },
            columnDefs:[
                {
                    orderable: false,targets:[0]
                },
                { className: "text-right", targets: [0] },
                { className: "text-center", targets: [4] },
                { render: function ( data, type, row ) {
                    return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item edit" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-edit"></i> Detail</a></li>
                    </ul></div>`
                },targets: [4]
                }
            ],
            columns: [
                { data: "no" },
                { data: "type" },
                { data: "name" },
                { data: "birth_date" },
                { data: "id" },
            ]
      });
      dataTableHistory = $('#table-history').DataTable( {
            stateSave:true,
            processing: true,
            serverSide: true,
            filter:false,
            info:false,
            lengthChange:false,
            responsive: true,
            order: [[1, "desc" ]],
            ajax: {
                url: "{{url('admin/personnel/history')}}",
                type: "GET",
                data:function(data){
                    data.employee_id = {{$employee->id}};
                }
            },
            columnDefs:[
                {
                    orderable: false,targets:[0]
                },
                { className: "text-right", targets: [0,2,3,4] },
                { className: "text-center", targets: [1,5] },
            ],
            columns: [
                { data: "no" },
                { data: "report_date" },
                { data: "sehat" },
                { data: "suhu_badan" },
                { data: "saturasi" },
                { data: "resiko" },
            ]
      });
      dataTableMedis = $('#table-medis').DataTable( {
            stateSave:true,
            processing: true,
            serverSide: true,
            filter:false,
            info:false,
            lengthChange:false,
            responsive: true,
            order: [[4, "asc" ]],
            ajax: {
                url: "{{url('admin/personnel/medis')}}",
                type: "GET",
                data:function(data){
                    data.employee_id = {{$employee->id}};
                }
            },
            columnDefs:[
                {
                    orderable: false,targets:[0]
                },
                { className: "text-right", targets: [0] },
                { className: "text-center", targets: [4] },
            ],
            columns: [
                { data: "no" },
                { data: "code" },
                { data: "name" },
                { data: "value" },
                { data: "id" },
            ]
      });
      $(document).on('click','.edit',function(){
          var id = $(this).data('id');
          $.ajax({
              url:`{{url('admin/employeefamily')}}/${id}/edit`,
              method:'GET',
              dataType:'json',
              beforeSend:function(){
                $('#employeefamily .overlay').removeClass('hidden');
              },
          }).done(function(response){
              $('#employeefamily .overlay').addClass('hidden');
              if(response.status){
                  $('#add-detail .modal-title').html('Ubah Tanggungan');
                  $('#add-detail').modal('show');
                  $('#form')[0].reset();
                  $('#form .invalid-feedback').each(function () { $(this).remove(); });
                  $('#form .form-group').removeClass('has-error').removeClass('has-success');
                  $('#form input[name=_method]').attr('value','PUT');
                  $('#form input[name=name]').attr('value',response.data.name);
                  $('#form input[name=birth_date]').attr('value',response.data.birth_date);
                  $('#form select[name=type]').select2('val',response.data.type);
                  $('#form').attr('action',`{{url('admin/employeefamily/')}}/${response.data.id}`);
              }
          }).fail(function(response){
              var response = response.responseJSON;
              $('#employeefamily .overlay').addClass('hidden');
              $.gritter.add({
                  title: 'Error!',
                  text: response.message,
                  class_name: 'gritter-error',
                  time: 1000,
              });
          })
      })
      $(document).on('click',".export-file",function(){
            $.ajax({
                url: "{{ route('personnel.exportmedis') }}",
                type: 'GET',
                dataType: 'JSON',
                data:{
                    employee_id:{{$employee->id}}
                },
                beforeSend:function(){
                    $('#employeemedis .overlay').removeClass('hidden');
                }
            }).done(function(response){
                if(response.status){
                    $('#employeemedis .overlay').addClass('hidden');
                    $.gritter.add({
                        title: 'Success!',
                        text: response.message,
                        class_name: 'gritter-success',
                        time: 1000,
                    });
                    let download = document.createElement("a");
                        download.href = response.file;
                        document.body.appendChild(download);
                        download.download = response.name;
                        download.click();
                        download.remove();
                }
                else{
                    $.gritter.add({
                        title: 'Warning!',
                        text: response.message,
                        class_name: 'gritter-warning',
                        time: 1000,
                    });
                }
            }).fail(function(response){
                var response = response.responseJSON;
                $('#employeemedis .overlay').removeClass('hidden');
                $.gritter.add({
                    title: 'Error!',
                    text: response.message,
                    class_name: 'gritter-error',
                    time: 1000,
                });
            });
        }); 
    });
    function setCoordinates(address,latitude,longitude) {
    // Mengecek apakah terdapat 'geocoded object'. Jika tidak maka buat satu.
      
          map.setCenter(new google.maps.LatLng(latitude, longitude));
    
        // Mengecek apakah terdapat objek marker
        if (!marker) {
          // Membuat objek marker dan menambahkan ke peta
          marker = new google.maps.Marker({
            map: map,
      draggable:false,
          });
        }

        // Menentukan posisi marker ke lokasi returned location
    
    marker.setPosition(new google.maps.LatLng(latitude, longitude));
  
        // Mengecek apakah terdapat InfoWindow object
        if (!infowindow) {
          // Membuat InfoWindow baru
          infowindow = new google.maps.InfoWindow();
        }
        // membuat konten InfoWindow ke alamat
        // dan posisi yang ditemukan
        var content = '<strong>' + address + '</strong><br/>';
        content += 'Lat: ' + latitude + '<br />';
        content += 'Lng: ' + longitude;
    
        // Menambahkan konten ke InfoWindow
        infowindow.setContent(content);

        // Membuka InfoWindow
        infowindow.open(map, marker);

    // Membuat rekues Geocode
    
  }
</script>
@endpush
