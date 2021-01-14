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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AssessmentController extends Controller
{
  function __construct(Request $request)
  {
    View::share('menu_active', url($request->site . '/assessment'));
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
      'site' => function ($q) use ($site_id) {
        $q->where('site_id', $site_id->id);
      },
      'workforcegroup' => function ($q) use ($workforce) {
        $q->where('workforce_group_id', $workforce);
      },
    ])->orderBy('order', 'asc')->get();
    return view('admin.assessment.create', compact('questions', 'site'));
  }

  public function information(Request $request)
  {
    $site = $request->site;
    $site_id = Site::where('code', $site)->first();
    $nid = Auth::user()->username;
    $employee = Employee::where('nid', $nid)->get()->first();
    $workforce = $employee->workforce_group_id ? $employee->workforce_group_id : null;
    $informations = AssessmentQuestion::with([
      'answer',
      'parent',
      'answercode',
      'site' => function ($q) use ($site_id) {
        $q->where('site_id', $site_id->id);
      },
      'workforcegroup' => function ($q) use ($workforce) {
        $q->where('workforce_group_id', $workforce);
      },
    ])->where('type', 'Informasi')->orderBy('order', 'asc')->get();
    $information = [];
    foreach ($informations as $key => $value) {
      $information[] =  "<div class='direct-chat-msg'>
                              <div class='direct-chat-info clearfix'>
                                <span class='direct-chat-name pull-left'>Bot Assessment</span>
                              </div>
                              <img class='direct-chat-img' src='" . asset('assets/user/1.png') . "' alt='Assesment Bot'>
                              <div class='direct-chat-text pull-left'>" . $value->description . "</div>
                          </div>";
    }
    return response()->json([
      'status'       => true,
      'information'  => $information,
    ], 200);
  }

  public function questionParent(Request $request)
  {
    $site = $request->site;
    $site_id = Site::where('code', $site)->first();
    $nid = Auth::user()->username;
    $employee = Employee::where('nid', $nid)->get()->first();
    $workforce = $employee->workforce_group_id ? $employee->workforce_group_id : null;
    $questionParents = AssessmentQuestion::with([
      'answer',
      'parent',
      'answercode',
      'site' => function ($q) use ($site_id) {
        $q->where('site_id', $site_id->id);
      },
      'workforcegroup' => function ($q) use ($workforce) {
        $q->where('workforce_group_id', $workforce);
      },
    ])->where('type', 'Pertanyaan')->where('is_parent', 0)->orderBy('order', 'asc')->get();
    $questionParent = [];
    foreach ($questionParents as $key => $value) {
      $questionParent[] =  "<div class='direct-chat-msg'>
                                  <div class='direct-chat-info clearfix'>
                                    <span class='direct-chat-name pull-left'>Bot Assessment</span>
                                  </div>
                                  <img class='direct-chat-img' src='" . asset('assets/user/1.png') . "' alt='Assesment Bot'>
                                  <div class='direct-chat-text pull-left'>" . $value->description . "</div>
                                </div>";
      $questionParent[] = "<div class='direct-chat-msg right'>
                                <div class='direct-chat-info clearfix'>
                                  <span class='direct-chat-name pull-right'>User</span>
                                </div>
                                <img class='direct-chat-img' src='{{ asset('assets/user/1.png') }}' alt='Assessment Bot'>
                              <div class='pull-right form-inline'>";
    }
    return response()->json([
      'status'    => true,
      'question'  => $questionParent,
    ], 200);
  }

  public function questionChild(Request $request)
  {
    $site = $request->site;
    $site_id = Site::where('code', $site)->first();
    $nid = Auth::user()->username;
    $employee = Employee::where('nid', $nid)->get()->first();
    $workforce = $employee->workforce_group_id ? $employee->workforce_group_id : null;
    $questionChild = AssessmentQuestion::with([
      'answer',
      'parent',
      'answercode',
      'site' => function ($q) use ($site_id) {
        $q->where('site_id', $site_id->id);
      },
      'workforcegroup' => function ($q) use ($workforce) {
        $q->where('workforce_group_id', $workforce);
      },
    ])->where('type', 'Pertanyaan')->where('is_parent', 1)->orderBy('order', 'asc')->get();
    return response()->json([
      'status'    => true,
      'question'  => $questionChild,
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
    $validator = Validator::make($request->all(), [
      'answer_choice' => 'required'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status'     => false,
        'message'     => $validator->errors()->first()
      ], 400);
    }

    foreach ($request->answer_choice as $key => $value) {
      # code...
    }
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