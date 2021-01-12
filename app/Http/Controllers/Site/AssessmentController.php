<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\Employee;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class AssessmentController extends Controller
{
    function __construct(Request $request){
        View::share('menu_active', url($request->site.'/assessment'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) 
    {
        $site = $request->site;
        $nid = Auth::user()->username;
        $employee = Employee::where('nid', $nid)->get()->first();
        $assessment = Assessment::where('assessment_date', date('Y-m-d'))->where('employee_id', 4)->get()->count();
        return view('admin.assessment.index', compact('assessment', 'site'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $site = $request->site;
        $site_id = Site::where('code', $site)->first();
        $nid = Auth::user()->username;
        $employee = Employee::where('nid', $nid)->get()->first();
        $workforce = $employee->workforce_group_id ? $employee->workforce_group_id : null;
        $questions = AssessmentQuestion::with([
            'answer',
            'parent',
            'answercode',
            'site' => function($q) use ($site_id){
                $q->where('site_id', $site_id->id);
            },
            'workforcegroup' => function($q) use ($workforce)
            {
                $q->where('workforce_group_id', $workforce);
            },
        ])->orderBy('order', 'asc')->get();
        return view('admin.assessment.create', compact('questions', 'site'));
    }

    public function information()
    {
        $information = AssessmentQuestion::with(['answer', 'parent', 'answercode'])->where('type', 'Informasi')->orderBy('order', 'asc')->get();
        return response()->json([
            'status'    => true,
            'information'  => $information,
        ], 200);
    }
    
    public function question(Request $request)
    {
        $limit = $request->limit;
        $questions = AssessmentQuestion::with(['answer', 'parent', 'answercode'])->limit($limit)->where('type', 'Pertanyaan')->orderBy('order', 'asc')->get();
        return response()->json([
            'status'    => true,
            'question'  => $questions,
        ], 200);
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