@extends('admin.layouts.app')

@section('title', 'Ubah Tingkat Evaluasi Pemeriksaan')
@push('breadcrump')
<li><a href="{{route('evaluationlevel.index')}}">Tingkat Evaluasi Pemeriksaan</a></li>
<li class="active">Ubah</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Ubah Tingkat Evaluasi Pemeriksaan</h3>
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
        <form id="form" action="{{route('evaluationlevel.update',['id'=>$evaluation->id])}}" class="form-horizontal"
          method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="box-body">
            <div class="form-group">
              <label for="evaluation" class="col-sm-2 control-label">Evaluasi Pemeriksaan <b
                  class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="evaluation" name="evaluation"
                  placeholder="Evaluasi Pemeriksaan" value="{{ $evaluation->examination_evaluation_id }}" required>
              </div>
            </div>
            <div class="form-group">
              <label for="level" class="col-sm-2 control-label">Tingkat Evaluasi <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="level" name="level" placeholder="Tingkat Evaluasi"
                  value="{{ $evaluation->examination_level }}" required>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" style="padding-top: 1px" for="status">Status Aktif</label>
              <div class="col-sm-4">
                <label><input class="form-control" type="checkbox" name="status" @if ($evaluation->status)
                  checked
                  @endif> <i></i></label>
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
      $('input[name=status]').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
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
      $("#evaluation").select2({
        ajax: {
          url: "{{route('examinationevaluation.select')}}",
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
                text: `${item.result_categories}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#evaluation", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      @if ($evaluation->examination_evaluation_id)
        $("#evaluation").select2('data',{id:{{$evaluation->evaluation->id}},text:'{{$evaluation->evaluation->result_categories}}'}).trigger('change');
      @endif
  });
</script>
@endpush