<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
class AccountController extends Controller
{
    public function info(){
        return view('admin.account.info');
    }
    public function update(Request $request, $id)
    {
        if($request->password){
            $user = Auth::guard('admin')->user();
            $validator = Validator::make($request->all(), [
                'password' => 'required|passcheck:' . $user->password,
                'newpassword' => 'required|confirmed|min:6',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' 	=> false,
                    'message' 	=> $validator->errors()->first()
                ], 400);
            }
    
            $user->password = Hash::make($request->newpassword);
            $user->save();
    
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' 	=> $user
                ], 400);
            }
        }
        $foto = $request->file('foto');
        if($foto){
            $user = Auth::guard('admin')->user();
            $path = 'assets/user/';
            $foto->move($path, $user->id.'.png');
        }
        return response()->json([
            'status' 	=> true,
            'results' 	=> route('account.info'),
        ], 200);
    }

}
