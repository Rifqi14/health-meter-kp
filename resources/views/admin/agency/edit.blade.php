@extends('admin.layouts.app')

@section('title', 'Ubah Instansi')
@push('breadcrump')
<li><a href="{{route('agency.index')}}">Instansi</a></li>
<li class="active">Ubah</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Ubah Instansi</h3>
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
        <form id="form" action="{{route('agency.update', ['id' => $agency->id])}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          @method('PUT')
          <div class="box-body">
            <div class="form-group">
              <label for="code" class="col-sm-2 control-label">Kode <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="code" name="code" placeholder="Kode"
                  value="{{ $agency->code }}" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Name <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                  value="{{ $agency->name }}" required>
              </div>
            </div>
            <div class="form-group">
              <label for="authentication" class="col-sm-2 control-label">Autentikasi <b
                  class="text-danger">*</b></label>
              <div class="col-sm-6">
                <select id="type" name="authentication" class="form-control select2" placeholder="Pilih Autentikasi" required>
                  <option value="local" @if($agency->authentication == 'local') selected @endif>Local</option>
                  <option value="ldap" @if($agency->authentication == 'ldap') selected @endif>LDAP</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="host" class="col-sm-2 control-label">Host <b
                class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="host" name="host" placeholder="Host"
                  value="{{ $agency->host }}" required>
              </div>
            </div>
            <div class="form-group">
              <label for="port" class="col-sm-2 control-label">Port <b
                class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="port" name="port" placeholder="Port"
                  value="{{ $agency->port }}" required>
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
<script>
  $(document).ready(function(){
    $("input[name=port]").inputmask("Regex", { regex: "[0-9]*" });
      $('.select2').select2({
        allowClear:true
      });
      $("select[name=authentication]").on('change',function(){
         $('input[name=host]').closest('.form-group').hide();
         $('input[name=port]').closest('.form-group').hide();
         if(this.value == 'ldap'){
          $('input[name=host]').closest('.form-group').show();
          $('input[name=port]').closest('.form-group').show();
         }
      });
      $("select[name=authentication]").trigger('change');
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