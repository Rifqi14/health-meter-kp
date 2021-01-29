@extends('admin.layouts.app')

@section('title', 'Umum')
@push('breadcrump')
<li class="active">Umum</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.css')}}" rel="stylesheet">
<style type="text/css">
  #map {
    height: 200px;
    border: 1px solid #CCCCCC;
  }
</style>
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Silahkan perbarui informasi di bawah ini.</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('config.update')}}" enctype="multipart/form-data" method="post" accept-charset="utf-8">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="well well-sm">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="app_name">Nama Aplikasi</label>
                  <input type="text" name="app_name" value="{{config('configs.app_name')}}" class="form-control" id="app_name" required />
                </div>
                <div class="form-group">
                  <label for="app_name">Copyright</label>
                  <input type="text" name="app_copyright" value="{{config('configs.company_name')}}" class="form-control" id="app_copyright" required />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="app_logo">Logo </label>
                  <input type="file" class="form-control" name="app_logo" id="app_logo" accept="image/*" />
                </div>
              </div>
            </div>
          </div>
          <div class="well well-sm">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="company_name">Nama Perusahaan</label>
                  <input type="text" name="company_name" value="{{config('configs.company_name')}}" class="form-control" id="company_name" required />
                </div>
                <div class="form-group">
                  <label for="company_email">Email Perusahaan</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                    <input type="email" name="company_email" value="{{config('configs.company_email')}}" class="form-control" id="company_email" required />
                  </div>
                </div>
                <div class="form-group">
                  <label for="company_phone">Telepon Perusahaan</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                    <input type="text" name="company_phone" value="{{config('configs.company_phone')}}" class="form-control" id="company_phone" required />
                  </div>
                </div>
                <div class="form-group">
                  <label for="company_address">Alamat</label>
                  <textarea class="form-control" id="company_address" name="company_address" placeholder="Alamat" required>{{config('configs.company_address')}}</textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="company_address">Peta</label>
                  <div id="map"></div>
                </div>
                <div class="form-group">
                  <label for="company_latitude">Latitude</label>
                  <input type="text" name="company_latitude" value="{{config('configs.company_latitude',-7.217416)}}" class="form-control" id="company_latitude" />
                </div>
                <div class="form-group">
                  <label for="company_longitude">Longitude</label>
                  <input type="text" name="company_longitude" value="{{config('configs.company_longitude',112.72990470000002)}}" class="form-control" id="company_longitude" />
                </div>
              </div>
            </div>
          </div>
          <div class="well well-sm">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="bot_icon">BOT Icon </label>
                  <input type="file" class="form-control" name="bot_icon" id="bot_icon" accept="image/*" />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="bot_username">BOT Username </label>
                  <input type="text" name="bot_username" value="{{config('configs.bot_username')}}" class="form-control" id="bot_username" required />
                </div>
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
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}
"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk0A3uPdfOld8ZG1ibIZRaEktd-2Kv33E"></script>
<script>
  var map, geocoder, marker, infowindow;
  $(document).ready(function(){
    var options = { 
      zoom: 10, 
      center: new google.maps.LatLng(-7.217416, 112.72990470000002), 
      mapTypeId: google.maps.MapTypeId.ROADMAP 
    }; 
		 
    map = new google.maps.Map(document.getElementById('map'), options);
    setCoordinates('{{config('configs.company_address')}}',{{config('configs.company_latitude',-7.217416)}},{{config('configs.company_longitude',112.72990470000002)}});
    $( "#form textarea[name=company_address]" ).keyup(function() {
      address = $(this).val();
      getCoordinates(address);
    });
    $("#app_logo").fileinput({
      browseClass: "btn btn-default",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png"],
          dropZoneEnabled: false,
          initialPreview: '<img src="{{asset(config('configs.app_logo'))}}" class="kv-preview-data file-preview-image">',
          initialPreviewAsData: false,
          initialPreviewFileType: 'image',
          initialPreviewConfig: [
          {caption: "{{config('configs.app_logo')}}", downloadUrl: "{{asset(config('configs.app_logo'))}}", size:"{{ File::size(public_path(config('configs.app_logo')))}}",url: false}
          ],
          theme:'explorer'
      });
    $("#bot_icon").fileinput({
      browseClass: "btn btn-default",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png", "jpeg", "jpg"],
          dropZoneEnabled: false,
          initialPreview: '<img src="{{asset(config('configs.bot_icon'))}}" class="kv-preview-data file-preview-image">',
          initialPreviewAsData: false,
          initialPreviewFileType: 'image',
          initialPreviewConfig: [
          {caption: "{{config('configs.bot_icon')}}", downloadUrl: "{{asset(config('configs.bot_icon'))}}", size:"{{ File::size(public_path(config('configs.bot_icon')))}}",url: false}
          ],
          theme:'explorer'
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

  function setCoordinates(address,latitude,longitude) {
    // Mengecek apakah terdapat 'geocoded object'. Jika tidak maka buat satu.
      
          map.setCenter(new google.maps.LatLng(latitude, longitude));
    
        // Mengecek apakah terdapat objek marker
        if (!marker) {
          // Membuat objek marker dan menambahkan ke peta
          marker = new google.maps.Marker({
            map: map,
      draggable:true,
          });
        }

        // Menentukan posisi marker ke lokasi returned location
    
    marker.setPosition(new google.maps.LatLng(latitude, longitude));
  
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
        var content = '<strong>' + address + '</strong><br/>';
        content += 'Lat: ' + latitude + '<br />';
        content += 'Lng: ' + longitude;
    
        // Menambahkan konten ke InfoWindow
        infowindow.setContent(content);

        // Membuka InfoWindow
        infowindow.open(map, marker);

    // Membuat rekues Geocode
    
  }
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
			
		  $('#form input[name=company_latitude]').attr('value',results[0].geometry.location.lat());
		  $('#form input[name=company_longitude]').attr('value',results[0].geometry.location.lng());
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
		
		  $('#form input[name=company_latitude]').attr('value',results[0].geometry.location.lat());
		  $('#form input[name=company_longitude]').attr('value',results[0].geometry.location.lng());
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