@extends('admin.layouts.app')

@section('title', 'Tambah Surat Pengantar')
@push('breadcrump')
<li><a href="{{route('healthmeter.index')}}">Surat Pengantar</a></li>
<li class="active">Tambah</li>
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
        <h3 class="box-title">Tambah Surat Pengantar</h3>
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
        <form id="form" action="{{route('coveringletter.store')}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <div class="form-group">
            <label for="type" class="col-sm-2 control-label">Jenis Surat <b class="text-danger">*</b></label>
            <div class="col-sm-6">
                <select id="type" name="type" class="form-control select2" data-placeholder="Pilih Jenis Surat" required>
                    <option value=""></option>
                    @foreach(config('enums.coveringletter_type') as $key => $coveringletter_type)
                    <option value="{{$key}}">{{$coveringletter_type}}</option>
                    @endforeach
                </select>
            </div>
          </div>
          <div class="form-group">
            <label for="number" class="col-sm-2 control-label">Nomor Surat<b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="number" placeholder="Nomor Surat"
                  name="number">
              </div>
          </div>
          <div class="form-group">
            <label for="date" class="col-sm-2 control-label">Tanggal Surat<b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <input type="text" class="form-control date" id="letter_date" placeholder="Tanggal Surat"
                  name="letter_date">
              </div>
          </div>
          <div class="form-group">
            <label for="workforce_id" class="col-sm-2 control-label">Workforce <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="workforce_id" name="workforce_id" placeholder="Pilih Workforce" required>
            </div>
          </div>
          <div class="form-group">
            <label for="patient_id" class="col-sm-2 control-label">Pasien <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="patient_id" name="patient_id" placeholder="Pilih Pasien" required>
              <input type="hidden" class="form-control" id="patient_site_id" name="patient_site_id">
            </div>
          </div>
          <div class="form-group">
            <label for="doctor_id" class="col-sm-2 control-label">Dokter <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="doctor_id" name="doctor_id" placeholder="Pilih Dokter" required>
              <input type="hidden" class="form-control" id="doctor_site_id" name="doctor_site_id">
              <input type="hidden" class="form-control" id="partner_id" name="partner_id">
              <input type="hidden" class="form-control" id="speciality_id" name="speciality_id">
            </div>
          </div>
          <div class="form-group">
            <label for="referral_doctor_id" class="col-sm-2 control-label">Dokter Rujukan <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="referral_doctor_id" name="referral_doctor_id" placeholder="Pilih Dokter Rujukan" required>
              <input type="hidden" class="form-control" id="referral_partner_id" name="referral_partner_id">
              <input type="hidden" class="form-control" id="referral_speciality_id" name="referral_speciality_id">
            </div>
          </div>
          <div class="form-group">
            <label for="consultation_id" class="col-sm-2 control-label">Konsultasi <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="consultation_id" name="consultation_id" placeholder="Pilih Konsultasi" required>
            </div>
          </div>
           <div class="form-group">
            <label for="medicine_id" class="col-sm-2 control-label">Obat <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="medicine_id" name="medicine_id" placeholder="Pilih Obat" required>
            </div>
          </div>
          <div class="form-group">
            <label for="using_rule_id" class="col-sm-2 control-label">Aturan Pakai <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="using_rule_id" name="using_rule_id" placeholder="Pilih Aturan Pakai" required>
            </div>
          </div>
          <div class="form-group">
            <label for="amount" class="col-sm-2 control-label">Jumlah <b class="text-danger">*</b></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="amount" name="amount" placeholder="Jumlah" required>
            </div>
          </div>
          <div class="form-group">
            <label for="description" class="col-sm-2 control-label">Keterangan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <textarea class="form-control summernote" name="description" id="description"></textarea>
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
     $('.select2').select2();
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
                text: `${item.name}`,
                namenid: `${item.namenid}`
               
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        formatResult: function(item) {
          return item.namenid
        },
        allowClear: true,
      });
      $(document).on("change", "#workforce_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#patient_id").select2({
        ajax: {
          url: "{{route('patient.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              name:term,
              page:page,
              limit:30,
              workforce_id: $("#workforce_id").val()
            
            };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
              option.push({
                id:item.id,  
                text: `${item.name}`,
                site: `${item.site_id}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#patient_id", function () {
          var site = $('#patient_id').select2('data').site;
            $('#patient_site_id').val(`${site}`);
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#doctor_id").select2({
        ajax: {
          url: "{{route('doctor.select')}}",
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
                text: `${item.name}`,
                site: `${item.site_id}`,
                partner: `${item.id_partner}`,
                speciality: item.id_speciality
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#doctor_id", function () {
          var site = $('#doctor_id').select2('data').site;
          var partner = $('#doctor_id').select2('data').partner;
          var speciality = $('#doctor_id').select2('data').speciality;

          $('#doctor_site_id').val(`${site}`);
          $('#partner_id').val(`${partner}`);
          $('#speciality_id').val(`${speciality}`);

        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#referral_doctor_id").select2({
        ajax: {
          url: "{{route('doctor.select')}}",
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
                text: `${item.name}`,
                site: `${item.site_id}`,
                partner: `${item.id_partner}`,
                speciality: item.id_speciality
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#referral_doctor_id", function () {
          var partner = $('#referral_doctor_id').select2('data').partner;
          var speciality = $('#referral_doctor_id').select2('data').speciality;

          $('#referral_partner_id').val(`${partner}`);
          $('#referral_speciality_id').val(`${speciality}`);

        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
       $( "#consultation_id" ).select2({
        ajax: {
          url: "{{route('healthconsultation.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              display_name:term,
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
                text: `${item.complaint}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#consultation_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
     
      $("#medicine_id").select2({
        ajax: {
          url: "{{route('medicine.select')}}",
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
                text: `${item.name}`,
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $(document).on("change", "#medicine_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });

      $("#using_rule_id").select2({
        ajax: {
          url: "{{route('usingrule.select')}}",
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
      $(document).on("change", "#using_rule_id", function () {
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