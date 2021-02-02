@extends('admin.layouts.app')

@section('title', 'Detail Assessment')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style>
.direct-chat-img {
      object-fit: contain;
      border:1px #d2d6de solid;
  }
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
@push('breadcrump')
<li><a href="{{route('assessment.index')}}">Assessment</a></li>
<li class="active">Detail Assessment</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary direct-chat direct-chat-primary">
      <div class="box-header">
        <h3 class="box-title">Detail Assessment</h3>
      </div>
      <div class="box-body">
        <div class="direct-chat-messages">
          {!!$bot->description!!}
        </div>
      </div>
      <div class="overlay hidden">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
@endsection
