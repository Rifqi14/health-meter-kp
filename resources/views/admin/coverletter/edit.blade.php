@extends('admin.layouts.app')

@section('title', 'Ubah Surat Pengantar')
@push('breadcrump')
    <li><a href="{{route('coverletter.index')}}">Surat Pengantar</a></li>
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
          <h3 class="box-title">Ubah Surat Pengantar</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form id="form" action="{{route('coverletter.update',['id'=>$medicalrecord->id])}}" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
               <input type="hidden" name="_method" value="put">
               <div class="well well-sm">
                <div class="form-group">
                  <label for="name" class="col-sm-2 control-label">No<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                  <input type="text" class="form-control" id="record_no" name="record_no" placeholder="No" value="{{$medicalrecord->record_no}}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="name" class="col-sm-2 control-label">Tanggal<b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                  <input type="text" class="form-control" id="date" name="date" placeholder="Tanggal" value="{{$medicalrecord->date}}"required readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="name" class="col-sm-2 control-label">Pegawai <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                  <input type="text" class="form-control" id="employee_id" name="employee_id" data-placeholder="Pilih Pegawai" required readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="name" class="col-sm-2 control-label">Pasien <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="employee_family_id" name="employee_family_id" data-placeholder="Pilih Pasien">
                    <p>Isi jika pasien anggota dari pegawai</p>
                  </div>
                </div>
                <div class="form-group">
                  <label for="complaint" class="col-sm-2 control-label">Keluhan <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <textarea type="text" class="form-control summernote" id="complaint" name="complaint" required>{!!$medicalrecord->complaint!!}</textarea>
                  </div>
                </div>
                <div class="form-group">
                  <label for="complaint" class="col-sm-2 control-label">Diagnosa</label>
                  <div class="col-sm-6">
                    <table class="table table-striped table-bordered">
                      <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                      </tr>
                      @foreach($medicalrecord->medicalrecorddiadnosis as $medicalrecorddiadnosis)
                      <tr>
                        <td>{{$medicalrecorddiadnosis->diagnosis->code}}</td>
                        <td>{{$medicalrecorddiadnosis->diagnosis->name}}</td>
                      </tr>
                      @endforeach
                    </table>
                  </div>
                </div>
              </div>
              <div class="well well-sm">

                <div class="form-group">
                  <label for="medical_action_id" class="col-sm-2 control-label">Tindakan <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                     <select id="medical_action_id" name="medical_action_id" class="form-control select2" placeholder="Pilih Tindakan" required>
                        <option value=""></option>
                        @foreach ($medicalactions as $medicalaction)
                        <option value="{{$medicalaction->id}}" data-code='{{$medicalaction->code}}' @if($medicalrecord->medical_action_id == $medicalaction->id) selected @endif>{{$medicalaction->name}}</option>
                        @endforeach
                     </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="partner_id" class="col-sm-2 control-label">Partner <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <input type="text" class="form-control" id="partner_id" name="partner_id" data-placeholder="Pilih Partner" required>
                  </div>
                </div>
                <div class="form-group" id="prescription">
                  <label class="col-sm-2 control-label">Resep Dokter <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                    <a class="btn btn-primary btn-sm pull-right"  onclick="addprescription()"><i class="fa fa-plus"></i></a>
                    <table class="table table-bordered table-striped" id="table-prescriptions">
                        <thead>
                            <th>Obat</th>
                            <th>Intruksi</th>
                            <th>#</th>
                        </thead>
                        <tbody>
                          @foreach($medicalrecord->medicalrecordpresciption as $medicalrecordpresciption)
                        <tr>
                          <td>
                          <input type="hidden" name="prescription_item[]" value="{{$medicalrecordpresciption->id}}"/>
                            <input type="text" class="form-control" name="prescribed[]" data-placeholder="Prescribed" required value="{{$medicalrecordpresciption->prescribed}}"></td>
                          <td><input type="text" class="form-control" name="instruction[]" data-placeholder="Instruction" required value="{{$medicalrecordpresciption->instruction}}"></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                  </div>
                </div>
                <div class="form-group">
                  <label for="medical_action_id" class="col-sm-2 control-label">Status <b class="text-danger">*</b></label>
                  <div class="col-sm-6">
                     <select id="status" name="status" class="form-control select2" placeholder="Pilih Status" required>
                        <option value=""></option>
                        <option value="Request" @if($medicalrecord->status == 'Request') selected @endif>Request</option>
                        <option value="Closed" @if($medicalrecord->status == 'Closed') selected @endif>Closed</option>
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
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script>
 function addprescription(){
    $('#table-prescriptions tbody').append(`
      <tr>
        <td>
            <input type="text" class="form-control" name="prescribed[]" data-placeholder="Prescribed" required>
          </td>
        <td>
            <input type="hidden" name="prescription_item[]" value="0"/>
            <input type="text" class="form-control" name="instruction[]" data-placeholder="Instruction" required>
          </td>
          <td class="text-center"><a class="btn btn-danger btn-sm remove"><i class="fa fa-trash"></i></a></td>
      </tr>
    `);
  }
  $(function(){
    $('#partner_id').closest('.form-group').hide();
    $('#prescription').hide();
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
    $( "#form textarea[name=address]" ).keyup(function() {
      address = $(this).val();
      getCoordinates(address);
    });
      $("input[name=nid]").inputmask("Regex", { regex: "[A-Za-z0-9]*" });
      $('.select2').select2({
        allowClear:true
      });
      $('input[name=date]').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
      $('input[name=date]').on('change', function(){
        if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
          $(this).closest("form").validate().form();
        }
      });
      $( "#employee_id" ).select2({
        ajax: {
          url: "{{route('employee.select')}}",
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
      @if($medicalrecord->employee)
      $("#employee_id").select2('data',{id:{{$medicalrecord->employee->id}},text:'{{$medicalrecord->employee->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#employee_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        $('#employee_family_id').select2('val','');
      });
     
      $( "#employee_family_id" ).select2({
        ajax: {
          url: "{{route('employeefamily.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              employee_id:$('#employee_id').val(),
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
      @if($medicalrecord->employeefamily)
      $("#employee_family_id").select2('data',{id:{{$medicalrecord->employeefamily->id}},text:'{{$medicalrecord->employeefamily->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#employee_family_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $( "#partner_id" ).select2({
        ajax: {
          url: "{{route('partner.select')}}",
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
                text: item.name
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      @if($medicalrecord->partner)
      $("#partner_id").select2('data',{id:{{$medicalrecord->partner->id}},text:'{{$medicalrecord->partner->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#partner_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $(document).on("change", "#medical_action_id", function () {
        $('#partner_id').closest('.form-group').hide();
        $('#prescription').hide();
        if($(this).find(':selected').data('code') == 'opnamepegawai' || $(this).find(':selected').data('code') == 'melahirkan' || $(this).find(':selected').data('code') == 'swab' || $(this).find(':selected').data('code') == 'hemodalisa' || $(this).find(':selected').data('code') == 'opnamepensiunan' || $(this).find(':selected').data('code') == 'laboraturium' || $(this).find(':selected').data('code') == 'isolasi' || $(this).find(':selected').data('code') == 'rawatjalan'){
          $('#prescription').hide();
          $('#partner_id').closest('.form-group').show();
        }
        if($(this).find(':selected').data('code') == 'resepdokter'){
          $('#prescription').show();
          $('#partner_id').closest('.form-group').show();
        }
      });
      $('#medical_action_id').trigger('change');
      $('#table-prescriptions').on('click','.remove',function(){
        var input = this;
        bootbox.confirm({
          buttons: {
            confirm: {
              label: '<i class="fa fa-check"></i>',
              className: 'btn-primary btn-sm'
            },
            cancel: {
              label: '<i class="fa fa-undo"></i>',
              className: 'btn-default  btn-sm'
            },
          },
          title:'Hapus Resep?',
          message:'Resep akan dihapus',
          callback: function(result) {
            if(result) {
              $(input).parents('tr').remove();
            }
          }
        });
        
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