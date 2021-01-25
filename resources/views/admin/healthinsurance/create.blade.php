@extends('admin.layouts.app')

@section('title', 'Tambah Jaminan Kesehatan')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-daterangepicker/css/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li><a href="{{route('healthinsurance.index')}}">Jaminan Kesehatan</a></li>
<li class="active">Tambah</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Tambah Jaminan Kesehatan</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('healthinsurance.store')}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="box-body">
            <div class="form-group">
              <label for="cover_letter_type" class="col-sm-2 control-label">Jenis Surat Pengantar <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <select name="cover_letter_type" id="cover_letter_type" class="form-control select2" required>
                  @foreach (config('enums.authority') as $key => $item)
                  @if (strpos(strtoupper($item), 'JAMINAN'))
                  <option value="{{ $key }}">{{ $item }}</option>
                  @endif
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="letter_number" class="col-sm-2 control-label">Nomor Surat <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="letter_number" name="letter_number" placeholder="Nomor Surat" required>
              </div>
            </div>
            <div class="form-group">
              <label for="workforce_id" class="col-sm-2 control-label">Workforce <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="workforce_id" name="workforce_id" data-placeholder="Pilih Workforce" required>
              </div>
            </div>
            <div class="form-group">
              <label for="patient_id" class="col-sm-2 control-label">Pasien <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="patient_id" name="patient_id" data-placeholder="Pilih Pasien" required>
              </div>
            </div>
            <div class="form-group">
              <label for="reference_id" class="col-sm-2 control-label">Rujukan</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="reference_id" name="reference_id" data-placeholder="Pilih Rujukan">
              </div>
            </div>
            <div class="form-group">
              <label for="partner_id" class="col-sm-2 control-label">Faskes</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="partner_id" name="partner_id" data-placeholder="Pilih Faskes">
              </div>
            </div>
            <div class="form-group">
              <label for="doctor_id" class="col-sm-2 control-label">Dokter</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="doctor_id" name="doctor_id" data-placeholder="Pilih Dokter">
              </div>
            </div>
            <div class="form-group">
              <label for="inpatient_id" class="col-sm-2 control-label">Tarif <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="inpatient_id" name="inpatient_id" data-placeholder="Pilih Tarif" required>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Keterangan</label>
              <div class="col-sm-6">
                <textarea class="form-control summernote" name="description" id="description"></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2" for="date_in">Tanggal Masuk <b class="text-danger">*</b></label>
              <div class="row">
                <div class="col-sm-6">
                  <div class="input-group" style="margin-right: 14px">
                    <input type="text" class="form-control date-picker" name="date_in" placeholder="Tanggal Masuk" required>
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2" for="date">Tanggal <b class="text-danger">*</b></label>
              <div class="row">
                <div class="col-sm-6">
                  <div class="input-group" style="margin-right: 14px">
                    <input type="text" class="form-control date-picker" name="date" placeholder="Tanggal" required>
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </div>
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
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/moment/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-daterangepicker/js/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script>
  $(document).ready(function(){
      $('.select2').select2();
      //Text Editor Component
      $('.summernote').summernote({
          height: 180,
          placeholder: 'Tulis sesuatu disini...',
          toolbar: [
              ['style', ['bold', 'italic', 'underline', 'clear']],
              ['font', ['strikethrough', 'superscript', 'subscript']],
              ['fontsize', ['fontsize']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['height', ['height']]
          ]
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
      //date
      $('.date-picker').datepicker({
          autoclose: true,
          format: 'yyyy-mm-dd'
      })
      // Select2
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
                    text: `${item.name}`,
                    namenid: `${item.namenid}`
                  });
              });
              return {
                  results: option, more: more,
              };
            },
        },
        formatResult: function(item) {
          return item.namenid
        },
        allowClear: true,
      });
      $(document).on("change", "#workforce_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#patient_id").select2({
        ajax: {
            url: "{{route('patient.select')}}",
            type:'GET',
            dataType: 'json',
            data: function (term,page) {
            return {
                name:term,
                page:page,
                limit:30,
                workforce_id: $("#workforce_id").val(),
            };
            },
            results: function (data,page) {
              var more = (page * 30) < data.total;
              var option = [];
              $.each(data.rows,function(index,item){
                  option.push({
                    id:item.id,  
                    text: `${item.name}`,
                  });
              });
              return {
                  results: option, more: more,
              };
            },
        },
        allowClear: true,
      });
      $(document).on("change", "#patient_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#partner_id").select2({
        ajax: {
            url: "{{route('partner.select')}}",
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
                    text: `${item.name}`,
                  });
              });
              return {
                  results: option, more: more,
              };
            },
        },
        allowClear: true,
      });
      $(document).on("change", "#partner_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#doctor_id").select2({
        ajax: {
            url: "{{route('doctor.select')}}",
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
                    text: `${item.name}`,
                  });
              });
              return {
                  results: option, more: more,
              };
            },
        },
        allowClear: true,
      });
      $(document).on("change", "#doctor_id", function () {
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
                    text: `${item.name}`,
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
  });
</script>
@endpush