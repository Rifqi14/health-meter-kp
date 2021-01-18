@extends('admin.layouts.app')

@section('title', 'Detail Dokter')
@push('breadcrump')
<li><a href="{{route('doctor.index')}}">Dokter</a></li>
<li class="active">Detail</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Dokter</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('doctor.update', ['id' => $doctor->id])}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="well well-sm">
            <div class="form-group">
              <label for="doctor_group" class="col-sm-2 control-label">Kelompok Dokter</label>
              <div class="col-sm-6">
                @if($doctor->doctor_group == 0)
                <p class="form-control-static">Dokter Perusahaan</p>
                @else 
                <p class="form-control-static">Dokter Eksternal</p>
                @endif
              </div>
            </div>

            <div class="form-group">
              <label for="site_id" class="col-sm-2 control-label">Distrik</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$doctor->site->name}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$doctor->name}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Telepon</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$doctor->phone}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="id_partner" class="col-sm-2 control-label">Faskes</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$doctor->partner?$doctor->partner->name:'Tidak Ada'}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="id_speciality" class="col-sm-2 control-label">Spesialisasi</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$doctor->speciality->name}}</p>
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
