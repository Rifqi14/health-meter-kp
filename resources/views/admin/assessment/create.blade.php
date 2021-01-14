@extends('admin.layouts.app')

@section('title', 'Tambah Assessment (Individu)')
@push('breadcrump')
<li><a href="{{route('assessment.index')}}">Assessment (Individu)</a></li>
<li class="active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<style>
  .direct-chat-messages {
    height: 400px !important;
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
        <form id="form" action="{{route('assessment.store')}}" class="form-horizontal" method="post" autocomplete="off">
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
  let question_child_data = [];
  let question_parameter = {
    offset: 0,
    limit: 0,
  };
  let message = [];
  let answer_choice = [];
  // To assign QuestionType Informasi to information_data variable
  function informationData(params) {
    $.each(params, function(i, message) {
      if (message.type == 'Informasi') {  
        information_data.push(message);
      }
    });
  }
  // To assign QuestionType Question Parent to question_data variable
  function questionData(params) {
    $.each(params, function(i, message) {
      if (message.type == 'Pertanyaan' && message.is_parent == 0) {  
        question_data.push(message);
      }
    });
  }
  // To assign QuestionType Question Child to question_child_data variable
  function questionChildData(params) {
    $.each(params, function(i, message) {
      if (message.type == 'Pertanyaan' && message.is_parent == 1) {  
        question_child_data.push(message);
      }
    });
  }
  // To Start conversation with bot
  // Can be change
  function reply() {
    var start = $('input[name=message]').val();
    if (start === '/mulai') {
      rerenderMsg(question(question_data));
      $('input[name=message]').val('');
      $('.box-footer').addClass('hidden');
    } else {
      let message_not_found = [{
        type: 'Informasi',
        description: 'Maaf kami tidak mengerti perintah ini.'
      }];
      rerenderMsg(information(message_not_found));
    }
    $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000);
  }
  // To Render question from question_data variable to message bubble
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
        html += `<div class="direct-chat-msg right">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-right">User</span>
                  </div>
                  <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assessment Bot">
                  <div class="pull-right form-inline">`
        if (message.answer.length > 0) {
          $.each(message.answer, function(i, answer) {
            switch (answer.answer_type) {
              case 'checkbox':
                html += `<div class="checkbox direct-chat-text">
                          <input type="checkbox" name="answer_choice" value="${answer.id}" data-description="${answer.description}">
                          ${answer.description}
                        </div>`
                break;
              case 'radio':
              html += `<div class="radio direct-chat-text">
                        <input type="radio" name="answer_choice" id="answerRadio${answer.id}" value="${answer.id}" data-description="${answer.description}">
                        ${answer.description}
                      </div>`
              break;
            
              default:
                break;
            }
          });
          html += `<button type="button" class="btn btn-default" data-question="${message.description}" data-question_id="${message.id}" data-answer_type="${message.answer[0].answer_type}" onclick="answer(this)">Kirim</button></div></div>`;
        } else {
          html += `<input type="text" class="form-control direct-chat-text"name="answer_choice" id="freeText${message.id}" placeholder="...">`
          html += `<button type="button" class="btn btn-default" data-question="${message.description}" data-question_id="${message.id}" data-answer_type="freetext" onclick="answer(this)">Kirim</button></div></div>`;
        }
      }
    });
    return html;
  }
  // To Render information from information_data variable to message bubble
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
    html += `<div class="direct-chat-msg right">
              <div class="direct-chat-info clearfix">
                <span class="direct-chat-name pull-right">User</span>
              </div>
              <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assessment Bot">
              <div class="pull-right form-inline">
                <input type="text" name="message" placeholder="Type Message ..." class="form-control direct-chat-text" value="/mulai" readonly>
                <button type="button" class="btn btn-default" onclick="reply()">Send</button>
              </div></div>`;
    return html;
  }
  // To Render Question child from question_child_data variable to message bubble
  function child(params) {
    html = '';
    $.each(question_child_data, function(i, child){
      if (child.question_parent_code == params.question_id && child.answer_parent_code == params.answer_id) {
        html += `<div class="direct-chat-msg">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">Bot Assessment</span>
                  </div>
                  <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assessment Bot">
                  <div class="direct-chat-text pull-left">${child.description}</div>
                </div>`
        html += `<div class="direct-chat-msg right">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-right">User</span>
                  </div>
                  <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assessment Bot">
                  <div class="pull-right form-inline">`
        if (child.answer.length > 0) {
          $.each(child.answer, function(i, answer) {
            switch (answer.answer_type) {
              case 'checkbox':
                html += `<div class="checkbox direct-chat-text">
                          <input type="checkbox" name="answer_choice" value="${answer.id}" data-description="${answer.description}">
                          ${answer.description}
                        </div>`
                break;
              case 'radio':
              html += `<div class="radio direct-chat-text">
                        <input type="radio" name="answer_choice" id="answerRadio${answer.id}" value="${answer.id}" data-description="${answer.description}">
                        ${answer.description}
                      </div>`
              break;
            
              default:
                break;
            }
          });
          html += `<button type="button" class="btn btn-default" data-question="${child.description}" data-question_id="${child.id}" data-answer_type="${child.answer[0].answer_type}" onclick="answer(this)">Kirim</button></div></div>`;
        } else {
          html += `<input type="text" class="form-control direct-chat-text" name="answer_choice" id="freeText${child.id}" placeholder="...">`
          html += `<button type="button" class="btn btn-default" data-question="${child.description}" data-question_id="${child.id}" data-answer_type="freetext" onclick="answer(this)">Kirim</button></div></div>`;
        }
      }
    });
    return html;
  }
  // To take value of choosen answer
  function answer(params) {
    var choice = {
      question: $(params).data('question'),
      question_id: $(params).data('question_id'),
      answer_id: answerValue($(params).data('answer_type')),
      label: answerDesc($(params).data('answer_type'))
    };
    answer_choice.push(choice);
    if (child(choice)) {
      message[message.length - 1] = `<div class="direct-chat-msg">
                                      <div class="direct-chat-info clearfix">
                                        <span class="direct-chat-name pull-left">Bot Assessment</span>
                                      </div>
                                      <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assessment Bot">
                                      <div class="direct-chat-text pull-left">${answer_choice[answer_choice.length - 1].question}</div>
                                    </div>
                                    <div class="direct-chat-msg right">
                                      <div class="direct-chat-info clearfix">
                                        <span class="direct-chat-name pull-right">User</span>
                                      </div>
                                      <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assessment Bot">
                                      <div class="direct-chat-text pull-right">${answer_choice[answer_choice.length - 1].label}</div>
                                    </div>`;
      rerenderMsg(child(choice));
    } else {
      ++question_parameter.limit;
      message[message.length - 1] = `<div class="direct-chat-msg">
                                      <div class="direct-chat-info clearfix">
                                        <span class="direct-chat-name pull-left">Bot Assessment</span>
                                      </div>
                                      <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assessment Bot">
                                      <div class="direct-chat-text pull-left">${answer_choice[answer_choice.length - 1].question}</div>
                                    </div>
                                    <div class="direct-chat-msg right">
                                      <div class="direct-chat-info clearfix">
                                        <span class="direct-chat-name pull-right">User</span>
                                      </div>
                                      <img class="direct-chat-img" src="{{ asset('assets/user/1.png') }}" alt="Assessment Bot">
                                      <div class="direct-chat-text pull-right">${answer_choice[answer_choice.length - 1].label}</div>
                                    </div>`;
      rerenderMsg(question(question_data));
    }
    $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000);
  }
  // To get value from data button
  function answerValue(params) {
    let answer_id = [];
    switch (params) {
      case 'checkbox':
        answer_id = $('input:checkbox[name=answer_choice]:checked').map(function(){
            return this.value;
        }).get();
        break;
      
      case 'radio':
        answer_id = $('input:radio[name=answer_choice]:checked').val();    
        break;

      case 'select':
        answer_id = $('select[name=answer_choice]').val();
        break;

      case 'freetext':
        answer_id = $('input:text[name=answer_choice]').val();
        break;

      default:
        answer_id = $('input[name=answer_choice]').val();
        break;
    }
    return answer_id;
  }
  // To get label from answer data button
  function answerDesc(params) {
    let answer_id = [];
    switch (params) {
      case 'checkbox':
        answer_id = $('input:checkbox[name=answer_choice]:checked').map(function(){
            return $(this).data('description');
        }).get();
        break;
      
      case 'radio':
        answer_id = $('input:radio[name=answer_choice]:checked').data('description');    
        break;

      case 'select':
        answer_id = $('select[name=answer_choice]').data('description');
        break;

      case 'freetext':
        answer_id = $('input:text[name=answer_choice]').val();
        break;

      default:
        answer_id = $('input[name=answer_choice]').val();
        break;
    }
    return answer_id;
  }
  // Re render message bubble, can call everywhere to refresh
  function rerenderMsg(params) {
    $('.information-msg').empty();
    message.push(params);
    $('.information-msg').append(message);
  }
  $(document).ready(function(){
    question_param = {
      limit: 1,
      workforce: 1,
      user: 1
    };
    informationData(assessment_data);
    questionData(assessment_data);
    questionChildData(assessment_data);
    rerenderMsg(information(information_data));
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
        var data = {
                      _token: "{{ csrf_token() }}",
                      answer_choice: answer_choice
                    };
        $.ajax({
          url:$('#form').attr('action'),
          method:'post',
          data: data,
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