@extends('admin.layouts.app')

@section('title', 'Tambah Faskes')
@push('breadcrump')
<li><a href="{{route('partner.index')}}">Faskes</a></li>
<li class="active">Tambah</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Tambah Faskes</h3>
                <!-- tools box -->
                <div class="pull-right box-tools">
                    <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i
                            class="fa fa-save"></i></button>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                </div>
                <!-- /. tools -->
            </div>
            <div class="box-body">
                <form id="form" action="{{ route('partner.store') }}" class="form-horizontal" method="post"
                    autocomplete="off">
                    {{ csrf_field() }}
                    <div class="well well-sm">
                        <div class="form-group">
                            <label for="site_id" class="col-sm-2 control-label">Distrik <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="site_id" name="site_id" placeholder="Pilih Distrik">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="partner_category_id" class="col-sm-2 control-label">Kategori <b
                                    class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="partner_category_id" name="partner_category_id"
                                    placeholder="Pilih Kategori" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Nama <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nama"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address" class="col-sm-2 control-label">Alamat <b
                                    class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <textarea name="address" id="address" class="form-control"
                                    placeholder="Alamat"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="collaboration_status">Status
                                Kerjasama</label>
                            <div class="col-sm-4">
                                <label><input class="form-control status" type="checkbox" name="collaboration_status">
                                    <i></i></label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="overlay hidden">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('.status').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
        $("#site_id").select2({
            ajax: {
                url: "{{route('site.select')}}",
                type:'GET',
                dataType: 'json',
                data: function (term,page) {
                return {
                    name:term,
                    page:page,
                    limit:30,
                    data_manager:{{$accesssite}},
                    site_id : {{$siteinfo->id}}
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

        $("#partner_category_id").select2({
            ajax: {
            url: "{{route('partnercategory.select')}}",
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
        $(document).on("change", "#partner_category_id", function () {
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
                        $('.overlay').removeClass('hidden');
                    }
                }).done(function (response) {
                    $('.overlay').addClass('hidden');
                    if (response.status) {
                        document.location = response.results;
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
                    $('.overlay').addClass('hidden');
                    var response = response.responseJSON;
                    $.gritter.add({
                        title: 'Error!',
                        text: response.message,
                        class_name: 'gritter-error',
                        time: 1000,
                    });
                })
            }
        });
    });

</script>
@endpush