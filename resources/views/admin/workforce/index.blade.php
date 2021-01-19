@extends('admin.layouts.app')

@section('title', 'Workforce')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="active">Workforce</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Data Workforce</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{route('workforce.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Tambah">
            <i class="fa fa-plus"></i>
          </a>
          {{-- <a href="{{route('workforce.import')}}" class="btn btn-success btn-sm" data-toggle="tooltip" title="Import">
          <i class="fa fa-upload"></i>
          </a> --}}
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
              <th width="200">Nama</th>
              <th width="200">Kelompok Workforce</th>
              <th width="200">Instansi</th>
              <th width="200">Unit</th>
              <th width="100">Terakhir Dirubah</th>
              <th width="100">Dirubah Oleh</th>
              <th width="100">Status</th>
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
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="name">Nama</label>
                <input type="text" name="name" class="form-control" placeholder="Nama">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="nid">NID</label>
                <input type="text" name="nid" class="form-control" placeholder="NID">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="site">Distrik</label>
                <input type="text" name="site" id="site" class="form-control" data-placeholder="Pilih Distrik">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="agency_id">Instansi</label>
                <input type="text" name="agency_id" id="agency_id" class="form-control" data-placeholder="Instansi">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label" for="workforce_group_id">Kelompok Workforce</label>
                <input type="text" name="workforce_group_id" id="workforce_group_id" class="form-control" data-placeholder="Kelompok Workforce">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="name" class="control-label">Arsip Kelompok</label>
                <select id="category" name="category" class="form-control select2" placeholder="Pilih Tipe Arsip">
                  <option value="">Non-Arsip</option>
                  <option value="1">Arsip</option>
                </select>
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
<script type="text/javascript">
  function filter(){
    $('#add-filter').modal('show');
  }
  $(function(){
    $(".select2").select2();
    $('#form-search').submit(function(e){
      e.preventDefault();
      dataTable.draw();
      $('#add-filter').modal('hide');
    });
    $("#site").select2({
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
              site_id:$('#site').val()==''?-1:$('#site').val()
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
            url: "{{route('workforce.read')}}",
            type: "GET",
            data:function(data){
              var name = $('#form-search').find('input[name=name]').val();
              var nid = $('#form-search').find('input[name=nid]').val();
              var agency_id = $('#form-search').find('input[name=agency_id]').val();
              var workforce_group_id = $('#form-search').find('input[name=workforce_group_id]').val();
              var site = $('#form-search').find('input[name=site]').val();
              var category = $('#form-search').find('select[name=category]').val();
              data.name = name;
              data.nid = nid;
              data.agency_id = agency_id;
              data.workforce_group_id = workforce_group_id;
              data.site = site;
              data.data_manager = {{$accesssite}};
              data.site_id = {{$siteinfo->id}};
              data.category = category;
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [6,7,8] },
            { render: function ( data, type, row) {
              return `${row.name}<br><small>${row.nid}</small>`
            },targets: [1] },
            { render: function ( data, type, row) {
              return `${row.workforce_group_id ? row.workforcegroup.name : ''}`
            },targets: [2] },
            { render: function ( data, type, row) {
              return `${row.agency_id ? row.agency.name : ''}`
            },targets: [3] },
            { render: function ( data, type, row) {
              return `${row.site_id ? row.site.name : ''}`
            },targets: [4] },
            { render: function ( data, type, row ) {
                  return `<span class="label bg-blue">${row.updatedby ? row.updatedby.name : ''}</span>`
            },targets: [6]
            },
            { render: function ( data, type, row ) {
              if (row.deleted_at) {
                bg = 'bg-red', teks = 'Non-Aktif';
              } else {
                bg = 'bg-green', teks = 'Aktif';
              }
              return `<span class="label ${bg}">${teks}</span>`
            },targets: [7]
            },
            { render: function ( data, type, row ) {
              return `<div class="dropdown">
                        <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            ${row.deleted_at ?
                            `<li><a class="dropdown-item" href="{{url('admin/workforce')}}/${row.id}"><i class="glyphicon glyphicon-info-sign"></i> Detail</a></li>
                            <li><a class="dropdown-item delete" href="#" data-id=${row.id}><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                            <li><a class="dropdown-item restore" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-refresh"></i> Restore</a></li>`
                            : 
                            `<li><a class="dropdown-item" href="{{url('admin/workforce')}}/${row.id}/edit"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                            <li><a class="dropdown-item" href="{{url('admin/workforce')}}/${row.id}"><i class="glyphicon glyphicon-info-sign"></i> Detail</a></li>
                            <li><a class="dropdown-item archive" href="#" data-id="${row.id}"><i class="fa fa-archive"></i> Archive</a></li>`
                            }
                        </ul>
                      </div>`
            },targets: [8]
            }
        ],
        columns: [
            { data: "no" },
            { data: "name" },
            { data: "workforce_group_id" },
            { data: "agency_id" },
            { data: "site_id" },
            { data: "updated_at" },
            { data: "updated_by" },
            { data: "deleted_at" },
            { data: "id" },
        ]
    });
    $(document).on('click','.archive',function(){
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
          title:'Mengarsipkan Workforce?',
          message:'Data ini akan diarsipkan dan tidak dapat digunakan pada menu lainnya.',
          callback: function(result) {
            if(result) {
              var data = { _token: "{{ csrf_token() }}" };
              $.ajax({
                url: `{{url('admin/workforce')}}/${id}`,
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
    $(document).on('click','.restore',function(){
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
          title:'Mengembalikan Workforce?',
          message:'Data ini akan dikembalikan dan dapat digunakan lagi pada menu lainnya.',
          callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}"
                          };
              $.ajax({
                url: `{{url('admin/workforce/restore')}}/${id}`,
                dataType: 'json', 
                data:data,
                type:'GET',
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
          title:'Menghapus Workforce?',
          message:'Data yang telah dihapus tidak dapat dikembalikan',
          callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}"
                          };
              $.ajax({
                url: `{{url('admin/workforce/delete')}}/${id}`,
                dataType: 'json', 
                data:data,
                type:'GET',
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