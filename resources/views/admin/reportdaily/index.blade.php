@extends('admin.layouts.app')

@section('title', 'Laporan Harian')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-daterangepicker/css/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
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
        background-size: 0;
        position: relative;
        overflow: hidden;
    }

    .animate:after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: calc(200% + 200px);
        bottom: 0;
        background: linear-gradient(to right, #eff1f3 4%, #e2e2e2 25%, #eff1f3 36%);
        animation: shimmer 4s infinite;
    }

    .animate.no-after:after {
        display: none;
    }

    .progress {
        background: rgba(0, 0, 0, 0.2);
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
<li class="active">Laporan Harian</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="nav-tabs-custom tab-primary">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#personnel" data-toggle="tab">Personil</a></li>
                <li><a href="#supervisor" data-toggle="tab">Atasan (Fungsi/Bidang)</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="personnel">
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
                                                <h2 class="m-b-xs animate total-personnel">0</h2>
                                                <span class="no-margins">
                                                    Total Personil
                                                </span>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: 100%;"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <small>Sudah Lapor</small>
                                                        <h4 class="animate last-week-personnel">0</h4>
                                                    </div>

                                                    <div class="col-xs-6">
                                                        <small>Belum Lapor</small>
                                                        <h4 class="animate today-personnel">0</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div id="chart-personnel" class=" animate" style="height: 160px"></div>
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
                    <table class="table table-striped table-bordered datatable" style="width:100%" id="table-personnel">
                        <thead>
                            <tr>
                                <th width="10">#</th>
                                <th width="100">Tanggal</th>
                                <th width="200">Nama</th>
                                <th width="200">Bidang</th>
                                <th width="200">Jabatan</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane" id="supervisor">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">Ikhtisar</h3>
                                    <div class="pull-right box-tools">
                                        <a href="#" onclick="exportfile2()" class="btn btn-info btn-sm" data-toggle="tooltip" title="Export">
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <a href="#" onclick="filter2()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
                                            <i class="fa fa-search"></i>
                                        </a>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div>
                                                <h2 class="m-b-xs animate total-supervisor">0</h2>
                                                <span class="no-margins">
                                                    Total Supervisor
                                                </span>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: 100%;"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <small>Sudah Lapor</small>
                                                        <h4 class="animate last-week-supervisor">0</h4>
                                                    </div>

                                                    <div class="col-xs-6">
                                                        <small>Belum Lapor</small>
                                                        <h4 class="animate today-supervisor">0</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div id="chart-supervisor" class=" animate" style="height: 160px"></div>
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
                    <table class="table table-striped table-bordered datatable" style="width:100%" id="table-supervisor">
                        <thead>
                            <tr>
                                <th width="10">#</th>
                                <th width="100">Tanggal</th>
                                <th width="200">Nama</th>
                                <th width="200">Bidang</th>
                                <th width="200">Jabatan</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
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
                                <label class="control-label" for="date">Tanggal</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control date-picker" name="date" placeholder="Tanggal" value="{{ date('Y-m-d')}}">
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
                <button form="form-search" type="submit" class="btn btn-default" title="Apply"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-filter2" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Pencarian</h4>
            </div>
            <div class="modal-body">
                <form id="form-search2" autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="date">Tanggal</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control date-picker" name="date" placeholder="Tanggal" value="{{ date('Y-m-d')}}">
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
                <button form="form-search2" type="submit" class="btn btn-default" title="Apply"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</div>
{{-- Modal Export --}}
<div class="modal fade" id="export-file" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Export</h4>
            </div>
            <div class="modal-body">
                <form id="form-export" action="{{ route('reportdaily.export') }}" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="date">Tanggal</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control date-picker" name="date" placeholder="Tanggal" value="{{ date('Y-m-d')}}">
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
                <button form="form-export" type="submit" class="btn btn-default" title="Apply"><i class="fa fa-download"></i></button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Export 2 --}}
<div class="modal fade" id="export-file2" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Export</h4>
            </div>
            <div class="modal-body">
                <form id="form-export2" action="{{ route('reportdailysuper.export') }}" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="order_date">Tanggal</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control date-picker" name="date" placeholder="Tanggal" value="{{ date('Y-m-d',mktime(0,0,0,date('m'),date('d')-6,date('Y')))}}">
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
                <button form="form-export2" type="submit" class="btn btn-default" title="Apply"><i class="fa fa-download"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/moment/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-daterangepicker/js/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">
    function filter(){
    $('#add-filter').modal('show');
}
function filter2(){
    $('#add-filter2').modal('show');
}
function exportfile(){
    $('#export-file').modal('show');
}
function exportfile2(){
    $('#export-file2').modal('show');
}
function totalpersonnel(){
    $.ajax({
        url:"{{route('reportdaily.totalpersonnel')}}",
        type:'GET',
        data:{
            date:$('#form-search input[name=date]').val(),
            department_id:$('#form-search input[name=department_id]').val()
        },
        beforeSend:function(){
            $('.total-personnel').removeClass('no-after');
        },
        success:function(data){
            $('.total-personnel').addClass('no-after');
            $('.total-personnel').html(data);
        }
    });
}
function todaypersonnel(){
    $.ajax({
        url:"{{route('reportdaily.todaypersonnel')}}",
        type:'GET',
        data:{
            date:$('#form-search input[name=date]').val(),
            department_id:$('#form-search input[name=department_id]').val()
        },
        beforeSend:function(){
            $('.today-personnel').removeClass('no-after');
        },
        success:function(data){
            $('.today-personnel').addClass('no-after');
            $('.today-personnel').html(data);
        }
    });
}
function lastweekpersonnel(){
    $.ajax({
        url:"{{route('reportdaily.lastweekpersonnel')}}",
        type:'GET',
        data:{
            date:$('#form-search input[name=date]').val(),
            department_id:$('#form-search input[name=department_id]').val()
        },
        beforeSend:function(){
            $('.last-week-personnel').removeClass('no-after');
        },
        success:function(data){
            $('.last-week-personnel').addClass('no-after');
            $('.last-week-personnel').html(data);
        }
    });
}

