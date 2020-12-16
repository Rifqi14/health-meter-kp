@extends('admin.layouts.app')

@section('title', 'Ubah Kategori')
@push('breadcrump')
    <li><a href="{{route('category.index')}}">Kategori</a></li>
    <li class="active">Ubah</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Ubah Kategori</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form id="form" action="{{route('category.update',['id'=>$category->id])}}" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
               <input type="hidden" name="_method" value="put">
                <div class="box-body">
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Nama" value="{{$category->name}}" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Parameter <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                       <select id="parameter" name="parameter" class="form-control select2" placeholder="Pilih Tipe Operasi" required>
                          <option value=""></option>
                          <option value="employee" @if($category->parameter == 'employee') selected @endif>Jumlah Personal</option>
                          <option value="subcategory" @if($category->parameter == 'subcategory') selected @endif>Sub Kategori</option>
                       </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Tipe <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                       <select id="type" name="type" class="form-control select2" placeholder="Pilih Tipe Kategori" required>
                          <option value=""></option>
                          <option value="summary"  @if($category->type == 'summary') selected @endif>Total</option>
                          <option value="filled"  @if($category->type == 'filled') selected @endif>Pengisian</option>
                       </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Input <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                       <select id="input" name="input" class="form-control select2" placeholder="Pilih Tipe Input" required>
                          <option value=""></option>
                          <option value="personil"  @if($category->input == 'personil') selected @endif>Personil</option>
                          <option value="supervisor"  @if($category->input == 'supervisor') selected @endif>Supervisor</option>
                       </select>
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
     $('.select2').select2({
        allowClear:true
      });
      $(document).on("change", ".select2", function () {
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