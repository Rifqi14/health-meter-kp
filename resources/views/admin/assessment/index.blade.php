@extends('admin.layouts.app')

@section('title', 'Assessment')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="active">Assessment</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Data Assessment</h3>
      </div>
      <div class="box-body">
        @if(!$assessment)
        <div class="alert alert-warning">
          <h4><i class="fa fa-warning"></i> Anda Belum Mengisi Assessment Hari Ini</h4>
          <p>Silahkan Klik Link <a href="{{route('assessment.create')}}">Disini</a> Untuk Mengisi Assessment
          </p>
        </div>
        @endif
        <table class="table table-striped table-bordered datatable" style="width:100%">
          <thead>
            <tr>
              <th width="10">#</th>
              <th width="100">Total Nilai</th>
              <th width="200">Hasil Kategori</th>
              <th width="100">Tanggal</th>
              <th width="100">Dibuat</th>
              <th width="100">Terakhir Dirubah</th>
              <th width="100">Dirubah Oleh</th>
              <th width="10">#</th>
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
              data.workforce_id = {{ Auth::id() }}
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0,1] },
            { className: "text-center", targets: [2] },
            { render: function ( type, data, row ) {
              return `<span class="label" style="background-color:${row.category.color}">${row.category.name}</span>`
            },targets:[2] },
            { render: function ( type, data, row ) {
              return `<span class="label bg-blue">${row.updatedby.name}</span>`
            },targets:[6] },
            { render: function ( type, data, row ) {
              return `<div class="dropdown">
                            <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bars"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a class="dropdown-item" href="{{url('admin/assessment')}}/${row.id}"><i class="glyphicon glyphicon-info-sign"></i> Detail</a></li>
                            </ul>
                        </div>`
            },targets:[7] }
        ],
        columns: [
            { data: "no" },
            { data: "value_total" },
            { data: "health_meter_id" },
            { data: "date" },
            { data: "created_at" },
            { data: "updated_at" },
            { data: "updated_by" },
            { data: "id" },
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