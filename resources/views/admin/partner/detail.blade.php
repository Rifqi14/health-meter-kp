@extends('admin.layouts.app')

@section('title', 'Detail Faskes')
@push('breadcrump')
<li><a href="{{route('partner.index')}}">Faskes</a></li>
<li class="active">Detail</li>
@endpush
@section('stylesheets')
<style type="text/css">
    #map {
        height: 300px;
        border: 1px solid #CCCCCC;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Detail Faskes</h3>
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                </div>
                <!-- /. tools -->
            </div>
            <div class="box-body">
                <form id="form" action="{{route('partner.update',['id'=>$partner->id])}}" class="form-horizontal"
                    method="post" autocomplete="off">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="put">
                    <div class="box-body">
                        <div class="well well-sm">
                            <div class="form-group">
                                <label for="site_id" class="col-sm-2 control-label">Distrik</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">{{$partner->site->name}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="partner_category_id" class="col-sm-2 control-label">Kategori</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">{{$partner->partnercategory->name}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Nama</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">{{$partner->name}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address" class="col-sm-2 control-label">Alamat</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">{{$partner->address}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                    for="collaboration_status">Status
                                    Kerjasama</label>
                                <div class="col-sm-4">
                                        @if ($partner->collaboration_status)
                                        <p class="form-control-static"><span class="label bg-green">Aktif</span></p>
                                        @else
                                        <p class="form-control-static"><span class="label bg-danger">Non-Aktif</span></p>
                                        @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class=" overlay hidden">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
</div>
@endsection
