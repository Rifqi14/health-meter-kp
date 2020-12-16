@extends('admin.layouts.app')

@section('title', 'Laporan Status')
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
    <li class="active">Laporan Status</li>
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Ikhtisar</h3>
          <div class="pull-right box-tools">
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
                        WFO
                    </span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%;"></div>
                      </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <small>Minggu Terakhir</small>
                            <h4 class="animate last-week">0</h4>
                        </div>

                        <div class="col-xs-6">
                            <small>Hari Ini</small>
                            <h4 class="animate today">0</h4>
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
        <div class="box-body">
            <table class="table table-striped table-bordered datatable" style="width:100%">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th width="100">Tanggal</th>
                        <th width="200">Nama</th>
                        <th width="200">Jabatan</th>
                        <th width="100">Type</th>
                        <th width="50">Status</th>
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
                                                value="{{ date('d/m/Y',mktime(0,0,0,date('m'),date('d')-6,date('Y'))).' - '.date('d/m/Y',mktime(23,59,0,date('m'),date('d'),date('Y'))) }}">
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
                                <label class="control-label" for="type">Tipe Pegawai</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <select type="text" class="select2 form-control" name="type" id="type" data-placeholder="Pilih Tipe Pegawai">
                                            <option value=""></option>
                                            <option value="permanent">Pegawai Tetap</option>
                                            <option value="internship">Alih Daya</option>
                                            <option value="pensiun">Pensiun</option>
                                            <option value="other">Lainya</option>
                                        </select>
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
                                            <option value=""></option>
                                            <option value="wfo">WFO</option>
                                            <option value="wfh">WFH</option>
                                            <option value="izin">Izin</option>
                                            <option value="dinas">Dinas Luar Kota</option>
                                            <option value="libur">Libur</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="name">Nama Pegawai</label>
                                <input type="text" name="name" class="form-control" placeholder="Nama Pegawai">
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

{{-- export --}}
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
                <form id="form-export" action="{{ route('reportstatus.export') }}" autocomplete="off">
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
                                                value="{{ date('d/m/Y',mktime(0,0,0,date('m'),date('d')-6,date('Y'))).' - '.date('d/m/Y',mktime(23,59,0,date('m'),date('d'),date('Y'))) }}">
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
                                <label class="control-label" for="type">Tipe Pegawai</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <select type="text" class="select2 form-control" name="type" id="type" data-placeholder="Pilih Tipe Pegawai">
                                            <option value=""></option>
                                            <option value="permanent">Pegawai Tetap</option>
                                            <option value="internship">Alih Daya</option>
                                            <option value="pensiun">Pensiun</option>
                                            <option value="other">Lainya</option>
                                        </select>
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
                                            <option value=""></option>
                                            <option value="wfo">WFO</option>
                                            <option value="wfh">WFH</option>
                                            <option value="izin">Izin</option>
                                            <option value="dinas">Dinas Luar Kota</option>
                                            <option value="libur">Libur</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="name">Nama Pegawai</label>
                                <input type="text" name="name" class="form-control" placeholder="Nama Pegawai">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="form-export" type="submit" class="btn btn-info" title="Export" id="btn-export"><i class="fa fa-download"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/moment/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-daterangepicker/js/daterangepicker.js')}}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">
function filter(){
    $('#add-filter').modal('show');
}
function exportfile(){
    $('#export-file').modal('show');
}
function total(){
    $.ajax({
        url:"{{route('reportstatus.total')}}",
        type:'GET',
        beforeSend:function(){
            $('.totaltotal').removeClass('no-after');
        },
        success:function(data){
            $('.total').addClass('no-after');
            $('.total').html(data);
        }
    });
}
function today(){
    $.ajax({
        url:"{{route('reportstatus.today')}}",
        type:'GET',
        beforeSend:function(){
            $('.today').removeClass('no-after');
        },
        success:function(data){
            $('.today').addClass('no-after');
            $('.today').html(data);
        }
    });
}
function lastweek(){
    $.ajax({
        url:"{{route('reportstatus.lastweek')}}",
        type:'GET',
        beforeSend:function(){
            $('.last-week').removeClass('no-after');
        },
        success:function(data){
            $('.last-week').addClass('no-after');
            $('.last-week').html(data);
        }
    });
}
function chart(){
    $.ajax({
        url: "{{route('reportstatus.chart')}}",
        method: 'GET',
        dataType: 'json',
        beforeSend:function() {
            $('.chart').removeClass('no-after');
        },
        success:function(response) {
            $('.chart').addClass('no-after');
            Highcharts.chart('chart', {
                title:{
                    text:'',
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
    today();
    lastweek();
    chart();
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
            url: "{{route('reportstatus.read')}}",
            type: "GET",
            data:function(data){
                var name = $('#form-search').find('input[name=name]').val();
                var date = $('#form-search').find('input[name=date]').data('daterangepicker');
                data.status = $('select[name=status]').val();
                data.type = $('select[name=type]').val();
                data.date_start = moment(date.startDate).format('YYYY-MM-DD HH:mm');
                data.date_finish = moment(date.endDate).format('YYYY-MM-DD HH:mm');
                data.name = name;
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            {
                render:function( data, type, row ) {
                    return `${row.name} <br>
                            <small>${row.nid}</small>`
                },targets: [2]
            },
            { className: "text-center", targets: [1,5] },
        ],
        columns: [
            { data: "no" },
            { data: "status_date" },
            { data: "name" },
            { data: "title_name" },
            { data: "type" },
            { data: "description" }
        ]
    });
    $(".select2").select2({allowClear:true});
    $('#form-search').submit(function(e){
        e.preventDefault();
        dataTable.draw();
        $('#add-filter').modal('hide');
    })

    $('#form-export').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('reportstatus.export') }}",
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
