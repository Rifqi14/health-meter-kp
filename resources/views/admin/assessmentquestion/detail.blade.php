@extends('admin.layouts.app')

@section('title', 'Detail Pertanyaan Assessment')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
<style type="text/css">
    .overlay-wrapper {
        position: relative;
    }
</style>
@endsection
@push('breadcrump')
<li><a href="{{route('category.index')}}">Pertanyaan Assessment</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Detail Pertanyaan Assessment</h3>
                <div class="pull-right box-tools">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                </div>
            </div>
            <div class="box-body box-profile">
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Jenis</b> <span class="pull-right">{{$question->type}}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Frekuensi</b> <span class="pull-right">{{$frequency[$question->frequency]}}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Waktu</b> <span
                            class="pull-right">{{$question->start_date . ' s/d ' . $question->finish_date}}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Kelompok Workforce</b> <span class="pull-right">{{$question->workforcegroup->name}}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Unit</b> <span class="pull-right">{{$question->site->name}}</span>
                    </li>
                </ul>
                <b>Deskripsi</b><br><span>{{$question->description}}</span>
            </div>
            <div class="overlay hidden">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="nav-tabs-custom tab-primary">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#subcategory" data-toggle="tab">Jawaban Assessment</a></li>
                <li><a href="#archive" data-toggle="tab">Arsip Jawaban</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="subcategory">
                    <div class="overlay-wrapper">
                        <a class="btn btn-primary pull-right btn-sm" href="#" onclick="adddetail()"><i
                                class="fa fa-plus"></i></a>
                        <table class="table table-bordered table-striped" id="table-subcategory" style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="text-align:center" width="10">#</th>
                                    <th width="300">Deskripsi</th>
                                    <th width="50">Tipe</th>
                                    <th width="50">Bobot</th>
                                    <th width="10">#</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="overlay hidden">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="archive">
                    <div class="overlay-wrapper">
                        <table class="table table-bordered table-striped" id="table-archive" style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="text-align:center" width="10">#</th>
                                    <th width="300">Deskripsi</th>
                                    <th width="50">Tipe</th>
                                    <th width="50">Bobot</th>
                                    <th width="10">#</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add-detail" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Tambah Jawaban Assessment</h4>
            </div>
            <div class="modal-body">
                <form id="form" method="post" action="{{route('assessmentanswer.store')}}" autocomplete="off">
                    {{ csrf_field() }}
                    <input type="hidden" name="assessment_question_id" value="{{$question->id}}" />
                    <input type="hidden" name="_method" />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="description">Deskripsi</label>
                                <input type="text" name="description" class="form-control" placeholder="Deskripsi"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="answer_type">Tipe</label>
                                <select name="answer_type" class="form-control select2" placeholder="Pilih Tipe"
                                    required>
                                    <option value=""></option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="radio">Radio Button</option>
                                    <option value="text">Teks</option>
                                    <option value="number">Angka</option>
                                    <option value="select">List Dropdown</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="rating">Bobot</label>
                                <input type="text" name="rating" class="form-control numberfield" placeholder="Bobot"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="receipt_header">Informasi</label>
                                <textarea class="form-control summernote" name="information"
                                    id="information"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="form" type="submit" class="btn btn-primary btn-sm" title="Simpan"><i
                        class="fa fa-save"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script>
    function adddetail() {
        $('#add-detail .modal-title').html('Tambah Jawaban Assessment');
        $('#add-detail').modal('show');
        $('#form')[0].reset();
        $('#form').attr('action', '{{route('assessmentanswer.store')}}');
        $('#form input[name=_method]').attr('value', 'POST');
        $('#form .invalid-feedback').each(function () {
            $(this).remove();
        });
        $('#form').find('.form-group').removeClass('has-error').removeClass('has-success');
        $('#form textarea[name=information]').summernote('code', '');
        $('#form').find('select[name=type]').select2('val', '');
        $('#form').find('input[name=min]').closest('.row').hide();
        $('#form').find('input[name=max]').closest('.row').hide();
    }
    //Text Editor Component
    $('.summernote').summernote({
        height: 180,
        placeholder: 'Tulis sesuatu disini...',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });
    $(document).on("change", "#form select[name=answer_type]", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
            $('#form').validate().form();
        }
    });
    $(document).ready(function () {
        $(".numberfield").inputmask('decimal', {
            rightAlign: false
        });
        $('.select2').select2({
            allowClear: true
        });
        dataTable = $('#table-subcategory').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: false,
            responsive: true,
            order: [
                [4, "asc"]
            ],
            ajax: {
                url: "{{url('admin/assessmentanswer/read')}}",
                type: "GET",
                data: function (data) {
                    data.assesment_question_id = {{$question->id}};
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                },
                {
                    className: "text-right",
                    targets: [0, 3]
                },
                {
                    className: "text-center",
                    targets: [4]
                },
                {
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item edit" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                        <li><a class="dropdown-item archive" href="#" data-id="${row.id}"><i class="fa fa-archive"></i> Archive</a></li>
                      </ul></div>`
                    },
                    targets: [4]
                }
            ],
            columns: [{
                    data: "no"
                },
                {
                    data: "description"
                },
                {
                    data: "type"
                },
                {
                    data: "rating"
                },
                {
                    data: "id"
                }
            ]
        });
        dataTableArsip = $('#table-archive').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: false,
            responsive: true,
            order: [
                [4, "asc"]
            ],
            ajax: {
                url: "{{url('admin/assessmentanswer/read')}}",
                type: "GET",
                data: function (data) {
                    data.assesment_question_id = {{$question->id}};
                    data.archive = 1;
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                },
                {
                    className: "text-right",
                    targets: [0, 3]
                },
                {
                    className: "text-center",
                    targets: [4]
                },
                {
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                        <li><a class="dropdown-item restore" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-refresh"></i> Restore</a></li>
                      </ul></div>`
                    },
                    targets: [4]
                }
            ],
            columns: [{
                    data: "no"
                },
                {
                    data: "description"
                },
                {
                    data: "type"
                },
                {
                    data: "rating"
                },
                {
                    data: "id"
                }
            ]
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
                if (element.is(':file')) {
                    error.insertAfter(element.parent().parent().parent());
                } else
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else
                if (element.attr('type') == 'checkbox') {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function () {
                $.ajax({
                    url: $('#form').attr('action'),
                    method: 'post',
                    data: new FormData($('#form')[0]),
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function () {
                        $('#subcategory .overlay').removeClass('hidden');
                    }
                }).done(function (response) {
                    $("#add-detail").modal('hide');
                    $('#subcategory .overlay').addClass('hidden');
                    if (response.status) {
                        dataTable.draw();
                        $.gritter.add({
                            title: 'Success!',
                            text: response.message,
                            class_name: 'gritter-success',
                            time: 1000,
                        });
                    } else {
                        $.gritter.add({
                            title: 'Warning!',
                            text: response.message,
                            class_name: 'gritter-warning',
                            time: 1000,
                        });
                    }
                    return;

                }).fail(function (response) {
                    var response = response.responseJSON;
                    $('#subcategory .overlay').addClass('hidden');
                    $.gritter.add({
                        title: 'Error!',
                        text: response.message,
                        class_name: 'gritter-error',
                        time: 1000,
                    });
                })
            }
        });
        $(document).on('click', '.edit', function () {
            var id = $(this).data('id');
            $.ajax({
                url: `{{url('admin/assessmentanswer')}}/${id}/edit`,
                method: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $('#subcategory .overlay').removeClass('hidden');
                },
            }).done(function (response) {
                $('#subcategory .overlay').addClass('hidden');
                if (response.status) {
                    $('#add-detail .modal-title').html('Ubah Jawaban Assessment');
                    $('#add-detail').modal('show');
                    $('#form')[0].reset();
                    $('#form .invalid-feedback').each(function () {
                        $(this).remove();
                    });
                    $('#form .form-group').removeClass('has-error').removeClass('has-success');
                    $('#form input[name=_method]').attr('value', 'PUT');
                    $('#form input[name=description]').attr('value', response.data.description);
                    $('#form select[name=answer_type]').select2('val', response.data.answer_type);
                    $('#form input[name=rating]').attr('value', response.data.rating);
                    $('#form textarea[name=information]').summernote('code', response.data
                        .information);
                    $('#form').attr('action',
                        `{{url('admin/assessmentanswer')}}/${response.data.id}`);
                }
            }).fail(function (response) {
                var response = response.responseJSON;
                $('#subcategory .overlay').addClass('hidden');
                $.gritter.add({
                    title: 'Error!',
                    text: response.message,
                    class_name: 'gritter-error',
                    time: 1000,
                });
            })
        });
        $(document).on('click', '.archive', function () {
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
                title: 'Mengarsipkan jawaban assessment?',
                message: 'Data ini akan diarsipkan dan tidak dapat digunakan pada menu lainnya.',
                callback: function (result) {
                    if (result) {
                        var data = {
                            _token: "{{ csrf_token() }}"
                        };
                        $.ajax({
                            url: `{{url('admin/assessmentanswer')}}/${id}`,
                            dataType: 'json',
                            data: data,
                            type: 'DELETE',
                            beforeSend: function () {
                                $('#subcategory .overlay').removeClass(
                                'hidden');
                            }
                        }).done(function (response) {
                            if (response.status) {
                                $('#subcategory .overlay').addClass('hidden');
                                $.gritter.add({
                                    title: 'Success!',
                                    text: response.message,
                                    class_name: 'gritter-success',
                                    time: 1000,
                                });
                                dataTable.ajax.reload(null, false);
                                dataTableArsip.ajax.reload( null, false );
                            } else {
                                $.gritter.add({
                                    title: 'Warning!',
                                    text: response.message,
                                    class_name: 'gritter-warning',
                                    time: 1000,
                                });
                            }
                        }).fail(function (response) {
                            var response = response.responseJSON;
                            $('#subcategory .overlay').addClass('hidden');
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
                title:'Mengembalikan jawaban assessment?',
                message:'Data ini akan dikembalikan dan dapat digunakan lagi pada menu lainnya.',
                callback: function(result) {
                        if(result) {
                            var data = {
                                _token: "{{ csrf_token() }}",
                                id: id
                            };
                            $.ajax({
                                url: `{{url('admin/assessmentanswer/restore')}}`,
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
                                    dataTableArsip.ajax.reload( null, false );
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
                            })		
                        }
                }
            });
        });
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
                title:'Menghapus jawaban assessment?',
                message:'Data yang telah dihapus tidak dapat dikembalikan',
                callback: function(result) {
                        if(result) {
                            var data = {
                                _token: "{{ csrf_token() }}",
                                id: id
                            };
                            $.ajax({
                                url: `{{url('admin/assessmentanswer/delete')}}`,
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
                                    dataTableArsip.ajax.reload( null, false );
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
                            })		
                        }
                }
            });
        });
    });

</script>
@endpush