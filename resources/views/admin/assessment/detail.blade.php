@extends('admin.layouts.app')

@section('title', 'Detail Assessment')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="active">Detail Assessment</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Data Detail Assessment</h3>
      </div>
      <div class="box-body">
        <table class="table table-striped table-bordered datatable" style="width:100%">
          <thead>
            <tr>
              <th width="10">#</th>
              <th width="100">Pertanyaan</th>
              <th width="100">Jawaban</th>
              <th width="100">Nilai</th>
              <th width="100">Deskripsi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($assessment as $key => $item)
            <tr>
              <td>{{ ++$key }}</td>
              <td>{{ @$item->question->description }}</td>
              <td>{{ @$item->answer->description }}</td>
              <td>{{ @$item->rating }}</td>
              <td>{{ @$item->description }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
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
@endpush