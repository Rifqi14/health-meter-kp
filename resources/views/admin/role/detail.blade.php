@extends('admin.layouts.app')

@section('title', 'Detail Role')
@section('stylesheets')
<style type="text/css">
    .overlay-wrapper{
      position:relative;
    }
</style>
@endsection
@push('breadcrump')
    <li><a href="{{route('role.index')}}">Group Modul</a></li>
    <li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Detail Group Modul</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
              <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form id="form" action="#" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
               <input type="hidden" name="_method" value="put">
                <div class="box-body">
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <p class="form-control-static">{{$role->name}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="display_name" class="col-sm-2 control-label">Nama Tampilan <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <p class="form-control-static">{{$role->display_name}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="description" class="col-sm-2 control-label">Keterangan</label>
                    <div class="col-sm-6">
                      <p class="form-control-static">{{$role->description}}</p>
                    </div>
                  </div>
                </div>
              </form>
        </div>
    </div>
    </div>
</div>

<div class="row">
  <div class="col-md-12">
    <!-- Custom Tabs -->
    <div class="nav-tabs-custom  tab-primary">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#menuweb" data-toggle="tab">Modul</a></li>
        <li><a href="#dashboard" data-toggle="tab">Dashboard</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="menuweb">
          <div class="overlay-wrapper">
            <table  class="table table-bordered table-striped">
              <thead>
                  <tr>
                      <th style="text-align:center" width="10">#</th>
                      <th width="250" >Nama Modul</th>
                      <th width="50"  style="text-align:center">Tampil</th>
                      <th width="50"  style="text-align:center">C</th>
                      <th width="50"  style="text-align:center">R</th>
                      <th width="50"  style="text-align:center">U</th>
                      <th width="50"  style="text-align:center">D</th>
                  </tr>
              </thead>
              <tbody>
                  @php 
                      $no = 1;
                  @endphp
                  @foreach($rolemenus as $rolemenu)
                  <tr>
                      <td style="text-align:center">{{$no++}}</td>
                      <td>
                          @if($rolemenu->parent_id)
                              &nbsp;&nbsp;&nbsp;&nbsp;{{$rolemenu->menu_name}}
                          @else
                              <b>{{$rolemenu->menu_name}}</b>
                          @endif
                          </td>
                      <td style="text-align:center" ><input type="checkbox" value="{{$rolemenu->id}}" class="i-checks updatemenu" @if($rolemenu->role_access) checked @endif autocomplete="off"/></td>
                          @if($rolemenu->parent_id)
                          <td style="text-align:center" ><input type="checkbox" value="{{$rolemenu->id}}" class="i-checks create" @if($rolemenu->create) checked @endif autocomplete="off"/></td>
                          <td style="text-align:center" ><input type="checkbox" value="{{$rolemenu->id}}" class="i-checks read" @if($rolemenu->read) checked @endif autocomplete="off"/></td>
                          <td style="text-align:center" ><input type="checkbox" value="{{$rolemenu->id}}" class="i-checks update" @if($rolemenu->update) checked @endif autocomplete="off"/></td>
                          <td style="text-align:center" ><input type="checkbox" value="{{$rolemenu->id}}" class="i-checks delete" @if($rolemenu->delete) checked @endif autocomplete="off"/></td>
                          @else
                          <td colspan="4"></td>
                          @endif
                  </tr>	
                  @endforeach
              </tbody>
            </table>
            <div class="overlay hidden">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
          </div>
        </div>
        <div class="tab-pane" id="dashboard">
          <div class="overlay-wrapper">
            <table  class="table table-bordered table-striped">
              <thead>
                  <tr>
                      <th style="text-align:center" width="10">#</th>
                      <th width="250" >Nama</th>
                      <th width="50"  style="text-align:center">#</th>
                  </tr>
              </thead>
              <tbody>
                  @php 
                      $no = 1;
                  @endphp
                  @foreach($roledashboards as $roledashboard)
                  <tr>
                      <td style="text-align:center">{{$no++}}</td>
                      <td>{{$roledashboard->dashboard_name}}</td>
                      <td style="text-align:center" ><input type="checkbox" value="{{$roledashboard->id}}" class="i-checks updatedashboard" @if($roledashboard->role_access) checked @endif autocomplete="off"/></td>
                  </tr>	
                  @endforeach
              </tbody>
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
<script>
  $(document).ready(function(){
      $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
      });
      $('.updatemenu').on('ifChanged',function(){
			$.ajax({
				url: "{{url('admin/rolemenu/update')}}",
				data: {
          _token: "{{ csrf_token() }}",
					id:this.value,
					type:'access',
					checked:this.checked?1:0
				},
				type:'POST',
				dataType:'json',
        beforeSend:function(){
          $('#menuweb .overlay').removeClass('hidden');
        }
			}).done(function(response){
          $('#menuweb .overlay').addClass('hidden');
          if(response.status){
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
          $('#menuweb  .overlay').addClass('hidden');
          $.gritter.add({
              title: 'Error!',
              text: response.message,
              class_name: 'gritter-error',
              time: 1000,
          });
      })
		});
    $('.create').on('ifChanged',function(){
			$.ajax({
				url: "{{url('admin/rolemenu/update')}}",
				data: {
          _token: "{{ csrf_token() }}",
					id:this.value,
          type:'create',
					checked:this.checked?1:0
				},
				type:'POST',
				dataType:'json',
        beforeSend:function(){
          $('#menuweb .overlay').removeClass('hidden');
        }
			}).done(function(response){
          $('#menuweb .overlay').addClass('hidden');
          if(response.status){
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
          $('#menuweb  .overlay').addClass('hidden');
          $.gritter.add({
              title: 'Error!',
              text: response.message,
              class_name: 'gritter-error',
              time: 1000,
          });
      })
    });
    $('.read').on('ifChanged',function(){
			$.ajax({
				url: "{{url('admin/rolemenu/update')}}",
				data: {
          _token: "{{ csrf_token() }}",
					id:this.value,
					type:'read',
					checked:this.checked?1:0
				},
				type:'POST',
				dataType:'json',
        beforeSend:function(){
          $('#menuweb .overlay').removeClass('hidden');
        }
			}).done(function(response){
          $('#menuweb .overlay').addClass('hidden');
          if(response.status){
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
          $('#menuweb  .overlay').addClass('hidden');
          $.gritter.add({
              title: 'Error!',
              text: response.message,
              class_name: 'gritter-error',
              time: 1000,
          });
      })
    });
    $('.update').on('ifChanged',function(){
			$.ajax({
				url: "{{url('admin/rolemenu/update')}}",
				data: {
          _token: "{{ csrf_token() }}",
          id:this.value,
          type:'update',
					checked:this.checked?1:0
				},
				type:'POST',
				dataType:'json',
        beforeSend:function(){
          $('#menuweb .overlay').removeClass('hidden');
        }
			}).done(function(response){
          $('#menuweb .overlay').addClass('hidden');
          if(response.status){
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
          $('#menuweb  .overlay').addClass('hidden');
          $.gritter.add({
              title: 'Error!',
              text: response.message,
              class_name: 'gritter-error',
              time: 1000,
          });
      })
    });
    $('.delete').on('ifChanged',function(){
			$.ajax({
				url: "{{url('admin/rolemenu/update')}}",
				data: {
          _token: "{{ csrf_token() }}",
          id:this.value,
          type:'delete',
					checked:this.checked?1:0
				},
				type:'POST',
				dataType:'json',
        beforeSend:function(){
          $('#menuweb .overlay').removeClass('hidden');
        }
			}).done(function(response){
          $('#menuweb .overlay').addClass('hidden');
          if(response.status){
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
          $('#menuweb  .overlay').addClass('hidden');
          $.gritter.add({
              title: 'Error!',
              text: response.message,
              class_name: 'gritter-error',
              time: 1000,
          });
      })
		});
    $('.updatedashboard').on('ifChanged',function(){
			$.ajax({
				url: "{{url('admin/roledashboard/update')}}",
				data: {
          _token: "{{ csrf_token() }}",
					id:this.value,
					role_access:this.checked?1:0
				},
				type:'POST',
				dataType:'json',
        beforeSend:function(){
          $('#dashboard .overlay').removeClass('hidden');
        }
			}).done(function(response){
          $('#dashboard .overlay').addClass('hidden');
          if(response.status){
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
          $('#dashboard  .overlay').addClass('hidden');
          $.gritter.add({
              title: 'Error!',
              text: response.message,
              class_name: 'gritter-error',
              time: 1000,
          });
      })
		});
  });
</script>
@endpush