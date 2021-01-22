@extends('admin.layouts.app')

@section('title', 'Ubah Penjadwalan Pemeriksaan')
@push('breadcrump')
<li><a href="{{route('healthmeter.index')}}">Penjadwalan Pemeriksaan</a></li>
<li class="active">Ubah</li>
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
        <h3 class="box-title">Ubah Penjadwalan Pemeriksaan</h3>
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
        <form id="form" action="{{route('checkupschedule.update', ['id'=>$checkupschedule->id])}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="form-group">
            <label for="patient_id" class="col-sm-2 control-label">Pasien <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="patient_id" name="patient_id" placeholder="Pilih Pasien" required>
            </div>
          </div>
          <div class="form-group">
            <label for="examination_type_id" class="col-sm-2 control-label">Jenis Pemeriksaan <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="examination_type_id" name="examination_type_id" placeholder="Pilih Jenis Pemeriksaan" required>
            </div>
          </div>
          <div class="form-group">
            <label for="date" class="col-sm-2 control-label">Tanggal Pemeriksaan<b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control date" id="checkup_date" placeholder="Tanggal Pemeriksaan"
                  name="checkup_date" value="{{$checkupschedule->checkup_date}}">
              </div>
          </div>
          <div class="form-group">
            <label for="description" class="col-sm-2 control-label">Keterangan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <textarea class="form-control summernote" name="description" id="description" value="{{$checkupschedule->description}}">{{$checkupschedule->description}}</textarea>
              </div>
          </div>
          <div class="form-group">
            <label for="schedules_maker_id" class="col-sm-2 control-label">NID Pembuat <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="schedules_maker_id" name="schedules_maker_id" placeholder="Pilih Pembuat" required>
              <input type="hidden" class="form-control" id="schedule_maker_title_id" value="{{$checkupschedule->schedule_maker_title_id}}" name="schedule_maker_title_id">
            </div>
          </div>
           <div class="form-group">
            <label for="first_approval_id" class="col-sm-2 control-label">NID Approval 1 <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="first_approval_id" name="first_approval_id" placeholder="Pilih NID Approval 1" required>
              <input type="hidden" class="form-control" id="first_approval_title_id" value="{{$checkupschedule->first_approval_title_id}}" name="first_approval_title_id">
            </div>
          </div>
          <div class="form-group">
            <label for="second_approval_id" class="col-sm-2 control-label">NID Approval 2 <b class="hidden-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="second_approval_id" name="second_approval_id" placeholder="Pilih NID Approval 2" required>
              <input type="hidden" class="form-control" id="second_approval_title_id" value="{{$checkupschedule->second_approval_title_id}}" name="second_approval_title_id">
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
     
       $('.date').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
      $("#patient_id").select2({
        ajax: {
          url: "{{route('patient.select')}}",
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
      $("#patient_id").select2('data',{
        id:{{$checkupschedule->patient_id}},
        text:'{{$checkupschedule->patient->name}}'
      }).trigger('change');
      $(document).on("change", "#patient_id", function () {
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
              limit:30
            
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
      $("#examination_type_id").select2('data',{
        id:{{$checkupschedule->examination_type_id}},
        text:'{{$checkupschedule->examinationtype->name}}'
      }).trigger('change');
      $(document).on("change", "#examination_type_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#schedules_maker_id").select2({
        ajax: {
          url: "{{route('workforce.select')}}",
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
                text: `${item.nid}`,
                title: `${item.title_id}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $("#schedules_maker_id").select2('data',{
        id:{{$checkupschedule->schedules_maker_id}},
        text:'{{$checkupschedule->w_schedulemaker->name}}'
      }).trigger('change');
      $(document).on("change", "#schedules_maker_id", function () {

        var title = $('#schedules_maker_id').select2('data').title;
        $('#schedule_maker_title_id').val(`${title}`);

        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });

      $("#first_approval_id").select2({
        ajax: {
          url: "{{route('workforce.select')}}",
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
                text: `${item.nid}`,
                title: `${item.title_id}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
       $("#first_approval_id").select2('data',{
        id:{{$checkupschedule->first_approval_id}},
        text:'{{$checkupschedule->w_firstapproval->name}}'
      }).trigger('change');
      $(document).on("change", "#first_approval_id", function () {

        var title = $('#first_approval_id').select2('data').title;
        $('#first_approval_title_id').val(`${title}`);

        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#second_approval_id").select2({
        ajax: {
          url: "{{route('workforce.select')}}",
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
                text: `${item.nid}`,
                title: `${item.title_id}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $("#second_approval_id").select2('data',{
        id:{{$checkupschedule->second_approval_id}},
        text:'{{$checkupschedule->w_secondapproval->name}}'
      }).trigger('change');
      $(document).on("change", "#second_approval_id", function () {

        var title = $('#second_approval_id').select2('data').title;
        $('#second_approval_title_id').val(`${title}`);

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