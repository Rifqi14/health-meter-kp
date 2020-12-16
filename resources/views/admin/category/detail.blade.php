@extends('admin.layouts.app')

@section('title', 'Detail Kategori')
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
<li><a href="{{route('category.index')}}">Kategori</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Detail Kategori</h3>
                <div class="pull-right box-tools">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                </div>
            </div>
            <div class="box-body box-profile">
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Nama</b> <span class="pull-right">{{$category->name}}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Parameter</b> <span class="pull-right">{{$parameter[$category->parameter]}}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Tipe</b> <span class="pull-right">{{$type[$category->type]}}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Input</b> <span class="pull-right">{{$input[$category->input]}}</span>
                    </li>
                </ul>

                <p class="text-danger">* Pastikan menambah 1 sub kategori untuk tipe parameter input Jumlah Personal</p>
            </div>
            <div class="overlay hidden">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="nav-tabs-custom tab-primary">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#subcategory" data-toggle="tab">Sub Kategori</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="subcategory">
                    <div class="overlay-wrapper">
                        <a class="btn btn-primary pull-right btn-sm" href="#" onclick="adddetail()"><i
                                class="fa fa-plus"></i></a>
                        <table class="table table-bordered table-striped" id="table-subcategory">
                            <thead>
                                <tr>
                                    <th style="text-align:center" width="10">#</th>
                                    <th width="200">Nama</th>
                                    <th width="100">Tipe</th>
                                    <th width="100">Min</th>
                                    <th width="100">Max</th>
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
<div class="modal fade" id="add-detail" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Tambah Sub Kategori</h4>
            </div>
            <div class="modal-body">
                <form id="form" method="post" action="{{route('subcategory.store')}}" autocomplete="off">
                    {{ csrf_field() }}
                    <input type="hidden" name="category_id" value="{{$category->id}}" />
                    <input type="hidden" name="_method" />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="name">Nama</label>
                                <input type="text" name="name" class="form-control" placeholder="Nama" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="type">Tipe</label>
                                <select name="type" class="form-control select2" placeholder="Pilih Tipe" required>
                                    <option value=""></option>
                                    <option value="range">Range</option>
                                    <option value="yesno">Yes/No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="min">Min</label>
                                <input type="text" name="min" class="form-control numberfield" placeholder="Min"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="max">Max</label>
                                <input type="text" name="max" class="form-control numberfield" placeholder="Max"
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
        $('#add-detail .modal-title').html('Tambah Sub Kategori');
        $('#add-detail').modal('show');
        $('#form')[0].reset();
        $('#form').attr('action', '{{route('subcategory.store')}}');
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
    $(document).on("change", "#form select[name=type]", function () {
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
        $('#form select[name=type]').on('change', function () {
            $('#form').find('input[name=min]').closest('.row').hide();
            $('#form').find('input[name=max]').closest('.row').hide();
            if (this.value == 'yesno') {
                $('#form').find('input[name=min]').closest('.row').hide();
                $('#form').find('input[name=max]').closest('.row').hide();
            }

            if (this.value == 'range') {
                $('#form').find('input[name=min]').closest('.row').show();
                $('#form').find('input[name=max]').closest('.row').show();
            }
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
                [5, "asc"]
            ],
            ajax: {
                url: "{{url('admin/subcategory/read')}}",
                type: "GET",
                data: function (data) {
                    data.category_id = {{$category->id}};
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                },
                {
                    className: "text-right",
                    targets: [0, 3, 4]
                },
                {
                    className: "text-center",
                    targets: [5]
                },
                {
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item edit" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
                        <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                      </ul></div>`
                    },
                    targets: [5]
                }
            ],
            columns: [{
                    data: "no"
                },
                {
                    data: "name"
                },
                {
                    data: "type"
                },
                {
                    data: "min"
                },
                {
                    data: "max"
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
                url: `{{url('admin/subcategory')}}/${id}/edit`,
                method: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $('#subcategory .overlay').removeClass('hidden');
                },
            }).done(function (response) {
                $('#subcategory .overlay').addClass('hidden');
                if (response.status) {
                    $('#add-detail .modal-title').html('Ubah Menu');
                    $('#add-detail').modal('show');
                    $('#form')[0].reset();
                    $('#form .invalid-feedback').each(function () {
                        $(this).remove();
                    });
                    $('#form .form-group').removeClass('has-error').removeClass('has-success');
                    $('#form input[name=_method]').attr('value', 'PUT');
                    $('#form input[name=name]').attr('value', response.data.name);
                    $('#form select[name=type]').select2('val', response.data.type);
                    $('#form input[name=min]').attr('value', response.data.min);
                    $('#form input[name=max]').attr('value', response.data.max);
                    $('#form textarea[name=information]').summernote('code', response.data
                        .information);
                    $('#form').find('input[name=min]').closest('.row').hide();
                    $('#form').find('input[name=max]').closest('.row').hide();
                    if (response.data.type == 'yesno') {
                        $('#form').find('input[name=min]').closest('.row').hide();
                        $('#form').find('input[name=max]').closest('.row').hide();
                    }

                    if (response.data.type == 'range') {
                        $('#form').find('input[name=min]').closest('.row').show();
                        $('#form').find('input[name=max]').closest('.row').show();
                    }
                    $('#form').attr('action',
                        `{{url('admin/subcategory/')}}/${response.data.id}`);
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
        })
        $(document).on('click', '.delete', function () {
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
                title: 'Menghapus akses sub kategori?',
                message: 'Data yang telah dihapus tidak dapat dikembalikan',
                callback: function (result) {
                    if (result) {
                        var data = {
                            _token: "{{ csrf_token() }}"
                        };
                        $.ajax({
                            url: `{{url('admin/subcategory')}}/${id}`,
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
        })
    });

</script>
@endpush
