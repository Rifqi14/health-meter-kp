@extends('admin.layouts.app')

@section('title', 'Laporan Surat')
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
<li class="active">Laporan Surat</li>
@endpush
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="nav-tabs-custom tab-primary">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#surat-pengantar" data-toggle="tab">Surat Pengantar</a></li>
        <li><a href="#jaminan-kesehatan" data-toggle="tab">Jaminan Kesehatan</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="surat-pengantar">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <!-- Box header -->
                <div class="box-header">
                  <h3 class="box-title">Laporan Surat Pengantar</h3>
                  <!-- Tools box -->
                  <div class="pull-right box-tools">
                    @if (in_array('export', $actionmenu))
                    <a href="#" onclick="exportcoverletter()" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Export">
                      <i class="fa fa-download"></i>
                    </a>
                    @endif
                    <a href="#" onclick="filtercoverletter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
                      <i class="fa fa-search"></i>
                    </a>
                  </div>
                  <!-- /. tools -->
                </div>
                <!-- /. box header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div id="chart-cover-letter" class=" animate" style="height: 160px"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <table class="table table-striped table-bordered datatable" style="width: 100%" id="table-surat-pengantar">
            <thead>
              <tr>
                <th width="10">#</th>
                <th width="10">Jenis Surat</th>
                <th width="10">Distrik</th>
                <th width="10">Tanggal</th>
                <th width="10">Workforce</th>
                <th width="10">Pasien</th>
                <th width="10">Distrik Pasien</th>
                <th width="10">Status Proses</th>
                <th width="10">Link Document</th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="tab-pane" id="jaminan-kesehatan">
          <div class="row">
            <div class="col-md-12">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Laporan Jaminan Kesehatan</h3>
                  <div class="pull-right box-tools">
                    @if (in_array('export', $actionmenu))
                    <a href="#" onclick="exporthealthinsurance()" class="btn btn-primary btn-sm">
                      <i class="fa fa-download"></i>
                    </a>
                    @endif
                    <a href="#" onclick="filterhealthinsurance()" class="btn btn-default btn-sm">
                      <i class="fa fa-search"></i>
                    </a>
                  </div>
                </div>
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div id="chart-jaminan-kesehatan" class=" animate" style="height: 160px"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <table class="table table-striped table-bordered datatable" style="width: 100%" id="table-jaminan-kesehatan">
            <thead>
              <tr>
                <th width="10">#</th>
                <th width="10">Jenis Surat</th>
                <th width="10">Distrik</th>
                <th width="10">Tanggal</th>
                <th width="10">Workforce</th>
                <th width="10">Pasien</th>
                <th width="10">Distrik Pasien</th>
                <th width="10">Status Proses</th>
                <th width="10">Link Document</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="add-filter-surat-pengantar" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Pencarian</h4>
      </div>
      <div class="modal-body">
        <form id="form-search-surat-pengantar" autocomplete="off">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="site_id" class="control-label">Distrik Pembuat</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="site_id" class="form-control" data-placeholder="Pilih Distrik">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="cover_letter_type" class="control-label">Jenis Surat</label>
                <div class="row">
                  <div class="col-md-12">
                    <select name="cover_letter_type" class="form-control select2" data-placeholder="Pilih Jenis Surat" multiple>
                      <option value=""></option>
                      @foreach (config('enums.coveringletter_type') as $key => $item)
                      <option value="{{ $item }}">{{ $item }}</option>
                      @endforeach
                    </select>
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
        <button form="form-search-surat-pengantar" type="submit" class="btn btn-default" title="Apply"><i class="fa fa-search"></i></button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="export-surat-pengantar" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Export</h4>
      </div>
      <div class="modal-body">
        <form id="form-export-surat-pengantar" action="{{ route('reportletter.exportletter') }}" autocomplete="off">
          @csrf
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="site_id" class="control-label">Distrik Pembuat</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="site_id" class="form-control" data-placeholder="Pilih Distrik">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="cover_letter_type" class="control-label">Jenis Surat</label>
                <div class="row">
                  <div class="col-md-12">
                    <select name="cover_letter_type" class="form-control select2" data-placeholder="Pilih Jenis Surat" multiple>
                      @foreach (config('enums.coveringletter_type') as $key => $item)
                      <option value="{{ $key }}">{{ $item }}</option>
                      @endforeach
                    </select>
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
        <button form="form-export-surat-pengantar" type="submit" class="btn btn-primary" title="Apply"><i class="fa fa-download"></i></button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="add-filter-surat-jaminan" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Pencarian</h4>
      </div>
      <div class="modal-body">
        <form id="form-search-surat-jaminan" autocomplete="off">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="site_id" class="control-label">Distrik Pembuat</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="site_id" class="form-control" data-placeholder="Pilih Distrik">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="cover_letter_type" class="control-label">Jenis Surat</label>
                <div class="row">
                  <div class="col-md-12">
                    <select name="cover_letter_type" class="form-control select2" data-placeholder="Pilih Jenis Surat" multiple>
                      @foreach (config('enums.authority') as $key => $item)
                      <option value="{{ $key }}">{{ $item }}</option>
                      @endforeach
                    </select>
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
        <button form="form-search-surat-jaminan" type="submit" class="btn btn-default" title="Apply"><i class="fa fa-search"></i></button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="export-surat-jaminan" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Export</h4>
      </div>
      <div class="modal-body">
        <form id="form-export-surat-jaminan" action="{{ route('reportletter.exportletter') }}" autocomplete="off">
          @csrf
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="site_id" class="control-label">Distrik Pembuat</label>
                <div class="row">
                  <div class="col-md-12">
                    <input type="text" name="site_id" class="form-control" data-placeholder="Pilih Distrik">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="cover_letter_type" class="control-label">Jenis Surat</label>
                <div class="row">
                  <div class="col-md-12">
                    <select name="cover_letter_type" class="form-control select2" data-placeholder="Pilih Jenis Surat" multiple>
                      @foreach (config('enums.authority') as $key => $item)
                      <option value="{{ $key }}">{{ $item }}</option>
                      @endforeach
                    </select>
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
        <button form="form-export-surat-jaminan" type="submit" class="btn btn-primary" title="Apply"><i class="fa fa-download"></i></button>
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
  function filtercoverletter() {
    $("#add-filter-surat-pengantar").modal("show");
  }
  function exportcoverletter() {
    $("#export-surat-pengantar").modal("show");
  }

  function filterhealthinsurance() {
    $("#add-filter-surat-jaminan").modal("show");
  }
  function exporthealthinsurance() {
    $("#export-surat-jaminan").modal("show");
  }

  // Chart Surat Pengantar
  function chartCoverLetter() {
    
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
      multiple: true,
    });
    dataTableCoverLetter = $('#table-surat-pengantar').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 3, "desc" ]],
        ajax: {
            url: "{{route('reportletter.readcoverletter')}}",
            type: "GET",
            data:function(data){
                data.date = $('#form-search-surat-pengantar input[name=date]').val();
                data.site_id = $('#form-search-surat-pengantar input[name=site_id]').val();
                data.cover_letter_type = $('#form-search-surat-pengantar select[name=cover_letter_type]').val();
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [0] },
            { render: function ( type, data, row ) {
              return row.updated_by ? `<span class="label label-primary">${row.updatedby.workforce.site.name}</span>` : ''
            },targets:[2] },
            { render: function ( type, data, row ) {
              return row.workforce_id ? `<span class="text-blue text-bold">${row.workforce.name}</span><br><small><i>${row.workforce.nid}</i></small>` : ''
            },targets:[4] },
            { render: function ( type, data, row ) {
              return row.patient_id ? `<span class="text-blue text-bold">${row.patient.name}</span><br><small><i>${row.patient.status}</i></small>` : ''
            },targets:[5] },
            { render: function ( type, data, row ) {
              return row.patient_site_id ? `<span class="label label-primary">${row.patientsite.name}</span>` : ''
            },targets:[6] },
            { render: function ( type, data, row ) {
              return row.status ? `<span class="label label-default">${row.status}</span>` : ''
            },targets:[7] },
        ],
        columns: [
            { data: "no" },
            { data: "type" },
            { data: "updated_by" },
            { data: "letter_date" },
            { data: "workforce_id" },
            { data: "patient_id" },
            { data: "patient_site_id" },
            { data: "status" },
            { data: "document_link" },
        ]
    });
    dataTableHealthInsurance = $('#table-jaminan-kesehatan').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 3, "desc" ]],
        ajax: {
            url: "{{route('reportletter.readhealthinsurance')}}",
            type: "GET",
            data:function(data){
                data.date = $('#form-search-surat-jaminan input[name=date]').val();
                data.site_id = $('#form-search-surat-jaminan input[name=site_id]').val();
                data.cover_letter_type = $('#form-search-surat-jaminan select[name=cover_letter_type]').val();
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [0] },
            { render: function ( type, data, row ) {
              return row.lettermakersite ? `<span class="label label-primary">${row.lettermakersite.name}</span>` : ''
            },targets:[2] },
            { render: function ( type, data, row ) {
              return row.workforce_id ? `<span class="text-blue text-bold">${row.workforce.name}</span><br><small><i>${row.workforce.nid}</i></small>` : ''
            },targets:[4] },
            { render: function ( type, data, row ) {
              return row.patient_id ? `<span class="text-blue text-bold">${row.patient.name}</span><br><small><i>${row.patient.status}</i></small>` : ''
            },targets:[5] },
            { render: function ( type, data, row ) {
              return row.patient_site_id ? `<span class="label label-primary">${row.patientsite.name}</span>` : ''
            },targets:[6] },
            { render: function ( type, data, row ) {
              return row.status ? `<span class="label label-default">${row.status}</span>` : ''
            },targets:[7] },
        ],
        columns: [
            { data: "no" },
            { data: "cover_letter_type" },
            { data: "letter_maker_id" },
            { data: "date" },
            { data: "workforce_id" },
            { data: "patient_id" },
            { data: "patient_site_id" },
            { data: "status" },
            { data: "document_link" },
        ]
    });
    $(".select2").select2({
      allowClear: true,
    });

    $("#form-search-surat-pengantar").submit(function(e) {
      e.preventDefault();
      dataTableCoverLetter.draw();
      chartCoverLetter();
      $("#add-filter-surat-pengantar").modal("hide");
    });
    $("#form-search-surat-jaminan").submit(function(e) {
      e.preventDefault();
      dataTableHealthInsurance.draw();
      chartCoverLetter();
      $("#add-filter-surat-jaminan").modal("hide");
    });
    $('#form-export-surat-pengantar').submit(function(e){
      e.preventDefault();
      $.ajax({
          url: "{{ route('reportletter.exportletter') }}",
          type: 'POST',
          dataType: 'JSON',
          data: $("#form-export-surat-pengantar").serialize(),
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
    $('#form-export-surat-jaminan').submit(function(e){
      e.preventDefault();
      $.ajax({
          url: "{{ route('reportletter.exportinsurance') }}",
          type: 'POST',
          dataType: 'JSON',
          data: $("#form-export-surat-jaminan").serialize(),
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