@extends('admin.layouts.app')

@section('title', 'Detail Laporan Medis')
@push('breadcrump')
    <li class="active">Detail</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<style type="text/css">
  #map {
       height: 300px;
       border: 1px solid #CCCCCC;
     }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Detil Laporan Medis</h3>
          <!-- tools box -->
          <div class="pull-right box-tools">
            <!-- <button form="form" type="submit" class="btn btn-sm btn-primary" title="Simpan"><i class="fa fa-save"></i></button>
            -->
          @if($medicalrecord->medicalaction->code == 'resepdokter' || $medicalrecord->medicalaction->code == 'istirahatmandiri')
            <a class="btn btn-sm btn-primary" title="Print" href="{{ route('coverletter.print',['id'=>$medicalrecord->id])}}" target="_blank"><i class="fa fa-print"></i></a>
          @endif  
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="box-body">
            <form id="form" action="{{route('coverletter.update',['id'=>$medicalrecord->id])}}" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
               <input type="hidden" name="_method" value="put">
               <div class="form-group">
                <label for="date" class="col-sm-2 control-label">No</label>
                <div class="col-sm-6">
                  <p class="form-control-static">{{$medicalrecord->record_no}}</p>
                </div>
              </div>
               <div class="form-group">
                  <label for="date" class="col-sm-2 control-label">Tanggal</label>
                  <div class="col-sm-6">
                    <p class="form-control-static">{{$medicalrecord->date}}</p>
                  </div>
                </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Nama</label>
                    <div class="col-sm-6">
                      
                        <p class="form-control-static">{{$medicalrecord->employee->name}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Pasien</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{isset($medicalrecord->employeefamily->name)?$medicalrecord->employeefamily->name:$medicalrecord->employee->name}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="nid" class="col-sm-2 control-label">NID</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{$medicalrecord->employee->nid}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="nid" class="col-sm-2 control-label">Usia</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{Carbon\Carbon::parse($medicalrecord->employee->birth_date)->age}} Tahun</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Jenis Kelamin</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{$medicalrecord->employee->gender=='m'?'Laki Laki':'Perempuan'}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="nid" class="col-sm-2 control-label">Telepon</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{$medicalrecord->employee->phone}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="nid" class="col-sm-2 control-label">Alamat</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{$medicalrecord->employee->address}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="complaint" class="col-sm-2 control-label">Keluhan</label>
                    <div class="col-sm-6">
                        {!!$medicalrecord->complaint!!}
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="complaint" class="col-sm-2 control-label">Diagnosa</label>
                    <div class="col-sm-6">
                      <table class="table table-striped table-bordered">
                        <tr>
                          <th>Kode</th>
                          <th>Nama</th>
                        </tr>
                        @foreach($medicalrecord->medicalrecorddiadnosis as $medicalrecorddiadnosis)
                        <tr>
                          <td>{{$medicalrecorddiadnosis->diagnosis->code}}</td>
                          <td>{{$medicalrecorddiadnosis->diagnosis->name}}</td>
                        </tr>
                        @endforeach
                      </table>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="action" class="col-sm-2 control-label">Action</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{@$medicalrecord->medicalaction->name}}</p>
                    </div>
                  </div>
                  @if($medicalrecord->partner)
                  <div class="form-group">
                    <label for="action" class="col-sm-2 control-label">Partner</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{$medicalrecord->partner->name}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="action" class="col-sm-2 control-label">Telepon</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{$medicalrecord->partner->phone}}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="action" class="col-sm-2 control-label">Alamat</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{{$medicalrecord->partner->address}}</p>
                    </div>
                  </div>
                  @endif

                  @if(count($medicalrecord->medicalrecordpresciption) > 0)
                  <div class="form-group">
                    <label for="action" class="col-sm-2 control-label">Resep</label>
                    <div class="col-sm-6">
                      <table class="table table-striped table-bordered">
                        <tr>
                          <th>Obat</th>
                          <th>Intruksi</th>
                        </tr>
                        @foreach($medicalrecord->medicalrecordpresciption as $medicalrecordpresciption)
                        <tr>
                          <td>{{$medicalrecordpresciption->prescribed}}</td>
                          <td>{{$medicalrecordpresciption->instruction}}</td>
                        </tr>
                        @endforeach
                      </table>
                    </div>
                  </div>
                  @endif
                  <div class="form-group">
                    <label for="print_status" class="col-sm-2 control-label">Status Print<b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{!!$medicalrecord->print_status==0?'<span class="label label-warning">Belum Dicetak</span>':'<span class="label label-success">Sudah Dicetak</span>'!!}</p>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="status" class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">{!!$medicalrecord->status=='Request'?'<span class="label label-warning">Request</span>':'<span class="label label-success">Closed</span>'!!}</p>
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
<div class="modal fade" id="create-print" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header no-print">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
				<h4 class="modal-title">Cetak</h4>
			</div>							
			<div class="modal-body">			
				<div class="row">
					<iframe id="bodyReplace" scrolling="no" allowtransparency="true" style="width: 69%; border-width: 0px; position: relative; margin: 0 auto; display: block;" onload="this.style.height=(this.contentDocument.body.scrollHeight+45) +'px';"></iframe>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
  function printpreview(id) {
      $('.overlay').removeClass('hidden');
      $.ajax({
          url: `{{url('admin/coverletter')}}/${id}`,
          method: 'GET',
          success:function( response ) {
              $('.overlay').addClass('hidden');
              var iframe = document.getElementById('bodyReplace');
              iframe = iframe.contentWindow || ( iframe.contentDocument.document || iframe.contentDocument);
              iframe.document.open();
              iframe.document.write(response);
              iframe.document.close();
          }
      });
  }
  $(function(){
    $('.select2').select2();
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