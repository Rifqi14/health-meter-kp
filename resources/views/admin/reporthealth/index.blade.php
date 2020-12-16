@extends('admin.layouts.app')

@section('title', 'Laporan Kesehatan')
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
    <li class="active">Laporan Kesehatan</li>
@endpush
@section('content')

<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Laporan Kesehatan</h3>
            <div class="pull-right box-tools">
                <a href="#" onclick="exportfile()" class="btn btn-info btn-sm" data-toggle="tooltip" title="Export">
                  <i class="fa fa-download"></i>
                </a>
              <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
                  <i class="fa fa-search"></i>
                </a>

            </div>
          </div>
        <div class="box-body">
            <table class="table table-striped table-bordered datatable" style="width:100%">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th width="100">Tanggal</th>
                        <th width="200">Bidang</th>
                        <th width="100">Total Nilai Bidang</th>
                        <th width="100">Peta Kesehatan</th>
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="department_id">Bidang</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="text" name="department_id" class="form-control" data-placeholder="Nama Bidang">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="form-search" type="submit" class="btn btn-default" title="Apply" id="btn-search"><i
                        class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Export --}}
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
                                                value="{{ date('d/m/Y',mktime(0,0,0,date('m'),date('d')-6,date('Y'))).' - '.date('d/m/Y',mktime(23,59,0,date('m'),date('d'),date('Y'))) }}">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="department_id">Bidang</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="text" name="department_id" class="form-control" data-placeholder="Nama Bidang">
                                    </div>
                                </div>
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
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
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
            url: "{{route('reporthealth.read')}}",
            type: "GET",
            data:function(data){
                var date = $('#form-search').find('input[name=date]').data('daterangepicker');
                data.department_id = $('#form-search input[name=department_id]').val();
                data.date_start = moment(date.startDate).format('YYYY-MM-DD');
                data.date_finish = moment(date.endDate).format('YYYY-MM-DD');
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [1,3,4] },
            {
                render: function ( data, type, row ) {
                    return `<span class="label" style="background-color:${row.color}">${row.peta_kesehatan} %</span>`;
                },
                targets: [4]
            },
        ],
        columns: [
            { data: "no" },
            { data: "report_date" },
            { data: "department_name" },
            { data: "total_bidang" },
            { data: "peta_kesehatan" }
        ]
    });
    $(".select2").select2();
    $('#form-search').submit(function(e){
        e.preventDefault();
        dataTable.draw();
        $('#add-filter').modal('hide');
    })

    $( "input[name=department_id]" ).select2({
        ajax: {
        url: "{{route('department.select')}}",
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
			title:'Menghapus bidang?',
			message:'Data yang telah dihapus tidak dapat dikembalikan',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}"
                        };
						$.ajax({
							url: `{{url('admin/medicalrecord')}}/${id}`,
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

    $('#form-export').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('reporthealth.export') }}",
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
