@extends('admin.layouts.app')

@section('title', 'Detail Jenis Pemeriksaan')
@push('breadcrump')
<li><a href="{{route('examination.index')}}">Jenis Pemeriksaan</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Jenis Pemeriksaan</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('examinationtype.update',['id'=>$examinationtype->id])}}" class="form-horizontal"
          method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="box-body">
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Pemeriksaan</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$examinationtype->examination->name}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Deskripsi</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$examinationtype->name}}</p>
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
