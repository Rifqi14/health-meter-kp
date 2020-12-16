@extends('admin.layouts.app')

@section('title', 'Detail Laporan Tagihan')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style type="text/css">
    .overlay-wrapper {
        position: relative;
    }

</style>
@endsection
@push('breadcrump')
<li><a href="{{ route('title.index') }}">Laporan Tagihan</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Detail Laporan Tagihan</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form class="form-horizontal">
                <div class="box-body">
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Tanggal Permohonan<b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{ $invoice->invoice_date }}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Rekanan <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{ $invoice->partner->name }}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Tanggal Penerimaan</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{ $invoice->receive_date }}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Dokumen</label>
                    <div class="col-sm-6">
                      <table class="table table-bordered table-striped" id="table-prescriptions">
                          <thead>
                              <th>Nama</th>
                              <th>Status</th>
                              <th>Catatan</th>
                          </thead>
                          <tbody>
                            @foreach ($invoice->document as $invoicedocument)
                                <tr>
                                    <td>{{$invoicedocument->doc->name}}</td>
                                    <td class="text-center status"><input type="checkbox"
                                            @if($invoicedocument->status == 1) checked @endif disabled></td>
                                    <td>{{ $invoicedocument->notes }}</td>
                                </tr>
                            @endforeach
                          </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Status <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                        <p class="form-control-static">
                            @if($invoice->status == 'Request') <span class="label label-warning">Request</span>
                            @elseif($invoice->status == 'Progress') <span class="label label-info">On Progress</span>
                            @elseif($invoice->status == 'Closed') <span class="label label-success">Closed</span>
                            @endif
                        </p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="close_date" class="col-sm-2 control-label">Tanggal Akhir</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{ $invoice->close_date }}</p>
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

{{-- Item --}}
<div class="row">
    <div class="col-lg-12">
        <div class="nav-tabs-custom tab-primary">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#employeefamily" data-toggle="tab">Item</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="employeefamily">
                    <div class="overlay-wrapper">
                        <div class="pull-right box-tools">
                            <a class="btn btn-primary btn-sm" onclick="addletter()"><i
                                class="fa fa-plus"></i></a>
                            <a class="btn btn-warning btn-sm" href="{{route('invoice.import',['id'=>$invoice->id])}}"
                                data-toggle="tooltip" title="Import" style="cursor: pointer;">
                                <i class="fa fa-upload"></i>
                            </a>
                        </div>
                        <table class="table table-bordered table-striped" id="table-detail"  style="width:100%">
                            <thead>
                                <tr>
                                    <th width="10">#</th>
                                    <th width="100">Tanggal</th>
                                    <th width="100">Name</th>
                                    <th width="100">Partner</th>
                                    <th width="100">Dibuat</th>
                                    <th width="10">#</th>
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

{{-- modal tambah --}}
<div class="modal fade" id="add-letter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Tambah Item</h4>
            </div>
            <div class="modal-body">

                <form id="form" method="post" action="{{route('invoice.coverstore')}}" autocomplete="off">
                    {{ csrf_field() }}
                <input type="hidden" name="invoice_id" value="{{$invoice->id}}"/>
                        <table class="table table-striped table-bordered" id="table-cover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th width="150">No.</th>
                                    <th width="150">Tanggal</th>
                                    <th class="text-center" width="100">Nama</th>
                                    <th class="text-center" width="200">Partner</th>
                                    <th class="text-center" width="100">Status</th>
                                    <th class="text-center" width="100">Status Tagihan</th>
                                    <th class="text-center" width="100">#</th>
                                </tr>
                            </thead>
                        </table>

                </form>
            </div>
            <div class="modal-footer">
                <button form="form" type="submit" class="btn btn-primary btn-sm" title="Simpan"><i
                        class="fa fa-save"></i></button>
            </div>
        </div>
    </div>
</div>

{{-- modal import --}}
<div class="modal fade" id="select-file" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Pilih File</h4>
            </div>
            <div class="modal-body">
                <form id="form-import" method="post" action="#" autocomplete="off">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-12">
                            <label class="col-sm-12 control-label" for="file">File Excel</label>
                            <div class="col-sm-12">
                                <input type="file" class="form-control" id="file" name="file" required
                                    accept=".xlsx" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="form-import" type="submit" class="btn btn-{{ config('configs.app_theme') }}"
                        title="Import"><i class="fa fa-upload"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script>
    function addletter(){
        $('#add-letter').modal('show');
        dataTableCover.draw();
    }

    $(document).ready(function () {

        $('.status').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
        dataTable = $('#table-detail').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: false,
            responsive: true,
            order: [
                [5, "desc"]
            ],
            ajax: {
                url: "{{route('invoice.itemread')}}",
                type: "GET",
                data:function(data){
                    data.invoice_id = {{ $invoice->id }};
                }
            },
            columnDefs: [
                {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [5] },
            {
                render:function( data, type, row ) {
                    return `${row.employee_name} <br>
                            <small>${row.medical_action_name}</small>`
                },targets: [2]
            },
            {
                render:function( data, type, row ) {
                    return `${row.partner_name} <br>
                            <small>${row.record_no}</small>`
                },targets: [3]
            },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                    </ul></div>`
            },targets: [5]
            }
            ],
            columns: [
                { data: "no" },
                { data: "date" },
                { data: "employee_name" },
                { data: "partner_name" },
                { data: "created_at" },
                { data: "id" },
            ]
        });

        dataTableCover = $('#table-cover').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 6, "asc" ]],
        ajax: {
            url: "{{route('invoice.coverread')}}",
            type: "GET",
            data:function(data){
                data.partner_id = {{$invoice->partner_id}};
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [4,5,6] },
            {
                render:function( data, type, row ) {
                    return `${row.employee_name} <br>
                            <small>${row.medical_action_name}</small>`
                },targets: [2]
            },
            {
                render: function ( data, type, row ) {
                    switch(row.status){
                        case 'Request':
                            return `<span class="label label-warning">Request</span>`
                            break;
                        case 'Closed':
                            return `<span class="label label-success">Closed</span>`
                            break;
                        default:
                            return `<span class="label label-default">-</span>`
                            break;
                    }
                },
                targets: [4]
            },
            {
                render: function ( data, type, row ) {
                    switch(row.status_invoice){
                        case 0:
                            return `<span class="label label-danger">Belum Ditagih</span>`
                            break;
                        case 1:
                            return `<span class="label label-warning">Proses Ditagih</span>`
                            break;
                        case 2:
                            return `<span class="label label-success">Sudah Ditagih</span>`
                            break;
                        default:
                            return `<span class="label label-default">-</span>`
                            break;
                    }
                },
                targets: [5]
            },
            { render: function ( data, type, row ) {
                    return `<input type="checkbox" name="medical_record_id[]" value="${row.id}" />`;
                    },
                    targets: [-1]
            }
        ],
        columns: [
            { data: "no" },
            { data: "date" },
            { data: "employee_name" },
            { data: "partner_name" },
            { data: "status" },
            { data: "status_invoice" },
            { data: "id" },
        ]
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
			title:'Menghapus item?',
			message:'Data yang telah dihapus tidak dapat dikembalikan',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}"
                        };
						$.ajax({
							url: `{{url('admin/invoice/coverdestroy')}}/${id}`,
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
                        })
					}
			}
		});
    })
    //cover store
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
                    $('#add-letter').modal('hide');
                        dataTable.draw();
                        dataTableCover.draw();
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
