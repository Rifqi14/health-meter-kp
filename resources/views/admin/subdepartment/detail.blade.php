@extends('admin.layouts.app')

@section('title', 'Ubah Sub Bidang')
@push('breadcrump')
<li><a href="{{route('subdepartment.index')}}">Sub Bidang</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Sub Bidang</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('subdepartment.update', ['id' => $subdepartment->id])}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          @method('PUT')
          <div class="box-body">
            <div class="form-group">
              <label for="site_id" class="col-sm-2 control-label">Distrik <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$subdepartment->department->site->name}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="department_id" class="col-sm-2 control-label">Bidang <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$subdepartment->department->name}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="code" class="col-sm-2 control-label">Kode <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$subdepartment->code}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$subdepartment->name}}</p>
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
