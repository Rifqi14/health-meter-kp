@extends('admin.layouts.app')

@section('title', 'Dokter')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="active">Dokter</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Data Dokter</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{route('doctor.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Tambah">
            <i class="fa fa-plus"></i>
          </a>
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
              <th width="200">Distrik</th>
              <th width="200">Nama</th>
              <th width="200">Kontak</th>
              <th width="150">Status</th>
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
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
  aria-hidden="true" data-backdrop="static">
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
                <label class="control-label" for="name">Nama</label>
                <input type="text" name="name" class="form-control" placeholder="Nama">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="site" class="control-label">Distrik</label>
                <input type="text" class="form-control" id="site" name="site" data-placeholder="Distrik">
              </div>
            </div>
            <div class="col-md-12">
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
        <button form="form-search" type="submit" class="btn btn-default btn-sm" title="Apply"><i
            class="fa fa-search"></i></button>
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
    dataTable = $('.datatable').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 5, "asc" ]],
        ajax: {
            url: "{{route('doctor.read')}}",
            type: "GET",
            data:function(data){
                var name = $('#form-search').find('input[name=name]').val();
                var category = $('#form-search').find('select[name=category]').val();
                var site = $('#form-search').find('input[name=site]').val();
                data.category = category;
                data.name = name;
                data.site = site;
                data.data_manager = {{$accesssite}};
                data.site_id = {{$siteinfo->id}};
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [4,5] },
            { render:function( data, type, row ) {
                    return `${row.site.name}`
            },targets: [1] },
            { render:function( data, type, row ) {
                    return `<span class="label ${row.deleted_at ? 'bg-red' : 'bg-green'}">${row.deleted_at ? 'Non-Aktif' : 'Aktif'}</span>`
            },targets: [4] },
            { render: function ( data, type, row ) {
              return `<div class="dropdown">
                        <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            ${row.deleted_at ?
                            `<li><a class="dropdown-item" href="{{url('admin/doctor')}}/${row.id}"><i class="glyphicon glyphicon-info-sign"></i> Detail</a></li>
                            <li><a class="dropdown-item delete" href="#" data-id=${row.id}><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                            <li><a class="dropdown-item restore" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-refresh"></i> Restore</a></li>`
                            : 
                            `<li><a class="dropdown-item" href="{{url('admin/doctor')}}/${row.id}/edit"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                            <li><a class="dropdown-item" href="{{url('admin/doctor')}}/${row.id}"><i class="glyphicon glyphicon-info-sign"></i> Detail</a></li>
                            <li><a class="dropdown-item archive" href="#" data-id="${row.id}"><i class="fa fa-archive"></i> Archive</a></li>`
                            }
                        </ul>
                      </div>`
            },targets: [5]
            }
        ],
        columns: [
            { data: "no" },
            { data: "site_id" },
            { data: "name" },
            { data: "phone" },
            { data: "deleted_at" },
            { data: "id" },
        ]
    });
    $(".select2").select2();
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
    $('#form-search').submit(function(e){
        e.preventDefault();
        dataTable.draw();
        $('#add-filter').modal('hide');
    })
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
          title:'Mengarsipkan Dokter?',
          message:'Data ini akan diarsipkan dan tidak dapat digunakan pada menu lainnya.',
          callback: function(result) {
            if(result) {
              var data = { _token: "{{ csrf_token() }}" };
              $.ajax({
                url: `{{url('admin/doctor')}}/${id}`,
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
          title:'Mengembalikan Dokter?',
          message:'Data ini akan dikembalikan dan dapat digunakan lagi pada menu lainnya.',
          callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}"
                          };
              $.ajax({
                url: `{{url('admin/doctor/restore')}}/${id}`,
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
          title:'Menghapus Dokter?',
          message:'Data yang telah dihapus tidak dapat dikembalikan',
          callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}"
                          };
              $.ajax({
                url: `{{url('admin/doctok/delete')}}/${id}`,
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