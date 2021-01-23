@extends('admin.layouts.app')

@section('title', 'Ubah Hasil Pemeriksaan')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-daterangepicker/css/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li><a href="{{route('checkupresult.index')}}">Hasil Pemeriksaan</a></li>
<li class="active">Ubah</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Ubah Hasil Pemeriksaan</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('checkupresult.update', ['id' => $result->id])}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          @method('PUT')
          <div class="box-body">
            <div class="well">
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
            </div>
            <div class="well">
              <div class="form-group">
                <label class="control-label col-sm-2" for="date">Tanggal Pemeriksaan <b class="text-danger">*</b></label>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="input-group" style="margin-right: 14px">
                      <input type="text" class="form-control date-picker" name="date" placeholder="Tanggal" required value="{{ $result->date }}">
                      <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="reference_id" class="col-sm-2 control-label">Rujukan</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="reference_id" name="reference_id" data-placeholder="Pilih Rujukan">
                </div>
              </div>
              <div class="form-group">
                <label for="checkup_schedule_id" class="col-sm-2 control-label">Panjadwalan Pemeriksaan</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="checkup_schedule_id" name="checkup_schedule_id" data-placeholder="Pilih Panjadwalan Pemeriksaan">
                </div>
              </div>
              <div class="form-group">
                <label for="partner_id" class="col-sm-2 control-label">Faskes <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="partner_id" name="partner_id" data-placeholder="Pilih Faskes" required>
                </div>
              </div>
              <div class="form-group">
                <label for="doctor_id" class="col-sm-2 control-label">Dokter</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="doctor_id" name="doctor_id" data-placeholder="Pilih Dokter">
                </div>
              </div>
            </div>
            <div class="well">
              <div class="form-group">
                <label for="examination_type_id" class="col-sm-2 control-label">Jenis Pemeriksaan <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="examination_type_id" name="examination_type_id" data-placeholder="Pilih Jenis Pemeriksaan" required>
                </div>
              </div>
              <div class="form-group">
                <label for="examination_evaluation_id" class="col-sm-2 control-label">Evaluasi Pemeriksaan <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="examination_evaluation_id" name="examination_evaluation_id" data-placeholder="Pilih Evaluasi Pemeriksaan" required>
                </div>
              </div>
              <div class="form-group">
                <label for="examination_evaluation_level_id" class="col-sm-2 control-label">Tingkat Evaluasi <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="examination_evaluation_level_id" name="examination_evaluation_level_id" data-placeholder="Pilih Tingkat Evaluasi" required>
                </div>
              </div>
              <div class="form-group">
                <label for="result" class="col-sm-2 control-label">Hasil Pemeriksaan <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="result" name="result" placeholder="Isi Hasil Pemeriksaan" value="{{ $result->result }}" required>
                </div>
              </div>
              <div class="form-group">
                <label for="normal_limit" class="col-sm-2 control-label">Batas Normal <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="normal_limit" name="normal_limit" placeholder="Isi Batas Normal" value="{{ $result->normal_limit }}" required>
                </div>
              </div>
              <div class="form-group">
                <label for="description" class="col-sm-2 control-label">Keterangan</label>
                <div class="col-sm-6">
                  <textarea class="form-control summernote" name="description" id="description">{{ $result->description }}</textarea>
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
      $("#examination_type_id").select2({
        ajax: {
            url: "{{route('examinationtype.select')}}",
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
      $(document).on("change", "#examination_type_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#examination_evaluation_id").select2({
        ajax: {
            url: "{{route('examinationevaluation.select')}}",
            type:'GET',
            dataType: 'json',
            data: function (term,page) {
            return {
                name:term,
                page:page,
                limit:30,
                examination_type_id:$("#examination_type_id").val(),
            };
            },
            results: function (data,page) {
              var more = (page * 30) < data.total;
              var option = [];
              $.each(data.rows,function(index,item){
                  option.push({
                    id:item.id,  
                    text: `${item.result_categories}`,
                  });
              });
              return {
                  results: option, more: more,
              };
            },
        },
        allowClear: true,
      });
      $(document).on("change", "#examination_evaluation_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#examination_evaluation_level_id").select2({
        ajax: {
            url: "{{route('evaluationlevel.select')}}",
            type:'GET',
            dataType: 'json',
            data: function (term,page) {
            return {
                name:term,
                page:page,
                limit:30,
                examination_evaluation_id: $("#examination_evaluation_id").val(),
            };
            },
            results: function (data,page) {
              var more = (page * 30) < data.total;
              var option = [];
              $.each(data.rows,function(index,item){
                  option.push({
                    id:item.id,  
                    text: `${item.examination_level}`,
                  });
              });
              return {
                  results: option, more: more,
              };
            },
        },
        allowClear: true,
      });
      $(document).on("change", "#examination_evaluation_level_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#checkup_schedule_id").select2({
        ajax: {
            url: "{{route('checkupschedule.select')}}",
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
                    text: `${item.patientname}`,
                    prod: `${item.prod}`
                  });
              });
              return {
                  results: option, more: more,
              };
            },
        },
        formatResult: function(item) {
          return item.prod;
        },
        formatSelection: function(item) {
          return item.prod;
        },
        allowClear: true,
      });
      $(document).on("change", "#checkup_schedule_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });

      @if($result->workforce_id)
        $("#workforce_id").select2('data', {id: {{ $result->workforce->id }}, text: `{{ $result->workforce->name }}`}).trigger('change');
      @endif
      @if($result->patient_id)
        $("#patient_id").select2('data', {id: {{ $result->patient->id }}, text: `{!! $result->patient->name !!}`}).trigger('change');
      @endif
      @if($result->checkup_schedule_id)
        $("#checkup_schedule_id").select2('data', {id: {{ $result->checkupschedule->id }}, text: `{!! $result->checkupschedule->patient->name !!}`, prod: `<span>{{ $result->checkupschedule->patient->name }}</span><span style='float:right'><i> {{ $result->checkupschedule->checkup_date }}</i></span>`}).trigger('change');
      @endif
      @if($result->partner_id)
        $("#partner_id").select2('data', {id: {{ $result->partner->id }}, text: `{!! $result->partner->name !!}`}).trigger('change');
      @endif
      @if($result->doctor_id)
        $("#doctor_id").select2('data', {id: {{ $result->doctor->id }}, text: `{!! $result->doctor->name !!}`}).trigger('change');
      @endif
      @if($result->examination_type_id)
        $("#examination_type_id").select2('data', {id: {{ $result->examinationtype->id }}, text: `{!! $result->examinationtype->name !!}`}).trigger('change');
      @endif
      @if($result->examination_evaluation_id)
        $("#examination_evaluation_id").select2('data', {id: {{ $result->evaluation->id }}, text: `{!! $result->evaluation->result_categories !!}`}).trigger('change');
      @endif
      @if($result->examination_evaluation_level_id)
        $("#examination_evaluation_level_id").select2('data', {id: {{ $result->evaluationlevel->id }}, text: `{!! $result->evaluationlevel->examination_level !!}`}).trigger('change');
      @endif
  });
</script>
@endpush