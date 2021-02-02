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
        $username = strtoupper($request->username);
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
        else if($authentication == 'web'){
            $user = User::whereRaw("upper(username) = '$username'")->first();
            if($user){
                $host = $workforce->agency->host;
                $curl = curl_init();
                $post = [
                    'username' => $request->username,
                    'password' => $request->password
                ];
                
                $curl = curl_init($host);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
                curl_setopt($curl, CURLOPT_USERAGENT,'MyAgent/1.0');
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST,'POST');
                curl_setopt($curl, CURLOPT_TIMEOUT,10);
                $response = curl_exec($curl);
                switch(curl_getinfo($curl, CURLINFO_HTTP_CODE)){
                    case 200 :
                            $response = json_decode($response);
                            if(isset($response->valid) && $response->valid == 1 ){
                                $username = $response->nid;
                                if($username){
                                    if (Auth::guard("admin")->loginUsingId($user->id)) {
                                        return redirect()->intended($this->redirectTo);
                                    }
                                }
                                else
                                {
                                    return back()->withErrors(['username' => 'These credentials do not match our records.']); 
                                }	
                            }
                            else{
                                return back()->withErrors(['username' => 'These credentials do not match our records.']);  
                            }
                            break;
                    default:
                        return back()->withErrors(['username' => 'Error Connection']);  
                }
                curl_close($curl);	
            }
            else{
                return back()->withErrors(['username' => 'These credentials do not match our records.']);
            }
            
        }
        else{
            $user = User::whereRaw("upper(username) = '$username'")->first();
            if($user){
                $ldap['user'] = $request->username; 
                $ldap['pass'] = $request->password; 
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