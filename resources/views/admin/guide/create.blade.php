@extends('admin.layouts.app')

@section('title', 'Tambah Guide')
@push('breadcrump')
    <li><a href="{{route('document.index')}}">Guide</a></li>
    <li class="active">Tambah</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Tambah Guide</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form id="form" action="{{route('guide.store')}}" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control numberfield" id="name" name="name" placeholder="Nama" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="file">File Excel</label>
                    <div class="col-sm-6">
                        <input type="file" class="form-control" id="file" name="file" required accept=".pdf"/>
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
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $("input[name=code]").inputmask("Regex", {
                regex: "[a-z]*"
        });

        $("#file").fileinput({
        browseClass: "btn btn-default",
        showRemove: false,
        showUpload: false,
        allowedFileExtensions: ["pdf"],
        dropZoneEnabled: false,
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
