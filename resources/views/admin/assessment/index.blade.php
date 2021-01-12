@extends('site.layouts.app')

@section('title', 'Assessment (Individu)')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="active">Assessment (Individu)</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Data Assessment (Individu)</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a href="{{route('assessment.create', $site)}}" class="btn btn-primary btn-sm" data-toggle="tooltip"
            title="Tambah">
            <i class="fa fa-plus"></i>
          </a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        @if(!$assessment)
        <div class="alert alert-warning">
          <h4><i class="fa fa-warning"></i> Anda Belum Mengisi Assessment (Individu) Hari Ini</h4>
          <p>Silahkan Klik Link <a href="{{route('assessment.create', $site)}}">Disini</a> Untuk Mengisi Assessment
            (Individu)
          </p>
        </div>
        @endif
        <table class="table table-striped table-bordered datatable" style="width:100%">
          <thead>
            <tr>
              <th width="10">#</th>
              <th width="200">Parameter</th>
              <th width="100">Nilai</th>
              <th width="100">Tanggal</th>
              <th width="100">Dibuat</th>
            </tr>
          </thead>
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
<script type="text/javascript">
  function filter(){
    $('#add-filter').modal('show');
}
$(function(){
    dataTable = $('.datatable').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 3, "desc" ]],
        ajax: {
            url: "{{route('assessment.read')}}",
            type: "GET",
            data:function(data){
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [2,3] },
        ],
        columns: [
            { data: "no" },
            { data: "category_name" },
            { data: "value" },
            { data: "report_date" },
            { data: "created_at" }
        ]
    });
    $('#form-search').submit(function(e){
        e.preventDefault();
        dataTable.draw();
        $('#add-filter').modal('hide');
    })
})
</script>
@endpush