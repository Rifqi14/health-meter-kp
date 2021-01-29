@extends('admin.layouts.app')

@section('title', 'Tambah Pejabat Berwenang')
@push('breadcrump')
<li><a href="{{route('authorizedofficial.index')}}">Pejabat Berwenang</a></li>
<li class="active">Tambah</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Tambah Pejabat Berwenang</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('authorizedofficial.store')}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="box-body">

            <div class="form-group">
              <label for="site_id" class="col-sm-2 control-label">Distrik <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="site_id" name="site_id" data-placeholder="Pilih Distrik"
                  required>
              </div>
            </div>
            <div class="form-group">
              <label for="title_id" class="col-sm-2 control-label">Jabatan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="title_id" name="title_id" data-placeholder="Pilih Jabatan"
                  required>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" style="padding-top: 1px" for="authority_type">Jenis Kewenangan</label>
              <div class="col-sm-6">
                <label><input class="form-control iCheck" type="checkbox" name="authority_type"> <i></i></label>
                <span class="help-block">Centang jika jenis kewenangan melalui approval sistem. Biarkan jika membutuhkan tanda tangan basah</span>
              </div>
            </div>
            <div class="form-group">
              <label for="level" class="col-sm-2 control-label">Level <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="number" class="form-control" id="level" name="level" placeholder="Level" value="0" required>
              </div>
            </div>
            <div class="form-group">
              <label for="authority" class="col-sm-2 control-label">Jenis Kewenangan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <select id="authority" name="authority" class="form-control select2" placeholder="Pilih Jenis Kewenangan" required>
                  <option value=""></option>
                  @foreach(config('enums.authority') as $key => $authority)
                    <option value="{{$authority}}">{{$authority}}</option>
                  @endforeach
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
      $('.iCheck').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
      });
      $(".select2").select2();
      $( "#site_id" ).select2({
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
      $( "#title_id" ).select2({
        ajax: {
          url: "{{route('title.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30,
              site_id:$('#site_id').val()==''?-1:$('#site_id').val()
            };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
              option.push({
                id:item.id,  
                text: `${item.name} - ${item.code}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#title_id", function () {
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