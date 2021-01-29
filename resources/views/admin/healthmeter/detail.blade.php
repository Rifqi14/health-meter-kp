@extends('admin.layouts.app')

@section('title', 'Detail Kategori Resiko')
@push('breadcrump')
<li><a href="{{route('healthmeter.index')}}">Kategori Resiko</a></li>
<li class="active">Detail</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Kategori Resiko</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('healthmeter.update',['id'=>$healthmeter->id])}}" class="form-horizontal"
          method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">

          <div class="form-group">
            <label for="site_id" class="col-sm-2 control-label">Distrik</label>
            <div class="col-sm-6">
              <p class="form-control-static">{{ $healthmeter->site->name }}</p>
            </div>
          </div>

          <div class="form-group">
            <label for="workforce_group_id" class="col-sm-2 control-label">Kelompok Workforce</label>
            <div class="col-sm-6">
              <p class="form-control-static">{{ $healthmeter->workforcegroup->name }}</p>
            </div>
          </div>
          <div class="form-group">
            <label for="min" class="col-sm-2 control-label">Min</label>
            <div class="col-sm-6">
              <p class="form-control-static">{{ $healthmeter->min }}</p>
            </div>
          </div>
          <div class="form-group">
            <label for="max" class="col-sm-2 control-label">Max</label>
            <div class="col-sm-6">
              <p class="form-control-static">{{ $healthmeter->max }}</p>
            </div>
          </div>
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Kategori</label>
            <div class="col-sm-6">
              <p class="form-control-static">{{ $healthmeter->name }}</p>
            </div>
          </div>
          <div class="form-group">
            <label for="color" class="col-sm-2 control-label">Warna</label>
            <div class="col-sm-6">
              <p class="form-control-static">{{ $healthmeter->color }}</p>
            </div>
          </div>
          <div class="form-group">
            <label for="recomendation" class="col-sm-2 control-label">Tindak lanjut</label>
            <div class="col-sm-6">
              <p class="form-control-static">{{ $healthmeter->recomendation }}</p>
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