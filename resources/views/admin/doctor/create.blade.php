@extends('admin.layouts.app')

@section('title', 'Tambah Dokter')
@push('breadcrump')
<li><a href="{{route('doctor.index')}}">Dokter</a></li>
<li class="active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Tambah Dokter</h3>
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
        <form id="form" action="{{route('doctor.store')}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="well well-sm">
            <div class="form-group">
              <label for="group" class="col-sm-2 control-label">Kelompok Dokter <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <select id="doctor_group" name="doctor_group" class="form-control select2" placeholder="Pilih Kelompok Dokter"
                  required>
                  <option value=""></option>
                  <option value="0">Dokter Perusahaan</option>
                  <option value="1">Dokter Eksternal</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="site_id" class="col-sm-2 control-label">Distrik <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="site_id" name="site_id" data-placeholder="Pilih Distrik" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="name" name="name" placeholder="Nama" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Telepon <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Telepon" required>
              </div>
            </div>
            <div class="form-group">
              <label for="id_partner" class="col-sm-2 control-label">Faskes</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="id_partner" name="id_partner" data-placeholder="Pilih Faskes">
              </div>
            </div>
            <div class="form-group">
              <label for="id_speciality" class="col-sm-2 control-label">Spesialisasi <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="id_speciality" name="id_speciality"
                  data-placeholder="Pilih Spesialisasi" required>
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
<script>
  $(document).ready(function(){
      $('input[name=status]').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
      });
      $('.select2').select2({
        allowClear: true
      });
      $( "#id_partner" ).select2({
        ajax: {
          url: "{{route('partner.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              display_name:term,
              page:page,
              limit:30,
              site_id:$('#site_id').val()==''?-1:$('#site_id').val()
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
      $(document).on("change", "#id_partner", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $( "#id_speciality" ).select2({
        ajax: {
          url: "{{route('speciality.select')}}",
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
      $(document).on("change", "#id_speciality", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#site_id").select2({
        ajax: {
            url: "{{route('site.select')}}",
            type:'GET',
            dataType: 'json',
            data: function (term,page) {
            return {
                name:term,
                page:page,
                limit:30,
                data_manager:{{$accesssite}},
                site_id : {{$siteinfo->id}}
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
      $(document).on("change", "#unit", function () {
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
</script>
@endpush