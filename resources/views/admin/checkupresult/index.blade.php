@extends('admin.layouts.app')

@section('title', 'Hasil Pemeriksaan')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-daterangepicker/css/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection
@push('breadcrump')
<li class="active">Hasil Pemeriksaan</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Data Hasil Pemeriksaan</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          @if(in_array('create',$actionmenu))
          <a href="{{route('checkupresult.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Tambah">
            <i class="fa fa-plus"></i>
          </a>
          @endif
          <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
            <i class="fa fa-search"></i>
          </a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <table class="table table-striped table-bordered datatable" style="width:100%">
          <thead>
            <tr>
              <th width="10">#</th>
              <th width="100">Workforce</th>
              <th width="100">Pasien</th>
              <th width="100">Tanggal</th>
              <th width="100">Faskes</th>
              <th width="100">Hasil Pemeriksaan</th>
              <th width="100">Batas Normal</th>
              <th width="100">Dibuat</th>
              <th width="100">Terakhir Dirubah</th>
              <th width="100">Diubah Oleh</th>
              <th width="10">#</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Pencarian</h4>
      </div>
      <div class="modal-body">
        <form id="form-search" autocomplete="off">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="workforce_id" class="control-label">Workforce</label>
                <input type="text" class="form-control" id="workforce_id" name="workforce_id" data-placeholder="Pilih Workforce">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="patient_id" class="control-label">Pasien</label>
                <input type="text" class="form-control" id="patient_id" name="patient_id" data-placeholder="Pilih Pasien">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="partner_id" class="control-label">Faskes</label>
                <input type="text" class="form-control" id="partner_id" name="partner_id" data-placeholder="Pilih Faskes">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="date">Tanggal Pemeriksaan</label>
                <div class="row">
                  <div class="col-md-12">
                    <div class="input-group">
                      <input type="text" class="form-control date-picker" name="date" placeholder="Tanggal">
                      <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button form="form-search" type="submit" class="btn btn-default btn-sm" title="Apply"><i class="fa fa-search"></i></button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script src="{{asset('adminlte/component/moment/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-daterangepicker/js/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript">
  function filter(){
    $('#add-filter').modal('show');
}
$(function(){
    $("input[name=workforce_id]").select2({
        ajax: {
            url: "{{route('workforce.select')}}",
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
                    text: `${item.name}`,
                    namenid: `${item.namenid}`
                  });
              });
              return {
                  results: option, more: more,
              };
            },
        },
        formatResult: function (item) {
          return item.namenid;
        },
        allowClear: true,
    });
    $("input[name=patient_id]").select2({
        ajax: {
            url: "{{route('patient.select')}}",
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
    $("input[name=partner_id]").select2({
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
    //date
    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    })
    dataTable = $('.datatable').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 8, "asc" ]],
        ajax: {
            url: "{{route('checkupresult.read')}}",
            type: "GET",
            data:function(data){
                var workforce_id = $('#form-search').find('input[name=workforce_id]').val();
                var patient_id = $('#form-search').find('input[name=patient_id]').val();
                var partner_id = $('#form-search').find('input[name=partner_id]').val();
                var date = $('#form-search').find('input[name=date]').val();
                data.workforce_id = workforce_id;
                data.patient_id = patient_id;
                data.partner_id = partner_id;
                data.date = date;
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0,9,10]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [5,6] },
            { render: function ( data, type, row ) {
              return row.workforce ? `${row.workforce.name}<br><small>${row.workforce.nid}</small>` : ''
            },targets: [1] },
            { render: function ( data, type, row ) {
              return row.patient ? `${row.patient.name}` : ''
            },targets: [2] },
            { render: function ( data, type, row ) {
              return row.partner ? `${row.partner.name}<br><small>${row.partner.address}</small>` : ''
            },targets: [4] },
            { render: function ( data, type, row ) {
              return row.updatedby ? `<span class="label bg-blue">${row.updatedby.name}</span>` : ''
            },targets: [9] },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                        <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            ${
                            `@if(in_array('update',$actionmenu))<li><a class="dropdown-item" href="{{url('admin/checkupresult')}}/${row.id}/edit"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>@endif
                            @if(in_array('read',$actionmenu))<li><a class="dropdown-item" href="{{url('admin/checkupresult')}}/${row.id}"><i class="glyphicon glyphicon-info-sign"></i> Detail</a></li>@endif
                            @if(in_array('delete',$actionmenu))
                            <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fa fa-trash"></i> Delete</a></li>@endif`
                            }
                        </ul>
                      </div>`
            },targets: [10]
            }
        ],
        columns: [
            { data: "no" },
            { data: "workforce_id" },
            { data: "patient_id" },
            { data: "date" },
            { data: "partner_id" },
            { data: "result" },
            { data: "normal_limit" },
            { data: "created_at" },
            { data: "updated_at" },
            { data: "updated_by" },
            { data: "id" },
        ]
    });
    $(".select2").select2();
    $('#form-search').submit(function(e){
        e.preventDefault();
        dataTable.draw();
        $('#add-filter').modal('hide');
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
          title:'Menghapus Hasil Pemeriksaan?',
          message:'Data yang telah dihapus tidak dapat dikembalikan',
          callback: function(result) {
            if(result) {
              var data = { _token: "{{ csrf_token() }}" };
              $.ajax({
                url: `{{url('admin/checkupresult')}}/${id}`,
                dataType: 'json', 
                data:data,
                type:'DELETE',
                beforeSend:function(){
                    $('.overlay').removeClass('hidden');
                }
              }).done(function(response){
                  if(response.status){
                      $('.overlay').addClass('hidden');
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
                  $('.overlay').addClass('hidden');
                  $.gritter.add({
                      title: 'Error!',
                      text: response.message,
                      class_name: 'gritter-error',
                      time: 1000,
                  });
              });
            }
          }
        });
    })
})
</script>
@endpush