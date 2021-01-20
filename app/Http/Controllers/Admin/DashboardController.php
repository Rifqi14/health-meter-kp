<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'dashboard'));
    }
    public function index()
    {
        $workforce = Auth::user()->workforce;
        $assessment = Assessment::where('assessment_date', date('Y-m-d'))->where('workforce_id', $workforce->id)->get()->count();
        return view('admin.dashboard',compact('assessment'));
    }
    
}