@extends('admin.layouts.app')

@section('title', 'Laporan Kehadiran')
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
<li class="active">Laporan Kehadiran</li>
@endpush
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="nav-tabs-custom tab-primary">
      <ul class="nav nav-tabs">
        <li><a href="#kehadiran" data-toggle="tab">Kehadiran</a></li>
        <li class="active"><a href="#self-assessment" data-toggle="tab">Self Assessment</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane hidden" id="kehadiran">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Laporan Kehadiran</h3>
                  <div class="pull-right box-tools">
                    <a href="#" onclick="exportattendance()" class="btn btn-info btn-sm" data-toggle="tooltip" title="Export">
                      <i class="fa fa-download"></i>
                    </a>
                    <a href="#" onclick="filterattendance()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
                      <i class="fa fa-search"></i>
                    </a>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-3">
                      <div>
                        <h2 class="m-b-xs animate total-attendance">0</h2>
                        <span class="no-margins">
                          Total Workforce
                        </span>
                        <div class="progress">
                          <div class="progress-bar" style="width: 100%;"></div>
                        </div>
                        <div class="row">
                          <div class="col-xs-6">
                            <small>Sudah Lapor</small>
                            <h4 class="animate last-week-attendance">0</h4>
                          </div>

                          <div class="col-xs-6">
                            <small>Belum Lapor</small>
                            <h4 class="animate today-attendance">0</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-9">
                      <div id="chart-attendance" class=" animate" style="height: 160px"></div>
                    </div>
                  </div>
                  <!-- /.row -->
                </div>
                <!-- ./box-body -->
              </div>
            </div>
          </div>
          <table class="table table-striped table-bordered datatable" style="width:100%" id="table-kehadiran">
            <thead>
              <tr>
                <th width="10">#</th>
                <th width="100">Tanggal</th>
                <th width="200">Nama</th>
                <th width="100">Distrik</th>
                <th width="100">Instansi</th>
                <th width="100">Divisi Bidang</th>
                <th width="100">Sub Divisi Bidang</th>
                <th width="100">Jabatan</th>
                <th width="200">Keterangan Kehadiran</th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="tab-pane active" id="self-assessment">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Laporan Self Assessment</h3>
                  <div class="pull-right box-tools">
                    <a href="#" onclick="exportassessment()" class="btn btn-info btn-sm" data-toggle="tooltip" title="Export">
                      <i class="fa fa-download"></i>
                    </a>
                    <a href="#" onclick="filterassessment()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
                      <i class="fa fa-search"></i>
                    </a>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-3 hidden">
                      <div>
                        <h2 class="m-b-xs animate total-assessment">0</h2>
                        <span class="no-margins">
                          Total Assessment
                        </span>
                        <div class="progress">
                          <div class="progress-bar" style="width: 100%;"></div>
                        </div>
                        <div class="row">
                          <div class="col-xs-6">
                            <small>Kategori Tinggi</small>
                            <h4 class="animate high-risk-assessment">0</h4>
                          </div>
                          <div class="col-xs-6">
                            <small>Kategori Rendah - Sedang</small>
                            <h4 class="animate low-risk-assessment">0</h4>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div id="chart-assessment" class=" animate" style="height: 160px"></div>
                    </div>
                  </div>
                  <!-- /.row -->
                </div>
                <!-- ./box-body -->
              </div>
            </div>
          </div>
          <table class="table table-striped table-bordered datatable" style="width:100%" id="table-assessment">
            <thead>
              <tr>
                <th width="10">#</th>
                <th width="50">Tanggal</th>
                <th width="150">Nama</th>
                <th width="100">Distrik</th>
                <th width="100">Instansi</th>
                <th width="100">Divisi Bidang</th>
                <th width="100">Sub Divisi Bidang</th>
                <th width="100">Jabatan</th>
                <th width="50">Total Bobot</th>
                <th width="100">Kategori Resiko</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
    <!-- /.box -->
  </div>
</div>

