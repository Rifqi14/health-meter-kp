@extends('admin.layouts.app')

@section('title', 'Import Instansi')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.css')}}" rel="stylesheet">
<style type="text/css">
  .overlay-wrapper {
    position: relative;
  }
</style>
@endsection
@push('breadcrump')
<li><a href="{{route('agency.index')}}">Instansi</a></li>
<li class="active">Import</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary" id="agency-preview">
      <div class="box-header">
        <h3 class="box-title">Pratinjau Import</h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
          <a onclick="addImport()" class="btn btn-success btn-sm" data-toggle="tooltip" title="Tambah">
            <i class="fa fa-upload"></i>
          </a>
          <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('agency.storemass')}}">
        </form>
        <table class="table table-striped table-bordered" style="width:100%" id="table-item">
          <thead>
            <tr>
              <th width="50">Kode</th>
              <th width="250">Nama</th>
              <th width="50">Autentikasi</th>
              <th width="50">Host</th>
              <th width="50">Port</th>
              <th width="20">Status</th>
              <th width="100">Error</th>
              <th width="20">#</th>
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
<div class="modal" id="select-file" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="overlay-wrapper">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Pilih File</h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal no-margin" id="form-import" action="#" method="post">
            {{ csrf_field() }}
            <div class="row">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="file">File Excel</label>
                <div class="col-sm-9">
                  <input type="file" class="form-control" id="file" name="file" required accept=".xlsx" />
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-import" type="submit" class="btn btn-success btn-sm" title="Import"><i class="fa fa-upload"></i></button>
        </div>
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
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer/theme.min.js')}}"></script>
<script type="text/javascript">
  var items = {},count=0;
function addImport(){
    $('#form-import')[0].reset();
    $('#form-import').find('.help-block').remove();
    //$('#file').closest('.main-file-input').find('.remove').trigger('click');
    $('#form-import .form-group').removeClass('has-error').removeClass('has-success');
    $('#select-file').modal('show');
}
function loadItem(table_item){
    count=0;
    $.each(items, function() {
        table_item.row.add([
                this.code,
                this.name,
                this.autentikasi,
                this.host,
                this.port,
                this.status,
                this.error,
                this.is_import,
        ]).draw(false);
        count++;
    })
}
$(function(){
    $("#file").fileinput({
        browseClass: "btn btn-default",
        showRemove: false,
        showUpload: false,
        allowedFileExtensions: ["xlsx"],
        dropZoneEnabled: false,
        theme:'explorer'
    });
    $(document).on("change", "#file", function () {
        if (!$.isEmptyObject($('#form-import').validate().submitted)) {
          $('#form-import').validate().form();
        }
    }); 
    var table_item = $('#table-item').DataTable({
        responsive:true,
        filter:false,
        info:false,
        lengthChange:true,
        autoWidth:false,
        paging:true,
        order: [[ 7, "asc" ]],
        columnDefs: [
            {
                orderable: false,targets:[0,1,2,3,4,5,6,7]
            },
            { className: "text-center", targets: [5,7] },
            {
                render:function( data, type, row ) {
                    return `<span class="label ${data == 0 ? 'bg-red' : 'bg-green'}">${data == 0 ? 'Non-Aktif' : 'Aktif'}</span>`
                },targets: [5]
            },
            {
                render:function( data, type, row ) {
                    return `<span class="label ${data == 0 ? 'bg-red' : 'bg-green'}">${data == 0 ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>'}</span>`
                },targets: [7]
            },
        ],
    });

    $("#form-import").validate({
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
                error.insertAfter(element.closest('.file-input'));
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
                url:"{{route('agency.preview')}}",
                method:'post',
                data: new FormData($('#form-import')[0]),
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend:function(){
                    $('#select-file .overlay').removeClass('hidden');
                }
            }).done(function(response){
                $('#select-file .overlay').addClass('hidden');
                $("#select-file").modal('hide');
                items = {};
                $.each(response.data,function(){
                    items[this.index] = this;
                });
                table_item.clear().draw();
                loadItem(table_item);
            })
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
                error.insertAfter(element.parent());
            }else if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } 
            else{
                error.insertAfter(element);
            }
        },
        submitHandler: function() { 
            if (count == 0) {
                $.gritter.add({
                    title: 'Peringatan',
                    text:  'Belum ada item yang ditambahkan',
                    class_name: 'gritter-error',
                }); 
                return false;
            }
            var agency =[];
            $.each(items, function() {
                if(this.is_import == 1){
                    agency.push(this);
                }
            });
            //waitingDialog.show('Silahkan tunggu sebentar...');
            $.ajax({
                url:$('#form').attr('action'),
                dataType: 'json',
                type:'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    agency: JSON.stringify(agency)
                },
                beforeSend:function(){
                    $('#agency-preview .overlay').removeClass('hidden');
                }
            }).done(function(response){
                $('.overlay').addClass('hidden');
                if(response.status){
                  document.location = response.results;
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
              $('.overlay').addClass('hidden');
              var response = response.responseJSON;
              $.gritter.add({
                  title: 'Error!',
                  text: response.message,
                  class_name: 'gritter-error',
                  time: 1000,
              });
          })	;		
        }
    });
})
</script>
@endpush