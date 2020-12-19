@extends('admin.layouts.empty')
@section('title', 'Login')
@section('class', 'login-page')

@section('stylesheets')
<style type="text/css">
    .login-page,
    .register-page {
        background: #d2d6de url("/adminlte/images/background.png");
    }
</style>
<link href="{{asset('css/login.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="login-box">
    <div class="col-md-7 col-sm-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Site</h3>
            </div>
            <div class="box-body no-padding">
                @foreach ($sites->take(6) as $item)
                <div class="col-md-4 col-sm-6 col-xs-6">
                    <a class="users-list-name" href="{{url($item->code)}}">
                        <img src="{{asset($item->logo)}}" alt="Site Logo" width="100%" class="site-logo">
                        <p class="site-name">{{ $item->name }}</p>
                    </a>
                </div>
                @endforeach
            </div>
            <div class="box-footer text-center">
                <a href="javascript:void(0)" onclick="filter()" class="uppercase">Semua Unit</a>
            </div>
        </div>
    </div>
    <div class="col-md-5 col-sm-6">
        <div class="login-box-body">
            <div class="login-logo">
                <a href="#"><img src="{{asset(config('configs.app_logo'))}}"></a>
            </div>
            <form action="{{route('admin.login.post')}}" method="post" autocomplete="off">
                @csrf
                <div class="form-group has-feedback{{ $errors->has('username') ? ' has-error' : '' }}">
                    <input id="username" type="username" class="form-control" name="username"
                        value="{{ old('username') }}" autofocus placeholder="{{ __('Username') }}">
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
</div>
<div class="modal fade" id="view-site" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Semua Unit</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach ($sites as $item)
                    <div class="col-md-3 col-sm-4 col-xs-4">
                        <a class="users-list-name" href="{{url($item->code)}}">
                            <img src="{{asset($item->logo)}}" alt="Site Logo" width="100%" class="site-logo">
                            <p class="site-name">{{ $item->name }}</p>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    function filter(){
    $('#view-site').modal('show');
}
</script>
@endpush