@extends('admin.layouts.app')

@section('title', 'Laporan Diagnosa')
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
<li class="active">Laporan Diagnosa</li>
@endpush
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title"></h3>
        <div class="pull-right box-tools">
          @if (in_array('export', $actionmenu))
          <a href="#" onclick="exportDiagnose()" class="btn btn-primary btn-sm"><i class="fa fa-download"></i></a>
          @endif
          <a href="#" onclick="filterDiagnose()" class="btn btn-default btn-sm"><i class="fa fa-search"></i></a>
        </div>
      </div>
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <div class="animate" id="chart-diagnose" style="height: 160px"></div>
          </div>
          <div class="col-md-12">
            <table class="table table-striped table-bordered datatable" id="table-diagnose" style="width: 100%">
              <thead>
                <tr>
                  <th width="10">#</th>
                  <th width="10">Tanggal</th>
                  <th width="10">Workforce</th>
                  <th width="10">Pasien</th>
                  <th width="10">Distrik Pasien</th>
                  <th width="10">Jenis Pemeriksaan</th>
                  <th width="10">Hasil Pemeriksaan</th>
                  <th width="10">Batas Normal</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="add-filter-diagnose" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Pencarian</h4>
      </div>
      <div class="modal-body">
        <form autocomplete="off" id="form-search">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="site_id" class="control-label">Distrik Pasien</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="site_id" class="form-control" data-placeholder="Pilih Distrik">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="examination_type_id" class="control-label">Tipe Pemeriksaan</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="examination_type_id" class="form-control" data-placeholder="Pilih Tipe Pemeriksaan">
                  </div>
                </div>
              </div>
            </div>
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
        <button class="btn btn-default" form="form-search" type="submit" title="Apply"><i class="fa fa-search"></i></button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="export-diagnose" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Export</h4>
      </div>
      <div class="modal-body">
        <form autocomplete="off" action="{{ route('reportdiagnose.exportdiagnose') }}" id="form-export">
          @csrf
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="site_id" class="control-label">Distrik Pasien</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="site_id" class="form-control" data-placeholder="Pilih Distrik">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="examination_type_id" class="control-label">Tipe Pemeriksaan</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="examination_type_id" class="form-control" data-placeholder="Pilih Tipe Pemeriksaan">
                  </div>
                </div>
              </div>
            </div>
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
        <button class="btn btn-primary" form="form-export" type="submit" title="Apply"><i class="fa fa-download"></i></button>
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
  function filterDiagnose() {
    $("#add-filter-diagnose").modal("show");
  }
  function exportDiagnose() {
    $("#export-diagnose").modal("show");
  }

  $(function() {
    $('.date-picker').daterangepicker();
    $("input[name=site_id]").select2({
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
    $("input[name=examination_type_id]").select2({
      ajax: {
        url: "{{route('examinationtype.select')}}",
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
    $(".select2").select2({
      allowClear: true,
    });
    dataTable = $('#table-diagnose').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 1, "desc" ]],
        ajax: {
            url: "{{route('reportdiagnose.readdiagnose')}}",
            type: "GET",
            data:function(data){
                data.date = $('#form-search input[name=date]').val();
                data.site_id = $('#form-search input[name=site_id]').val();
                data.examination_type_id = $('#form-search input[name=examination_type_id]').val();
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [0] },
            { render: function ( type, data, row ) {
              return row.updated_by ? `<span class="text-blue text-bold">${row.workforce.name}</span><br><small><i>${row.workforce.nid}</i></small>` : ''
            },targets:[2] },
            { render: function ( type, data, row ) {
              return row.patient_id ? `<span class="text-blue text-bold">${row.patient.name}</span><br><small><i>${row.patient.status}</i></small>` : ''
            },targets:[3] },
            { render: function ( type, data, row ) {
              return row.patient_site_id ? `<span class="label label-primary">${row.patientsite.name}</span>` : ''
            },targets:[4] },
            { render: function ( type, data, row ) {
              return row.examination_type_id ? `<span class="label label-primary">${row.examinationtype.name}</span>` : ''
            },targets:[5] },
        ],
        columns: [
            { data: "no" },
            { data: "date" },
            { data: "workforce_id" },
            { data: "patient_id" },
            { data: "patient_site_id" },
            { data: "examination_type_id" },
            { data: "result" },
            { data: "normal_limit" },
        ]
    });
    $("#form-search").submit(function(e) {
      e.preventDefault();
      dataTable.draw();
      $("#add-filter-diagnose").modal("hide");
    });
    $('#form-export').submit(function(e){
      e.preventDefault();
      $.ajax({
          url: "{{ route('reportdiagnose.exportdiagnose') }}",
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
    });
  });
</script>
@endpush