@extends('admin.layouts.app')

@section('title', 'Tambah Laporan Atasan (Fungsi/Bidang)')
@push('breadcrump')
    <li><a href="{{route('supervisor.index')}}">Lapor Laporan Atasan (Fungsi/Bidang)</a></li>
    <li class="active">Tambah</li>
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
          <h3 class="box-title">Tambah  Laporan Atasan (Fungsi/Bidang)</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form id="form" action="{{route('supervisor.store')}}" method="post" autocomplete="off">
               {{ csrf_field() }}
               <fieldset class="scheduler-border">
                <legend class="scheduler-border">Laporan</legend>
                <div class="form-group">
                  <label for="name" class="col-sm-3 control-label">Tanggal Laporan <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                  <input type="text" class="form-control" id="report_date" name="report_date" placeholder="Tanggal Laporan" required value="{{date('Y-m-d')}}">
                  </div>
                </div>
               </fieldset>
                  @foreach($categories as $category)
                  <fieldset class="scheduler-border">
                    <legend class="scheduler-border">{{$category->name}}</legend>
                    @foreach($subcategories as $subcategory)
                    @if($subcategory->category_id == $category->id)
                    <input type="hidden" name="id[]" value="{{$subcategory->id}}"/>
                    <div class="form-group">
                      <label for="name" class="control-label">{{$subcategory->name}} <b class="text-danger">*</b> 
                        <i class="fa fa-info-circle text-primary" style="cursor: pointer" data-toggle="modal"
                        data-target="#modalinfo{{$subcategory->id}}"></i></label>
                       @if($subcategory->type == 'range')
                      <input type="text" class="form-control range" name="subcategory_{{$subcategory->id}}" required placeholder="{{$subcategory->name}}" data-rule-min="{{$subcategory->min}}" data-rule-max="{{$subcategory->max}}">
                        @endif
                        @if($subcategory->type == 'yesno')
                        <select class="select2 form-control" name="subcategory_{{$subcategory->id}}" placeholder="Pilih {{$subcategory->name}}" required>
                          <option value=""></option>
                          <option value="1">Yes</option>
                          <option value="0">No</option>
                        </select>
                        @endif
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
@foreach($categories as $category)
@foreach($subcategories as $subcategory)
@if($subcategory->category_id == $category->id)
<div class="modal fade" id="modalinfo{{$subcategory->id}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Cara Pengisian</h5>
            </div>
            <div class="modal-body">
            {!!$subcategory->information!!}
            </div>
        </div>
    </div>

</div>
@endif
@endforeach
@endforeach
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
  $(document).ready(function(){
    $('input[name=report_date]').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
      $('.range').inputmask('decimal', {
        rightAlign: false
      });
      $('.select2').select2({
        allowClear:true
      });
      $('.select2').on('change',function(){
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