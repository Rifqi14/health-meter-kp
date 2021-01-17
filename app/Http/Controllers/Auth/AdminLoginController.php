<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Workforce;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AdminLoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/admin/dashboard';
    public function __construct()
    {
        $this->middleware('guest.admin')->except('logout');
    }
    protected function credentials(Request $request)
    {
        return $request->only($this->username());
    }
    public function username()
    {
        return 'username';
    }
    public function guard()
    {
        return Auth::guard('admin');
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }
    public function logout(Request $request)
    {
        Auth::guard("admin")->logout();
        $request->session()->forget('role_id');
        return redirect('/admin');
    }
    public function authenticate()
    {
        if (Auth::attempt([$this->username() => $email, 'password' => $password])) 
        {
            // Authentication passed...
            //return redirect()->intended($this->redirectTo);
        }
    }
    protected function login(Request $request){
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $authentication = 'local';
        $username = $request->username;
        $workforce = Workforce::with('agency')->whereRaw("upper(nid) = '$username'")->first();
        if(!$workforce){
            return back()->withErrors(['username' => 'These credentials do not match our records.']);
        }
        $authentication = $workforce->agency->authentication;
        if($authentication == 'local'){
            if (Auth::guard("admin")->attempt([$this->username() => $username,'password'=>$request->password])) {
                return redirect()->intended($this->redirectTo);
            }
        }
        else{
            $user = User::whereRaw("upper(username) = '$username'")->first();
            if($user){
                $ldap['user'] = 'admin'; 
                $ldap['pass'] = 'Kediri92'; 
                $ldap['host'] = $workforce->agency->host; 
                $ldap['port'] = $workforce->agency->port;
                $ldap['conn'] = ldap_connect( $ldap['host'], $ldap['port'] );
                ldap_set_option($ldap['conn'], LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldap['conn'], LDAP_OPT_NETWORK_TIMEOUT, 2);
                $ldap['bind'] = @ldap_bind($ldap['conn'], $ldap['user'], $ldap['pass']);
                if($ldap['bind']){
                    if (Auth::guard("admin")->loginUsingId($user->id)) {
                        return redirect()->intended($this->redirectTo);
                    }
                }
                else{
                    switch(ldap_errno( $ldap['conn'])){
                        case 49:
                            return back()->withErrors(['username' => 'These credentials do not match our records.']);
                            break;
                        default:
                            return back()->withErrors(['username' => ldap_error( $ldap['conn'])]);
                    } 
                }
                ldap_close( $ldap['conn'] );
            }
            else{
                return back()->withErrors(['username' => 'These credentials do not match our records.']);
            }
        }
        return back()->withErrors(['username' => 'These credentials do not match our records.']);
    }
}