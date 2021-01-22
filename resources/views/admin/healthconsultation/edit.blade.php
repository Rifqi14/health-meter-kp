@extends('admin.layouts.app')

@section('title', 'Ubah Konsultasi Kesehatan')
@push('breadcrump')
<li><a href="{{route('healthconsultation.index')}}">Konsultasi Kesehatan</a></li>
<li class="active">Ubah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Ubah Konsultasi Kesehatan</h3>
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
        <form id="form" action="{{route('healthconsultation.update',['id'=>$healthconsultation->id])}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="well well-sm">
          <div class="form-group">
              <label for="group" class="col-sm-2 control-label">Tanggal <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="date" name="tanggal" id="tanggal" class="form-control" placeholder="Tanggal" required value="{{$healthconsultation->tanggal}}">
              </div>
            </div>
            <div class="form-group">
              <label for="group" class="col-sm-2 control-label">Pasien <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" name="patient" id="patient" class="form-control" data-placeholder="Pilih Pasien">
              </div>
            </div>
            <div class="form-group">
              <label for="site_id" class="col-sm-2 control-label">Distrik Pasien<b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="site_id" name="site_id" data-placeholder="Pilih Distrik Pasien" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Dokter <b class="text-danger">*</b></label>
              <div class="col-sm-6">
              <input type="text" name="doctor" id="doctor" class="form-control" data-placeholder="Pilih Dokter" required>
              </div>
            </div>
            <div class="form-group">
              <label for="site_id" class="col-sm-2 control-label">Distrik Dokter <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="distrik_id" name="distrik_id" data-placeholder="Pilih Distrik Dokter" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Keluhan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <textarea name="complaint" id="complaint" class="form-control" cols="30" rows="3" placeholder="Keluhan" require>{{$healthconsultation->complaint}}</textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Diagnosa</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="diagnose" name="diagnose" data-placeholder="Pilih Diagnosa">
              </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Keterangan</label>
                <div class="col-sm-6">
                    <textarea class="form-control summernote" name="note"
                            id="note">{{$healthconsultation->note}}</textarea>
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
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
  $(document).ready(function(){
    $('input[name=status]').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
      //Text Editor Summernote
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
      $('.select2').select2({
        allowClear: true
      });
      $( "#diagnose" ).select2({
        ajax: {
          url: "{{route('diagnosis.select')}}",
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
      @if($healthconsultation->diagnose)
      $("#diagnose").select2('data',{id:{{$healthconsultation->diagnose->id}},text:'{{$healthconsultation->diagnose->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#diagnose", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $( "#patient" ).select2({
        ajax: {
          url: "{{route('patient.select')}}",
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
      @if($healthconsultation->patient)
      $("#patient").select2('data',{id:{{$healthconsultation->patient->id}},text:'{{$healthconsultation->patient->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#patient", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $( "#site_id" ).select2({
        ajax: {
          url: "{{route('site.select')}}",
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
      @if($healthconsultation->site_patient)
      $("#site_id").select2('data',{id:{{$healthconsultation->site_patient->id}},text:'{{$healthconsultation->site_patient->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#site_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $( "#doctor" ).select2({
        ajax: {
          url: "{{route('doctor.select')}}",
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
      @if($healthconsultation->doctor)
      $("#doctor").select2('data',{id:{{$healthconsultation->doctor->id}},text:'{{$healthconsultation->doctor->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#doctor", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $( "#distrik_id" ).select2({
        ajax: {
          url: "{{route('site.select')}}",
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
      @if($healthconsultation->site_doctor)
      $("#distrik_id").select2('data',{id:{{$healthconsultation->site_doctor->id}},text:'{{$healthconsultation->site_doctor->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#distrik_id", function () {
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