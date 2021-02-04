@extends('admin.layouts.app')

@section('title', 'Ubah Workforce')
@push('breadcrump')
<li><a href="{{route('workforce.index')}}">Workforce</a></li>
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
        <h3 class="box-title">Ubah Workforce</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('workforce.update', ['id' => $workforce->id])}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          @method('PUT')
          <div class="box-body">
            <div class="well well-sm">
              <div class="form-group">
                <label for="site_id" class="col-sm-2 control-label">Distrik <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="site_id" name="site_id" data-placeholder="Pilih Distrik" required>
                </div>
              </div>
              <div class="form-group">
                <label for="nid" class="col-sm-2 control-label">NID <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="nid" name="nid" placeholder="NID" value="{{ $workforce->nid }}" required>
                </div>
              </div>
              <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="name" name="name" placeholder="Nama" value="{{ $workforce->name }}" required>
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
                  <input type="text" class="form-control datepicker" id="start_date" name="start_date" value="{{ $workforce->start_date }}" placeholder="Tanggal Mulai">
                </div>
                <div class="col-sm-3">
                  <input type="text" class="form-control datepicker" id="finish_date" name="finish_date" value="{{ $workforce->finish_date }}" placeholder="Tanggal Sampai">
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
                  <input type="text" class="form-control" id="department_id" name="department_id" data-placeholder="Bidang" >
                </div>
              </div>
              <div class="form-group">
                <label for="sub_department_id" class="col-sm-2 control-label">Sub Bidang</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="sub_department_id" name="sub_department_id" data-placeholder="Sub Bidang" >
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
                  <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ $workforce->user->email }}" required>
                </div>
              </div>
              <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Password</label>
                <div class="col-sm-6">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password">
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
              text: `${item.name} - ${item.code}`
              });
          });
          return {
              results: option, more: more,
          };
          },
      },
      allowClear: true,
    });
    $(document).on("change", "#guarantor_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });

    @if ($workforce->workforcegroup)
      $('#workforce_group_id').select2('data', {id: {{ $workforce->workforcegroup->id }}, text: `{!! $workforce->workforcegroup->name !!}`}).trigger('change');
    @endif
    @if ($workforce->site)
      $('#site_id').select2('data', {id: {{ $workforce->site->id }}, text: `{!! $workforce->site->name !!}`}).trigger('change');
    @endif
    @if ($workforce->agency)
      $('#agency_id').select2('data', {id: {{ $workforce->agency->id }}, text: `{!! $workforce->agency->name !!}`}).trigger('change');
    @endif
    @if ($workforce->grade)
      $('#grade_id').select2('data', {id: {{ $workforce->grade->id }}, text: `{!! $workforce->grade->name !!}`}).trigger('change');
    @endif
    @if ($workforce->title)
      $('#title_id').select2('data', {id: {{ $workforce->title->id }}, text: `{!! $workforce->title->name !!}`}).trigger('change');
    @endif
    @if ($workforce->department)
      $('#department_id').select2('data', {id: {{ $workforce->department->id }}, text: `{!! $workforce->department->name !!}`}).trigger('change');
    @endif
    @if ($workforce->subdepartment)
      $('#sub_department_id').select2('data', {id: {{ $workforce->subdepartment->id }}, text: `{!! $workforce->subdepartment->name !!}`}).trigger('change');
    @endif
    @if ($workforce->guarantor)
      $('#guarantor_id').select2('data', {id: {{ $workforce->guarantor->id }}, text: `{!! $workforce->guarantor->title->name.' - '.$workforce->guarantor->title->code !!}`}).trigger('change');
    @endif
  });
</script>
@endpush