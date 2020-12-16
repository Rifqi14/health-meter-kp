<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoleDashboard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RoleDashboardController extends Controller
{
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'id' 	=> 'required',
            'role_access' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $roledashboard = RoleDashboard::find($request->id);
        $roledashboard->role_access = $request->role_access;
        $roledashboard->save();
        if (!$roledashboard) {
            return response()->json([
                'success' => false,
                'message' 	=> $roledashboard
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'message' 	=> 'Role access has been updated',
        ], 200);
    }
}
