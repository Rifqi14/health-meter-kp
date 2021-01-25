@extends('admin.layouts.app')

@section('title', 'Detail Distrik')
@push('breadcrump')
<li><a href="{{route('site.index')}}">Distrik</a></li>
<li class="active">Detail</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Distrik</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('site.update',['id'=>$site->id])}}" method="post" class="form-horizontal" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="form-group">
            <label for="code" class="col-sm-2 control-label">Kode</label>
            <div class="col-sm-6">
              <p class="form-control-static">{{ $site->code }}</p>
            </div>
          </div>
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Nama</label>
            <div class="col-sm-6">
              <p class="form-control-static">{{ $site->name }}</p>
            </div>
          </div>
          <div class="form-group">
            <label for="logo" class="col-sm-2 control-label">Logo </label>
            <div class="col-sm-6">
              @if (is_file($site->logo))
              <img src="{{asset($site->logo)}}" class="image-responsive">
              @else
              <p class="form-control-static">No Image</p>
              @endif
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
