@extends('admin.layouts.app')

@section('title', 'Tambah Kategori Resiko')
@push('breadcrump')
<li><a href="{{route('healthmeter.index')}}">Kategori Resiko</a></li>
<li class="active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Tambah Kategori Resiko</h3>
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
        <form id="form" action="{{route('healthmeter.store')}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <div class="form-group">
            <label for="site_id" class="col-sm-2 control-label">Distrik <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="site_id" name="site_id" placeholder="Pilih Distrik" required>
            </div>
          </div>
          <div class="form-group">
            <label for="workforce_group_id" class="col-sm-2 control-label">Kelompok Workforce <b
                class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="workforce_group_id" name="workforce_group_id"
                placeholder="Pilih Kelompok Workforce" required>
            </div>
          </div>
          <div class="form-group">
            <label for="min" class="col-sm-2 control-label">Min <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control numberfield" id="min" name="min" placeholder="Min" required>
            </div>
          </div>
          <div class="form-group">
            <label for="min" class="col-sm-2 control-label">Max <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control numberfield" id="max" name="max" placeholder="Max" required>
            </div>
          </div>
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Kategori <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="name" name="name" placeholder="Kategori" required>
            </div>
          </div>
          <div class="form-group">
            <label for="color" class="col-sm-2 control-label">Warna <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control my-colorpicker1" id="color" name="color" placeholder="Warna" required>
            </div>
          </div>
          <div class="form-group">
            <label for="recomendation" class="col-sm-2 control-label">Tindak lanjut <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <textarea class="form-control" id="recomendation" name="recomendation"
                placeholder="Tindak lanjut" required></textarea>
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
<script src="{{asset('adminlte/component/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script>
  $(document).ready(function(){
      $('.my-colorpicker1').colorpicker();
      $('.select2').select2({
        allowClear:true
      });
      $("#site_id").select2({
        ajax: {
          url: "{{route('site.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30,
              data_manager:{{$accesssite}},
              site_id : {{$siteinfo->id}}
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
      $(document).on("change", "#site_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#workforce_group_id").select2({
        ajax: {
          url: "{{route('workforcegroup.select')}}",
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
      $(document).on("change", "#workforce_group_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $(".numberfield").inputmask('decimal', {
        rightAlign: false
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