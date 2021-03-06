@extends('admin.layouts.app')

@section('title', 'Ubah Kontak Erat')
@push('breadcrump')
<li><a href="{{route('healthmeter.index')}}">Kontak Erat</a></li>
<li class="active">Ubah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Ubah Kontak Erat</h3>
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
        <form id="form" action="{{route('closecontact.update', ['id'=>$closecontact->id])}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="form-group">
            <label for="workforce_id" class="col-sm-2 control-label">Workforce <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="workforce_id" name="workforce_id" placeholder="Pilih NID" required>
            </div>
          </div>
          <div class="form-group">
            <label for="date" class="col-sm-2 control-label">Tanggal <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control date" id="date" value="{{$closecontact->date}}" placeholder="Tanggal"
                  name="date">
              </div>
          </div>
          <div class="form-group">
            <label for="description" class="col-sm-2 control-label">Keterangan Interview <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <textarea class="form-control summernote" value="{{$closecontact->description}}" name="description" id="description">{{$closecontact->description}}</textarea>
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
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script>
  $(document).ready(function(){
     
       $('.date').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
      $("#workforce_id").select2({
        ajax: {
          url: "{{route('workforce.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30
            
            };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
              option.push({
                id:item.id,  
                text: `${item.nid}`,
                namenid: `${item.namenid}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
        formatResult: function(item) {
          return item.namenid
        },
      });
      $("#workforce_id").select2('data',{
        id:{{$closecontact->workforce_id}},
        text:'{{$closecontact->workforce->name}}'
      }).trigger('change');

      $(document).on("change", "#workforce_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
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