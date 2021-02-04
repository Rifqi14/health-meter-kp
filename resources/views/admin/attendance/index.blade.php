@extends('admin.layouts.app')

@section('title', 'Kehadiran')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-daterangepicker/css/daterangepicker.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection
@push('breadcrump')
<li class="active">Kehadiran</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Data Kehadiran</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          @if (in_array('create', $actionmenu))
          <a href="{{route('attendance.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Tambah">
            <i class="fa fa-plus"></i>
          </a>
          @endif
          <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
            <i class="fa fa-search"></i>
          </a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        @if (!$assessment)
        <div class="alert alert-warning">
          <h4><i class="fa fa-warning"></i> Anda Belum Mengisi Assessment Hari Ini</h4>
          <p>Silahkan Klik Link <a href="{{route('assessment.create')}}">Disini</a> Untuk Mengisi Assessment
          </p>
        </div>
        @elseif ($assessment && !$attendance)
        <div class="alert alert-warning">
          <h4><i class="fa fa-warning"></i> Anda telah mengisi assessment tapi belum melakukan presensi kehadiran</h4>
          <p>Silahkan Klik Link <a href="{{ route('attendance.create') }}"><b>Disini</b></a> Untuk Mengisi Kehadiran</p>
        </div>
        @endif
        <table class="table table-striped table-bordered datatable" style="width:100%">
          <thead>
            <tr>
              <th width="10">#</th>
              <th width="200">Tanggal</th>
              <th width="200">Deskripsi Kehadiran</th>
              <th width="100">Dibuat</th>
              <th width="100">Terakhir Dirubah</th>
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
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Pencarian</h4>
      </div>
      <div class="modal-body">
        <form id="form-search" autocomplete="off">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="attendance_description_id">Ketarangan Kehadiran</label>
                <input type="text" name="attendance_description_id" class="form-control" data-placeholder="Pilih Keterangan Kehadiran" id="attendance_description_id">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="date">Tanggal</label>
                <div class="input-group">
                  <input type="text" class="form-control date-picker" name="date" placeholder="Tanggal Masuk">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button form="form-search" type="submit" class="btn btn-default btn-sm" title="Apply"><i class="fa fa-search"></i></button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/moment/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-daterangepicker/js/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script type="text/javascript">
  function filter(){
    $('#add-filter').modal('show');
  }
  $(function(){
    @if ($errors->any())
    $.gritter.add({
        title: 'Warning!',
        text: `{{ $errors->first() }}`,
        class_name: 'gritter-warning',
        time: 1000,
    });
    @endif
    // Select2
    $("#attendance_description_id").select2({
      ajax: {
          url: "{{route('attendancedescription.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
          return {
              name:term,
              page:page,
              limit:30,
          };
          },
          results: function (data,page) {
            var more = (page * 30) < data.total;
            var option = [];
            $.each(data.rows,function(index,item){
                option.push({
                  id:item.id,  
                  text: `${item.description}`,
                });
            });
            return {
                results: option, more: more,
            };
          },
      },
      allowClear: true,
    });
    $(document).on("change", "#attendance_description_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    //date
    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    })
    $(".select2").select2();
    $('#form-search').submit(function(e){
      e.preventDefault();
      dataTable.draw();
      $('#add-filter').modal('hide');
    })
    dataTable = $('.datatable').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 1, "desc" ]],
        ajax: {
            url: "{{route('attendance.read')}}",
            type: "GET",
            data:function(data){
              var attendance_description_id = $('#form-search').find('input[name=attendance_description_id]').val();
              var date = $('#form-search').find('input[name=date]').val();
              data.attendance_description_id = attendance_description_id;
              data.date = date;
              data.workforce_id = {{ Auth::user()->workforce->id }};
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [1] },
            { render: function ( data, type, row ) {
                  return `<span class="label bg-blue">${row.description ? row.description.description : ''}</span>`
            },targets: [2]
            },
        ],
        columns: [
            { data: "no" },
            { data: "date" },
            { data: "attendance_description_id" },
            { data: "created_at" },
            { data: "updated_at" },
        ]
    });
    $(document).on('click','.archive',function(){
        var id = $(this).data('id');
        bootbox.confirm({
          buttons: {
            confirm: {
              label: '<i class="fa fa-check"></i>',
              className: 'btn-primary btn-sm'
            },
            cancel: {
              label: '<i class="fa fa-undo"></i>',
              className: 'btn-default btn-sm'
            },
          },
          title:'Mengarsipkan Kehadiran?',
          message:'Data ini akan diarsipkan dan tidak dapat digunakan pada menu lainnya.',
          callback: function(result) {
            if(result) {
              var data = { _token: "{{ csrf_token() }}" };
              $.ajax({
                url: `{{url('admin/attendancedescription')}}/${id}`,
                dataType: 'json', 
                data:data,
                type:'DELETE',
                beforeSend:function(){
                    $('.overlay').removeClass('hidden');
                }
              }).done(function(response){
                  if(response.status){
                      $('.overlay').addClass('hidden');
                      $.gritter.add({
                          title: 'Success!',
                          text: response.message,
                          class_name: 'gritter-success',
                          time: 1000,
                      });
                      dataTable.ajax.reload( null, false );
                  }
                  else{
                      $.gritter.add({
                          title: 'Warning!',
                          text: response.message,
                          class_name: 'gritter-warning',
                          time: 1000,
                      });
                  }
              }).fail(function(response){
                  var response = response.responseJSON;
                  $('.overlay').addClass('hidden');
                  $.gritter.add({
                      title: 'Error!',
                      text: response.message,
                      class_name: 'gritter-error',
                      time: 1000,
                  });
              });
            }
          }
        });
    })
    $(document).on('click','.restore',function(){
        var id = $(this).data('id');
        bootbox.confirm({
          buttons: {
            confirm: {
              label: '<i class="fa fa-check"></i>',
              className: 'btn-primary btn-sm'
            },
            cancel: {
              label: '<i class="fa fa-undo"></i>',
              className: 'btn-default btn-sm'
            },
          },
          title:'Mengembalikan Kehadiran?',
          message:'Data ini akan dikembalikan dan dapat digunakan lagi pada menu lainnya.',
          callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}",
                              id: id
                          };
              $.ajax({
                url: `{{url('admin/attendancedescription/restore')}}`,
                dataType: 'json', 
                data:data,
                type:'POST',
                beforeSend:function(){
                    $('.overlay').removeClass('hidden');
                }
              }).done(function(response){
                  if(response.status){
                      $('.overlay').addClass('hidden');
                      $.gritter.add({
                          title: 'Success!',
                          text: response.message,
                          class_name: 'gritter-success',
                          time: 1000,
                      });
                      dataTable.ajax.reload( null, false );
                  }
                  else{
                      $.gritter.add({
                          title: 'Warning!',
                          text: response.message,
                          class_name: 'gritter-warning',
                          time: 1000,
                      });
                  }
              }).fail(function(response){
                  var response = response.responseJSON;
                  $('.overlay').addClass('hidden');
                  $.gritter.add({
                      title: 'Error!',
                      text: response.message,
                      class_name: 'gritter-error',
                      time: 1000,
                  });
              });		
            }
          }
        });
    })
    $(document).on('click','.delete',function(){
        var id = $(this).data('id');
        bootbox.confirm({
          buttons: {
            confirm: {
              label: '<i class="fa fa-check"></i>',
              className: 'btn-primary btn-sm'
            },
            cancel: {
              label: '<i class="fa fa-undo"></i>',
              className: 'btn-default btn-sm'
            },
          },
          title:'Menghapus Kehadiran?',
          message:'Data yang telah dihapus tidak dapat dikembalikan',
          callback: function(result) {
            if(result) {
              var data = {
                              _token: "{{ csrf_token() }}",
                              id: id
                          };
              $.ajax({
                url: `{{url('admin/attendancedescription/delete')}}`,
                dataType: 'json', 
                data:data,
                type:'POST',
                beforeSend:function(){
                    $('.overlay').removeClass('hidden');
                }
              }).done(function(response){
                  if(response.status){
                      $('.overlay').addClass('hidden');
                      $.gritter.add({
                          title: 'Success!',
                          text: response.message,
                          class_name: 'gritter-success',
                          time: 1000,
                      });
                      dataTable.ajax.reload( null, false );
                  }
                  else{
                      $.gritter.add({
                          title: 'Warning!',
                          text: response.message,
                          class_name: 'gritter-warning',
                          time: 1000,
                      });
                  }
              }).fail(function(response){
                  var response = response.responseJSON;
                  $('.overlay').addClass('hidden');
                  $.gritter.add({
                      title: 'Error!',
                      text: response.message,
                      class_name: 'gritter-error',
                      time: 1000,
                  });
              });		
            }
          }
        });
    })
})
</script>
@endpush