@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('breadcrump')
<ol class="breadcrumb">
    <li><a href="#">Home</a></li>
    <li class="active">Dashboard</li>
</ol>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        @if(!$assessment)
        <div class="alert alert-warning">
          <h4><i class="fa fa-warning"></i> Anda Belum Mengisi Assessment Hari Ini</h4>
          <p>Silahkan Klik Link <a href="{{route('assessment.create')}}">Disini</a> Untuk Mengisi Assessment
          </p>
        </div>
        @endif
    </div>
</div>
@endsection