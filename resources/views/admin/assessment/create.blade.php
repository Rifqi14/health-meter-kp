@extends('admin.layouts.app')

@section('title', 'Tambah Assessment')
@push('breadcrump')
<li><a href="{{route('assessment.index')}}">Assessment</a></li>
<li class="active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<style>

  .direct-chat-messages {
    height: 400px !important;
  }
  .direct-chat-text{
    margin-right: 20% !important;
  }
  .right .direct-chat-text {
    margin-right: 10px !important;
    float: right;
  }
  .checkbox input[type="checkbox"], .checkbox-inline input[type="checkbox"], .radio input[type="radio"], .radio-inline input[type="radio"] {
      position: unset;
      margin-left: 0;
  }
  .dot-typing {
    position: relative;
    left: -9999px;
    width: 8px;
    height: 8px;
    border-radius: 5px;
    background-color: #444;
    color: #444;
    box-shadow: 9984px 0 0 0 #444, 9999px 0 0 0 #444, 10014px 0 0 0 #444;
    animation: dotTyping 1.5s infinite linear;
  }

  @keyframes dotTyping {
    0% {
      box-shadow: 9984px 0 0 0 #444, 9999px 0 0 0 #444, 10014px 0 0 0 #444;
    }
    16.667% {
      box-shadow: 9984px -10px 0 0 #444, 9999px 0 0 0 #444, 10014px 0 0 0 #444;
    }
    33.333% {
      box-shadow: 9984px 0 0 0 #444, 9999px 0 0 0 #444, 10014px 0 0 0 #444;
    }
    50% {
      box-shadow: 9984px 0 0 0 #444, 9999px -10px 0 0 #444, 10014px 0 0 0 #444;
    }
    66.667% {
      box-shadow: 9984px 0 0 0 #444, 9999px 0 0 0 #444, 10014px 0 0 0 #444;
    }
    83.333% {
      box-shadow: 9984px 0 0 0 #444, 9999px 0 0 0 #444, 10014px -10px 0 0 #444;
    }
    100% {
      box-shadow: 9984px 0 0 0 #444, 9999px 0 0 0 #444, 10014px 0 0 0 #444;
    }
  }
