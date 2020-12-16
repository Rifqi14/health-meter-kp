@extends('admin.layouts.app')

@section('title', 'Ubah Medical Checkup')
@push('breadcrump')
    <li><a href="{{route('checkup.index')}}">Medical Checkup</a></li>
    <li class="active">Ubah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">

<style>
    fieldset.scheduler-border {
      border-radius: 3px;
      border: 1px solid #d2d6de !important;
      padding: 0 1.4em 1.4em 1.4em !important;
      margin: 0 0 1.5em 0 !important;
    }

    legend.scheduler-border {
        color:#333;
        font-size: 1.2em !important;
        text-align: left !important;
        width:auto;
        padding:0 10px;
        border-bottom: none
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Ubah  Medical Checkup</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form id="form" action="{{route('checkup.update',['id'=>$checkup->id])}}" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
               <input type="hidden" name="_method" value="put">
               <fieldset class="scheduler-border">
                <legend class="scheduler-border">Medical Checkup</legend>
                <div class="form-group">
                  <label for="name" class="col-sm-3 control-label">No Dokumen <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                  <input type="text" class="form-control" id="code" name="code" placeholder="No Dokumen" value="{{$checkup->code}}" readonly
                   required>
                  </div>
                </div>
                <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">Pegawai <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="employee_id" name="employee_id" data-placeholder="Pilih Pegawai" readonly required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">Tanggal Checkup <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                    <input type="text" class="form-control" id="checkup_date" name="checkup_date" placeholder="Tanggal Checkup" value="{{$checkup->checkup_date}}" required>
                    </div>
                  </div>
               </fieldset>
                  @foreach($medicals as $medical)
                  <fieldset class="scheduler-border">
                    <legend class="scheduler-border">{{$medical->name}}</legend>
                    @foreach($medicaldetails as $medicaldetail)
                    @if($medicaldetail->medical_id == $medical->id)
                    <input type="hidden" name="medicaldetail[]" value="{{$medicaldetail->id}}"/>
                    <div class="form-group">
                      <label for="name" class="col-sm-3 control-label">{{$medicaldetail->name}}</label>
                      <div class="col-sm-6">
                      <input type="text" class="form-control range" name="medicaldetail_{{$medicaldetail->id}}" placeholder="{{$medicaldetail->name}}" value="{{$medicaldetail->value}}">
                        
                      </div>
                    </div>
                    @endif
                    @endforeach
                  </fieldset>
                  @endforeach
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
    $('input[name=checkup_date]').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
    $( "#employee_id" ).select2({
        ajax: {
          url: "{{route('employee.select')}}",
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
      @if($checkup->employee)
      $("#employee_id").select2('data',{id:{{$checkup->employee->id}},text:'{{$checkup->employee->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#employee_id", function () {
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