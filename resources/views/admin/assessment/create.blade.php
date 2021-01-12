@extends('site.layouts.app')

@section('title', 'Tambah Assessment (Individu)')
@push('breadcrump')
<li><a href="{{route('assessment.index', $site)}}">Assessment (Individu)</a></li>
<li class="active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<style>
  .direct-chat-messages {
    height: 100% !important;
  }

  .right .direct-chat-text {
    margin-right: 20px !important;
  }
</style>
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title">Tambah Assessment (Individu)</h3>
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
        <form id="form" action="{{route('assessment.store', $site)}}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <!-- Conversations are loaded here -->
          <div class="direct-chat-messages assessment-msg">

            {{-- Information Section Message --}}
            <div class="information-msg">
            </div>
            {{-- .Information Section Message --}}
            {{-- Question Message --}}
            <div class="question-msg">

            </div>
            {{-- .Question Message --}}

            {{-- Answer Message --}}
            <div class="answer-msg form-inline">
            </div>
            {{-- .Answer Message --}}
          </div>
          <!--/.direct-chat-messages-->
        </form>
      </div>
      <div class="box-footer">
        <div class="input-group">
          <input type="text" name="message" placeholder="Type Message ..." class="form-control">
          <span class="input-group-btn">
            <button type="button" class="btn btn-primary btn-flat" onclick="reply()">Send</button>
          </span>
        </div>
      </div>
      <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
@foreach($questions as $question)
@foreach($question->answer as $answer)
<div class="modal fade" id="modalinfo{{$answer->id}}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title">Cara Pengisian</h5>
      </div>
      <div class="modal-body">
        {!!$answer->information!!}
      </div>
    </div>
  </div>
</div>
@endforeach
@endforeach
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
  let assessment_data = @json($questions, JSON_PRETTY_PRINT);
  let information_data = [];
  let question_data = [];
  let question_parameter = {
    offset: 0,
    limit: 0,
  };
  let message = [];
  let answer_choice = [];
  function informationData(params) {
    $.each(params, function(i, message) {
      if (message.type == 'Informasi') {  
        information_data.push(message);
      }
    });
  }
  function questionData(params) {
    $.each(params, function(i, message) {
      if (message.type == 'Pertanyaan') {  
        question_data.push(message);
      }
    });
  }
  function reply() {
    var start = $('input[name=message]').val();
    if (start === '/mulai') {
      $('.information-msg').empty();
      message.push(question(question_data));
      console.log(question_parameter);
      $('.information-msg').append(message);
      $('input[name=message]').val('')
    } else {
      let message_not_found = [{
        type: 'Informasi',
        description: 'Maaf kami tidak mengerti perintah ini.'
      }];
      $('.information-msg').empty();
      message.push(information(message_not_found));
      $('.information-msg').append(message);
    }
  }
  function question(params) {
    html = '';
    countData = params.length;
    $.each(params, function(i, message) {
      if (question_parameter.limit == i) {
        html += `<div class="direct-chat-msg">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">Bot Assessment</span>
                  </div>
                  <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assessment Bot">
                  <div class="direct-chat-text pull-left">${message.description}</div>
                </div>`
        if (message.answer.length > 0) {
          html += `<div class="direct-chat-msg right">
                    <div class="direct-chat-info clearfix">
                      <span class="direct-chat-name pull-right">User</span>
                    </div>
                    <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assessment Bot">
                    <div class="pull-right form-inline">`
          $.each(message.answer, function(i, answer) {
            switch (answer.answer_type) {
              case 'checkbox':
                html += `<div class="checkbox direct-chat-text">
                          <input type="checkbox" value="${answer.id}">
                          ${answer.description}
                        </div>`
                break;
              case 'radio':
              html += `<div class="radio direct-chat-text">
                        <input type="radio" name="answer_radio" id="answerRadio${answer.id}" value="${answer.id}">
                        ${answer.description}
                      </div>`
              break;
            
              default:
                break;
            }
          });
          html += `<button type="button" class="btn btn-default" data-question_id="${message.id}" data-answer_type="${message.answer[0].answer_type}" onclick="answer(this)">Pilih</button></div></div>`;
        }
      }
    });
    return html;
  }

  function information(params) {
    html = '';
    countData = params.length;
    $.each(params, function(i, message) {
      if (message.type == 'Informasi') {  
        html += `<div class="direct-chat-msg">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">Bot Assessment</span>
                  </div>
                  <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assesment Bot">
                  <div class="direct-chat-text pull-left">${message.description}${i === (countData - 1) ? '<br>Ketik <b>/mulai</b> untuk melakukan assessment' : ''}</div>
                </div>`;
      }
    });
    return html;
  }

  function answer(params) {
    console.log($(params).data('answer_type'));
  }
  $(document).ready(function(){
    question_param = {
      limit: 1,
      workforce: 1,
      user: 1
    };
    informationData(assessment_data);
    questionData(assessment_data);
    $('.information-msg').empty();
    message.push(information(information_data));
    $('.information-msg').append(message);
     $('input[name=report_date]').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
      })
      $('input[name=report_date]').on('change', function(){
        if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
          $(this).closest("form").validate().form();
        }
      });
      $('.range').inputmask('decimal', {
        rightAlign: false
      });
      $('.select2').select2({
        allowClear:true
      });
      $('.select2').on('change',function(){
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