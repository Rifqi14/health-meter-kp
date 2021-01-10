<?php

namespace App\Http\Controllers\Site;

use App\Role;
use App\Models\RoleMenu;
use App\Models\Dashboard;
use App\Models\RoleDashboard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    function __construct(Request $request)
    {
        View::share('menu_active', url($request->site.'/'. 'role'));
        $this->middleware('accessmenu', ['except' => ['set']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function set(Request $request)
    {
        $request->session()->put('site_role_id', $request->id);
        return redirect()->back();
    }
}