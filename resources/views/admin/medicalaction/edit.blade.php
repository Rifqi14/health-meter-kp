@extends('admin.layouts.app')

@section('title', 'Ubah Tindakan')
@push('breadcrump')
<li><a href="{{route('medicalaction.index')}}">Tindakan</a></li>
<li class="active">Ubah</li>
@endpush
@section('stylesheets')
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Ubah Tindakan</h3>
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
        <form id="form" action="{{route('medicalaction.update',['id'=>$medicalaction->id])}}" class="form-horizontal"
          method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="form-group">
            <label for="examination" class="col-sm-2 control-label">Jenis Pemeriksaan <b
                class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="examination" name="examination"
                placeholder="Jenis Pemeriksaan" value="{{$medicalaction->examination_type_id}}" required>
            </div>
          </div>
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Kode <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="code" name="code" placeholder="Kode"
                value="{{$medicalaction->code}}" required>
            </div>
          </div>
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="name" name="name" placeholder="Nama"
                value="{{$medicalaction->name}}" required>
            </div>
          </div>
          <div class="form-group">
            <label for="price" class="col-sm-2 control-label">Template <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <select name="template_id" id="template_id" class="form-control select2" placeholder="Pilih Template"
                required>
                <option value=""></option>
                @foreach($templates as $template)
                <option value="{{$template->id}}" @if($template->id == $medicalaction->template_id) selected
                  @endif>{{$template->name}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="price" class="col-sm-2 control-label">Keterangan <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <textarea name="description" id="description" class="form-control"
                placeholder="Keterangan">{{$medicalaction->description}}</textarea>
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
    $('.select2').select2();
    $("input[name=code]").inputmask("Regex", {
          regex: "[a-z]*"
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
    $("#examination").select2({
      ajax: {
        url: "{{route('examinationtype.select')}}",
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
    $(document).on("change", "#examination", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    @if ($medicalaction->examination_type_id)
      $("#examination").select2('data',{id:{{$medicalaction->examination->id}},text:'{{$evaluation->examination->name}}'}).trigger('change');
    @endif
  });
</script>
@endpush