@extends('admin.layouts.app')

@section('title', 'Detail Pejabat Berwenang')
@push('breadcrump')
<li><a href="{{route('authorizedofficial.index')}}">Pejabat Berwenang</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Pejabat Berwenang</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('authorizedofficial.update', ['id' => $authority->id])}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          @method('PUT')
          <div class="box-body">
            <div class="form-group">
              <label for="site_id" class="col-sm-2 control-label">Distrik <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$authority->site->name}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="title_id" class="col-sm-2 control-label">Jabatan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$authority->title->name.' '.$authority->title->code}}</p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" style="padding-top: 1px" for="authority_type">Jenis Kewenangan</label>
              <div class="col-sm-6">
                <span class="label {{$authority->type == 1 ? 'bg-green' : 'bg-red'}}">{{$authority->type == 1 ? 'Approval Sistem' : 'Tanda Tangan Basah'}}</span>
              </div>
            </div>
            <div class="form-group">
              <label for="level" class="col-sm-2 control-label">Level <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$authority->level}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="authority" class="col-sm-2 control-label">Jenis Kewenangan <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$authority->authority}}</p>
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