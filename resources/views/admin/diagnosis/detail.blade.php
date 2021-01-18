@extends('admin.layouts.app')

@section('title', 'Detail Diagnosa')
@push('breadcrump')
<li><a href="{{route('diagnosis.index')}}">Diagnosa</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Diagnosa</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('diagnosis.update',['id'=>$diagnosis->id])}}" class="form-horizontal"
          method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="box-body">

            <div class="form-group">
              <label for="category" class="col-sm-2 control-label">Kategori <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$diagnosis->category->name}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="code" class="col-sm-2 control-label">Kode <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$diagnosis->code}}</p>
              </div>
            </div>

            <div class="form-group">
              <label for="sub_category" class="col-sm-2 control-label">Sub Kategori</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$diagnosis->sub_category}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$diagnosis->name}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="english_name" class="col-sm-2 control-label">English Name <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$diagnosis->english_name}}</p>
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
