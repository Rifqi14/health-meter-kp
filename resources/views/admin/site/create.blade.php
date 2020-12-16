@extends('admin.layouts.app')

@section('title', 'Tambah Unit')
@push('breadcrump')
    <li><a href="{{route('site.index')}}">Unit</a></li>
    <li class="active">Tambah</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Tambah Unit</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form id="form" action="{{route('site.store')}}"  method="post" autocomplete="off">
               {{ csrf_field() }}
               <div class="col-md-6">
                <div class="form-group">
                  <label for="code">Kode <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" id="code" name="code" placeholder="Kode" required>
                        <p class="help-block">Ex. upgresik (Only letters lowercase input).</p>
                </div>
                <div class="form-group">
                  <label for="name">Nama <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Nama" required>
                </div>
                <div class="form-group">
                  <label for="phone">Telepon <b class="text-danger">*</b></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Telepon" required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="email">Email <b class="text-danger">*</b></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email"  required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="province_id">Provinsi <b class="text-danger">*</b></label>
                    <input type="text" class="form-control" id="province_id" name="province_id" data-placeholder="Pilih Provinsi" required>
                </div>
                <div class="form-group">
                  <label for="region_id">Kota/Kabupaten <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" id="region_id" name="region_id" data-placeholder="Pilih Kota/Kabupaten" required>
                </div>
                <div class="form-group">
                  <label for="district_id">Kecamatan <b class="text-danger">*</b></label>
                    <input type="text" class="form-control" id="district_id" name="district_id" data-placeholder="Pilih Kecamatan" required>
                </div>
                <div class="form-group">
                  <label for="address">Alamat <b class="text-danger">*</b></label>
                  <textarea class="form-control" id="address" name="address" placeholder="Alamat" required></textarea>
                </div>

               <div class="form-group">
                <label for="postal_code">Kode Pos <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="Kode Pos" required>
              </div>
               </div>
               <div class="col-md-6">
                <div class="form-group">
                  <label for="logo">Logo </label>
                    <input type="file" class="form-control" name="logo" id="logo" accept="image/*" required/>
                </div>
                <div class="form-group">
                  <label for="receipt_header">Catatan Kepala </label>
                    <textarea class="form-control summernote" name="receipt_header" id="receipt_header"></textarea>
                </div>
                <div class="form-group">
                  <label for="receipt_footer">Catatan Kaki </label>
                  <textarea class="form-control summernote" name="receipt_footer" id="receipt_footer"></textarea>
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
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.js')}}"></script>
<script>
  $(document).ready(function(){
      //Input Mask Component 
      $("input[name=code]").inputmask("Regex", { regex: "[a-z]*" });
      //Select2 Component
      $( "#province_id" ).select2({
        ajax: {
          url: "{{route('province.select')}}",
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
      $( "#region_id" ).select2({
        ajax: {
          url: "{{route('region.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              province_id:$('#province_id').val(),
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
                text: `${item.type} ${item.name}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $( "#district_id" ).select2({
        ajax: {
          url: "{{route('district.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              region_id:$('#region_id').val(),
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
      $(document).on("change", "#province_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        $('#region_id').select2('val','');
        $('#district_id').select2('val','');
      });
      $(document).on("change", "#region_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        $('#district_id').select2('val','');
      });
      $(document).on("change", "#district_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      //Text Editor Component
      $('.summernote').summernote({
            height:225,
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
      //Bootstrap fileinput component
      $("#logo").fileinput({
          browseClass: "btn btn-default",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png"],
          dropZoneEnabled: false,
          theme:'explorer'
      });
      $(document).on("change", "#logo", function () {
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
            error.insertAfter(element.parent());
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