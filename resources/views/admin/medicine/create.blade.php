@extends('admin.layouts.app')

@section('title', 'Tambah Obat')
@push('breadcrump')
<li><a href="{{route('medicine.index')}}">Obat</a></li>
<li class="active">Tambah</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Tambah Obat</h3>
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
        <form id="form" action="{{route('medicine.store')}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="box-body">
            <div class="form-group">
              <label for="code" class="col-sm-2 control-label">Kode <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="code" name="code" placeholder="Kode" required>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="name" name="name" placeholder="Nama" required>
              </div>
            </div>
            <div class="form-group">
              <label for="medicine_category" class="col-sm-2 control-label">Kategori Obat <b
                  class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="medicine_category" name="medicine_category"
                  placeholder="Kategori Obat" required>
              </div>
            </div>
            <div class="form-group">
              <label for="medicine_group" class="col-sm-2 control-label">Kelompok Obat <b
                  class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="medicine_group" name="medicine_group"
                  placeholder="Kelompok Obat" required>
              </div>
            </div>
            <div class="form-group">
              <label for="medicine_type" class="col-sm-2 control-label">Jenis Obat <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="medicine_type" name="medicine_type" placeholder="Jenis Obat"
                  required>
              </div>
            </div>
            <div class="form-group">
              <label for="level" class="col-sm-2 control-label">Kadar <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="level" name="level" placeholder="Kadar" required>
              </div>
            </div>
            <div class="form-group">
              <label for="medicine_unit" class="col-sm-2 control-label">Satuan Obat <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="medicine_unit" name="medicine_unit"
                  placeholder="Satuan Obat" required>
              </div>
            </div>
            <div class="form-group">
              <label for="price" class="col-sm-2 control-label">Harga</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="price" name="price" placeholder="Harga">
              </div>
            </div>
            <div class="form-group">
              <label for="description" class="col-sm-2 control-label">Deskripsi</label>
              <div class="col-sm-6">
                <textarea class="form-control" id="description" name="description" placeholder="Deskripsi"></textarea>
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
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
  $(document).ready(function(){
      $("#medicine_category").select2({
        ajax: {
          url: "{{route('medicinecategory.select')}}",
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
                text: `${item.description}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#medicine_category", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#medicine_group").select2({
        ajax: {
          url: "{{route('medicinegroup.select')}}",
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
                text: `${item.description}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#medicine_group", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#medicine_unit").select2({
        ajax: {
          url: "{{route('medicineunit.select')}}",
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
                text: `${item.description}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#medicine_unit", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#medicine_type").select2({
        ajax: {
          url: "{{route('medicinetype.select')}}",
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
                text: `${item.description}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#medicine_type", function () {
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