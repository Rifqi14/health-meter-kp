@extends('admin.layouts.app')

@section('title', 'Kategori Resiko')
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
<li class="active">Kategori Resiko</li>
@endpush
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Kategori Resiko</h3>
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
          <div class="col-md-12">
            <div id="chart-personnel" class=" animate" style="height: 180px"></div>
          </div>
        </div>
        <!-- /.row -->
        <table class="table table-striped table-bordered datatable" style="width:100%" id="table-personnel">
          <thead>
            <tr>
              <th width="10">#</th>
              <th width="200">Nama</th>
              <th width="200">Bidang</th>
              <th width="200">Jabatan</th>
              <th width="50">Jumlah</th>
            </tr>
          </thead>
        </table>
      </div>
      <!-- ./box-body -->
    </div>
    <!-- /.box -->
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
                <label class="control-label" for="site_id">Distrik</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="site_id" class="form-control" data-placeholder="Nama Distrik">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="workforce_group_id">Kelompok Workforce</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="workforce_group_id" class="form-control" data-placeholder="Nama Kelompok Workforce">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="health_meter_id">Kategori Resiko</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="health_meter_id" class="form-control" data-placeholder="Nama Kategori Resiko">
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
        <form id="form-export" action="{{ route('reportweekly.export') }}" autocomplete="off">
          @csrf
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="site_id">Distrik</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="site_id" class="form-control" data-placeholder="Nama Distrik">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="workforce_group_id">Kelompok Workforce</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="workforce_group_id" class="form-control" data-placeholder="Nama Kelompok Workforce">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="health_meter_id">Kategori Resiko</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="health_meter_id" class="form-control" data-placeholder="Nama Kategori Resiko">
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
  function filter(){
      $('#add-filter').modal('show');
  }
  function exportfile(){
      $('#export-file').modal('show');
  }

  //chart personel
  function chartpersonnel(){
      $.ajax({
          url: "{{route('reportweekly.chartpersonnel')}}",
          method: 'GET',
          dataType: 'json',
          data:{
              date:$('#form-search input[name=date]').val(),
              site_id:$('#form-search input[name=site_id]').val(),
              health_meter_id:$('#form-search input[name=health_meter_id]').val(),
              workforce_group_id:$('#form-search input[name=workforce_group_id]').val(),
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
                  subtitle:{
                      text:response.subtitle,
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
                        text: false,
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
      $('.date-picker').datepicker({
          autoclose: true,
          format: 'yyyy-mm-dd'
      })
      $( "input[name=site_id]" ).select2({
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

      $( "input[name=workforce_group_id]" ).select2({
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
      });

      $( "input[name=health_meter_id]" ).select2({
          ajax: {
          url: "{{route('reportweekly.selectcategory')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
              return {
              name:term,
              page:page,
              limit:30,
              site_id: $("#form-search").find('input[name=site_id]').val(),
              workforce_group_id: $("#form-search").find('input[name=workforce_group_id]').val(),
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
      chartpersonnel();
      dataTablePersonnel = $('#table-personnel').DataTable( {
          stateSave:true,
          processing: true,
          serverSide: true,
          filter:false,
          info:false,
          lengthChange:true,
          responsive: true,
          order: [[ 4, "desc" ]],
          ajax: {
              url: "{{route('reportweekly.personnel')}}",
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
              { className: "text-right", targets: [0] },
              { className: "text-center", targets: [4] },
              { render:function( data, type, row ) {
                  return row.department ? row.department.name : ''
                },targets: [2] },
              { render:function( data, type, row ) {
                  return row.title ? row.title.name : ''
                },targets: [3] },
          ],
          columns: [
              { data: "no" },
              { data: "name" },
              { data: "department_id" },
              { data: "title_id" },
              { data: "total" }
          ]
      });
      $(".select2").select2();

      $('#form-search').submit(function(e){
          e.preventDefault();
          dataTablePersonnel.draw();
          chartpersonnel();
          $('#add-filter').modal('hide');
      })

      $('#form-export').submit(function(e){
          e.preventDefault();
          $.ajax({
              url: "{{ route('reportweekly.export') }}",
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