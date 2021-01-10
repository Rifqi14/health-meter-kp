<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Rules\Site;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class SiteLoginController extends Controller
{
    use AuthenticatesUsers;
    public function __construct()
    {
        $this->middleware('guest.site')->except('logout');
    }
    public function index(Request $request)
    {
        if (!$request->session()->get('site')) {
            $siteinfo = $request->siteinfo;
            return view('site.login', compact('siteinfo'));
        } else {
            return redirect($request->site . '/dashboard');
        }
    }
    public function redirectTo(){
        return request()->site.'/dashboard';
    }
    public function username()
    {
        return 'username';
    }
    public function guard()
    {
        return Auth::guard('site');
    }

    public function showLoginForm()
    {
        return view('site.login');
    }
    public function logout(Request $request)
    {
        Auth::guard("site")->logout();
        $request->session()->forget('site_role_id');
        return redirect('/'.$request->site);
    }
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => ['required',
            new Site($request->site)],
            'password' => 'required'
        ]);
    }
}