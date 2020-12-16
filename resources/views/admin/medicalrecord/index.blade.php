@extends('admin.layouts.app')

@section('title', 'Laporan Medis')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-daterangepicker/css/daterangepicker.css')}}">
@endsection
@push('breadcrump')
    <li class="active">Laporan Medis</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Data Laporan Medis</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <a href="{{route('medicalrecord.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Tambah">
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
                        <th width="100">Tanggal</th>
                        <th width="200">Nama</th>
                        <th width="50">Status</th>
                        <th width="100">Dibuat</th>
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
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog"  aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
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
                                <label class="control-label" for="date">Tanggal</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control date-picker" id="date"
                                                name="date" placeholder="Tanggal"
                                                value="{{ date('d/m/Y H:i',mktime(0,0,0,date('m'),date('d')-6,date('Y'))).' - '.date('d/m/Y  H:i',mktime(23,59,0,date('m'),date('d'),date('Y'))) }}">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="partner_id">Partner</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="text" name="partner_id" class="form-control" data-placeholder="Nama Partner">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="status">Status</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <select type="text" class="select2 form-control" name="status" id="status" data-placeholder="Status">
                                            <option value="">Pilih Status</option>
                                            <option value="Request">Request</option>
                                            <option value="Closed">Closed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="employee_name">Nama Pegawai</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="text" name="employee_name" class="form-control" placeholder="Nama Pegawai">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="form-search" type="submit" class="btn btn-default" title="Apply"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/moment/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-daterangepicker/js/daterangepicker.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script type="text/javascript">
function filter(){
    $('#add-filter').modal('show');
}
$(function(){
    $('#form-search .date-picker').daterangepicker({
			timePicker: false,
			timePicker24Hour:false,
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            applyButtonClasses: "btn-{{config('configs.app_theme')}}",
            cancelClass: "btn-{{config('configs.app_theme')}}",
			ranges: {
				'Today': [moment(new Date().setHours(0,0)), moment(new Date().setHours(23,59))],
				'Yesterday': [moment(new Date().setHours(0,0)).subtract(1, 'days'), moment(new Date().setHours(23,59)).subtract(1, 'days')],
				'Last 7 Days': [moment(new Date().setHours(0,0)).subtract(6, 'days'), moment(new Date().setHours(23,59))],
				'Last 30 Days': [moment(new Date().setHours(0,0)).subtract(29, 'days'), moment(new Date().setHours(23,59))],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			},
			locale: {
				format: 'DD/MM/YYYY'
			}
		});

    $('#form-search .date-picker').on('apply.daterangepicker', function(ev, picker) {
		if(picker.endDate.diff(picker.startDate, 'days') > 30){
                $('#btn-search').attr('disabled', true);
				$.gritter.add({
					title: 'Peringatan!',
					text: 'Maksimal 30 Hari',
					class_name: 'gritter-error',
					time: 1000,
				});
				$('#form-search').find('input[name=date]').data('daterangepicker').setStartDate(moment(new Date().setHours(0,0)).subtract(6, 'days'));
	            $('#form-search').find('input[name=date]').data('daterangepicker').setEndDate(moment(new Date().setHours(23,59)));
				return false;
			}
        else{
            $('#btn-search').attr('disabled', false);
        }
    });

    dataTable = $('.datatable').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 1, "desc" ]],
        ajax: {
            url: "{{route('medicalrecord.read')}}",
            type: "GET",
            data:function(data){
                var date = $('#form-search').find('input[name=date]').data('daterangepicker');
                data.partner_id = $('input[name=partner_id]').val();
                data.employee_name = $('input[name=employee_name]').val();
                data.status = $('select[name=status]').val();
                data.date_start = moment(date.startDate).format('YYYY-MM-DD');
                data.date_finish = moment(date.endDate).format('YYYY-MM-DD');
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [3,5] },
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
                targets: [3]
            },
            {
                render: function (data, type, row) {
                    return `<div class="dropdown">
                <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                       <li><a class="dropdown-item" href="{{url('admin/medicalrecord')}}/${row.id}"><i class="glyphicon glyphicon-info-sign"></i> Detail</a></li>
                </ul></div>`
                },
                targets: [5]
            }
        ],
        columns: [
            { data: "no" },
            { data: "date" },
            { data: "employee_name" },
            { data: "status" },
            { data: "created_at" },
            { data: "id" },
        ]
    });
    $( "input[name=partner_id]" ).select2({
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
                text: item.name
            });
            });
            return {
            results: option, more: more,
            };
        },
        },
        allowClear: true,
    });

    $(".select2").select2();
    $('#form-search').submit(function(e){
        e.preventDefault();
        dataTable.draw();
        $('#add-filter').modal('hide');
    })
})
</script>
@endpush
