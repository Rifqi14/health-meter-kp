@extends('admin.layouts.app')

@section('title', 'Tambah Kartu Kontrol')
@push('breadcrump')
<li><a href="{{route('controlcard.index')}}">Kartu Kontrol</a></li>
<li class="active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Tambah Kartu Kontrol</h3>
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
        <form id="form" action="{{route('controlcard.store')}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <div class="form-group">
            <label for="checkup_result_id" class="col-sm-2 control-label">Hasil Pemeriksaan<b class="text-danger">*</b></label>
              <div class="col-sm-6">
                  <input type="text" class="form-control" id="checkup_result_id" placeholder="Hasil Pemeriksaan" name="checkup_result_id" required>
                  <input type="hidden" name="nid" id="workforce_id">
                  <input type="hidden" name="checkup_examination_evaluation_id" id="checkup_examination_evaluation_id">
                  <input type="hidden" name="checkup_examination_evaluation_level_id" id="checkup_examination_evaluation_level_id">
              </div>
          </div>
          <div class="form-group">
            <label for="date" class="col-sm-2 control-label">Tanggal<b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control date" id="control_date" placeholder="Tanggal"
                  name="control_date">
              </div>
          </div>
          <div class="form-group">
            <label for="examination_evaluation_id" class="col-sm-2 control-label">Evaluasi Pemeriksaan <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="examination_evaluation_id" name="examination_evaluation_id" placeholder="Pilih Evaluasi Pemeriksaan" required>
            </div>
          </div>
          <div class="form-group">
            <label for="examination_evaluation_level_id" class="col-sm-2 control-label">Tingkat Evaluasi <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="examination_evaluation_level_id" name="examination_evaluation_level_id" placeholder="Pilih Tingkat Evaluasi" required>
            </div>
          </div>
           <div class="form-group">
            <label for="description" class="col-sm-2 control-label">Keterangan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <textarea class="form-control summernote" name="description" id="description"></textarea>
              </div>
          </div>
          <div class="form-group">
            <label for="authorized_official_id" class="col-sm-2 control-label">Approval 1 <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="authorized_official_id" name="authorized_official_id" placeholder="Pilih Approval 1" required>
            </div>
          </div>
          <div class="form-group">
            <label for="guarantor_id" class="col-sm-2 control-label">Approval 2 <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="guarantor_id" name="guarantor_id" placeholder="Pilih Approval 1" required>
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
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script>
  $(document).ready(function(){
     $('.select2').select2();
       $('.date').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
      $("#checkup_result_id").select2({
        ajax: {
          url: "{{route('checkupresult.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30
            
            };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
              option.push({
                id:item.id,  
                text: `${item.result}`,
                custom: `${item.custom}`,
                examination_evaluation_id: item.examination_evaluation_id,
                examination_evaluation_level_id: item.examination_evaluation_level_id,
                workforce_id: item.workforce_id
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        formatResult: function(item) {
          return item.custom
        },
        allowClear: true,
      });
      $(document).on("change", "#checkup_result_id", function () {
        var workforce_id = $('#checkup_result_id').select2('data').workforce_id;
        $('#workforce_id').val(`${workforce_id}`);

        var examinationEvaluation = $('#checkup_result_id').select2('data').examination_evaluation_id;
        $('#checkup_examination_evaluation_id').val(`${examinationEvaluation}`);

        var examination_evaluation_level_id = $('#checkup_result_id').select2('data').examination_evaluation_level_id;
        $('#checkup_examination_evaluation_level_id').val(`${examination_evaluation_level_id}`);
        
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        // $('#patient_id').select2('val','');
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
              limit:30
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
      $("#authorized_official_id").select2({
        ajax: {
          url: "{{route('authorizedofficial.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30
            
            };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
              option.push({
                id:item.id,  
                text: `${item.authority}`
                
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#authorized_official_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#guarantor_id").select2({
        ajax: {
          url: "{{route('guarantor.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30
            
            };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
              option.push({
                id:item.id,  
                text: `${item.title_name}`,
                custom: `${item.custom}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        formatResult: function(item) {
          return item.custom
        },
        allowClear: true,
      });
      $(document).on("change", "#guarantor_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
     
     
      //Text Editor Component
      $('.summernote').summernote({
            height:180,
            placeholder:'Tulis sesuatu disini...',
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
  });
</script>
@endpush