</style>
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary direct-chat direct-chat-primary">
      <div class="box-header">
        <h3 class="box-title">Tambah Assessment</h3>
        <!-- tools box -->
        <!-- /. tools -->
      </div>
      <div class="box-body">
        <form id="form" action="{{route('assessment.store')}}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="direct-chat-messages">
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
  var assessments = [];
  var questions = [];
  var question_childs = [];
  var answers = [];
  var answer_questions = [];
  var start = 0;
  $.each(@json($questions, JSON_PRETTY_PRINT), function(i, message) {
    if (message.is_parent == 0) {  
        questions.push(message);
    }
  });
  $.each(@json($questions, JSON_PRETTY_PRINT), function(i, message) {
    if (message.is_parent == 1) {  
        question_childs[message.answer_parent_code] = message;
    }
  });
  $.each(@json($questions, JSON_PRETTY_PRINT), function(i, message) { 
      assessments[message.id] = message;
  });
  $.each(@json($answers, JSON_PRETTY_PRINT), function(i, message) { 
    if(!answers[message.assessment_question_id]){
      answers[message.assessment_question_id] = [];
      answers[message.assessment_question_id].push(message);
    }
    else{
      answers[message.assessment_question_id].push(message);
    }
    
  });
  $.each(@json($answers, JSON_PRETTY_PRINT), function(i, message) { 
    answer_questions[message.id] = message;
  });
  function loader(){
    if(start == questions.length){
      $.ajax({
          url:'{{route('assessment.check')}}',
          method:'post',
          data: new FormData($('#form')[0]),
          processData: false,
          contentType: false,
          dataType: 'json', 
          beforeSend:function(){
            $('.direct-chat-messages').append(`
                <div class="direct-chat-msg loader">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">Bot Assessment</span>
                  </div>
                  <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
                  <div class="direct-chat-text">
                    <div style="padding:5px 0 5px 15px">
                      <div class="dot-typing"></div>
                    </div>
                  </div>
                </div>
              `);
          }
        }).done(function(response){
              $('.loader').remove();
              if(response.status){
                  $('.direct-chat-messages').append(`
                  <div class="direct-chat-msg loader">
                    <div class="direct-chat-info clearfix">
                      <span class="direct-chat-name pull-left">Bot Assessment</span>
                    </div>
                    <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
                    <div class="direct-chat-text">
                      ${response.message} 
                      </div>
                  </div>
                `);
                $('.direct-chat-messages').append(`
                  <div class="direct-chat-msg right" id="finish_answer">
                    <div class="direct-chat-info clearfix">
                      <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
                    </div>
                    <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
                    <div class="direct-chat-text pull-right">
                      <input type="radio" value="1" onclick="finish(this)">
                      Ya <br/><input type="radio" value="0" onclick="finish(this)"> Tidak
                    </div>
                  </div>
              `);
              }else{
                $('.direct-chat-messages').append(`
                  <div class="direct-chat-msg error">
                    <div class="direct-chat-info clearfix">
                      <span class="direct-chat-name pull-left">Bot Assessment</span>
                    </div>
                    <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
                    <div class="direct-chat-text">
                      Maaf bot assessment sedang mengalami gangguan :( <br/> Apakah anda mau melanjutkan pengisian ?
                      </div>
                  </div>
                `);
              $('.direct-chat-messages').append(`
                <div class="direct-chat-msg right" id="error_answer">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
                  </div>
                  <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
                  <div class="direct-chat-text pull-right">
                    <input type="radio" value="1" onclick="reload(this)">
                    Ya <br/><input type="radio" value="0" onclick="reload(this)"> Tidak
                  </div>
                </div>`);
              }
              $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000); 
              return;
        }).fail(function(response){
            $('.loader').remove();
            $('.direct-chat-messages').append(`
                <div class="direct-chat-msg error" >
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">Bot Assessment</span>
                  </div>
                  <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
                  <div class="direct-chat-text">
                    Maaf bot assessment sedang mengalami gangguan :( <br/> Apakah anda mau melanjutkan pengisian ?
                    </div>
                </div>
              `);
            $('.direct-chat-messages').append(`
              <div class="direct-chat-msg right" id="error_answer">
                <div class="direct-chat-info clearfix">
                  <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
                </div>
                <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
                <div class="direct-chat-text pull-right">
                  <input type="radio" value="1" onclick="reload(this)">
                  Ya <br/><input type="radio" value="0" onclick="reload(this)"> Tidak
                </div>
              </div>`);

            $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000); 
        })	
      return;
    }
    $('.direct-chat-messages').append(`
      <div class="direct-chat-msg loader">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-left">Bot Assessment</span>
        </div>
        <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
        <div class="direct-chat-text">
          <div style="padding:5px 0 5px 15px">
            <div class="dot-typing"></div>
          </div>
        </div>
      </div>
    `);
    $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000);
    setTimeout(function() {
      bot();
    }, 1000);
  }
  function bot(){
    $('.loader').remove();
    var message = '';
    switch(questions[start].type){
      case 'Pertanyaan':
            message = questions[start].description;
            write(message,questions[start].id);
            user();
            break;
      case 'Informasi' :
            message = questions[start].description_information;
            write(message,questions[start].id);
            setTimeout(function() {
              start++;
              loader();
            }, 1000);
            break;
      default:
            message = questions[start].description;
            write(message,questions[start].id);
            
    }
  }
  function botchild(id){
    $('.loader').remove();
    var message = '';
    switch(question_childs[id].type){
      case 'Pertanyaan':
            message = question_childs[id].description;
            write(message,question_childs[id].id);
            userchild(id);
            break;
      case 'Informasi' :
            message = question_childs[id].description_information;
            write(message,question_childs[id].id);
            setTimeout(function() {
              start++;
              loader();
            }, 1000);
            break;
      default:
            message = question_childs[id].description;
            write(message,question_childs[id].id);
            
    }
  }
  function botchildreset(id,next){
    var message = '';
    switch(question_childs[id].type){
      case 'Pertanyaan':
            message = question_childs[id].description;
            writenext(message,question_childs[id].id,next);
            userchildreset(id,question_childs[id].id);
            break;
      case 'Informasi' :
            message = question_childs[id].description_information;
            writenext(message,question_childs[id].id,next);
            break;
      default:
            message = question_childs[id].description;
            writenext(message,question_childs[id].id,next);
            
    }
  }
  function write(message,id){
    $('.direct-chat-messages').append(`
      <div class="direct-chat-msg question" id="question_${id}">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-left">Bot Assessment</span>
        </div>
        <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
        <div class="direct-chat-text">
          ${message}
        </div>
      </div>
    `);
  }
  function writenext(message,id,next){
    $('#answerdesc_'+next).after(`
      <div class="direct-chat-msg question" id="question_${id}">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-left">Bot Assessment</span>
        </div>
        <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
        <div class="direct-chat-text">
          ${message}
        </div>
      </div>
    `);
  }
  function user(){
    var message = '';
    var chatclass = '';
    switch(questions[start].answer_type){
      case 'checkbox':
            $.each(answers[questions[start].id], function(i, answer) {
              message += `<input type="checkbox" name="answer_choice_${questions[start].id}[]" value="${answer.id}" data-reset="0">
                        ${answer.description} <br/>`;   
            });
            message += `<button type="button" class="btn btn-block btn-default btn-sm" onclick="answer(${questions[start].id})"><i class="fa fa-check"></i></button>`;
            chatclass = 'pull-right';
            break;
      case 'radio':
        $.each(answers[questions[start].id], function(i, answer) {
          message += `<input type="radio" name="answer_choice_${questions[start].id}" value="${answer.id}" onclick="answer(${questions[start].id})" data-reset="0">
                    ${answer.description} <br/>`;  
          chatclass = 'pull-right'; 
        });
        break;
        case 'text':
          message += `<div class="input-group"><input type="text" name="answer_choice_${questions[start].id}" value="" class="form-control" placeholder="........."  data-reset="0"><span class="input-group-addon" onclick="answer(${questions[start].id})" style="cursor:pointer"><i class="fa fa-check"></i></span></div>`; 
          chatclass = 'form-inline';
          break;
        case 'number':
          message += `<div class="input-group"><input type="text" name="answer_choice_${questions[start].id}" value="" class="form-control numberfield" placeholder="........."  data-reset="0"><span class="input-group-addon" onclick="answer(${questions[start].id})" style="cursor:pointer"><i class="fa fa-check"></i></span></div>`;
          chatclass = 'form-inline'; 
        break;
    }
    $('.direct-chat-messages').append(`
        <div class="direct-chat-msg right answer" id="answer_${questions[start].id}">
          <div class="direct-chat-info clearfix">
            <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
          </div>
          <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
          <div class="direct-chat-text ${chatclass}">
            ${message}
          </div>
        </div>
    `);
    
    $(".numberfield").inputmask('decimal', {
      rightAlign: true
    });
    $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000); 
  }
  function userchild(id){
    var message = '';
    var chatclass = '';
    switch(question_childs[id].answer_type){
      case 'checkbox':
            $.each(answers[question_childs[id].id], function(i, answer) {
              message += `<input type="checkbox" name="answer_choice_${question_childs[id].id}[]" value="${answer.id}"  data-reset="0">
                        ${answer.description} <br/>`;   
            });
            message += `<button type="button" class="btn btn-block btn-default btn-sm" onclick="answer(${question_childs[id].id})"><i class="fa fa-check"></i></button>`;
            chatclass = 'pull-right';
            break;
      case 'radio':
        $.each(answers[question_childs[id].id], function(i, answer) {
          message += `<input type="radio" name="answer_choice_${question_childs[id].id}" value="${answer.id}" onclick="answer(${question_childs[id].id})" data-reset="0">
                    ${answer.description} <br/>`; 
          chatclass = 'pull-right';  
        });
        break;
      case 'text':
          message += `<div class="input-group"><input type="text" name="answer_choice_${question_childs[id].id}" value="" class="form-control" placeholder="........."  data-reset="0"><span class="input-group-addon" onclick="answer(${question_childs[id].id})" style="cursor:pointer"><i class="fa fa-check"></i></span></div>`; 
          chatclass = 'form-inline'; 
          break;
      case 'number':
           message += `<div class="input-group"><input type="text" name="answer_choice_${question_childs[id].id}" value="" class="form-control numberfield" placeholder="........."  data-reset="0"><span class="input-group-addon" onclick="answer(${question_childs[id].id})" style="cursor:pointer"><i class="fa fa-check"></i></span></div>`; 
           chatclass = 'form-inline'; 
        break;
    }
    $('.direct-chat-messages').append(`
        <div class="direct-chat-msg right answer" id="answer_${question_childs[id].id}">
          <div class="direct-chat-info clearfix">
            <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
          </div>
          <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
          <div class="direct-chat-text ${chatclass}">
            ${message}
          </div>
        </div>
      `);
    
    $(".numberfield").inputmask('decimal', {
      rightAlign: true
    });
    $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000); 
  }
  function userchildreset(id,next){
    var message = '';
    var chatclass = '';
    switch(question_childs[id].answer_type){
      case 'checkbox':
            $.each(answers[question_childs[id].id], function(i, answer) {
              message += `<input type="checkbox" name="answer_choice_${question_childs[id].id}[]" value="${answer.id}"  data-reset="0">
                        ${answer.description} <br/>`;   
            });
            message += `<button type="button" class="btn btn-block btn-default btn-sm" onclick="answerreset(${question_childs[id].id})"><i class="fa fa-check"></i></button>`;
            chatclass = 'pull-right';
            break;
      case 'radio':
        $.each(answers[question_childs[id].id], function(i, answer) {
          message += `<input type="radio" name="answer_choice_${question_childs[id].id}" value="${answer.id}" onclick="answerreset(${question_childs[id].id})" data-reset="0">
                    ${answer.description} <br/>`; 
          chatclass = 'pull-right';  
        });
        break;
      case 'text':
          message += `<div class="input-group"><input type="text" name="answer_choice_${question_childs[id].id}" value="" class="form-control" placeholder="........."  data-reset="0"><span class="input-group-addon" onclick="answerreset(${question_childs[id].id})" style="cursor:pointer"><i class="fa fa-check"></i></span></div>`; 
          chatclass = 'form-inline'; 
          break;
      case 'number':
           message += `<div class="input-group"><input type="text" name="answer_choice_${question_childs[id].id}" value="" class="form-control numberfield" placeholder="........."  data-reset="0"><span class="input-group-addon" onclick="answerreset(${question_childs[id].id})" style="cursor:pointer"><i class="fa fa-check"></i></span></div>`; 
           chatclass = 'form-inline'; 
        break;
    }
    $('#question_'+next).after(`
        <div class="direct-chat-msg right answer" id="answer_${question_childs[id].id}">
          <div class="direct-chat-info clearfix">
            <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
          </div>
          <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
          <div class="direct-chat-text ${chatclass}">
            ${message}
          </div>
        </div>
      `);
    
    $(".numberfield").inputmask('decimal', {
      rightAlign: true
    });
  }
  function answer(id){
    var isreset = 0;
    switch(assessments[id].answer_type){
      case 'checkbox':
          var answerusers = $('input[name^=answer_choice_'+id+']:checked').map(function(){
            return this.value;
          }).get();
          if(answerusers.length > 0){
            var answerdesc = [];
            var answeruser = 0;
            $.each(answerusers, function( index, value ) {
              if(question_childs[value]){
                answeruser = value; 
              }
              answerdesc.push(answer_questions[value].description);
            });
            answerdescription = answerdesc.join(',');
          }
          else{
            answerdescription = 'Tidak ada opsi yang dipilih';
          }
          isreset = $('input[name^=answer_choice_'+id+']').attr('data-reset');
          break;
      case 'radio':
          var answeruser = $('input[name=answer_choice_'+id+']:checked').val();
          answerdescription = answer_questions[answeruser].description;
          isreset = $('input[name^=answer_choice_'+id+']').attr('data-reset');
          break;
      case 'text':
          answerdescription = $('input[name=answer_choice_'+id+']').val();
          if(answerdescription == ''){
            $.gritter.add({
                title: 'Warning!',
                text: 'Jawaban tidak boleh kosong',
                class_name: 'gritter-warning',
                time: 1000,
            });
            return;
          }
          isreset = $('input[name^=answer_choice_'+id+']').attr('data-reset');
          break;
      case 'number':
          answerdescription = $('input[name=answer_choice_'+id+']').val();
          if(answerdescription == ''){
          $.gritter.add({
                title: 'Warning!',
                text: 'Jawaban tidak boleh kosong',
                class_name: 'gritter-warning',
                time: 1000,
            });
            return;
          }
          isreset = $('input[name^=answer_choice_'+id+']').attr('data-reset');
          break;
    }
    if(isreset == 0){
        $('#answer_'+id).hide();
        $('.direct-chat-messages').append(`
          <div class="direct-chat-msg right answerdesc" id="answerdesc_${id}">
            <div class="direct-chat-info clearfix">
              <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
            </div>
            <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
            <div class="direct-chat-text pull-right" style="cursor:pointer" onclick="reset(${id})">
              ${answerdescription}
            </div>
          </div>
        `);
        if(question_childs[answeruser]){
            botchild(answeruser);
        }
        else{
          start++;
          loader();
        }
    }
    else{
      $('#answer_'+id).hide();
      $('#answerdesc_'+id).find('.direct-chat-text').html(answerdescription);
      $('#answerdesc_'+id).show();
      var havechild = null;
      $.each(assessments,function(){
          if(this.question_parent_code == id){
            havechild = this;
          }
      });
      
      if(question_childs[answeruser]){
          botchildreset(answeruser,id);
      }
      else{
        if(havechild){
          $('#question_'+havechild.id).remove();
          $('#answer_'+havechild.id).remove();
          $('#answerdesc_'+havechild.id).remove();
        }
        if(questions[start].id == id){
          start++;
          loader();
        }
      }
    }
    
  }
  function answerreset(id){
    var isreset = 0;
    switch(assessments[id].answer_type){
      case 'checkbox':
          var answerusers = $('input[name^=answer_choice_'+id+']:checked').map(function(){
            return this.value;
          }).get();
          if(answerusers.length > 0){
            var answerdesc = [];
            var answeruser = 0;
            $.each(answerusers, function( index, value ) {
              if(question_childs[value]){
                answeruser = value; 
              }
              answerdesc.push(answer_questions[value].description);
            });
            answerdescription = answerdesc.join(',');
          }
          else{
            answerdescription = 'Tidak ada opsi yang dipilih';
          }
          isreset = $('input[name^=answer_choice_'+id+']').attr('data-reset');
          break;
      case 'radio':
          var answeruser = $('input[name=answer_choice_'+id+']:checked').val();
          answerdescription = answer_questions[answeruser].description;
          isreset = $('input[name^=answer_choice_'+id+']').attr('data-reset');
          break;
      case 'text':
          answerdescription = $('input[name=answer_choice_'+id+']').val();
          if(answerdescription == ''){
            $.gritter.add({
                title: 'Warning!',
                text: 'Jawaban tidak boleh kosong',
                class_name: 'gritter-warning',
                time: 1000,
            });
            return;
          }
          isreset = $('input[name^=answer_choice_'+id+']').attr('data-reset');
          break;
      case 'number':
          answerdescription = $('input[name=answer_choice_'+id+']').val();
          if(answerdescription == ''){
          $.gritter.add({
                title: 'Warning!',
                text: 'Jawaban tidak boleh kosong',
                class_name: 'gritter-warning',
                time: 1000,
            });
            return;
          }
          isreset = $('input[name^=answer_choice_'+id+']').attr('data-reset');
          break;
    }
    $('#answer_'+id).hide();
    $('#answer_'+id).after(`
      <div class="direct-chat-msg right answerdesc" id="answerdesc_${id}">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
        </div>
        <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
        <div class="direct-chat-text pull-right" style="cursor:pointer" onclick="reset(${id})">
          ${answerdescription}
        </div>
      </div>
    `);
    
  }
  function reset(id){
    $('input[name^=answer_choice_'+id+']').attr('data-reset',1);
    $('#answer_'+id).show();
    $('#answerdesc_'+id).hide();
  }
  function finish(e){
    if(e.value == 1){
      $('#finish_answer').hide();
      $('.direct-chat-messages').append(`
      <div class="direct-chat-msg right">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
        </div>
        <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
        <div class="direct-chat-text pull-right">
          Ya
        </div>
      </div>
    `);
      $('.direct-chat-messages').append(`
          <div class="direct-chat-msg loader">
            <div class="direct-chat-info clearfix">
              <span class="direct-chat-name pull-left">Bot Assessment</span>
            </div>
            <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
            <div class="direct-chat-text">
               Terimakasih semua jawaban anda akan dikirim ke server.
            </div>
          </div>
        `);
      $('#form').submit();
    }
    else{
      $('#finish_answer').hide();
      $('.direct-chat-messages').append(`
      <div class="direct-chat-msg right">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
        </div>
        <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
        <div class="direct-chat-text pull-right">
          Tidak
        </div>
      </div>
    `);
      $('.direct-chat-messages').append(`
          <div class="direct-chat-msg loader">
            <div class="direct-chat-info clearfix">
              <span class="direct-chat-name pull-left">Bot Assessment</span>
            </div>
            <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
            <div class="direct-chat-text">
               Pertanyaan akan diatur ulang beberapa detik lagi.
            </div>
          </div>
        `);
        setTimeout(function() {
          start = 0;
          $('.direct-chat-messages').empty();
          loader();
        }, 2000);
    }

    $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000); 
  }
  function reload(e){
    if(e.value == 1){
      $('#error_answer').remove();
      $('.direct-chat-messages').append(`
      <div class="direct-chat-msg right">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
        </div>
        <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
        <div class="direct-chat-text pull-right">
          Ya
        </div>
      </div>
    `);
      loader();
    }
    else{
      $('#error_answer').remove();
      $('.direct-chat-messages').append(`
      <div class="direct-chat-msg right">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
        </div>
        <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
        <div class="direct-chat-text pull-right">
          Tidak
        </div>
      </div>
    `);
      $('.direct-chat-messages').append(`
          <div class="direct-chat-msg loader">
            <div class="direct-chat-info clearfix">
              <span class="direct-chat-name pull-left">Bot Assessment</span>
            </div>
            <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
            <div class="direct-chat-text">
               Pertanyaan akan diatur ulang beberapa detik lagi.
            </div>
          </div>
        `);
        setTimeout(function() {
          start = 0;
          $('.direct-chat-messages').empty();
          loader();
        }, 2000);
    }

    $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000); 
  }
  $(document).ready(function(){
    loader();
    $(document).on('keypress','input',function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
        event.preventDefault()
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
        if(start != questions.length){
          $.gritter.add({
              title: 'Warning!',
              text: 'Silahkan lengkapi isian semua jawaban terlebih dahulu',
              class_name: 'gritter-warning',
              time: 1000,
          });
        }
        var record = '';
        $(".direct-chat-msg").each(function() {
          if($(this).hasClass('question')){
            var question_id = $(this).attr('id');
            var question_class = $(this).attr('class');
            record+=`<div id="${question_id}" class="${question_class}">`;
            record+=$(this).html();
            record+=`</div>`;
          }
          if($(this).hasClass('answer')){
            var answer_id = $(this).attr('id');
            var answer_class = $(this).attr('class');
            record+=`<div id="answer_${answer_id}" class="${answer_class}" style="display:none">`;
            record+=$(this).html();
            record+=`</div>`;
          }
          if($(this).hasClass('answerdesc')){
            var answerdesc_id = $(this).attr('id');
            var answerdesc_class = $(this).attr('class');
            record+=`<div id="${answerdesc_id}" class="${answerdesc_class}">`;
            record+=$(this).html();
            record+=`</div>`;
          }
        });
        var data = new FormData($('#form')[0]);
        data.append('record',record);
        $.ajax({
          url:$('#form').attr('action'),
          method:'post',
          data: data,
          processData: false,
          contentType: false,
          dataType: 'json', 
          beforeSend:function(){
              $('.overlay').removeClass('hidden');
          }
        }).done(function(response){
              $('.overlay').addClass('hidden');
              $('#error_answer').remove();
              if(response.status){
                document.location = response.results;
              }
              else{	
                $('.direct-chat-messages').append(`
                <div class="direct-chat-msg error" >
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">Bot Assessment</span>
                  </div>
                  <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
                  <div class="direct-chat-text">
                    Maaf bot assessment sedang mengalami gangguan :( <br/> Apakah anda mau melanjutkan pengisian ?
                    </div>
                </div>
              `);
            $('.direct-chat-messages').append(`
              <div class="direct-chat-msg right" id="error_answer">
                <div class="direct-chat-info clearfix">
                  <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
                </div>
                <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
                <div class="direct-chat-text pull-right">
                  <input type="radio" value="1" onclick="finish(this)">
                  Ya <br/><input type="radio" value="0" onclick="finish(this)"> Tidak
                </div>
              </div>`);

            $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000); 
              }
              return;
        }).fail(function(response){
            $('.overlay').addClass('hidden');
            $('#error_answer').remove();
            $('.direct-chat-messages').append(`
                <div class="direct-chat-msg error" >
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">Bot Assessment</span>
                  </div>
                  <img class="direct-chat-img" src="{{asset('assets/bot.png')}}" alt="Assessment Bot">
                  <div class="direct-chat-text">
                    Maaf bot assessment sedang mengalami gangguan :( <br/> Apakah anda mau mengirimkan lagi ?
                    </div>
                </div>
              `);
            $('.direct-chat-messages').append(`
              <div class="direct-chat-msg right" id="error_answer">
                <div class="direct-chat-info clearfix">
                  <span class="direct-chat-name pull-right">{{$workforce->name}}</span>
                </div>
                <img class="direct-chat-img" src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}" alt="{{$workforce->name}}">
                <div class="direct-chat-text pull-right">
                  <input type="radio" value="1" onclick="finish(this)">
                  Ya <br/><input type="radio" value="0" onclick="finish(this)"> Tidak
                </div>
              </div>`);

            $(".direct-chat-messages").stop().animate({ scrollTop: $(".direct-chat-messages")[0].scrollHeight}, 1000); 
        })		
      }
    });
  });
</script>
@endpush