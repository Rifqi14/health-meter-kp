@extends('admin.layouts.app')
@section('title', 'Detail Peta Kesehatan')

@section('breadcrump')
<ol class="breadcrumb">
    <li class="active">Detail</li>
</ol>
@endsection
@section('stylesheets')
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
@section('content')
<div class="tab-pane active" id="department">
                   
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Personil</span>
              <span class="info-box-number">{{count($employees)}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-4 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-building"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Total Nilai Bidang</span>
              <span class="info-box-number">
                @if(count($formulas) == 0)
                    0
                @endif
                @foreach($formulas as $formula)
                    @if($formula->name ==  'Total Nilai Bidang')
                        {{$formula->value}}
                    @endif
                @endforeach
              </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon"   style="background:none">

                    <input type="text" class="knob" value="@if(count($formulas) == 0)
                    0
                @endif @foreach($formulas as $formula)
                    @if($formula->name ==  'Peta Kesehatan')
                        {{round($formula->value?$formula->value:0,2)}}
                    @endif
                @endforeach" data-width="90" data-height="90" data-thickness="0.1" data-fgColor="@if(count($formulas) == 0)
                0
            @endif @foreach($formulas as $formula)
                @if($formula->name ==  'Peta Kesehatan')
                    {{healthMeter($formula->value?$formula->value:0,2)}}
                @endif
            @endforeach" autocomplete="off">
                
                </span>
    
                <div class="info-box-content">
                    <span class="info-box-text">Peta Kesehatan</span>
                    <span class="info-box-number">
                    @foreach($formulas as $formula)
                        @if($formula->name ==  'Peta Kesehatan')
                            {{round($formula->value,2)}} %
                        @endif
                    @endforeach
                    </span>
                </div>
            <!-- /.info-box-content -->
            </div>
        </div>
        
      </div>
        @foreach($formulas as $formula)
        @if($formula->name ==  'Peta Kesehatan')
        <div class="alert" style="background:{{healthMeter($formula->value)}};color:#fff">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="fa fa-info"></i> Rekomendasi</h4>
            {!!healthRecomendation($formula->value)!!}
        </div>
        @endif
        @endforeach
    
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="box-header">
                            <h3 class="box-title">Riwayat Peta Kesehatan ( Last 7 days)</h3>
                        </div>
                        <div id="chart-history">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @php $no = 0;
            @endphp
    @foreach ($categories as $category)
        @if($no % 2 == 0)
        </div>
        <div class="row">
        @endif
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header">
            <h3 class="box-title">{{$category->name}}</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              @if($category->parameter == 'subcategory')
              <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Nama</th>
                      <th>Nilai</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach ($subcategories as $subcategory)
                          @if($subcategory->category_id == $category->id)
                          <tr>
                              <td>{{$subcategory->name}}</td>
                              @foreach($reports as $report)
                                @if($subcategory->id == $report->sub_category_id)
                                    @if($report->type == 'yesno')
                                        <td class="text-center">{{$report->value?'Ya':'Tidak'}}</td>
                                    @endif
                                    @if($report->type == 'range')
                                        <td class="text-right">{{$report->value}}</td>
                                    @endif
                                @endif
                              @endforeach
                          </tr>
                          @endif
                      @endforeach
                  </tbody>
              </table>
              @endif

              @if($category->parameter == 'employee')
              <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Nama</th>
                      @php
                        $header = '';
                      @endphp
                      @foreach($reports as $report)
                        @if($category->id == $report->category_id)
                        @if($header != $report->category_name)
                        <th class="text-center">{{$report->category_name}}</th>
                        @php
                        $header = $report->category_name;
                        @endphp
                        @endif
                        @endif
                     @endforeach
                    </tr>
                  </thead>
                  <tbody>
                      @foreach ($employees as $employee)
                          <tr>
                              <td>{{$employee->name}}</td>
                              @foreach($reports as $report)
                                @if($category->id == $report->category_id && $employee->id == $report->employee_id)
                                    @if($report->type == 'yesno')
                                        <td class="text-center">{{$report->value?'Ya':'Tidak'}}</td>
                                    @endif
                                    @if($report->type == 'range')
                                        <td class="text-right">{{$report->value}}</td>
                                    @endif
                                @endif
                              @endforeach
                          </tr>
                      @endforeach
                  </tbody>
              </table>
              @endif
            </div>
        </div>
    </div>
    @php $no++;
    @endphp
    @endforeach
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Temperatur Karyawan</h3>
                </div>
                <div class="box-body">
                    <div id="chart-temperature">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Saturasi Oksigen</h3>
                </div>
                <div class="box-body">
                    <div id="chart-saturasi">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="{{asset('adminlte/component/jquery-knob/jquery.knob.js')}}"></script>
<script type="text/javascript">
    function chart(){
        $.ajax({
            url: "{{route('dashboard.chart')}}",
            method: 'GET',
            dataType: 'json',
            data:{
                department_id:{{$department_id}}
            },
            beforeSend:function() {
                $('#chart-history').removeClass('no-after');
            },
            success:function(response) {
                $('#chart-history').addClass('no-after');
                Highcharts.chart('chart-history', {
                    title:{
                        text:response.date,
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
    @if($employees)
    Highcharts.chart('chart-temperature', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Temperatur Karyawan'
        },
        subtitle: {
            text: '{{date('d/m/Y')}}'
        },
        xAxis: {
            categories: [
                @foreach($employees as $employee)
                {!!"'".addslashes($employee->name)."'".","!!}
                @endforeach
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '°C'
            }
        },
        legend: {
            enabled:false,
        },
        credits: false,
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="padding:0"><b>{point.y:.1f} °C</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        colors: ['#3c8dbc'],
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            data: [
                @foreach ($categories as $category)
                @if($category->name == 'Temperatur Karyawan' &&$category->parameter == 'employee')
                    
                    @foreach ($employees as $employee)
                    @php
                        $value = 0;
                    @endphp
                        @foreach($reports as $report)
                            @if($category->id == $report->category_id && $employee->id == $report->employee_id)
                                @if($report->type == 'range')
                                    @php
                                        $value = $report->value;
                                    @endphp
                                @endif
                            @endif
                        @endforeach
                        {{$value.','}}
                    @endforeach
                @endif
                @endforeach

            ]

        }]
    });
    Highcharts.chart('chart-saturasi', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Saturasi Oksigen'
        },
        subtitle: {
            text: '{{date('d/m/Y')}}'
        },
        xAxis: {
            categories: [
                @foreach($employees as $employee)
                {!!"'".addslashes($employee->name)."'".","!!}
                @endforeach
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '%'
            }
        },
        legend: {
            enabled:false,
        },
        credits: false,
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        colors: ['#3c8dbc'],
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            data: [
                @foreach ($categories as $category)
                @if($category->name == 'Saturasi Oksigen' &&$category->parameter == 'employee')
                    
                    @foreach ($employees as $employee)
                        @php
                            $value = 0;
                        @endphp
                        @foreach($reports as $report)
                            @if($category->id == $report->category_id && $employee->id == $report->employee_id)
                                @if($report->type == 'range')
                                    @php
                                        $value = $report->value;
                                    @endphp
                                @endif
                            @endif
                        @endforeach
                        {{$value.','}}
                    @endforeach
                @endif
                @endforeach

            ]

        }]
    });
    @endif
    chart();
    $(function() {
        $(".knob").knob({
            readOnly:true
        });
    });
</script>
@endpush