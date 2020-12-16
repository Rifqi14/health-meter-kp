@extends('admin.layouts.app')

@section('title', 'Info Akun')
@push('breadcrump')
    <li class="active">Info Akun</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.css')}}" rel="stylesheet">
<style type="text/css">
  #map {
       height: 200px;
       border: 1px solid #CCCCCC;
     }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Silahkan perbarui informasi di bawah ini.</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
          <form id="form" action="{{route('account.update',['id'=>Auth::guard('admin')->user()->id])}}"  enctype="multipart/form-data" method="post" accept-charset="utf-8">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put">
            <div class="well well-sm">
              <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" value="{{Auth::guard('admin')->user()->username}}"  class="form-control" id="username" readonly />
                      </div>
                    <div class="form-group">
                      <label for="name">Nama</label>
                      <input type="text" name="name" value="{{Auth::guard('admin')->user()->name}}"  class="form-control" id="name" readonly />
                    </div>
                    <div class="form-group">
                      <label for="email">Email</label>
                      <input type="text" name="email" value="{{Auth::guard('admin')->user()->email}}"  class="form-control" id="email" readonly />
                    </div>
                    <div class="form-group">
                      <label for="password">Kata Sandi</label>
                      <input type="password" name="password"  class="form-control" id="password"/>
                    </div>
                    <div class="form-group">
                    <label for="password">Kata Sandi Baru</label>
                    <input type="password" name="newpassword"  class="form-control" id="newpassword"/>
                    </div>
                    <div class="form-group">
                    <label for="password">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="newpassword_confirmation"  class="form-control" id="newpassword_confirmation"/>
                    </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                      <label for="foto">Foto </label>
                      <input type="file" class="form-control" name="foto" id="foto" accept="image/*"/>
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
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}
"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.js')}}"></script>
<script>
  $(document).ready(function(){
    $("#foto").fileinput({
      browseClass: "btn btn-default",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png","jpg"],
          dropZoneEnabled: false,
          initialPreview: '<img src="{{asset('assets/user/'.Auth::guard('admin')->user()->id.'.png')}}" class="kv-preview-data file-preview-image">',
          initialPreviewAsData: false,
          initialPreviewFileType: 'image',
          initialPreviewConfig: [
          {caption: "{{'assets/user/'.Auth::guard('admin')->user()->id.'.png'}}", downloadUrl: "{{asset('assets/user/'.Auth::guard('admin')->user()->id.'.png')}}", size:"{{ @File::size(public_path('assets/user/'.Auth::guard('admin')->user()->id.'.png'))}}",url: false}
          ],
          theme:'explorer'
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