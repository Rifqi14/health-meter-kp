<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->session()->get('site')) {
            return view('site.login');
        } else {
            return redirect($request->site . '/dashboard');
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $username = strtoupper($request->username);
        $password = $request->password;
        $site = Site::where('code', $request->site)->first();
        $query = DB::table('users');
        $query->select('users.*', 'site_users.site_id');
        $query->leftJoin('site_users', 'site_users.user_id', '=', 'users.id');
        $query->where('site_users.site_id', $site->id);
        $query->whereRaw("(upper(username) = '$username' or upper(email) = '$username')");
        $user = $query->get()->first();
        if ($user) {
            if (Hash::check($password, $user->password)) {
                session([
                    'site' => (object) array(
                        'id'        => $user->id,
                        'name'      => $user->name,
                        'username'  => $user->username,
                        'email'     => $user->email,
                        'site_id'   => $user->site_id,
                        'role_id'   => 0
                    ),
                ]);
                return redirect($site->site_code . '/dashboard');
                return response()->json([
                    'status'    => true,
                    'data'      => url('admin/dashboard'),
                ], 200);
            } else {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Login Fail! Password Wrong',
                ], 400);
            }
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Login Fail! Username Or Email Not Found',
            ], 400);
        }
    }
    public function logout(Request $request)
    {
        $request->session()->forget('site');
        return redirect($request->site);
    }
    public function selectrole(Request $request)
    {
        $session = $request->session()->get('site');
        $session->role_id = $request->id;
        session([
            'site' => $session
        ]);
        return redirect($request->site . '/dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}