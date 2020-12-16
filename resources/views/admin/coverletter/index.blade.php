@extends('admin.layouts.app')

@section('title', 'Surat Pengantar')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-daterangepicker/css/daterangepicker.css')}}">
<style type="text/css">
    .m-t-xs {
        margin-top: 5px;
    }
    .m-b-xs {
        margin-bottom: 5px;
    }
    .m-t-xl {
        margin-top: 40px;
    }
    .m-b-xs {
        margin-bottom: 5px;
    }
    .animate {
		background-image: linear-gradient(to right, #ebebeb calc(50% - 100px), #c5c5c5 50%, #ebebeb calc(50% + 100px));
		background-size:0;
		position:relative;
		overflow:hidden;
	}
	.animate:after {
		content:"";
		position:absolute;
		top:0;
		left:0;
		width:calc(200% + 200px);
		bottom:0;
		background: linear-gradient(to right, #eff1f3 4%, #e2e2e2 25%, #eff1f3 36%);
		animation : shimmer 4s infinite;
	}
	.animate.no-after:after{
		display: none;
	}
    .progress {
        background: rgba(0,0,0,0.2);
        margin: 5px 0 5px 0;
        height: 2px;
    }
	@keyframes shimmer {
		0% {
			background-position: -1000px 0;
		}
		100% {
			background-position: 1000px 0;
		}
	}
</style>
@endsection
@push('breadcrump')
    <li class="active">Surat Pengantar</li>
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Ikhtisar</h3>
          <div class="pull-right box-tools">
            <a href="{{route('coverletter.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Tambah">
                <i class="fa fa-plus"></i>
              </a>

              <a href="#" onclick="exportfile()" class="btn btn-info btn-sm" data-toggle="tooltip" title="Export">
                <i class="fa fa-download"></i>
              </a>
            <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
                <i class="fa fa-search"></i>
              </a>

          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-3">
                <div>
                    <h2 class="m-b-xs animate total">0</h2>
                    <span class="no-margins">
                        Surat Pengantar
                    </span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%;"></div>
                      </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <small>Closed</small>
                            <h4 class="animate lastweek">0</h4>
                        </div>

                        <div class="col-xs-6">
                            <small>Request</small>
                            <h4 class="animate request">0</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div id="chart" class="chart animate" style="height: 160px"></div>
            </div>
          </div>
          <!-- /.row -->
        </div>
        <!-- ./box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>

<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Data Surat Pengantar</h3>
        </div>
        <div class="box-body">
            <table class="table table-striped table-bordered datatable" style="width:100%">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th width="100">Tanggal</th>
                        <th width="100">Nama</th>
                        <th width="100">Pasien</th>
                        <th width="100">Partner</th>
                        <th width="50">Status</th>
                        <th width="100">Status Tagihan</th>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="employee_name">Nama</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control" type="text" name="employee_name" placeholder="Nama">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="patient_name">Nama Pasien</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control" type="text" name="patient_name" placeholder="Nama Pasien">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="form-search" type="submit" class="btn btn-default" title="Apply"><i class="fa fa-search" id="btn-search"></i></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="export-file" tabindex="-1" role="dialog"  aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Export Data</h4>
			</div>
            <div class="modal-body">
                <form id="form-export" action="{{ route('coverletter.export') }}" autocomplete="off">
                    @csrf
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
                                        <input class="form-control" type="text" name="partner_id" data-placeholder="Nama Partner">
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
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="form-export" type="submit" class="btn btn-info" title="Export"><i class="fa fa-download" id="btn-export"></i></button>
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
<script src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">
function filter(){
    $('#add-filter').modal('show');
}
function exportfile(){
    $('#export-file').modal('show');
}
function total(){
    var date = $('#form-search').find('input[name=date]').data('daterangepicker');
    $.ajax({
        url:"{{route('coverletter.total')}}",
        type:'GET',
        data:{
            partner_id : $('input[name=partner_id]').val(),
            status : $('select[name=status]').val(),
            date_start : moment(date.startDate).format('YYYY-MM-DD'),
            date_finish : moment(date.endDate).format('YYYY-MM-DD')
        },
        beforeSend:function(){
            $('.totaltotal').removeClass('no-after');
        },
        success:function(data){
            $('.total').addClass('no-after');
            $('.total').html(data);
        }
    });
}
function request(){
    var date = $('#form-search').find('input[name=date]').data('daterangepicker');
    $.ajax({
        url:"{{route('coverletter.request')}}",
        type:'GET',
        data:{
            partner_id : $('input[name=partner_id]').val(),
            status : $('select[name=status]').val(),
            date_start : moment(date.startDate).format('YYYY-MM-DD'),
            date_finish : moment(date.endDate).format('YYYY-MM-DD')
        },
        beforeSend:function(){
            $('.request').removeClass('no-after');
        },
        success:function(data){
            $('.request').addClass('no-after');
            $('.request').html(data);
        }
    });
}
function closed(){
    var date = $('#form-search').find('input[name=date]').data('daterangepicker');
    $.ajax({
        url:"{{route('coverletter.closed')}}",
        type:'GET',
        data:{
            partner_id : $('input[name=partner_id]').val(),
            status : $('select[name=status]').val(),
            date_start : moment(date.startDate).format('YYYY-MM-DD'),
            date_finish : moment(date.endDate).format('YYYY-MM-DD')
        },
        beforeSend:function(){
            $('.lastweek').removeClass('no-after');
        },
        success:function(data){
            $('.lastweek').addClass('no-after');
            $('.lastweek').html(data);
        }
    });
}
function chart(){
    var date = $('#form-search').find('input[name=date]').data('daterangepicker');
    $.ajax({
        url: "{{route('coverletter.chart')}}",
        method: 'GET',
        dataType: 'json',
        data:{
            partner_id : $('input[name=partner_id]').val(),
            status : $('select[name=status]').val(),
            date_start : moment(date.startDate).format('YYYY-MM-DD'),
            date_finish : moment(date.endDate).format('YYYY-MM-DD')
        },
        beforeSend:function() {
            $('.chart').removeClass('no-after');
        },
        success:function(response) {
            $('.chart').addClass('no-after');
            Highcharts.chart('chart', {
                title:{
                    text:response.title,
                },
                chart: {
                    type: 'area'
                },
                xAxis: {
                    categories: response.categories,
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: false
                    }
                },
                legend: {
                    enabled:false,
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<td style="padding:0"><b>{point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    area: {
                        marker: {
                            enabled: false,
                            symbol: 'circle',
                            radius: 2,
                            states: {
                                hover: {
                                    enabled: true
                                }
                            }
                        }
                    }
                },
                colors: ['#CAE3BF'],
                credits: false,
                series: [{
                    data: response.series
                }]
            });
        }
    });
}
$(function(){
    //date
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

    $('#form-export .date-picker').daterangepicker({
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

    $('#form-export .date-picker').on('apply.daterangepicker', function(ev, picker) {
		if(picker.endDate.diff(picker.startDate, 'days') > 30){
                $('#btn-export').attr('disabled', true);
				$.gritter.add({
					title: 'Peringatan!',
					text: 'Maksimal 30 Hari',
					class_name: 'gritter-error',
					time: 1000,
				});
				$('#form-export').find('input[name=date]').data('daterangepicker').setStartDate(moment(new Date().setHours(0,0)).subtract(6, 'days'));
	            $('#form-export').find('input[name=date]').data('daterangepicker').setEndDate(moment(new Date().setHours(23,59)));
				return false;
			}
        else{
            $('#btn-export').attr('disabled', false);
        }
    });

    total();
    request();
    closed();
    chart();
    dataTable = $('.datatable').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 8, "desc" ]],
        ajax: {
            url: "{{route('coverletter.read')}}",
            type: "GET",
            data:function(data){
                var date = $('#form-search').find('input[name=date]').data('daterangepicker');
                data.partner_id = $('input[name=partner_id]').val();
                data.employee_name = $('input[name=employee_name]').val();
                data.patient_name = $('input[name=patient_name]').val();
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
            { className: "text-center", targets: [5,6,8] },
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
                targets: [5]
            },
            {
                render: function ( data, type, row ) {
                    if(row.medical_action_name == 'Error Entry' || row.medical_action_name == 'Observasi Dan Dalam Pantuan Dokter' || row.medical_action_name == 'Istirahat Mandiri'){
                        return `<span class="label label-default">Tidak Perlu Ditagih</span>`
                    }
                    else{
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
                    }
                    
                },
                targets: [6]
            },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/coverletter')}}/${row.id}/edit"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                        <li><a class="dropdown-item" href="{{url('admin/coverletter')}}/${row.id}"><i class="glyphicon glyphicon-info-sign"></i> Detail</a></li>
                    </ul></div>`
            },targets: [8]
            }
        ],
        columns: [
            { data: "no" },
            { data: "date" },
            { data: "employee_name" },
            { data: "patient_name" },
            { data: "partner_name" },
            { data: "status" },
            { data: "status_invoice" },
            { data: "created_at" },
            { data: "id" },
        ]
    });
    $(".select2").select2();
    $('#form-search').submit(function(e){
        e.preventDefault();
        dataTable.draw();
        total();
        request();
        closed();
        chart();
        $('#add-filter').modal('hide');
    })

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

    $('#form-export').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('coverletter.export') }}",
            type: 'POST',
            dataType: 'JSON',
            data: $("#form-export").serialize(),
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
                let download = document.createElement("a");
					download.href = response.file;
					document.body.appendChild(download);
					download.download = response.name;
					download.click();
					download.remove();
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

    })
})
</script>
@endpush