<div class="modal fade" id="add-filter-assessment" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
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
                      <input type="text" class="form-control date-picker" name="date" placeholder="Tanggal">
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
                <label class="control-label" for="site_id">Distrik</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="site_id" class="form-control" data-placeholder="Pilih Distrik">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="workforce_group_id">Kelompok Workforce</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="workforce_group_id" class="form-control" data-placeholder="Pilih Kelompok Workforce">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="health_meter_id">Kategori Resiko</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="health_meter_id" class="form-control" data-placeholder="Pilih Kategori Resiko">
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
{{-- Modal Export --}}
<div class="modal fade" id="export-assessment" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
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
                      <input type="text" class="form-control date-picker" name="date" placeholder="Tanggal">
                      <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </span>
                    </div>
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
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/moment/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-daterangepicker/js/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">
  function filterassessment(){
      $('#add-filter-assessment').modal('show');
  }
  function exportassessment(){
      $('#export-assessment').modal('show');
  }

  //chart personel
  function chartassessment(){
      $.ajax({
          url: "{{route('reportattendance.chartassessment')}}",
          method: 'GET',
          dataType: 'json',
          data:{
              date:$('#form-search input[name=date]').val(),
              site_id:$('#form-search input[name=site_id]').val(),
              health_meter_id:$('#form-search input[name=health_meter_id]').val(),
              workforce_group_id:$('#form-search input[name=workforce_group_id]').val()
          },
          beforeSend:function() {
              $('#chart-assessment').removeClass('no-after');
          },
          success:function(response) {
              $('#chart-assessment').addClass('no-after');
              Highcharts.chart('chart-assessment', {
                  chart: {
                      type: 'column'
                  },
                  title: {
                      text: response.title
                  },
                  subtitle: {
                      text: response.subtitle
                  },
                  xAxis: {
                      categories: response.categories,
                      crosshair: true
                  },
                  yAxis: {
                      min: 0,
                      title: {
                          text: 'Health Meter'
                      }
                  },
                  colors: response.colors,
                  tooltip: {
                      headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                      pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                          '<td style="padding:0"><b>{point.y:.1f} kali</b></td></tr>',
                      footerFormat: '</table>',
                      shared: true,
                      useHTML: true
                  },
                  plotOptions: {
                      column: {
                          pointPadding: 0.2,
                          borderWidth: 0
                      }
                  },
                  series: response.series
              });
          }
      });
  }
  $(function(){
      //date
      $('.date-picker').daterangepicker({
        startDate: moment().startOf('month').format('DD/MM/YYYY'),
      });
      $("input[name=site_id]").select2({
          multiple: true,
          ajax: {
            url: "{{route('site.select')}}",
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
      $("input[name=workforce_group_id]").select2({
          ajax: {
          url: "{{route('workforcegroup.select')}}",
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
          multiple: true
      });
      $("input[name=health_meter_id]").select2({
        ajax: {
          url: "{{route('healthmeter.select')}}",
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
      chartassessment();
      dataTablePersonnel = $('#table-assessment').DataTable( {
          stateSave:true,
          processing: true,
          serverSide: true,
          filter:false,
          info:false,
          lengthChange:true,
          responsive: true,
          order: [[ 1, "desc" ]],
          ajax: {
              url: "{{route('reportattendance.assessment')}}",
              type: "GET",
              data:function(data){
                  data.date = $('#form-search input[name=date]').val();
                  data.site_id = $('#form-search input[name=site_id]').val();
                  data.health_meter_id = $('#form-search input[name=health_meter_id]').val();
                  data.workforce_group_id = $('#form-search input[name=workforce_group_id]').val();
              }
          },
          columnDefs:[
              {
                  orderable: false,targets:[0]
              },
              { className: "text-right", targets: [0,8] },
              { className: "text-center", targets: [9] },
              { render: function ( type, data, row ) {
                return row.workforce_id ? `${row.workforce.name}<br><small>${row.workforce.nid}</small>` : ''
              },targets:[2] },
              { render: function ( type, data, row ) {
                return row.workforce_id ? row.workforce.site ? row.workforce.site.name : '' : ''
              },targets:[3] },
              { render: function ( type, data, row ) {
                return row.workforce_id ? row.workforce.agency ? row.workforce.agency.name : '' : ''
              },targets:[4] },
              { render: function ( type, data, row ) {
                return row.workforce_id ? row.workforce.department ? row.workforce.department.name : '' : ''
              },targets:[5] },
              { render: function ( type, data, row ) {
                return row.workforce_id ? row.workforce.subdepartment ? row.workforce.subdepartment.name : '' : ''
              },targets:[6] },
              { render: function ( type, data, row ) {
                return row.workforce_id ? row.workforce.title ? row.workforce.title.name : '' : ''
              },targets:[7] },
              { render: function ( type, data, row ) {
                return row.health_meter_id ? `<span class="label" style="background-color: ${row.category.color}">${row.category.name}</span>` : ''
              },targets:[9] },
          ],
          columns: [
              { data: "no" },
              { data: "date" },
              { data: "workforce_id" },
              { data: "site_id" },
              { data: "agency_id" },
              { data: "department_id" },
              { data: "sub_department_id" },
              { data: "title_id" },
              { data: "value_total" },
              { data: "health_meter_id" },
          ]
      });
      $(".select2").select2();

      $('#form-search').submit(function(e){
          e.preventDefault();
          dataTablePersonnel.draw();
          chartassessment();
          $('#add-filter-assessment').modal('hide');
      })

      $('#form-export').submit(function(e){
          e.preventDefault();
          $.ajax({
              url: "{{ route('reportattendance.export') }}",
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