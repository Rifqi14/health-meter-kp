@extends('admin.layouts.app')

@section('title', 'Ubah Pasien')
@push('breadcrump')
<li><a href="{{route('patient.index')}}">Pasien</a></li>
<li class="active">Ubah</li>
@endpush
@section('stylesheet')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Ubah Pasien</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('patient.update', ['id' => $patient->id])}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          @method('PUT')
          <div class="box-body">
            <div class="form-group">
              <label for="workforce_id" class="col-sm-2 control-label">Keluarga Workforce <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="workforce_id" name="workforce_id" placeholder="Keluarga Workforce" required>
              </div>
            </div>
            <div class="form-group">
              <label for="code" class="col-sm-2 control-label">Kode <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="code" name="code" placeholder="Kode" required value="{{ $patient->code }}">
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="name" name="name" placeholder="Nama" required value="{{ $patient->name }}">
              </div>
            </div>
            <div class="form-group">
              <label for="nid" class="col-sm-2 control-label">NID <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="nid" name="nid" placeholder="NID" required value="{{ $patient->nid }}">
              </div>
            </div>
            <div class="form-group">
              <label for="status" class="col-sm-2 control-label">Status <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="status" name="status" placeholder="Status" required value="{{ $patient->status }}">
              </div>
            </div>
            <div class="form-group">
              <label for="birth_date" class="col-sm-2 control-label">Tanggal Lahir <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="birth_date" name="birth_date" placeholder="Tanggal Lahir" required value="{{ $patient->birth_date }}">
              </div>
            </div>
            <div class="form-group">
              <label for="site_id" class="col-sm-2 control-label">Unit <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="site_id" name="site_id" data-placeholder="Pilih Unit" required>
              </div>
            </div>
            <div class="form-group">
              <label for="department_id" class="col-sm-2 control-label">Divisi Bidang <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="department_id" name="department_id" data-placeholder="Pilih Divisi Bidang" required>
              </div>
            </div>
            <div class="form-group">
              <label for="sub_department_id" class="col-sm-2 control-label">Sub Divisi Bidang <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="sub_department_id" name="sub_department_id" data-placeholder="Pilih Sub Divisi Bidang" required>
              </div>
            </div>
            <div class="form-group">
              <label for="inpatient_id" class="col-sm-2 control-label">Tarif <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="inpatient_id" name="inpatient_id" data-placeholder="Pilih Tarif" required>
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
      $('input[name=birth_date]').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
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
      $(document).on("change", "#site_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#department_id").select2({
        ajax: {
            url: "{{route('department.select')}}",
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
      $(document).on("change", "#department_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#sub_department_id").select2({
        ajax: {
            url: "{{route('subdepartment.select')}}",
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
      $(document).on("change", "#sub_department_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#inpatient_id").select2({
        ajax: {
            url: "{{route('inpatient.select')}}",
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
      $(document).on("change", "#inpatient_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#workforce_id").select2({
        ajax: {
            url: "{{route('workforce.select')}}",
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
      $(document).on("change", "#workforce_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });

      @if ($patient->workforce_id)
        $('#workforce_id').select2('data', {id:{{ $patient->workforce_id }}, text: `{{ $patient->workforce->name }}`}).trigger('change');
      @endif
      @if ($patient->site_id)
        $('#site_id').select2('data', {id:{{ $patient->site_id }}, text: `{{ $patient->site->name }}`}).trigger('change');
      @endif
      @if ($patient->department_id)
        $('#department_id').select2('data', {id:{{ $patient->department_id }}, text: `{{ $patient->department->name }}`}).trigger('change');
      @endif
      @if ($patient->sub_department_id)
        $('#sub_department_id').select2('data', {id:{{ $patient->sub_department_id }}, text: `{{ $patient->subdepartment->name }}`}).trigger('change');
      @endif
      @if ($patient->inpatient_id)
        $('#inpatient_id').select2('data', {id:{{ $patient->inpatient_id }}, text: `{{ $patient->inpatient->name }}`}).trigger('change');
      @endif
  });
</script>
@endpush