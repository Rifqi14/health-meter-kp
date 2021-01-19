@extends('admin.layouts.app')

@section('title', 'Detail Obat')
@push('breadcrump')
<li><a href="{{route('medicine.index')}}">Obat</a></li>
<li class="active">Detail Ubah</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Obat</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('medicine.update',['id'=>$medicine->id])}}" class="form-horizontal"
          method="post" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="put">
          <div class="box-body">
            <div class="form-group">
              <label for="code" class="col-sm-2 control-label">Kode</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$medicine->code}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">Nama</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$medicine->name}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="medicine_category" class="col-sm-2 control-label">Kategori Obat</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$medicine->medicine_category->description}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="medicine_group" class="col-sm-2 control-label">Kelompok Obat</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$medicine->medicine_group->description}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="medicine_type" class="col-sm-2 control-label">Jenis Obat</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$medicine->medicine_type->description}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="level" class="col-sm-2 control-label">Kadar</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$medicine->level}}</p>
              </div>
            </div>

            <div class="form-group">
              <label for="medicine_unit" class="col-sm-2 control-label">Satuan Obat</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$medicine->medicine_unit->description}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="price" class="col-sm-2 control-label">Harga</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{number_format($medicine->price,0,',','.')}}</p>
              </div>
            </div>
            <div class="form-group">
              <label for="description" class="col-sm-2 control-label">Deskripsi</label>
              <div class="col-sm-6">
                <p class="form-control-static">{{$medicine->description}}</p>
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