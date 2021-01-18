@extends('admin.layouts.app')

@section('title', 'Detail Pemeriksaan')
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
<li><a href="{{route('examination.index')}}">Pemeriksaan</a></li>
<li class="active">Detail</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-4">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Pemeriksaan</h3>
        <div class="pull-right box-tools">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="box-body box-profile">
        <ul class="list-group list-group-unbordered">
          <li class="list-group-item">
            <b>Nama</b> <span class="pull-right">{{$examination->name}}</span>
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
        <li class="active"><a href="#medicaldetail" data-toggle="tab">Detail</a></li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="medicaldetail">
          <div class="overlay-wrapper">
            <a class="btn btn-primary pull-right btn-sm" href="#" onclick="adddetail()"><i class="fa fa-plus"></i></a>
            <table class="table table-bordered table-striped" id="table-detail">
              <thead>
                <tr>
                  <th style="text-align:center" width="10">#</th>
                  <th width="200">Nama</th>
                  <th width="200">Input</th>
                  <th width="200">Status</th>
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
        <h4 class="modal-title">Tambah Pemeriksaan Detail</h4>
      </div>
      <div class="modal-body">
        <form id="form" method="post" action="{{route('examinationtype.store')}}" autocomplete="off">
          {{ csrf_field() }}
          <input type="hidden" name="examination_id" value="{{$examination->id}}" />
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
                <label class="control-label" for="input">Input</label>
                <select name="input" class="form-control select2" placeholder="Pilih Input" required>
                  <option value=""></option>
                  <option value="numeric">Angka</option>
                  <option value="string">Text</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="status">Status Aktif</label>
                <select name="status" class="form-control select2" placeholder="Pilih Status" required>
                  <option value=""></option>
                  <option value="1">Aktif</option>
                  <option value="0">Non-Aktif</option>
                </select>
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
        $('#add-detail .modal-title').html('Tambah Detail');
        $('#add-detail').modal('show');
        $('#form')[0].reset();
        $('#form input[name=name]').attr('value', '');
        $('#form input[name=status]').attr('checked', false);
        $('#form select[name=input]').select2('val','');
        $('#form').attr('action', '{{route('examinationtype.store')}}');
        $('#form input[name=_method]').attr('value', 'POST');
        $('#form .invalid-feedback').each(function () {
            $(this).remove();
        });
        $('#form').find('.form-group').removeClass('has-error').removeClass('has-success');
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
        $('input[name=status]').iCheck({
          checkboxClass: 'icheckbox_square-green',
          radioClass: 'iradio_square-green',
        });
        $(".numberfield").inputmask('decimal', {
            rightAlign: false
        });
        $('.select2').select2({
            allowClear: true
        });
        dataTable = $('#table-detail').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: false,
            responsive: true,
            order: [
                [3, "asc"]
            ],
            ajax: {
                url: "{{url('admin/examinationtype/read')}}",
                type: "GET",
                data: function (data) {
                    data.examination_id = {{$examination->id}};
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
                    targets: [3,4]
                },
                {
                    render: function (data, type, row) {
                      if (row.status == 1) {
                        return `<span class="label bg-green">Aktif</span>`
                      } else {
                        return `<span class="label bg-red">Non-Aktif</span>`
                      }
                    },
                    targets: [3]
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
                    targets: [4]
                }
            ],
            columns: [{
                    data: "no"
                },
                {
                    data: "name"
                },
                {
                    data: "input"
                },
                {
                    data: "status"
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
                        $('#medicaldetail .overlay').removeClass('hidden');
                    }
                }).done(function (response) {
                    $("#add-detail").modal('hide');
                    $('#medicaldetail .overlay').addClass('hidden');
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
                    $('#site .overlay').addClass('hidden');
                    $("#site_id").select2('val', '');
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
                url: `{{url('admin/examinationtype')}}/${id}/edit`,
                method: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $('#medicaldetail .overlay').removeClass('hidden');
                },
            }).done(function (response) {
                $('#medicaldetail .overlay').addClass('hidden');
                if (response.status) {
                    $('#add-detail .modal-title').html('Ubah Detail');
                    $('#add-detail').modal('show');
                    $('#form')[0].reset();
                    $('#form .invalid-feedback').each(function () {
                        $(this).remove();
                    });
                    $('#form .form-group').removeClass('has-error').removeClass('has-success');
                    $('#form input[name=_method]').attr('value', 'PUT');
                    $('#form input[name=name]').attr('value', response.data.name);
                    $('#form select[name=input]').select2('val',response.data.input);
                    $('#form select[name=status]').select2('val',response.data.status);
                    $('#form').attr('action',
                        `{{url('admin/examinationtype/')}}/${response.data.id}`);
                }
            }).fail(function (response) {
                var response = response.responseJSON;
                $('#medicaldetail .overlay').addClass('hidden');
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
                title: 'Menghapus detail pemeriksaan?',
                message: 'Data yang telah dihapus tidak dapat dikembalikan',
                callback: function (result) {
                    if (result) {
                        var data = {
                            _token: "{{ csrf_token() }}"
                        };
                        $.ajax({
                            url: `{{url('admin/examinationtype')}}/${id}`,
                            dataType: 'json',
                            data: data,
                            type: 'DELETE',
                            beforeSend: function () {
                                $('#medicaldetail .overlay').removeClass(
                                'hidden');
                            }
                        }).done(function (response) {
                            if (response.status) {
                                $('#medicaldetail .overlay').addClass('hidden');
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
                            $('#medicaldetail .overlay').addClass('hidden');
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