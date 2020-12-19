@extends('admin.layouts.empty')
@section('title', 'Login')
@section('class', 'login-page')

@section('stylesheets')
<style type="text/css">
  .login-page,
  .register-page {
    background: #d2d6de url("/adminlte/images/background.png");
  }

  .site-logo {
    width: 7rem;
    height: 7rem;
    object-fit: contain;
  }

  .site-name {
    font-size: 3rem;
    font-weight: 600;
  }
</style>
@endsection
@section('content')
<div class="login-box">
  <div class="login-box-body">
    <div class="login-logo">
      <a href="#">
        <img src="{{asset($siteinfo->logo)}}" class="site-logo">
        <p class="site-name">{{ $siteinfo->name }}</p>
      </a>
    </div>
    <form action="{{ url()->current().'/login' }}" id="form" method="post" autocomplete="off">
      @csrf
      <div class="form-group has-feedback{{ $errors->has('username') ? ' has-error' : '' }}">
        <input id="username" type="username" class="form-control" name="username" value="{{ old('username') }}"
          autofocus placeholder="{{ __('Username') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        {!! $errors->first('username', '<p class="help-block"><small>:message</small></p>') !!}
      </div>
      <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
        <input type="password" name="password" class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        {!! $errors->first('password', '<p class="help-block"><small>:message</small></p>') !!}
      </div>
      <div class="form-group">
        <button type="submit" class="submit btn btn-primary btn-block btn-flat">Login</button>
      </div>
    </form>
  </div>
</div>
@endsection