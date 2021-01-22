@extends('admin.layouts.app')

@section('title', 'Ubah Pertanyaan Assessment')
@push('breadcrump')
<li><a href="{{route('assessmentquestion.index')}}">Pertanyaan Assessment</a></li>
<li class="active">Ubah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Ubah Pertanyaan Assessment</h3>
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
        <form id="form" action="{{route('assessmentquestion.update',['id'=>$question->id])}}" class="form-horizontal"
          method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="box-body">
            <div class="form-group">
              <label for="order" class="col-sm-2 control-label">Urutan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="number" class="form-control" id="order" name="order" placeholder="Urutan" required
                  value="{{ $question->order }}">
              </div>
            </div>
            <div class="form-group">
              <label for="question_parent_code" class="col-sm-2 control-label">Parent Pertanyaan</label>
              <div class="col-sm-6" style="padding-top: 5px">
                <input type="text" class="form-control" id="question_parent_code" placeholder="Parent Pertanyaan"
                  name="question_parent_code">
              </div>
            </div>
            <div class="form-group">
              <label for="answer_parent_code" class="col-sm-2 control-label">Parent Opsi Jawaban</label>
              <div class="col-sm-6" style="padding-top: 5px">
                <input type="text" class="form-control" id="answer_parent_code" placeholder="Parent Opsi Jawaban"
                  name="answer_parent_code">
              </div>
            </div>
            <div class="form-group">
              <label for="type" class="col-sm-2 control-label">Jenis Pertanyaan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <select id="type" name="type" class="form-control select2" placeholder="Pilih Type"
                  required>
                  <option value=""></option>
                  <option value="Informasi" @if($question->type == 'Informasi') selected @endif>Informasi</option>
                  <option value="Pertanyaan" @if($question->type == 'Pertanyaan') selected @endif>Pertanyaan</option>
                  <option value="Informasi Dan Pertanyaan" @if($question->type == 'Informasi Dan Pertanyaan') selected @endif>Informasi Dan Pertanyaan</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="description" class="col-sm-2 control-label">Pertanyaan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <textarea class="form-control" style="resize: vertical" id="description" placeholder="Pertanyaan"
                  name="description" required>{{ $question->description }}</textarea>
              </div>
            </div>
            <div class="form-group">
              <label for="description_information" class="col-sm-2 control-label">Informasi <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <textarea class="form-control" style="resize: vertical" id="description_information" placeholder="Informasi"
                  name="description_information" required>{{ $question->description_information }}</textarea>
              </div>
            </div>
            <div class="form-group">
              <label for="frequency" class="col-sm-2 control-label">Frekuensi <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <select id="frequency" name="frequency" class="form-control select2" placeholder="Pilih Frekuensi"
                  required>
                  <option value=""></option>
                  <option value="harian" @if ($question->frequency == 'harian') selected @endif>Harian</option>
                  <option value="bulanan" @if ($question->frequency == 'bulanan') selected @endif>Bulanan</option>
                  <option value="tahunan" @if ($question->frequency == 'tahunan') selected @endif>Tahunan</option>
                  <option value="perkejadian" @if ($question->frequency == 'perkejadian') selected @endif>Perkejadian
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="answer_type">Tipe Jawaban <b class="text-danger">*</b></label>
              <div class="col-sm-6">  
              <select name="answer_type" class="form-control select2" placeholder="Pilih Tipe Jawaban">
                    <option value=""></option>
                    <option value="checkbox"  @if ($question->answer_type == 'checkbox') selected @endif>Checkbox</option>
                    <option value="radio" @if ($question->answer_type == 'radio') selected @endif>Radio Button</option>
                    <option value="text" @if ($question->answer_type == 'text') selected @endif>Teks</option>
                    <option value="number" @if ($question->answer_type == 'number') selected @endif>Angka</option>
              </select>
              </div>
            </div>
            <div class="form-group">
              <label for="date" class="col-sm-2 control-label">Tanggal Mulai - Sampai</label>
              <div class="col-sm-3">
                <input type="text" class="form-control date" id="start_date" placeholder="Tanggal Mulai"
                  name="start_date" value="{{ $question->start_date }}">
              </div>
              <div class="col-sm-3">
                <input type="text" class="form-control date" id="finish_date" placeholder="Tanggal Sampai"
                  name="finish_date" value="{{ $question->finish_date }}">
              </div>
            </div>
            <div class="form-group">
              <label for="workforce_group_id" class="col-sm-2 control-label">Kelompok Workforce <b
                  class="text-danger">*</b></label>
              <div class="col-sm-6">
                  <table class="table table-bordered table-striped" id="table-workforcegroup">
                    <thead>
                        <th>Nama</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                      @foreach ($workforcegroups as $workforcegroup)
                      <tr>
                      <td><input type="hidden" name="workforcegroup[]" value="{{$workforcegroup->id}}"/>{{$workforcegroup->name}}</td>
                        <td class="text-center"><input type="checkbox" name="workforcegroup_status[{{$workforcegroup->id}}]" @if($workforcegroup->assessment_question_workforce_group_id) checked @endif></td>
                      </tr>
                      @endforeach
                    </tbody>
                </table>
              </div>
            </div>
            <div class="form-group">
              <label for="site_id" class="col-sm-2 control-label">Unit <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <table class="table table-bordered table-striped" id="table-workforcegroup">
                  <thead>
                      <th>Nama</th>
                      <th>Status</th>
                  </thead>
                  <tbody>
                    @foreach ($sites as $site)
                    <tr>
                    <td><input type="hidden" name="site[]" value="{{$site->id}}"/>{{$site->name}}</td>
                      <td class="text-center"><input type="checkbox" name="site_status[{{$site->id}}]" @if($site->assessment_question_site_id) checked @endif></td>
                    </tr>
                    @endforeach
                  </tbody>
              </table>
              </div>
            </div>
          </div>
      </div>
      </form>
      <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
  $(document).ready(function(){
      $('.date').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
      $('input[name^=workforcegroup_status]').iCheck({
          checkboxClass: 'icheckbox_square-green',
          radioClass: 'iradio_square-green',
      });
      $('input[name^=site_status]').iCheck({
          checkboxClass: 'icheckbox_square-green',
          radioClass: 'iradio_square-green',
      });
      $('#type').on('change',function(){
         $('#description').closest('.form-group').hide();
         $('#description_information').closest('.form-group').hide();
         if(this.value == 'Pertanyaan'){
          $('#description').closest('.form-group').show();
         }
         if(this.value == 'Informasi'){
          $('#description_information').closest('.form-group').show();
         }
         if(this.value == 'Informasi Dan Pertanyaan'){
          $('#description').closest('.form-group').show();
          $('#description_information').closest('.form-group').show();
         }
      }).trigger('change');
      $('.select2').select2();
      $("#question_parent_code").select2({
        ajax: {
          url: "{{route('assessmentquestion.select')}}",
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
              if (item.is_parent == 0) {
                option.push({
                  id:item.id,  
                  text: `${item.description?item.description:item.description_information}`
                });
              }
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });


      @if ($question->question_parent_code)
      $("#question_parent_code").select2('data',{id:{{$question->parent->id}},text:'{{$question->parent->description?$question->parent->description:$question->parent->description_information}}'}).trigger('change');
      @endif
      $(document).on("change", "#question_parent_code", function () {
        $('#answer_parent_code').select2('val','');
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#answer_parent_code").select2({
        ajax: {
          url: "{{route('assessmentanswer.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30,
              question_id:$('#question_parent_code').val()==''?-1:$('#question_parent_code').val()
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
      @if ($question->answer_parent_code)
      $("#answer_parent_code").select2('data',{id:{{$question->answercode->id}},text:'{{$question->answercode->description}}'}).trigger('change');
      @endif
      $(document).on("change", "#answer_parent_code", function () {
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