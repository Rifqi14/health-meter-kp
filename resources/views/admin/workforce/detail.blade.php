@extends('admin.layouts.app')

@section('title', 'Detail Workforce')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<style type="text/css">
  #map {
    height: 370px;
    border: 1px solid #CCCCCC;
  }

  .overlay-wrapper {
    position: relative;
  }
</style>
@endsection
@push('breadcrump')
<li><a href="{{route('workforce.index')}}">Workforce</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-5">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Workforce</h3>
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="box-body box-profile">
        <table class="table">
          <tr>
            <td><strong>Distrik</strong></td>
            <td class="text-right" id="unit">{{@$workforce->site->name}}</td>
          </tr>
          <tr>
            <td><strong>NID</strong></td>
            <td class="text-right">{{$workforce->nid}}</td>
          </tr>
          <tr>
            <td width="100"><strong>Nama</strong></td>
            <td width="150" class="text-right">{{$workforce->name}}</td>
          </tr>
          <tr>
            <td><strong>Kelompok Workforce</strong></td>
            <td class="text-right">{{@$workforce->workforcegroup->name}}</td>
          </tr>
          <tr>
            <td><strong>Instansi</strong></td>
            <td class="text-right">{{@$workforce->agency->name}}</td>
          </tr>
          <tr>
            <td><strong>Tanggal Mulai</strong></td>
            <td class="text-right">{{\Carbon\Carbon::parse($workforce->start_date)->format('d F Y')}}</td>
          </tr>
          <tr>
            <td><strong>Tanggal Akhir</strong></td>
            <td class="text-right">{{\Carbon\Carbon::parse($workforce->finish_date)->format('d F Y')}}</td>
          </tr>
          <tr>
            <td width="25%"><strong>Jabatan</strong></td>
            <td width="25%" class="text-right">
              {{$workforce->title?$workforce->title->name:'Tidak Ada'}}</td>
          </tr>
          <tr>
            <td width="25%"><strong>Bidang</strong></td>
            <td width="25%" class="text-right">
              {{@$workforce->department->name}}</td>
          </tr>
          <tr>
            <td width="25%"><strong>Sub Bidang</strong></td>
            <td width="25%" class="text-right">
              {{@$workforce->subdepartment->name}}</td>
          </tr>
          <tr>
            <td width="25%"><strong>Jabatan Penanggung Jawab</strong></td>
            <td width="25%" class="text-right">
              {{$workforce->guarantor?$workforce->guarantor->title->name:'Tidak Ada'}}</td>
          </tr>
          <tr>
            <td width="25%"><strong>Email</strong></td>
            <td width="25%" class="text-right">
              {{@$workforce->user->email}}</td>
          </tr>
        </table>

      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="nav-tabs-custom tab-primary">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#employeefamily" data-toggle="tab">Pasien</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="employeefamily">
          <div class="overlay-wrapper">
            <table class="table table-bordered table-striped" id="table-family">
              <thead>
                <tr>
                  <th style="text-align:center" width="10">#</th>
                  <th width="100">Status</th>
                  <th width="250">Nama</th>
                  <th width="100">Tgl Lahir</th>
                  <th width="200">Hak Rawat Inap</th>
                </tr>
              </thead>
            </table>
            <div class="overlay hidden">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="add-detail" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Tambah Tangguan</h4>
      </div>
      <div class="modal-body">
        <form id="form" method="post" action="{{route('employeefamily.store')}}" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="employee_id" value="{{$workforce->id}}" />
          <input type="hidden" name="_method" />
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="type">Tipe</label>
                <select name="type" class="form-control select2" placeholder="Pilih Tipe Tanggungan" required>
                  <option value=""></option>
                  <option value="couple">Pasangan</option>
                  <option value="child">Anak</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="name">Nama <b class="text-danger">*</b></label>
                <input type="text" name="name" class="form-control" placeholder="Nama">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="birth_date">Tgl Lahir <b class="text-danger">*</b></label>
                <input type="text" name="birth_date" class="form-control" placeholder="Tgl Lahir">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button form="form" type="submit" class="btn btn-primary btn-sm" title="Simpan"><i class="fa fa-save"></i></button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
  var map, geocoder, marker, infowindow;
  $(document).ready(function () {
    $('.select2').select2();
    dataTableFamily = $('#table-family').DataTable( {
          stateSave:true,
          processing: true,
          serverSide: true,
          filter:false,
          info:false,
          lengthChange:false,
          responsive: true,
          order: [[0, "asc" ]],
          ajax: {
              url: "{{url('admin/patient/read')}}",
              type: "GET",
              data:function(data){
                  data.workforce_id = {{$workforce->id}};
              }
          },
          columnDefs:[
              {
                  orderable: false,targets:[0]
              },
              { className: "text-right", targets: [0] },
              { className: "text-center", targets: [3] },
              { render: function ( data, type, row ) {
                  return `${row.inpatient_id ? row.inpatient.name : ''}`
              },targets: [4]
              }
          ],
          columns: [
              { data: "no" },
              { data: "status" },
              { data: "name" },
              { data: "birth_date" },
              { data: "inpatient_id" },
          ]
    });
  });
</script>
@endpush