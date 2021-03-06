@extends('admin.layouts.app')

@section('title', 'Detail Formula')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style type="text/css">
  .overlay-wrapper {
    position: relative;
  }
</style>
@endsection
@push('breadcrump')
<li><a href="{{route('formula.index')}}">Formula</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-4">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Formula</h3>
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="box-body box-profile">
        <ul class="list-group list-group-unbordered">
          <li class="list-group-item">
            <b>Nama</b> <span class="pull-right">{{$formula->name}}</span>
          </li>
          <li class="list-group-item">
            <b>Dibuat</b> <span class="pull-right">{{$formula->created_at}}</span>
          </li>
        </ul>
      </div>
      <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="nav-tabs-custom tab-primary">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#formuladetail" data-toggle="tab">Formula Detail</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="formuladetail">
          <div class="overlay-wrapper">
            <a class="btn btn-primary pull-right btn-sm" href="#" onclick="adddetail()"><i class="fa fa-plus"></i></a>
            <table class="table table-bordered table-striped" id="table-detail">
              <thead>
                <tr>
                  <th style="text-align:center" width="10">#</th>
                  <th width="250">Pertanyaan</th>
                  <th width="250">Jawaban</th>
                  <th width="100">Operasi</th>
                  <th width="10">#</th>
                </tr>
              </thead>
            </table>
            <div class="overlay hidden">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="add-detail" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
  aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Tambah Formula Detail</h4>
      </div>
      <div class="modal-body">
        <form id="form" method="post" action="{{route('formuladetail.store')}}" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="formula_id" value="{{$formula->id}}" />
          <input type="hidden" name="_method" />
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="question_id">Pertanyaan</label>
                <input type="text" id="question_id" name="question_id" class="form-control"
                  data-placeholder="Pilih Pertanyaan" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="answer_id">Jawaban</label>
                <input type="text" id="answer_id" name="answer_id" class="form-control" data-placeholder="Pilih Jawaban"
                  required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="operation">Operasi</label>
                <select name="operation" class="form-control select2" placeholder="Pilih Tipe Operasi" required>
                  <option value=""></option>
                  <option value="percentage">Presentasi</option>
                  <option value="add">Penambahan</option>
                  <option value="subtract">Pengurangan</option>
                  <option value="multiplay">Pengkalian</option>
                  <option value="divide">Pembagian</option>
                  <option value="origin">Asli</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button form="form" type="submit" class="btn btn-primary btn-sm" title="Simpan"><i
            class="fa fa-save"></i></button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script>
  function adddetail(){
     $('#add-detail .modal-title').html('Tambah Formula Detail');
      $('#add-detail').modal('show');
      $('#form')[0].reset();
      $('#form').attr('action','{{route('formuladetail.store')}}');
      $('#form input[name=_method]').attr('value','POST');
      $('#form .invalid-feedback').each(function () { $(this).remove(); });
      $('#form').find('.form-group').removeClass('has-error').removeClass('has-success');
      $('#form').find('select[name=operation]').select2('val','');
      $('#form').find('input[name=question_id]').select2('val','');
      $('#form').find('input[name=answer_id]').select2('val','');
      $('#form input[name=value]').attr('value','');
      $('#answer_id').closest('.row').hide();
  }
  $(document).ready(function(){
    $("#question_id").select2({
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
            option.push({
              id:item.id,  
              text: `${item.type} - ${item.description}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    $(document).on("change", "#question_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
      if (this.value) {
        $('#answer_id').select2('val', '');
        $('#answer_id').closest('.row').show();
      } else {
        $('#answer_id').closest('.row').hide();
      }
    });
    $("#answer_id").select2({
      ajax: {
        url: "{{route('assessmentanswer.select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            question_id: $('input[name=question_id]').val(),
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
              text: `${item.answer_type} - ${item.description}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    $(document).on("change", "#answer_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $('.select2').select2({
      allowClear:true,
    });
    $(".numberfield").inputmask('decimal', {
			rightAlign: false
		});
    dataTable = $('#table-detail').DataTable( {
      stateSave:true,
      processing: true,
      serverSide: true,
      filter:false,
      info:false,
      lengthChange:false,
      responsive: true,
      order: [[4, "asc" ]],
      ajax: {
          url: "{{url('admin/formuladetail/read')}}",
          type: "GET",
          data:function(data){
              data.formula_id = {{$formula->id}};
          }
      },
      columnDefs:[
          {
              orderable: false,targets:[0]
          },
          { className: "text-right", targets: [0] },
          { className: "text-center", targets: [4] },
          { render: function (data, type, row) {
            return row.question ? row.question.description : ''
          }, targets: [1] },
          { render: function (data, type, row) {
            return row.answer ? row.answer.description : ''
          }, targets: [2] },
          { render: function ( data, type, row ) {
              return `<div class="dropdown">
              <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-bars"></i>
              </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li><a class="dropdown-item edit" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                  <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                </ul></div>`
          },targets: [4]
          }
      ],
      columns: [
          { data: "no" },
          { data: "assessment_question_id" },
          { data: "assessment_answer_id" },
          { data: "operation" },
          { data: "id" },
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
              $('#formuladetail .overlay').removeClass('hidden');
            }
          }).done(function(response){
            $('#add-detail').modal('hide');
            $('#formuladetail .overlay').addClass('hidden');
            if(response.status){
              dataTable.draw();
              $.gritter.add({
                  title: 'Success!',
                  text: response.message,
                  class_name: 'gritter-success',
                  time: 1000,
              });
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
              var response = response.responseJSON;
              $('#formuladetail .overlay').addClass('hidden');
              $.gritter.add({
                  title: 'Error!',
                  text: response.message,
                  class_name: 'gritter-error',
                  time: 1000,
              });
          })
        }
      });
      $(document).on('click','.edit',function(){
          var id = $(this).data('id');
          $.ajax({
              url:`{{url('admin/formuladetail')}}/${id}/edit`,
              method:'GET',
              dataType:'json',
              beforeSend:function(){
                $('#formuladetail .overlay').removeClass('hidden');
              },
          }).done(function(response){
              $('#formuladetail .overlay').addClass('hidden');
              if(response.status){
                  var type = {
                      checkbox  : 'Checkbox',
                      radio     : 'Radio Button',
                      text      : 'Teks',
                      number    : 'Angka',
                      select    : 'List Dropdown'
                  }
                  $('#add-detail .modal-title').html('Ubah Formula Detail');
                  $('#add-detail').modal('show');
                  $('#form')[0].reset();
                  $('#form .invalid-feedback').each(function () { $(this).remove(); });
                  $('#form .form-group').removeClass('has-error').removeClass('has-success');
                  $('#form input[name=_method]').attr('value','PUT');
                  $('#form input[name=question_id]').attr('value',response.data.assessment_question_id);
                  $('#form input[name=answer_id]').attr('value',response.data.assessment_answer_id);
                  $('#form select[name=operation]').select2('val',response.data.operation);
                  $("#question_id").select2('data',{id:response.data.question.id,text:response.data.question.type + ' - ' + response.data.question.description});
                  $("#answer_id").select2('data',{id:response.data.answer.id,text:type[response.data.answer.answer_type] + ' - ' + response.data.answer.description});
                  $('#form').attr('action',`{{url('admin/formuladetail/')}}/${response.data.id}`);
              }
          }).fail(function(response){
              var response = response.responseJSON;
              $('#formuladetail .overlay').addClass('hidden');
              $.gritter.add({
                  title: 'Error!',
                  text: response.message,
                  class_name: 'gritter-error',
                  time: 1000,
              });
          })
      })
      $(document).on('click','.delete',function(){
            var id = $(this).data('id');
            bootbox.confirm({
                buttons: {
                    confirm: {
                        label: '<i class="fa fa-check"></i>',
                        className: 'btn-primary btn-sm'
                    },
                    cancel: {
                        label: '<i class="fa fa-undo"></i>',
                        className: 'btn-default btn-sm'
                    },
                },
                title:'Menghapus detail formula?',
                message:'Data yang telah dihapus tidak dapat dikembalikan',
                callback: function(result) {
                      if(result) {
                          var data = {
                              _token: "{{ csrf_token() }}"
                          };
                          $.ajax({
                              url: `{{url('admin/formuladetail')}}/${id}`,
                              dataType: 'json',
                              data:data,
                              type:'DELETE',
                              beforeSend:function(){
                                  $('#formuladetail .overlay').removeClass('hidden');
                              }
                          }).done(function(response){
                              if(response.status){
                                $('#formuladetail .overlay').addClass('hidden');
                                  $.gritter.add({
                                      title: 'Success!',
                                      text: response.message,
                                      class_name: 'gritter-success',
                                      time: 1000,
                                  });
                                  dataTable.ajax.reload( null, false );
                              }
                              else{
                                  $.gritter.add({
                                      title: 'Warning!',
                                      text: response.message,
                                      class_name: 'gritter-warning',
                                      time: 1000,
                                  });
                              }
                          }).fail(function(response){
                              var response = response.responseJSON;
                              $('#formuladetail .overlay').addClass('hidden');
                              $.gritter.add({
                                  title: 'Error!',
                                  text: response.message,
                                  class_name: 'gritter-error',
                                  time: 1000,
                              });
                          })
                      }
                }
            });
        })
  });
</script>
@endpush