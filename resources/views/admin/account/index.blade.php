@extends('admin.layouts.app')

@section('title', 'Akun')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="active">Akun</li>
@endpush
@section('content')
<div class="row">
    <div class="col-md-3">
    </div>
    <div class="col-md-9">
    </div>
</div>
@endsection

@push('scripts')
@endpush
