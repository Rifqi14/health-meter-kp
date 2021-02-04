@extends('admin.layouts.app')

@section('title', 'Detail Jabatan')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style type="text/css">
    .overlay-wrapper {
        position: relative;
    }
</style>
@endsection
@push('breadcrump')
<li><a href="{{ route('title.index') }}">Jabatan</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Detail Jabatan</h3>
                <div class="pull-right box-tools">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
                </div>
            </div>
            <div class="box-body box-profile">
                <input type="hidden" name="title_id" value="{{ $title->id }}">
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Distrik</b> <span class="pull-right">{{ $title->site->name }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Nama</b> <span class="pull-right">{{ $title->name }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Nama Singkat</b> <span class="pull-right">{{ $title->shortname }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Kode</b> <span class="pull-right">{{ $title->code }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Instansi</b> <span class="pull-right">{{ $title->agency?$title->agency->name:'-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Bidang</b> <span class="pull-right">{{ $title->department?$title->department->name:'-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Sub Bidang</b> <span class="pull-right">{{ $title->sub_department?$title->sub_department->name:'-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Terakhir Dirubah</b> <span class="pull-right">{{ $title->updated_at }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Dirubah Oleh</b> <span class="pull-right">{{ $title->updated_by ? $title->user->name : '' }}</span>
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
                <li><a href="#detail" data-toggle="tab">Data Pegawai</a></li>
                <li  class="active"><a href="#role" data-toggle="tab">Assign Role</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane" id="detail">
                    <div class="overlay-wrapper">
                        <table class="table table-bordered table-striped" id="table-detail">
                            <thead>
                                <tr>
                                    <th style="text-align:center" width="10">#</th>
                                    <th width="200">Nama</th>
                                    <th width="200">Kelompok Workforce</th>
                                    <th width="200">Instansi</th>
                                    <th width="100">Status</th>
                            </thead>
                        </table>
                        <div class="overlay hidden">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                </div>
                <div class="tab-pane  active" id="role">
                    <div class="overlay-wrapper">
                        <form id="form-role" class="form-horizontal" action="{{url('admin/title/assignrole')}}" method="post" autocomplete="off">
                            {{ csrf_field() }}
                            <input type="hidden" name="title_role_id" value="{{$title->id}}" />
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Role</label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="role_id" name="role_id" data-placeholder="Pilih Role" required />
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
    $(document).ready(function () {
        dataTableDetail = $('#table-detail').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: false,
            responsive: true,
            order: [
                [2, "asc"]
            ],
            ajax: {
                url: "{{url('admin/title/employee')}}",
                type: "GET",
                data:function(data){
                    data.title_id = {{$title->id}};
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                },
                {
                    className: "text-right",
                    targets: [0]
                },
                {
                    className: "text-center",
                    targets: [4]
                },
                { render: function ( data, type, row) {
                return `${row.name}<br><small>${row.nid}</small>`
                },targets: [1] },
                { render: function ( data, type, row) {
                return `${row.workforce_group_id ? row.workforcegroup.name : ''}`
                },targets: [2] },
                { render: function ( data, type, row) {
                return `${row.agency_id ? row.agency.name : ''}`
                },targets: [3] },
                { render: function ( data, type, row ) {
                    if (row.deleted_at) {
                        bg = 'bg-red', teks = 'Non-Aktif';
                    } else {
                        bg = 'bg-green', teks = 'Aktif';
                    }
                    return `<span class="label ${bg}">${teks}</span>`
                    },targets: [4]
                    },
            ],
            columns: [
                {data: "no"},
                { data: "name" },
                { data: "workforce_group_id" },
                { data: "agency_id" },
                { data: "deleted_at" },
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
                url: "{{url('admin/title/readrole')}}",
                type: "GET",
                data:function(data){
                    data.title_id = {{$title->id}};
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
                        <li><a class="dropdown-item delete-role" href="#" data-role_id="${row.id}" data-title_id="{{ $title->id }}"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
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
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var currentTab = $(e.target).text(); 
            switch (currentTab)   {
            case 'Data Pegawai' :
                $('#table-detail').css("width", '100%')
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
                break ;
            case 'Assign Role' :
                $('#table-role').css("width", '100%')
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
                break ;
            };
        }) ;

        $( "#role_id" ).select2({
            ajax: {
                url: "{{route('role.selecttitle')}}",
                type:'GET',
                dataType: 'json',
                data: function (term,page) {
                return {
                    display_name:term,
                    title_id:{{$title->id}},
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
        $(document).on('click','.delete-role',function(){
            var role_id = $(this).data('role_id');
            var title_id = $(this).data('title_id');
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
                title:'Menghapus title role?',
                message:'Data yang telah dihapus tidak dapat dikembalikan',
                callback: function(result) {
                        if(result) {
                            var data = {
                                _token: "{{ csrf_token() }}",
                                role_id: role_id,
                                title_id: title_id
                            };
                            $.ajax({
                                url: `{{url('admin/title/deleterole')}}`,
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