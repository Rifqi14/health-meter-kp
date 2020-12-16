@extends('admin.layouts.app')

@section('title', 'Guide List')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style>
    li a.guide{
        color: black;
        margin-left: 10px;
    }

    /* li a.guide:hover {
        color : blue;
    } */

    li:hover {
        cursor: pointer;
    }
</style>
@endsection
@push('breadcrump')
    <li class="active">Guide List</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Daftar Guide</h3>
          <!-- tools box -->
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <ul class="list-group">
            @foreach ($guides as $guide)
                <li class="list-group-item "><i class="fa fa-circle-o"></i>
                    <a href="{{ asset($guide->file) }}" target="_blank" class="guide">{{ $guide->name }}</a>
                </li>
            @endforeach
            </ul>
        </div>
        <div class="overlay hidden">
            <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script type="text/javascript">

</script>
@endpush
