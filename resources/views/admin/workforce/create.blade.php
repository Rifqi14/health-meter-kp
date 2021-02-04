@extends('admin.layouts.app')

@section('title', 'Tambah Workforce')
@push('breadcrump')
<li><a href="{{route('workforce.index')}}">Workforce</a></li>
<li class="active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Tambah Workforce</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('workforce.store')}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="box-body">
            <div class="well well-sm">
              <div class="form-group">
                <label for="site_id" class="col-sm-2 control-label">Distrik <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="site_id" name="site_id" data-placeholder="Pilih Distrik" required>
                </div>
              </div>
              <div class="form-group">
                <label for="code" class="col-sm-2 control-label">Employee ID <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="code" name="code" placeholder="Employee ID" required>
                </div>
              </div>
              <div class="form-group">
                <label for="nid" class="col-sm-2 control-label">NID <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="nid" name="nid" placeholder="NID" required>
                </div>
              </div>
              <div class="form-group">
                <label for="id_card_number" class="col-sm-2 control-label">Nomor KTP <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="id_card_number" name="id_card_number" placeholder="No KTP" required>
                </div>
              </div>
              <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="name" name="name" placeholder="Nama" required>
                </div>
              </div>
              <div class="form-group">
                <label for="address" class="col-sm-2 control-label">Alamat <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <textarea class="form-control" name="address" id="address" cols="30" rows="3" style="resize: vertical" placeholder="Alamat"></textarea>
                </div>
              </div>
              <div class="form-group">
                <label for="region_id" class="col-sm-2 control-label">Kabupaten / Kota <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="region_id" name="region_id" placeholder="Kabupaten / Kota" required>
                </div>
              </div>
              <div class="form-group">
                <label for="phone" class="col-sm-2 control-label">Nomor HP <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="Nomor HP" required>
                </div>
              </div>
              <div class="form-group">
                <label for="workforce_group_id" class="col-sm-2 control-label">Kelompok Workforce <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="workforce_group_id" name="workforce_group_id" data-placeholder="Kelompok Workforce" required>
                </div>
              </div>
              <div class="form-group">
                <label for="agency_id" class="col-sm-2 control-label">Instansi <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="agency_id" name="agency_id" data-placeholder="Instansi" required>
                </div>
              </div>
              <div class="form-group">
                <label for="date" class="col-sm-2 control-label">Tanggal Mulai & Sampai</label>
                <div class="col-sm-3">
                  <div class="input-group">
                    <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Tanggal Mulai">
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="input-group">
                    <input type="text" class="form-control datepicker" id="finish_date" name="finish_date" placeholder="Tanggal Sampai">
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="well well-sm">
              <div class="form-group">
                <label for="place_of_birth" class="col-sm-2 control-label">Tempat Lahir <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" placeholder="Tempat Lahir" required>
                </div>
              </div>
              <div class="form-group">
                <label for="birth_date" class="col-sm-2 control-label">Tanggal Lahir <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <div class="input-group">
                    <input type="text" class="form-control datepicker" id="birth_date" name="birth_date" placeholder="Tanggal Lahir" required>
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="gender" class="col-sm-2 control-label" style="padding-top: 1px">Jenis Kelamin <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="radio" name="gender" value="m" checked> <span style="margin-left: 1em; margin-right: 2em"> Laki-laki</span>
                  <input type="radio" name="gender" value="f"> <span style="margin-left: 1em"> Perempuan</span>
                </div>
              </div>
              <div class="form-group">
                <label for="religion" class="col-sm-2 control-label">Agama <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <select name="religion" id="religion" class="form-control select2" data-placeholder="Agama" required>
                    <option value=""></option>
                    @foreach (config('enums.religion') as $religion)
                    <option value="{{ $religion }}">{{ $religion }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="marriage_status" class="col-sm-2 control-label">Status Pernikahan <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <select name="marriage_status" id="marriage_status" class="form-control select2" data-placeholder="Status Pernikahan" required>
                    <option value=""></option>
                    @foreach (config('enums.marriage_status') as $marriage_status)
                    <option value="{{ $marriage_status }}">{{ $marriage_status }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="last_education" class="col-sm-2 control-label">Pendidikan Terakhir <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <select name="last_education" id="last_education" class="form-control select2" data-placeholder="Pendidikan Terakhir" required>
                    <option value=""></option>
                    @foreach (config('enums.last_education') as $last_education)
                    <option value="{{ $last_education }}">{{ $last_education }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="well well-sm">
              <div class="form-group">
                <label for="blood_type" class="col-sm-2 control-label">Golongan Darah <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <select name="blood_type" id="blood_type" class="form-control select2" data-placeholder="Golongan Darah" required>
                    <option value=""></option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="rhesus" class="col-sm-2 control-label">Rhesus <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <select name="rhesus" id="rhesus" class="form-control select2" data-placeholder="Rhesus" required>
                    <option value=""></option>
                    <option value="Positif">Positif</option>
                    <option value="Negatif">Negatif</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="bpjs_employment_number" class="col-sm-2 control-label">No BPJS Ketenagakerjaan</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="bpjs_employment_number" name="bpjs_employment_number" placeholder="No BPJS Ketenagakerjaan">
                </div>
              </div>
              <div class="form-group">
                <label for="bpjs_health_number" class="col-sm-2 control-label">No BPJS Kesehatan</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="bpjs_health_number" name="bpjs_health_number" placeholder="No BPJS Kesehatan">
                </div>
              </div>
            </div>
            <div class="well well-sm">
              <div class="form-group">
                <label for="grade_id" class="col-sm-2 control-label">Jenjang Jabatan</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="grade_id" name="grade_id" data-placeholder="Jenjang Jabatan">
                </div>
              </div>
              <div class="form-group">
                <label for="title_id" class="col-sm-2 control-label">Jabatan</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="title_id" name="title_id" data-placeholder="Jabatan">
                </div>
              </div>
              <div class="form-group">
                <label for="department_id" class="col-sm-2 control-label">Bidang</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="department_id" name="department_id" data-placeholder="Bidang">
                </div>
              </div>
              <div class="form-group">
                <label for="sub_department_id" class="col-sm-2 control-label">Sub Bidang</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="sub_department_id" name="sub_department_id" data-placeholder="Sub Bidang">
                </div>
              </div>
              <div class="form-group">
                <label for="guarantor_id" class="col-sm-2 control-label">Jabatan Penanggung Jawab</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="guarantor_id" name="guarantor_id" data-placeholder="Jabatan Penanggung Jawab">
                </div>
              </div>
            </div>
            <div class="well well-sm">
              <div class="form-group">
                <label for="email" class="col-sm-2 control-label">Email <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                </div>
              </div>
              <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Password <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
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
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
  $(document).ready(function(){
    $('input[name=gender]').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    $("input[name=nid]").inputmask("Regex", { regex: "[A-Za-z0-9]*" });
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    })
    $('.datepicker').on('change', function(){
      if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
        $(this).closest("form").validate().form();
      }
    });
    $('.select2').select2({
      allowClear: true
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

    // Select2 Section
    $("#region_id").select2({
      ajax: {
          url: "{{route('region.select')}}",
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
    $(document).on("change", "#region_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#place_of_birth").select2({
      ajax: {
          url: "{{route('region.select')}}",
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
    $(document).on("change", "#place_of_birth", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#site_id").select2({
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
      $('#title_id').select2('val','');
      $('#department_id').select2('val','');
      $('#sub_department_id').select2('val','');
      $('#agency_id').select2('val','');
      $('#guarantor_id').select2('val','');
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#workforce_group_id").select2({
      ajax: {
          url: "{{route('workforcegroup.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
          return {
              name:term,
              page:page,
              limit:30,
              data_manager:{{$accesssite}},
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
    $(document).on("change", "#workforce_group_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#agency_id").select2({
      ajax: {
          url: "{{route('agency.select')}}",
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
    $(document).on("change", "#agency_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#grade_id").select2({
      ajax: {
          url: "{{route('grade.select')}}",
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
    $(document).on("change", "#grade_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#title_id").select2({
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
    $(document).on("change", "#title_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#department_id").select2({
      ajax: {
          url: "{{route('department.select')}}",
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
    $(document).on("change", "#department_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#sub_department_id").select2({
      ajax: {
          url: "{{route('subdepartment.select')}}",
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
    $(document).on("change", "#sub_department_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#guarantor_id").select2({
      ajax: {
          url: "{{route('guarantor.select')}}",
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
              text: `${item.code}`,
              title_name: `${item.title}`,
              code: `${item.site}`,
              custom: `${item.custom}`
              });
          });
          return {
              results: option, more: more,
          };
          },
      },
      allowClear: true,
      formatResult: function(item) {
        return item.custom
      },
      formatSelection: function(item) {
        return item.title_name + ' - ' + item.code
      },
    });
    $(document).on("change", "#guarantor_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#role_id").select2({
      ajax: {
          url: "{{route('role.select')}}",
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
              text: `${item.display_name}`
              });
          });
          return {
              results: option, more: more,
          };
          },
      },
      allowClear: true,
    });
    $(document).on("change", "#role_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
  });
</script>
@endpush