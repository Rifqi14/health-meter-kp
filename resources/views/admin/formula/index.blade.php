@extends('admin.layouts.app')

@section('title', 'Formula Assessment')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="active">Formula Assessment</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Data Formula Assessment</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{route('formula.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Tambah">
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
              <th width="200">Nama</th>
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
    $('.select2').select2();
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
            url: "{{route('formula.read')}}",
            type: "GET",
            data:function(data){
                var name = $('#form-search').find('input[name=name]').val();
                var category = $('#form-search').find('select[name=category]').val();
                data.name = name;
                data.category = category;
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0,2] },
            { className: "text-center", targets: [3,4,5] },
            { render: function (data, type, row) {
                return `<span class="label bg-blue">${row.user ? row.user.name : ''}</span>`
            }, targets: [3] },
            { render: function (data, type, row) {
                if (row.deleted_at) {
                    bg = 'bg-red', teks = 'Non-Aktif';
                } else {
                    bg = 'bg-green', teks = 'Aktif';
                }
                return `<span class="label ${bg}">${teks}</span>`
            }, targets: [4] },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                            <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bars"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                ${row.deleted_at ?
                                `<li><a class="dropdown-item restore" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-refresh"></i> Restore</a></li>`
                                : 
                                `<li><a class="dropdown-item" href="{{url('admin/formula')}}/${row.id}/edit"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                                <li><a class="dropdown-item" href="{{url('admin/formula')}}/${row.id}"><i class="glyphicon glyphicon-info-sign"></i> Detail</a></li>`
                                }
                            </ul>
                        </div>`
            },targets: [5]
            }
        ],
        columns: [
            { data: "no" },
            { data: "name" },
            { data: "updated_at" },
            { data: "updated_by" },
            { data: "deleted_at" },
            { data: "id" },
        ]
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
        title:'Mengarsipkan formula?',
        message:'Data ini akan diarsipkan dan tidak dapat digunakan pada menu lainnya.',
        callback: function(result) {
          if(result) {
            var data = {
              _token: "{{ csrf_token() }}"
            };
            $.ajax({
              url: `{{url('admin/formula')}}/${id}`,
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
    });
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
        title:'Mengembalikan formula?',
        message:'Data ini akan dikembalikan dan dapat digunakan lagi pada menu lainnya.',
        callback: function(result) {
          if(result) {
            var data = {
              _token: "{{ csrf_token() }}"
            };
            $.ajax({
              url: `{{url('admin/formula/restore')}}/${id}`,
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
    });
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
			title:'Menghapus formula?',
			message:'Data yang telah dihapus tidak dapat dikembalikan',
			callback: function(result) {
                if(result) {
                    var data = {
                        _token: "{{ csrf_token() }}"
                    };
                    $.ajax({
                        url: `{{url('admin/formula/delete')}}/${id}`,
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
                    })		
                }
			}
		});
    })
})
</script>
@endpush