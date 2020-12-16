@extends('admin.layouts.app')

@section('title', 'Ubah Laporan Tagihan')
@push('breadcrump')
    <li><a href="{{route('invoice.index')}}">Laporan Tagihan</a></li>
    <li class="active">Ubah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Ubah Laporan Tagihan</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form id="form" action="{{route('invoice.update',['id'=>$invoice->id])}}" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
               <input type="hidden" name="_method" value="put">
                <div class="box-body">
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Tanggal Permohonan<b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="invoice_date" name="invoice_date" placeholder="Tanggal Permohonan" required value="{{$invoice->invoice_date}}">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Rekanan <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="partner_id" name="partner_id" data-placeholder="Pilih Rekanan" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Tanggal Penerimaan</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="receive_date" name="receive_date" placeholder="Tanggal Penerimaan" required value="{{$invoice->receive_date}}">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Dokumen</label>
                    <div class="col-sm-6">
                      <table class="table table-bordered table-striped" id="table-prescriptions">
                          <thead>
                              <th>Nama</th>
                              <th>Status</th>
                              <th>Catatan</th>
                          </thead>
                          <tbody>
                            @foreach ($documents as $document)
                            @foreach ($invoice->document as $invoicedocument)
                            @if($invoicedocument->document_id == $document->id)
                            <tr>
                            <td><input type="hidden" name="invoice_document[]" value="{{$document->id}}"/>{{$document->name}}</td>
                              <td class="text-center"><input type="checkbox" name="document_status[{{$document->id}}]" @if($invoicedocument->status == 1) checked @endif></td>
                            <td><input type="text" name="notes[{{$document->id}}]" class="form-control" value="{{$invoicedocument->notes}}"/></td>
                            </tr>
                            @endif
                            @endforeach
                            @endforeach
                          </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Status <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                       <select id="status" name="status" class="form-control select2" placeholder="Pilih Status" required>
                          <option value=""></option>
                          <option value="Request" @if($invoice->status == 'Request') selected @endif>Request</option>
                          <option value="Progress" @if($invoice->status == 'Progress') selected @endif>On Progress</option>
                          <option value="Closed" @if($invoice->status == 'Closed') selected @endif>Closed</option>
                       </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="close_date" class="col-sm-2 control-label">Tanggal Akhir</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="close_date" name="close_date" placeholder="Tanggal Akhir" required value="{{$invoice->close_date}}" disabled>
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
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
  $(document).ready(function(){
    $('.select2').select2().select2('readonly',true);
    $('input[name=invoice_date],input[name=receive_date],input[name=close_date]').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
      $('input[name^=document_status]').iCheck({
          checkboxClass: 'icheckbox_square-green',
          radioClass: 'iradio_square-green',
      });
      $('input[name^=document_status]').on('ifChanged',function(){

        var length = $('input[name^=document_status]').length;
        var check = 0;
        $( "input[name^=document_status]" ).each(function( index ) {
            if(this.checked){
              check++;
            }
        });
        $('input[name=close_date]').val('')
        $('input[name=close_date]').prop('disabled',true);
        $('input[name=close_date]').prop('required',false);
        if(length == check){
          $('select[name=status]').select2('val','Closed');
          $('input[name=close_date]').prop('disabled',false);
          $('input[name=close_date]').prop('required',true);
        }
        if(length > check){
          $('select[name=status]').select2('val','Progress');
        }
        if(check == 0){
          $('select[name=status]').select2('val','Request');
        }
      });
    $( "#partner_id" ).select2({
        ajax: {
          url: "{{route('partner.select')}}",
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
      @if($invoice->partner)
      $("#partner_id").select2('data',{id:{{$invoice->partner_id}},text:'{{$invoice->partner->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#partner_id", function () {
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
               $('.overlay').removeClass('hidden');
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
          })
        }
      });
  });
</script>
@endpush

