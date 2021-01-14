<!DOCTYPE html>
<html>

<head>
  <title>@yield('title') - {{config('configs.app_name')}}</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="stylesheet" href="{{asset('adminlte/component/bootstrap/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/component/font-awesome/css/font-awesome.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/component/Ionicons/css/ionicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/component/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/component/select2/css/select2.bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/component/iCheck/all.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/component/gritter/css/jquery.gritter.min.css')}}">
  @yield('stylesheets')
  <link rel="stylesheet" href="{{asset('adminlte/css/AdminLTE.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/css/skins/_all-skins.min.css')}}">
  <!-- Google Font -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="sidebar-mini skin-blue-light fixed">
  <div class="wrapper">
    <header class="main-header">
      <a href="{{route('dashboard.index')}}" class="logo">
        <span class="logo-mini"><b>{{substr(config('configs.app_name'),0,3)}}</b></span>
        <span class="logo-lg"><b>{{config('configs.app_name')}}</b></span>
      </a>
      <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li>
              <a href="{{ route('guide.list') }}">
                <i style="font-size: 20px;" class="fa fa-book"></i>
              </a>
            </li>
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img
                  src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}"
                  class="user-image" alt="User Image">
                <span class="hidden-xs">{{ Auth::guard('admin')->user()->name }}</span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header">
                  <img
                    src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}"
                    class="img-circle" alt="User Image">
                  <p>
                    {{ Auth::guard('admin')->user()->name }}
                    <small>{{ Auth::guard('admin')->user()->email }}</small>
                  </p>
                </li>
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="{{ route('account.info') }}" class="btn btn-default btn-flat">Akun</a>
                  </div>
                  <div class="pull-right">
                    <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                                          document.getElementById('logout-form').submit();" alt="Logout"
                      class="btn btn-default btn-flat">Sign out</a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                      @csrf
                    </form>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <aside class="main-sidebar">
      <section class="sidebar">
        <div class="user-panel">
          <div class="pull-left image">
            <img
              src="{{is_file('assets/user/'.Auth::guard('admin')->user()->id.'.png')?asset('assets/user/'.Auth::guard('admin')->user()->id.'.png'):asset('adminlte/images/user2-160x160.jpg')}}"
              class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p>{{ Auth::guard('admin')->user()->name }}</p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>
        <ul class="sidebar-menu" data-widget="tree">
          <li class="header">Main Navigation</li>
          {!!buildMenuAdmin($menuaccess)!!}
        </ul>
      </section>
    </aside>
    <div class="content-wrapper">
      <section class="content-header">
        <ol class="breadcrumb">
          <li><a href="{{route('dashboard.index')}}"><i class="fa fa-home"></i> Home</a></li>
          @stack('breadcrump')
        </ol>
      </section>
      <section class="content">
        @yield('content')
      </section>
    </div>
    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Version</b> 0.0.1
      </div>
      <strong>Copyright &copy; {{config('configs.app_copyright')}}</strong>
    </footer>
  </div>
  <div class="modal fade" id="select-role" class="modal hide fade in" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Pilih Role</h4>
        </div>
        <div class="modal-body">
          <ul class="list-group">
            @foreach(Auth::guard('admin')->user()->roles()->get() as $role)
            <li class="list-group-item ">
              <a href="{{url('admin/role/set/'.$role->id)}}"
                class="text-muted font-bold"><strong>{{$role->display_name}}</strong></a>
            </li>
            @endforeach
          </ul>
        </div>
        <div class="modal-footer">
          <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();" class="btn btn-primary"><i
              class="fa fa-sign-out"></i></a>
        </div>
      </div>
    </div>
  </div>
  <script src="{{asset('adminlte/component/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('adminlte/component/bootstrap/js/bootstrap.min.js')}}"></script>
  <script src="{{asset('adminlte/component/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
  <script src="{{asset('adminlte/component/inputmask/jquery.inputmask.js')}}"></script>
  <script src="{{asset('adminlte/component/select2/js/select2.min.js')}}"></script>
  <script src="{{asset('adminlte/component/gritter/js/jquery.gritter.min.js')}}"></script>
  <script src="{{asset('adminlte/component/iCheck/icheck.min.js')}}"></script>
  <script src="{{asset('adminlte/component/bootbox/bootbox.min.js')}}"></script>
  <script>
    $(function() {
          $(".sidebar-menu").find("a[href='{{@$menu_active}}']").parent().addClass("active");
          $(".sidebar-menu").find("a[href='{{@$menu_active}}']").closest('.treeview').addClass("active menu-open");
      })
  </script>
  <script src="{{asset('adminlte/js/adminlte.min.js')}}"></script>
  @stack('scripts')
</body>

</html>