//chart personel
function chartpersonnel(){
    $.ajax({
        url: "{{route('reportdaily.chartpersonnel')}}",
        method: 'GET',
        dataType: 'json',
        data:{
            date:$('#form-search input[name=date]').val(),
            department_id:$('#form-search input[name=department_id]').val()
        },
        beforeSend:function() {
            $('#chart-personnel').removeClass('no-after');
        },
        success:function(response) {
            $('#chart-personnel').addClass('no-after');
            Highcharts.chart('chart-personnel', {
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

//chart supervisor
function chartsupervisor(){
    $.ajax({
        url: "{{route('reportdaily.chartsupervisor')}}",
        method: 'GET',
        dataType: 'json',
        data:{
            date:$('#form-search2 input[name=date]').val(),
            department_id:$('#form-search2 input[name=department_id]').val()
        },
        beforeSend:function() {
            $('#chart-supervisor').removeClass('no-after');
        },
        success:function(response) {
            $('#chart-supervisor').addClass('no-after');
            Highcharts.chart('chart-supervisor', {
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

function totalsupervisor(){
    $.ajax({
        url:"{{route('reportdaily.totalsupervisor')}}",
        type:'GET',
        data:{
            date:$('#form-search2 input[name=date]').val(),
            department_id:$('#form-search2 input[name=department_id]').val()
        },
        beforeSend:function(){
            $('.total-supervisor').removeClass('no-after');
        },
        success:function(data){
            $('.total-supervisor').addClass('no-after');
            $('.total-supervisor').html(data);
        }
    });
}
function todaysupervisor(){
    $.ajax({
        url:"{{route('reportdaily.todaysupervisor')}}",
        type:'GET',
        data:{
            date:$('#form-search2 input[name=date]').val(),
            department_id:$('#form-search2 input[name=department_id]').val()
        },
        beforeSend:function(){
            $('.today-supervisor').removeClass('no-after');
        },
        success:function(data){
            $('.today-supervisor').addClass('no-after');
            $('.today-supervisor').html(data);
        }
    });
}
function lastweeksupervisor(){
    $.ajax({
        url:"{{route('reportdaily.lastweeksupervisor')}}",
        type:'GET',
        data:{
            date:$('#form-search2 input[name=date]').val(),
            department_id:$('#form-search2 input[name=department_id]').val()
        },
        beforeSend:function(){
            $('.last-week-supervisor').removeClass('no-after');
        },
        success:function(data){
            $('.last-week-supervisor').addClass('no-after');
            $('.last-week-supervisor').html(data);
        }
    });
}
$(function(){
    //date
    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
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
    totalpersonnel();
    todaypersonnel();
    lastweekpersonnel();
    chartpersonnel();

    totalsupervisor();
    todaysupervisor();
    lastweeksupervisor();
    chartsupervisor()
    dataTablePersonnel = $('#table-personnel').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 5, "desc" ]],
        ajax: {
            url: "{{route('reportdaily.personnel')}}",
            type: "GET",
            data:function(data){
                data.date = $('#form-search input[name=date]').val();
                data.department_id = $('#form-search input[name=department_id]').val();
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
            { className: "text-center", targets: [5] },
            {
                render: function ( data, type, row ) {
                    if(row.total > 0){
                        return `<span class="label label-success"><i class="fa fa-check"></i></span>`
                    }
                    else{
                        return `<span class="label label-danger"><i class="fa fa-times"></i></span>`
                    }
                },
                targets: [5]
            },
        ],
        columns: [
            { data: "no" },
            { data: "report_date" },
            { data: "name" },
            { data: "department_name" },
            { data: "title_name" },
            { data: "total" }
        ]
    });

    dataTableSupervisor = $('#table-supervisor').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 5, "desc" ]],
        ajax: {
            url: "{{route('reportdaily.supervisor')}}",
            type: "GET",
            data:function(data){
                data.date = $('#form-search2 input[name=date]').val();
                data.department_id = $('#form-search2 input[name=department_id]').val();
                // data.date = '{{date('Y-m-d')}}'
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
            { className: "text-center", targets: [5] },
            {
                render: function ( data, type, row ) {
                    if(row.total > 0){
                        return `<span class="label label-success"><i class="fa fa-check"></i></span>`
                    }
                    else{
                        return `<span class="label label-danger"><i class="fa fa-times"></i></span>`
                    }
                },
                targets: [5]
            },
        ],
        columns: [
            { data: "no" },
            { data: "report_date" },
            { data: "name" },
            { data: "department_name" },
            { data: "title_name" },
            { data: "total" }
        ]
    });
    $(".select2").select2();

    $('#form-search').submit(function(e){
        e.preventDefault();
        dataTablePersonnel.draw();
        totalpersonnel();
        todaypersonnel();
        lastweekpersonnel();
        chartpersonnel();
        $('#add-filter').modal('hide');
    })

    $('#form-search2').submit(function(e){
        e.preventDefault();
        dataTableSupervisor.draw();
        totalsupervisor();
        todaysupervisor();
        lastweeksupervisor();
        chartsupervisor();
        $('#add-filter2').modal('hide');
    })

    $('#form-export').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('reportdaily.export') }}",
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

    $('#form-export2').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('reportdailysuper.export') }}",
            type: 'POST',
            dataType: 'JSON',
            data: $("#form-export2").serialize(),
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