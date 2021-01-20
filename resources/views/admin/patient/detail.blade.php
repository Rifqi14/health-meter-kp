@extends('admin.layouts.app')

@section('title', 'Detail Pasien')
@push('breadcrump')
<li><a href="{{route('patient.index')}}">Pasien</a></li>
<li class="active">Detail</li>
@endpush
@section('stylesheet')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Pasien</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
         <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('patient.update', ['id' => $patient->id])}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          @method('PUT')
          <div class="box-body">
            <div class="form-group">
              <label for="site_id" class="col-sm-2 control-label">Distrik </label>
              <div class="col-sm-6">
                <p class="form-control-static">{{ $patient->site->name }}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="workforce_id" class="col-sm-2 control-label">Workforce </label>
              <div class="col-sm-6">
                <p class="form-control-static">{{ $patient->workforce->name }}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama </label>
              <div class="col-sm-6">
                <p class="form-control-static">{{ $patient->name }}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="status" class="col-sm-2 control-label">Status </label>
              <div class="col-sm-6">
                <p class="form-control-static">{{ $patient->status }}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="birth_date" class="col-sm-2 control-label">Tanggal Lahir </label>
              <div class="col-sm-6">
                <p class="form-control-static">{{ $patient->birth_date }}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="inpatient_id" class="col-sm-2 control-label">Tarif </label>
              <div class="col-sm-6">
                <p class="form-control-static">{{ $patient->inpatient->name }}</p>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
@endsection
