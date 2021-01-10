@extends('admin.layouts.app')

@section('title', 'Detail User')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style type="text/css">
  .overlay-wrapper {
    position: relative;
  }
</style>
@endsection
@push('breadcrump')
<li><a href="{{route('user.index')}}">User</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-4">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail User</h3>
      </div>
      <div class="box-body box-profile">
        <img class="profile-user-img img-responsive img-circle" src="{{asset('adminlte/images/user2-160x160.jpg')}}"
          alt="User profile picture">
        <h3 class="profile-username text-center">{{$user->name}}</h3>
        <p class="text-muted text-center">{{$user->display_name}}</p>
        <ul class="list-group list-group-unbordered">
          <li class="list-group-item">
            <b>Email</b> <span class="pull-right">{{$user->email}}</span>
          </li>
          <li class="list-group-item">
            <b>Username</b> <span class="pull-right">{{$user->username}}</span>
          </li>
          <li class="list-group-item">
            <b>Status</b>
            <span class="pull-right">
              <label class="label @if($user->status==1) label-success @else label-danger @endif">
                @if($user->status==1)
                Aktif
                @else Tidak Aktif @endif
              </label>
            </span>
          </li>
          <li class="list-group-item">
            <b>Dibuat</b> <span class="pull-right">{{$user->created_at}}</span>
          </li>
          <li class="list-group-item">
            <b>Login Terakhir</b> <span class="pull-right">{{$user->last_login}}</span>
          </li>
        </ul>
        <a onclick="event.preventDefault(); document.getElementById('reset-form').submit();" alt="Reset"
          class="btn btn-primary btn-block">
          <b>Reset Password</b></a>
        <form id="reset-form" action="{{ route('user.reset') }}" method="POST" style="display: none;">
          @csrf
          <input type="hidden" name="id" value="{{$user->id}}" />
        </form>
      </div>
      <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="nav-tabs-custom tab-primary">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#site" data-toggle="tab">Akses Unit</a></li>
        <li><a href="#role" data-toggle="tab">Assign Role</a></li>
        <li><a href="#log" data-toggle="tab">Log Login</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="site">
          <div class="overlay-wrapper">
            <form id="form" class="form-horizontal" action="{{url('admin/siteuser/store')}}" method="post"
              autocomplete="off">
              {{ csrf_field() }}
              <input type="hidden" name="user_id" value="{{$user->id}}" />
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
        <div class="tab-pane" id="role">
          <div class="overlay-wrapper">
            <form id="form-role" class="form-horizontal" action="{{url('admin/user/assignrole')}}" method="post"
              autocomplete="off">
              {{ csrf_field() }}
              <input type="hidden" name="user_role_id" value="{{$user->id}}" />
              <div class="form-group">
                <label class="col-sm-2 control-label">Role</label>
                <div class="col-sm-6">
                  <div class="input-group">
                    <input type="text" class="form-control" id="role_id" name="role_id" data-placeholder="Pilih Role"
                      required />
                    <div class="input-group-btn">
                      <button class="btn btn-primary" type="submit">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <table class="table table-bordered table-striped" id="table-role">
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
        <div class="tab-pane" id="log">
          <table class="table table-bordered table-striped" id="table-log">
            <thead>
              <tr>
                <th style="text-align:center" width="10">#</th>
                <th width="100">Alamat IP</th>
                <th width="250">Perangkat</th>
                <th width="100">Login Terakhir</th>
              </tr>
            </thead>
          </table>
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
              url: "{{url('admin/siteuser/read')}}",
              type: "GET",
              data:function(data){
                  data.user_id = {{$user->id}};
              }
          },
          columnDefs:[
              {
                  orderable: false,targets:[0]
              },
              { className: "text-right", targets: [0] },
              { className: "text-center", targets: [3] },
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
              { data: "code" },
              { data: "name" },
              { data: "id" },
          ]
    });
    datatableRole = $('#table-role').DataTable({
      stateSave:true,
      processing: true,
      serverSide: true,
      filter:false,
      info:false,
      lengthChange:false,
      responsive: true,
      order: [[3, "asc" ]],
      ajax: {
          url: "{{url('admin/user/readrole')}}",
          type: "GET",
          data:function(data){
              data.user_id = {{$user->id}};
          }
      },
      columnDefs:[
          {
              orderable: false,targets:[0]
          },
          { className: "text-right", targets: [0] },
          { className: "text-center", targets: [3] },
          { render: function ( data, type, row ) {
              return `<div class="dropdown">
              <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-bars"></i>
              </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li><a class="dropdown-item delete-role" href="#" data-role_id="${row.id}" data-user_id="{{ $user->id }}"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                </ul></div>`
          },targets: [3]
          }
      ],
      columns: [
          { data: "no" },
          { data: "name" },
          { data: "display_name" },
          { data: "id" },
      ]
    });
    dataTableLog = $('#table-log').DataTable( {
          stateSave:true,
          processing: true,
          serverSide: true,
          filter:false,
          info:false,
          lengthChange:false,
          responsive: true,
          order: [[3, "asc" ]],
          ajax: {
              url: "{{url('admin/user/log')}}",
              type: "GET",
              data:function(data){
                  data.user_id = {{$user->id}};
              }
          },
          columnDefs:[
              {
                  orderable: false,targets:[0]
              },
              { className: "text-right", targets: [0] },
              { className: "text-center", targets: [3] }
          ],
          columns: [
              { data: "no" },
              { data: "ip_address" },
              { data: "device" },
              { data: "last_login" },
          ]
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
      var currentTab = $(e.target).text(); 
        switch (currentTab)   {
          case 'Log Login' :
              $('#table-log').css("width", '100%')
              $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
              break ;
          case 'Assign Role' :
              $('#table-role').css("width", '100%')
              $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
              break ;
          case 'Akses Unit' :  
            $('#table-site').css("width", '100%')
              $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
              break ;
          default: 
        };
    }) ;
    $( "#site_id" ).select2({
      ajax: {
        url: "{{url('admin/siteuser/select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            user_id:{{$user->id}},
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
    $( "#role_id" ).select2({
      ajax: {
        url: "{{route('role.select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            display_name:term,
            user_id:{{$user->id}},
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
              text: `${item.display_name}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    $(document).on("change", "#role_id", function () {
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
    $("#form-role").validate({
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
          url:$('#form-role').attr('action'),
          method:'post',
          data: new FormData($('#form-role')[0]),
          processData: false,
          contentType: false,
          dataType: 'json', 
          beforeSend:function(){
            $('#role .overlay').removeClass('hidden');
          }
        }).done(function(response){
          $('#role .overlay').addClass('hidden');
          $( "#role_id" ).select2('val','');
          if(response.status){
            datatableRole.draw();
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
            $('#role .overlay').addClass('hidden');
            $( "#role_id" ).select2('val','');
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
                  className: 'btn-danger'
              },
              cancel: {
                  label: '<i class="fa fa-undo"></i>',
                  className: 'btn-default'
              },
          },
          title:'Menghapus akses unit?',
          message:'Data yang telah dihapus tidak dapat dikembalikan',
          callback: function(result) {
                if(result) {
                    var data = {
                        _token: "{{ csrf_token() }}"
                    };
                    $.ajax({
                        url: `{{url('admin/siteuser')}}/${id}`,
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
    $(document).on('click','.delete-role',function(){
      var role_id = $(this).data('role_id');
      var user_id = $(this).data('user_id');
      bootbox.confirm({
          buttons: {
              confirm: {
                  label: '<i class="fa fa-check"></i>',
                  className: 'btn-danger'
              },
              cancel: {
                  label: '<i class="fa fa-undo"></i>',
                  className: 'btn-default'
              },
          },
          title:'Menghapus user role?',
          message:'Data yang telah dihapus tidak dapat dikembalikan',
          callback: function(result) {
                if(result) {
                    var data = {
                        _token: "{{ csrf_token() }}",
                        role_id: role_id,
                        user_id: user_id
                    };
                    $.ajax({
                        url: `{{url('admin/user/deleterole')}}`,
                        dataType: 'json', 
                        data:data,
                        type:'DELETE',
                        beforeSend:function(){
                            $('#role .overlay').removeClass('hidden');
                        }
                    }).done(function(response){
                        if(response.status){
                          $('#role .overlay').addClass('hidden');
                            $.gritter.add({
                                title: 'Success!',
                                text: response.message,
                                class_name: 'gritter-success',
                                time: 1000,
                            });
                            datatableRole.ajax.reload( null, false );
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
                        $('#role .overlay').addClass('hidden');
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