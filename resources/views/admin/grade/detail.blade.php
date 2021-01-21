@extends('admin.layouts.app')

@section('title', 'Detail Jenjang Jabatan')
@push('breadcrump')
<li><a href="{{route('title.index')}}">Jenjang Jabatan</a></li>
<li class="active">Ubah</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Jenjang Jabatan</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
         <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('grade.update',['id'=>$grade->id])}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="box-body">
            <div class="form-group">
              <label for="code" class="col-sm-2 control-label">Kode</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$grade->code}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$grade->name}}</p>
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
