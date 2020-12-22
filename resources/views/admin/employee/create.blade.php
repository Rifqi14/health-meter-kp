@extends('admin.layouts.app')

@section('title', 'Tambah Pegawai')
@push('breadcrump')
<li><a href="{{route('employee.index')}}">Pegawai</a></li>
<li class="active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<style type="text/css">
  #map {
    height: 300px;
    border: 1px solid #CCCCCC;
  }
</style>
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Tambah Pegawai</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i
              class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('employee.store')}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="well well-sm">
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Jabatan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="title_id" name="title_id" data-placeholder="Pilih Jabatan"
                  required>
              </div>
            </div>
            <div class="form-group">
              <label for="nid" class="col-sm-2 control-label">NID <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="nid" name="nid" placeholder="NID" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="name" name="name" placeholder="Nama" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Tipe <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <select id="type" name="type" class="form-control select2" placeholder="Pilih Tipe" required>
                  <option value=""></option>
                  <option value="permanent">Pegawai Tetap</option>
                  <option value="internship">Alih Daya</option>
                  <option value="pensiun">Pensiun</option>
                  <option value="other">Lainya</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Jenis Kelamin <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <select id="type" name="gender" class="form-control select2" placeholder="Pilih Jenis Kelamin" required>
                  <option value=""></option>
                  <option value="m">Laki - Laki</option>
                  <option value="f">Perempuan</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Tempat Lahir <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="place_of_birth" name="place_of_birth"
                  placeholder="Tempat Lahir" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Tanggal Lahir <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="birth_date" name="birth_date" placeholder="Tanggal Lahir"
                  required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Jenjang Jabatan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="grade_id" name="grade_id"
                  data-placeholder="Pilih Jenjang Jabatan" required>
              </div>
            </div>
            <div class="form-group">
              <label for="unit" class="col-sm-2 control-label">Unit <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="unit" name="unit" data-placeholder="Pilih Unit" required>
              </div>
            </div>
          </div>
          <div class="well well-sm">
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Telepon <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Telepon" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Alamat <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <textarea name="address" id="address" class="form-control" placeholder="Alamat"></textarea>
                <div id="map"></div>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Latitude</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude">
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Longitude</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude">
              </div>
            </div>
          </div>
          <div class="well well-sm">
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Role <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="role_id" name="role_id" data-placeholder="Pilih Role"
                  required>
              </div>
            </div>
            <div class="form-group">
              <label for="email" class="col-sm-2 control-label">Email <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
              </div>
            </div>
            <div class="form-group">
              <label for="password" class="col-sm-2 control-label">Password <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                  required>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript"
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk0A3uPdfOld8ZG1ibIZRaEktd-2Kv33E"></script>
<script>
  var map, geocoder, marker, infowindow;
  $(document).ready(function(){
    var options = { 
      zoom: 10, 
      center: new google.maps.LatLng(-7.217416, 112.72990470000002), 
      mapTypeId: google.maps.MapTypeId.ROADMAP 
    }; 
		 
    map = new google.maps.Map(document.getElementById('map'), options);
		 
    // Mengambil referensi ke form HTML
    $( "#form textarea[name=address]" ).keyup(function() {
      address = $(this).val();
      getCoordinates(address);
    });
      $("input[name=nid]").inputmask("Regex", { regex: "[A-Za-z0-9]*" });
      $('.select2').select2({
        allowClear:true
      });
      $('input[name=birth_date]').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
      $('input[name=birth_date]').on('change', function(){
        if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
          $(this).closest("form").validate().form();
        }
      });
      $( "#role_id" ).select2({
        ajax: {
          url: "{{route('role.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              display_name:term,
              page:page,
              limit:30,
            };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
              option.push({
                id:item.id,  
                text: `${item.display_name}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $("#unit").select2({
        ajax: {
            url: "{{route('site.select')}}",
            type:'GET',
            dataType: 'json',
            data: function (term,page) {
            return {
                name:term,
                page:page,
                limit:30,
            };
            },
            results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
                option.push({
                id:item.id,  
                text: `${item.name}`
                });
            });
            return {
                results: option, more: more,
            };
            },
        },
        allowClear: true,
      });
      $(document).on("change", "#role_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $( "#title_id" ).select2({
        ajax: {
          url: "{{route('title.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30,
            };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
              option.push({
                id:item.id,  
                text: `${item.name}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#title_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $( "#grade_id" ).select2({
        ajax: {
          url: "{{route('grade.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30,
            };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
              option.push({
                id:item.id,  
                text: `${item.name}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#grade_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $( "#place_of_birth" ).select2({
        ajax: {
          url: "{{route('region.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30,
            };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
              option.push({
                id:item.id,  
                text: `${item.type} ${item.name}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#title_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#form").validate({
        errorElement: 'span',
        errorClass: 'help-block',
        focusInvalid: false,
        highlight: function (e) {
          $(e).closest('.form-group').removeClass('has-success').addClass('has-error');
        },
    
        success: function (e) {
          $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
          $(e).remove();
        },
        errorPlacement: function (error, element) {
          if(element.is(':file')) {
            error.insertAfter(element.parent().parent().parent());
          }else
          if(element.parent('.input-group').length) {
            error.insertAfter(element.parent());
          } 
          else
          if (element.attr('type') == 'checkbox') {
            error.insertAfter(element.parent());
          }
          else{
            error.insertAfter(element);
          }
        },
        submitHandler: function() { 
          $.ajax({
            url:$('#form').attr('action'),
            method:'post',
            data: new FormData($('#form')[0]),
            processData: false,
            contentType: false,
            dataType: 'json', 
            beforeSend:function(){
               $('.overlay').removeClass('hidden');
            }
          }).done(function(response){
                $('.overlay').addClass('hidden');
                if(response.status){
                  document.location = response.results;
                }
                else{	
                  $.gritter.add({
                      title: 'Warning!',
                      text: response.message,
                      class_name: 'gritter-warning',
                      time: 1000,
                  });
                }
                return;
          }).fail(function(response){
              $('.overlay').addClass('hidden');
              var response = response.responseJSON;
              $.gritter.add({
                  title: 'Error!',
                  text: response.message,
                  class_name: 'gritter-error',
                  time: 1000,
              });
          })		
        }
      });
  });

  function updateMarkerPosition(latLng) {
	  if(!geocoder) {
        geocoder = new google.maps.Geocoder(); 
      }
 
      // Membuat objek GeocoderRequest
      var geocoderRequest = {
        latLng: latLng
      }
 
      // Membuat rekues Geocode
      geocoder.geocode(geocoderRequest, function(results, status) {
 
        // Mengecek apakah ststus OK sebelum proses
        if (status == google.maps.GeocoderStatus.OK) {
 
          // Menengahkan peta pada lokasi
 
          // Mengecek apakah terdapat objek marker
          if (!marker) {
            // Membuat objek marker dan menambahkan ke peta
            marker = new google.maps.Marker({
              map: map,
			  draggable: true
            });
          }
 
          // Menentukan posisi marker ke lokasi returned location
          marker.setPosition(results[0].geometry.location);
 
          // Mengecek apakah terdapat InfoWindow object
          if (!infowindow) {
            // Membuat InfoWindow baru
            infowindow = new google.maps.InfoWindow();
          }
		  google.maps.event.addListener(marker, 'drag', function() {
			
			updateMarkerPosition(marker.getPosition());
		  });

          // membuat konten InfoWindow ke alamat
          // dan posisi yang ditemukan
          var content = '<strong>' + results[0].formatted_address + '</strong><br />';
          content += 'Lat: ' + results[0].geometry.location.lat() + '<br />';
          content += 'Lng: ' + results[0].geometry.location.lng();
			
		  $('#form input[name=latitude]').attr('value',results[0].geometry.location.lat());
		  $('#form input[name=longitude]').attr('value',results[0].geometry.location.lng());
          // Menambahkan konten ke InfoWindow
          infowindow.setContent(content);
 
          // Membuka InfoWindow
          infowindow.open(map, marker);
 
        }
 
      });
	}

  // Membuat sebuah fungsi yang mengembalikan koordinat alamat
  function getCoordinates(address) {
      // Mengecek apakah terdapat 'geocoded object'. Jika tidak maka buat satu.
      if(!geocoder) {
        geocoder = new google.maps.Geocoder(); 
      }
 
      // Membuat objek GeocoderRequest
      var geocoderRequest = {
        address: address
      }
 
      // Membuat rekues Geocode
      geocoder.geocode(geocoderRequest, function(results, status) {
 
        // Mengecek apakah ststus OK sebelum proses
        if (status == google.maps.GeocoderStatus.OK) {
 
          // Menengahkan peta pada lokasi
          map.setCenter(results[0].geometry.location);
 
          // Mengecek apakah terdapat objek marker
          if (!marker) {
            // Membuat objek marker dan menambahkan ke peta
            marker = new google.maps.Marker({
              map: map,
			  draggable: true
            });
          }
 
          // Menentukan posisi marker ke lokasi returned location
          marker.setPosition(results[0].geometry.location);
 
          // Mengecek apakah terdapat InfoWindow object
          if (!infowindow) {
            // Membuat InfoWindow baru
            infowindow = new google.maps.InfoWindow();
          }
		  google.maps.event.addListener(marker, 'drag', function() {
			
			updateMarkerPosition(marker.getPosition());
		  });

          // membuat konten InfoWindow ke alamat
          // dan posisi yang ditemukan
          var content = '<strong>' + results[0].formatted_address + '</strong><br />';
          content += 'Lat: ' + results[0].geometry.location.lat() + '<br />';
          content += 'Lng: ' + results[0].geometry.location.lng();
		
		  $('#form input[name=latitude]').attr('value',results[0].geometry.location.lat());
		  $('#form input[name=longitude]').attr('value',results[0].geometry.location.lng());
		  //$('#alamat_pelanggan_tmp').attr('value',results[0].formatted_address);
          // Menambahkan konten ke InfoWindow
          infowindow.setContent(content);
 
          // Membuka InfoWindow
          infowindow.open(map, marker);
 
        }
 
      });
 
    }
</script>
@endpush