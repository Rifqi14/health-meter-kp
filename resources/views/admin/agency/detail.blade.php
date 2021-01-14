@extends('admin.layouts.app')

@section('title', 'Detail Instansi')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style type="text/css">
  .overlay-wrapper {
    position: relative;
  }
</style>
@endsection
@push('breadcrump')
<li><a href="{{route('agency.index')}}">Instansi</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-4">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Instansi</h3>
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="box-body box-profile">
        <ul class="list-group list-group-unbordered">
          <li class="list-group-item">
            <b>Kode</b> <span class="pull-right">{{$agency->code}}</span>
          </li>
          <li class="list-group-item">
            <b>Nama</b> <span class="pull-right">{{$agency->name}}</span>
          </li>
          <li class="list-group-item">
            <b>Autentikasi</b> <span class="pull-right">{{$agency->authentication}}</span>
          </li>
          <li class="list-group-item">
            <b>Link</b> <span class="pull-right">{{$agency->link}}</span>
          </li>
        </ul>
      </div>
      <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="nav-tabs-custom tab-primary">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#site" data-toggle="tab">Instansi Unit</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="site">
          <div class="overlay-wrapper">
            <form id="form" class="form-horizontal" action="{{route('agencysite.store')}}" method="post"
              autocomplete="off">
              {{ csrf_field() }}
              <input type="hidden" name="agency_id" value="{{$agency->id}}" />
              <div class="form-group">
                <label class="col-sm-2 control-label">Unit</label>
                <div class="col-sm-6">
                  <div class="input-group">
                    <input type="text" class="form-control" id="site_id" name="site_id" data-placeholder="Pilih Unit"
                      required />
                    <div class="input-group-btn"><button class="btn btn-primary" type="submit"><i
                          class="fa fa-plus"></i></button></div>
                  </div>
                </div>
              </div>
            </form>
            <table class="table table-bordered table-striped" id="table-site">
              <thead>
                <tr>
                  <th style="text-align:center" width="10">#</th>
                  <th width="100">Kode</th>
                  <th width="250">Nama</th>
                  <th width="10">#</th>
                </tr>
              </thead>
            </table>
            <div class="overlay hidden">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script>
  $(document).ready(function(){
   
    dataTableSite = $('#table-site').DataTable( {
          stateSave:true,
          processing: true,
          serverSide: true,
          filter:false,
          info:false,
          lengthChange:false,
          responsive: true,
          order: [[3, "asc" ]],
          ajax: {
              url: "{{url('admin/agencysite/read')}}",
              type: "GET",
              data:function(data){
                  data.agency_id = {{$agency->id}};
              }
          },
          columnDefs:[
              {
                  orderable: false,targets:[0]
              },
              { className: "text-right", targets: [0] },
              { className: "text-center", targets: [3] },
              { render: function ( data, type, row ) {
                  return `${row.site.code}`
              },targets: [1]
              },
              { render: function ( data, type, row ) {
                  return `${row.site.name}`
              },targets: [2]
              },
              { render: function ( data, type, row ) {
                  return `<div class="dropdown">
                  <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bars"></i>
                  </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                    </ul></div>`
              },targets: [3]
              }
          ],
          columns: [
              { data: "no" },
              { data: "site_id" },
              { data: "site_id" },
              { data: "id" },
          ]
    });
    $( "#site_id" ).select2({
      ajax: {
        url: "{{url('admin/agencysite/select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            agency_id:{{$agency->id}},
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
              text: `${item.name}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    $(document).on("change", "#site_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
    });
    $("#form").validate({
      errorElement: 'span',
      errorClass: 'help-block',
      focusInvalid: false,
      highlight: function (e) {
        $(e).closest('.form-group').removeClass('has-success').addClass('has-error');
      },
  
      success: function (e) {
        $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
        $(e).remove();
      },
      errorPlacement: function (error, element) {
        if(element.is(':file')) {
          error.insertAfter(element.parent().parent().parent());
        }else
        if(element.parent('.input-group').length) {
          error.insertAfter(element.parent());
        } 
        else
        if (element.attr('type') == 'checkbox') {
          error.insertAfter(element.parent());
        }
        else{
          error.insertAfter(element);
        }
      },
      submitHandler: function() { 
        $.ajax({
          url:$('#form').attr('action'),
          method:'post',
          data: new FormData($('#form')[0]),
          processData: false,
          contentType: false,
          dataType: 'json', 
          beforeSend:function(){
            $('#site .overlay').removeClass('hidden');
          }
        }).done(function(response){
          $('#site .overlay').addClass('hidden');
          $( "#site_id" ).select2('val','');
          if(response.status){
            dataTableSite.draw();
            $.gritter.add({
                title: 'Success!',
                text: response.message,
                class_name: 'gritter-success',
                time: 1000,
            });
          }
          else{	
              $.gritter.add({
                  title: 'Warning!',
                  text: response.message,
                  class_name: 'gritter-warning',
                  time: 1000,
              });
          }
          return;

        }).fail(function(response){
            var response = response.responseJSON;
            $('#site .overlay').addClass('hidden');
            $( "#site_id" ).select2('val','');
            $.gritter.add({
                title: 'Error!',
                text: response.message,
                class_name: 'gritter-error',
                time: 1000,
            });
        })		
      }
    });
    $(document).on('click','.delete',function(){
      var id = $(this).data('id');
      bootbox.confirm({
          buttons: {
              confirm: {
                  label: '<i class="fa fa-check"></i>',
                  className: 'btn-primary'
              },
              cancel: {
                  label: '<i class="fa fa-undo"></i>',
                  className: 'btn-default'
              },
          },
          title:'Menghapus instansi unit?',
          message:'Data yang telah dihapus tidak dapat dikembalikan',
          callback: function(result) {
                if(result) {
                    var data = {
                        _token: "{{ csrf_token() }}"
                    };
                    $.ajax({
                        url: `{{url('admin/agencysite')}}/${id}`,
                        dataType: 'json', 
                        data:data,
                        type:'DELETE',
                        beforeSend:function(){
                            $('#site .overlay').removeClass('hidden');
                        }
                    }).done(function(response){
                        if(response.status){
                          $('#site .overlay').addClass('hidden');
                            $.gritter.add({
                                title: 'Success!',
                                text: response.message,
                                class_name: 'gritter-success',
                                time: 1000,
                            });
                            dataTableSite.ajax.reload( null, false );
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
                        $('#site .overlay').addClass('hidden');
                        $.gritter.add({
                            title: 'Error!',
                            text: response.message,
                            class_name: 'gritter-error',
                            time: 1000,
                        });
                    })		
                }
          }
      });
    });
  });
</script>
@endpush