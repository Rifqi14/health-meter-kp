@extends('admin.layouts.app')

@section('title', 'Jaminan Kesehatan')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-daterangepicker/css/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection
@push('breadcrump')
<li class="active">Jaminan Kesehatan</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Data Jaminan Kesehatan</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          @if(in_array('create',$actionmenu))
          <a href="{{route('healthinsurance.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Tambah">
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
              <th width="100">Nomor Surat</th>
              <th width="100">Workforce</th>
              <th width="100">Pasien</th>
              <th width="100">Pembuat</th>
              <th width="100">Pejabat Berwenang</th>
              <th width="100">Tarif</th>
              <th width="100">Dibuat</th>
              <th width="100">Terakhir Dirubah</th>
              <th width="100">Diubah Oleh</th>
              <th width="100">Status Cetak</th>
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
                <label for="cover_letter_type" class="control-label">Jenis Surat Jaminan</label>
                <select name="cover_letter_type" id="cover_letter_type" class="form-control select2" data-placeholder="Pilih Jenis Surat">
                  <option value=""></option>
                  @foreach (config('enums.authority') as $key => $item)
                  @if (strpos(strtoupper($item), 'JAMINAN'))
                  <option value="{{ $key }}">{{ $item }}</option>
                  @endif
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="workforce_id" class="control-label">Workforce</label>
                <input type="text" class="form-control workforce_id" id="workforce_id" name="workforce_id" data-placeholder="Pilih Workforce">
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
                <label for="letter_maker_id" class="control-label">Pembuat Surat</label>
                <input type="text" class="form-control workforce_id" id="letter_maker_id" name="letter_maker_id" data-placeholder="Pilih Pembuat Surat">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="authorized_official_id" class="control-label">Pejabat Berwenang</label>
                <input type="text" class="form-control" id="authorized_official_id" name="authorized_official_id" data-placeholder="Pilih Pejabat Berwenang">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="site_id" class="control-label">Distrik</label>
                <input type="text" class="form-control" id="site_id" name="site_id" data-placeholder="Pilih Distrik">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="date">Tanggal</label>
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
    $(".workforce_id").select2({
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
                  workforce_id: $("#workforce_id").val()
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
    $("input[name=authorized_official_id]").select2({
        ajax: {
            url: "{{route('authorizedofficial.select')}}",
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
                  text: `${item.type}`
                  });
              });
              return {
                  results: option, more: more,
              };
            },
        },
        allowClear: true,
    });
    $("input[name=site_id]").select2({
        ajax: {
            url: "{{route('site.select')}}",
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
            url: "{{route('healthinsurance.read')}}",
            type: "GET",
            data:function(data){
                var cover_letter_type = $('#form-search').find('select[name=cover_letter_type]').val();
                var workforce_id = $('#form-search').find('input[name=workforce_id]').val();
                var letter_maker_id = $('#form-search').find('input[name=letter_maker_id]').val();
                var patient_id = $('#form-search').find('input[name=patient_id]').val();
                var authorized_official_id = $('#form-search').find('input[name=authorized_official_id]').val();
                var site_id = $('#form-search').find('input[name=site_id]').val();
                var date = $('#form-search').find('input[name=date]').val();
                data.cover_letter_type = cover_letter_type;
                data.workforce_id = workforce_id;
                data.letter_maker_id = letter_maker_id;
                data.patient_id = patient_id;
                data.authorized_official_id = authorized_official_id;
                data.site_id = site_id;
                data.date = date;
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0,9,10]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [10,11] },
            { render: function ( data, type, row ) {
              return `<span><b>${row.letter_number}</b><br><small>${row.cover_letter_type}</small></span>`
            },targets: [1] },
            { render: function ( data, type, row ) {
              return row.workforce_id ? `<span><b>${row.workforce.name}</b><br><small>${row.workforce.nid} - ${row.workforce.site.name}</small></span>` : ''
            },targets: [2] },
            { render: function ( data, type, row ) {
              return row.patient_id ? `<span><b>${row.patient.name}</b><br><small>${row.patient.status} - ${row.patient.site.name}</small></span>` : ''
            },targets: [3] },
            { render: function ( data, type, row ) {
              return row.letter_maker_id ? `<span><b>${row.lettermaker.name}</b><br><small>${row.lettermaker.nid} - ${row.lettermaker.site.name}</small></span>` : ''
            },targets: [4] },
            { render: function ( data, type, row ) {
              return row.authorized_official_id ? `<span class="label bg-blue">${row.authorizer.authority_type ? `Tanda Tangan Basah` : `Approval Sistem`}</span><br><b>${row.authorizer.title.name}</b> - ${row.authorizer.site.name}` : ''
            },targets: [5] },
            { render: function ( data, type, row ) {
              return row.inpatient_id ? `${row.inpatient.name}` : ''
            },targets: [6] },
            { render: function ( data, type, row ) {
              return row.updated_by ? `<span class="label bg-blue">${row.updatedby.name}</span>` : ''
            },targets: [9] },
            { render: function ( data, type, row ) {
              switch (row.print_status) {
                case 0:
                  return `<span class="label label-default">Draft</span>`
                  break;
                case 1:
                  return `<span class="label label-primary">Printed</span>`
                  break;
              
                default:
                  return `<span class="label label-success">Uploaded</span>`
                  break;
              }
            },targets: [10] },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                        <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            ${
                            `@if(in_array('update',$actionmenu))<li><a class="dropdown-item" href="{{url('admin/healthinsurance')}}/${row.id}/edit"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>@endif
                            @if(in_array('read',$actionmenu))<li><a class="dropdown-item" href="{{url('admin/healthinsurance')}}/${row.id}"><i class="glyphicon glyphicon-info-sign"></i> Detail</a></li>@endif
                            @if(in_array('delete',$actionmenu))
                            <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fa fa-trash"></i> Delete</a></li>@endif
                            <li><a class="dropdown-item" href="{{url('admin/healthinsurance')}}/${row.id}/print"><i class="glyphicon glyphicon-print "></i> Print</a></li>`
                            
                            }
                        </ul>
                      </div>`
            },targets: [11]
            }
        ],
        columns: [
            { data: "no" },
            { data: "letter_number" },
            { data: "workforce_id" },
            { data: "patient_id" },
            { data: "letter_maker_id" },
            { data: "authorized_official_id" },
            { data: "inpatient_id" },
            { data: "created_at" },
            { data: "updated_at" },
            { data: "updated_by" },
            { data: "print_status" },
            { data: "id" },
        ]
    });
    $(".select2").select2({
      allowClear: true,
    });
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
          title:'Menghapus Jaminan Kesehatan?',
          message:'Data yang telah dihapus tidak dapat dikembalikan',
          callback: function(result) {
            if(result) {
              var data = { _token: "{{ csrf_token() }}" };
              $.ajax({
                url: `{{url('admin/healthinsurance')}}/${id}`,
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