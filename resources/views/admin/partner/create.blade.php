@extends('admin.layouts.app')

@section('title', 'Tambah Partner')
@push('breadcrump')
<li><a href="{{route('partner.index')}}">Partner</a></li>
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
                <h3 class="box-title">Tambah Partner</h3>
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
                <form id="form" action="{{ route('partner.store') }}" class="form-horizontal" method="post"
                    autocomplete="off">
                    {{ csrf_field() }}
                    <div class="well well-sm">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nama"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="category" class="col-sm-2 control-label">Kategori <b
                                    class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <Select id="type" name="category" class="form-control select2" placeholder="Pilih Kategori"
                                    required>
                                    <option value=""></option>
                                    <option value="drugstore">Apotek</option>
                                    <option value="hospital">Rumah Sakit</option>
                                    <option value="laboratorium">Laboratorium</option>
                                </Select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="col-sm-2 control-label">Phone <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Telepon"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-6">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address" class="col-sm-2 control-label">Alamat <b
                                    class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <textarea name="address" id="address" class="form-control"
                                    placeholder="Alamat"></textarea>
                                <div id="map"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="latitude" class="col-sm-2 control-label">Latitude</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="latitude" name="latitude"
                                    placeholder="Latitude">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="longtitude" class="col-sm-2 control-label">Longitude</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="longitude" name="longitude"
                                    placeholder="Longitude">
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
    $(document).ready(function () {
        var options = {
            zoom: 10,
            center: new google.maps.LatLng(-7.217416, 112.72990470000002),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById('map'), options);

        // Mengambil referensi ke form HTML
        $("#form textarea[name=address]").keyup(function () {
            address = $(this).val();
            getCoordinates(address);
        });
        $('.select2').select2({
            allowClear: true
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
                if (element.is(':file')) {
                    error.insertAfter(element.parent().parent().parent());
                } else
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else
                if (element.attr('type') == 'checkbox') {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function () {
                $.ajax({
                    url: $('#form').attr('action'),
                    method: 'post',
                    data: new FormData($('#form')[0]),
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function () {
                        $('.overlay').removeClass('hidden');
                    }
                }).done(function (response) {
                    $('.overlay').addClass('hidden');
                    if (response.status) {
                        document.location = response.results;
                    } else {
                        $.gritter.add({
                            title: 'Warning!',
                            text: response.message,
                            class_name: 'gritter-warning',
                            time: 1000,
                        });
                    }
                    return;
                }).fail(function (response) {
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
        if (!geocoder) {
            geocoder = new google.maps.Geocoder();
        }

        // Membuat objek GeocoderRequest
        var geocoderRequest = {
            latLng: latLng
        }

        // Membuat rekues Geocode
        geocoder.geocode(geocoderRequest, function (results, status) {

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
                google.maps.event.addListener(marker, 'drag', function () {

                    updateMarkerPosition(marker.getPosition());
                });

                // membuat konten InfoWindow ke alamat
                // dan posisi yang ditemukan
                var content = '<strong>' + results[0].formatted_address + '</strong><br />';
                content += 'Lat: ' + results[0].geometry.location.lat() + '<br />';
                content += 'Lng: ' + results[0].geometry.location.lng();

                $('#form input[name=latitude]').attr('value', results[0].geometry.location.lat());
                $('#form input[name=longitude]').attr('value', results[0].geometry.location.lng());
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
        if (!geocoder) {
            geocoder = new google.maps.Geocoder();
        }

        // Membuat objek GeocoderRequest
        var geocoderRequest = {
            address: address
        }

        // Membuat rekues Geocode
        geocoder.geocode(geocoderRequest, function (results, status) {

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
                google.maps.event.addListener(marker, 'drag', function () {

                    updateMarkerPosition(marker.getPosition());
                });

                // membuat konten InfoWindow ke alamat
                // dan posisi yang ditemukan
                var content = '<strong>' + results[0].formatted_address + '</strong><br />';
                content += 'Lat: ' + results[0].geometry.location.lat() + '<br />';
                content += 'Lng: ' + results[0].geometry.location.lng();

                $('#form input[name=latitude]').attr('value', results[0].geometry.location.lat());
                $('#form input[name=longitude]').attr('value', results[0].geometry.location.lng());
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
