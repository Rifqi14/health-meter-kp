@extends('admin.layouts.app')

@section('title', 'Detail Tarif')
@push('breadcrump')
    <li><a href="{{route('inpatient.index')}}">Tarif</a></li>
    <li class="active">Detail</li>
@endpush
@section('stylesheets')
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Detail Tarif</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form id="form" action="{{route('inpatient.update',['id'=>$inpatient->id])}}" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
               <input type="hidden" name="_method" value="put">
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Nama</label>
                    <div class="col-sm-6">
                    <p class="form-control-static">{{$inpatient->name}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="price" class="col-sm-2 control-label">Harga</label>
                    <div class="col-sm-6">
                      <p class="form-control-static">{{number_format($inpatient->price,0,',','.')}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="price" class="col-sm-2 control-label">Catatan</label>
                    <div class="col-sm-6">
                      <p class="form-control-static">{{$inpatient->note}}</p>
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
      $(".numberfield").inputmask('decimal', {
        rightAlign: false
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