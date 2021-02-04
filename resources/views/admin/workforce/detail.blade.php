@extends('admin.layouts.app')

@section('title', 'Detail Workforce')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<style type="text/css">
  #map {
    height: 370px;
    border: 1px solid #CCCCCC;
  }

  .overlay-wrapper {
    position: relative;
  }
</style>
@endsection
@push('breadcrump')
<li><a href="{{route('workforce.index')}}">Workforce</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-5">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Workforce</h3>
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="box-body box-profile">
        <table class="table">
          <tr>
            <td><strong>Distrik</strong></td>
            <td class="text-right" id="unit">{{@$workforce->site->name}}</td>
          </tr>
          <tr>
            <td><strong>NID</strong></td>
            <td class="text-right">{{$workforce->nid}}</td>
          </tr>
          <tr>
            <td width="100"><strong>Nama</strong></td>
            <td width="150" class="text-right">{{$workforce->name}}</td>
          </tr>
          <tr>
            <td><strong>Kelompok Workforce</strong></td>
            <td class="text-right">{{@$workforce->workforcegroup->name}}</td>
          </tr>
          <tr>
            <td><strong>Instansi</strong></td>
            <td class="text-right">{{@$workforce->agency->name}}</td>
          </tr>
          <tr>
            <td><strong>Tanggal Mulai</strong></td>
            <td class="text-right">{{\Carbon\Carbon::parse($workforce->start_date)->format('d F Y')}}</td>
          </tr>
          <tr>
            <td><strong>Tanggal Akhir</strong></td>
            <td class="text-right">{{\Carbon\Carbon::parse($workforce->finish_date)->format('d F Y')}}</td>
          </tr>
          <tr>
            <td width="25%"><strong>Jabatan</strong></td>
            <td width="25%" class="text-right">
              {{$workforce->title?$workforce->title->name:'Tidak Ada'}}</td>
          </tr>
          <tr>
            <td width="25%"><strong>Bidang</strong></td>
            <td width="25%" class="text-right">
              {{@$workforce->department->name}}</td>
          </tr>
          <tr>
            <td width="25%"><strong>Sub Bidang</strong></td>
            <td width="25%" class="text-right">
              {{@$workforce->subdepartment->name}}</td>
          </tr>
          <tr>
            <td width="25%"><strong>Jabatan Penanggung Jawab</strong></td>
            <td width="25%" class="text-right">
              {{$workforce->guarantor?$workforce->guarantor->title->name:'Tidak Ada'}}</td>
          </tr>
          <tr>
            <td width="25%"><strong>Email</strong></td>
            <td width="25%" class="text-right">
              {{@$workforce->user->email}}</td>
          </tr>
        </table>

      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="nav-tabs-custom tab-primary">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#employeefamily" data-toggle="tab">Pasien</a></li>
        <li><a href="#secondarytitle" data-toggle="tab">Secondary Position</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="employeefamily">
          <div class="overlay-wrapper">
            <table class="table table-bordered table-striped" id="table-family">
              <thead>
                <tr>
                  <th style="text-align:center" width="10">#</th>
                  <th width="250">Nama</th>
                  <th width="100">Tgl Lahir</th>
                  <th width="200">Tarif</th>
                </tr>
              </thead>
            </table>
            <div class="overlay hidden">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </div>
        </div>
        <div class="tab-pane" id="secondarytitle">
          <div class="overlay-wrapper">
            <form id="form-title" class="form-horizontal" action="{{url('admin/workforce/secondarytitle')}}" method="post" autocomplete="off">
              {{ csrf_field() }}
              <input type="hidden" name="workforce_title_id" value="{{$workforce->id}}" />
              <div class="form-group">
                <label class="col-sm-2 control-label">Jabatan</label>
                <div class="col-sm-6">
                  <div class="input-group">
                    <input type="text" class="form-control" id="title_id" name="title_id" data-placeholder="Pilih Jabatan" required />
                    <div class="input-group-btn">
                      <button class="btn btn-primary" type="submit">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <table class="table table-bordered table-striped" id="table-title">
              <thead>
                <tr>
                  <th style="text-align:center" width="10">#</th>
                  <th width="250">Kode</th>
                  <th width="250">Nama</th>
                  <th style="text-align:center" width="10">#</th>
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
<div class="modal fade" id="add-detail" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Tambah Tanggungan</h4>
      </div>
      <div class="modal-body">
        <form id="form" method="post" action="{{route('employeefamily.store')}}" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="employee_id" value="{{$workforce->id}}" />
          <input type="hidden" name="_method" />
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="type">Tipe</label>
                <select name="type" class="form-control select2" placeholder="Pilih Tipe Tanggungan" required>
                  <option value=""></option>
                  <option value="couple">Pasangan</option>
                  <option value="child">Anak</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="name">Nama <b class="text-danger">*</b></label>
                <input type="text" name="name" class="form-control" placeholder="Nama">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="birth_date">Tgl Lahir <b class="text-danger">*</b></label>
                <input type="text" name="birth_date" class="form-control" placeholder="Tgl Lahir">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button form="form" type="submit" class="btn btn-primary btn-sm" title="Simpan"><i class="fa fa-save"></i></button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
  var map, geocoder, marker, infowindow;
  $(document).ready(function () {
    $('.select2').select2();
    dataTableFamily = $('#table-family').DataTable( {
          stateSave:true,
          processing: true,
          serverSide: true,
          filter:false,
          info:false,
          lengthChange:false,
          responsive: true,
          order: [[1, "asc" ]],
          ajax: {
              url: "{{url('admin/patient/read')}}",
              type: "GET",
              data:function(data){
                  data.workforce_id = {{$workforce->id}};
              }
          },
          columnDefs:[
              {
                  orderable: false,targets:[0]
              },
              { className: "text-right", targets: [0] },
              { className: "text-center", targets: [2] },
              { render: function ( data, type, row ) {
                  return `${row.name} <br><small>${row.status}</small>`
              },targets: [1]
              },
              { render: function ( data, type, row ) {
                  return `${row.inpatient_id ? row.inpatient.name : ''}`
              },targets: [3]
              }
          ],
          columns: [
              { data: "no" },
              { data: "name" },
              { data: "birth_date" },
              { data: "inpatient_id" },
          ]
    });
    datatableTitle = $('#table-title').DataTable( {
          stateSave:true,
          processing: true,
          serverSide: true,
          filter:false,
          info:false,
          lengthChange:false,
          responsive: true,
          order: [[3, "asc" ]],
          ajax: {
              url: "{{url('admin/workforce/readsecondarytitle')}}",
              type: "GET",
              data:function(data){
                  data.workforce_id = {{$workforce->id}};
              }
          },
          columnDefs:[
              {
                  orderable: false,targets:[0,1,2]
              },
              { className: "text-right", targets: [0] },
              { className: "text-center", targets: [3] },
              { render: function ( data, type, row ) {
                  return `<div class="dropdown">
                            <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bars"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                              <li><a class="dropdown-item delete-title" href="#" data-title_id="${row.title_id}" data-workforce_id="{{ $workforce->id }}"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                            </ul>
                          </div>`
              },targets: [3]
              }
          ],
          columns: [
              { data: "no" },
              { data: "title.code" },
              { data: "title.name" },
              { data: "title_id" },
          ]
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var currentTab = $(e.target).text(); 
        switch (currentTab)   {
        case 'Pasien' :
            $('#table-family').css("width", '100%')
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
            break ;
        case 'Secondary Position' :
            $('#table-title').css("width", '100%')
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
            break ;
        };
    });
    $( "#title_id" ).select2({
        ajax: {
            url: "{{route('workforce.selectsecondarytitle')}}",
            type:'GET',
            dataType: 'json',
            data: function (term,page) {
            return {
                name:term,
                workforce_id:{{$workforce->id}},
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
    $(document).on("change", "#title_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
            $('#form').validate().form();
        }
    });
    
    $("#form-title").validate({
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
            url:$('#form-title').attr('action'),
            method:'post',
            data: new FormData($('#form-title')[0]),
            processData: false,
            contentType: false,
            dataType: 'json', 
            beforeSend:function(){
                $('#secondarytitle .overlay').removeClass('hidden');
            }
            }).done(function(response){
            $('#secondarytitle .overlay').addClass('hidden');
            $( "#title_id" ).select2('val','');
            if(response.status){
                datatableTitle.draw();
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
                $('#secondarytitle .overlay').addClass('hidden');
                $( "#role_id" ).select2('val','');
                $.gritter.add({
                    title: 'Error!',
                    text: response.message,
                    class_name: 'gritter-error',
                    time: 1000,
                });
            })		
        }
    });
    $(document).on('click','.delete-title',function(){
      var workforce_id = $(this).data('workforce_id');
      var title_id = $(this).data('title_id');
      bootbox.confirm({
          buttons: {
              confirm: {
                  label: '<i class="fa fa-check"></i>',
                  className: 'btn-danger'
              },
              cancel: {
                  label: '<i class="fa fa-undo"></i>',
                  className: 'btn-default'
              },
          },
          title:'Menghapus secondary title workforce?',
          message:'Data yang telah dihapus tidak dapat dikembalikan',
          callback: function(result) {
                  if(result) {
                      var data = {
                          _token: "{{ csrf_token() }}",
                          workforce_id: workforce_id,
                          title_id: title_id
                      };
                      $.ajax({
                          url: `{{url('admin/workforce/deletesecondarytitle')}}`,
                          dataType: 'json', 
                          data:data,
                          type:'POST',
                          beforeSend:function(){
                              $('#secondarytitle .overlay').removeClass('hidden');
                          }
                      }).done(function(response){
                          if(response.status){
                          $('#secondarytitle .overlay').addClass('hidden');
                              $.gritter.add({
                                  title: 'Success!',
                                  text: response.message,
                                  class_name: 'gritter-success',
                                  time: 1000,
                              });
                              datatableTitle.ajax.reload( null, false );
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
                          $('#secondarytitle .overlay').addClass('hidden');
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
    });
  });
</script>
@endpush