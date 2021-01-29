<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentBot;
use App\Models\AssessmentLog;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\Employee;
use App\Models\Formula;
use App\Models\HealthMeter;
use App\Models\Site;
use App\Models\Workforce;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Symfony\Component\Console\Question\Question;

class AssessmentController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'assessment'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        $workforce = Auth::user()->workforce;
        $assessment = Assessment::where('assessment_date', date('Y-m-d'))->where('workforce_id', $workforce->id)->get()->count();
        return view('admin.assessment.index', compact('assessment'));
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $workforce_id = $request->workforce_id;

        //Count Data
        $query = AssessmentResult::with(['category', 'updatedby'])->where('workforce_id', $workforce_id);
        $query->orderBy('created_at', 'desc');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = AssessmentResult::with(['category', 'updatedby'])->where('workforce_id', $workforce_id);
        $query->orderBy('created_at', 'desc');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $data[] = $result;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $nid = strtoupper($request->nid);

        //Count Data
        $query = Workforce::whereRaw("upper(name) like '%$name%'")->whereRaw("upper(nid) like '%$nid%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Workforce::whereRaw("upper(name) like '%$name%'")->whereRaw("upper(nid) like '%$nid%'");
        $query->orderBy('name', 'asc');
        $query->offset($start);
        $query->limit($length);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $data[] = $result;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $workforce = Auth::user()->workforce;
        $workforce_group_id = $workforce->workforce_group_id;
        $site_id = $workforce->site_id;
        // $questions = AssessmentQuestion::with([
        //   'answer',
        //   'parent',
        //   'answercode',
        //   'site' => function ($q) use ($site_id) {
        //     $q->where('site_id', $site_id);
        //   },
        //   'workforcegroup' => function ($q) use ($workforce_group_id) {
        //     $q->where('workforce_group_id', $workforce_group_id);
        //   },
        // ])->orderBy('order', 'asc')->get();
        // $assessment = AssessmentResult::where('date', date('Y-m-d'))->where('workforce_id', Auth::id())->first();
        $questions = AssessmentQuestion::select('assessment_questions.*')
                            ->leftJoin('assessment_question_workforce_groups','assessment_question_workforce_groups.assessment_question_id','=','assessment_questions.id')
                            ->leftJoin('assessment_question_sites','assessment_question_sites.assessment_question_id','=','assessment_questions.id')
                            ->where('workforce_group_id',$workforce_group_id)
                            ->where('site_id',$site_id)
                            ->orderBy('order','asc')
                            ->get();
        $filters = [];
        $actions = [];
        foreach($questions as $question){
            switch($question->frequency){
                case 'harian':
                    $start_date = $question->start_date;
                    $finish_date = $question->finish_date;
                    if($start_date && $start_date > date('Y-m-d')){
                        continue;
                    }
                    if($finish_date && $finish_date < date('Y-m-d')){
                        continue;
                    }
                    $filters[] = $question;
                    break;
                case 'bulanan':
                    $start_date = $question->start_date;
                    $finish_date = $question->finish_date;
                    if($start_date && $start_date > date('Y-m-d')){
                        continue;
                    }
                    if($finish_date && $finish_date < date('Y-m-d')){
                        continue;
                    }
                    if($start_date && date('d',strtotime($start_date)) != date('d')){
                        continue;
                    }
                    $filters[] = $question;
                    break;
                case 'tahunan':
                    $start_date = $question->start_date;
                    $finish_date = $question->finish_date;
                    if($start_date && $start_date > date('Y-m-d')){
                        continue;
                    }
                    if($finish_date && $finish_date < date('Y-m-d')){
                        continue;
                    }
                    if($start_date && date('m-d',strtotime($start_date)) != date('m-d')){
                        continue;
                    }
                    $filters[] = $question;
                    break;
                case 'perkejadian':
                    $start_date = $question->start_date;
                    $finish_date = $question->finish_date;
                    if($start_date && $start_date > date('Y-m-d')){
                        continue;
                    }
                    if($finish_date && $finish_date < date('Y-m-d')){
                        continue;
                    }
                    $assessmentresult = AssessmentResult::where('workforce_id',$workforce->id)->orderBy('date','desc')->first();
                    if($assessmentresult){
                        if($question->answer_type == 'checkbox'){
                            $assessmentanswers = AssessmentAnswer::where('assessment_question_id',$question->id)->get();
                            foreach($assessmentanswers as $assessmentanswer){
                                $assessments = Assessment::with('answer')->where('assessment_question_id',$question->id)->where('assessment_answer_id',$assessmentanswer->id)->where('assessment_date',$assessmentresult->date)->get();
                                foreach($assessments as $assessment){
                                    $actions[] = $assessment;
                                }
                            }  
                        }
                        else{
                            $assessments = Assessment::with('answer')->where('assessment_question_id',$question->id)->where('assessment_date',$assessmentresult->date)->get();
                            foreach($assessments as $assessment){
                                $actions[] = $assessment;
                            }
                        }
                    }
                    
                    $filters[] = $question;
                    break;
            }
        }
        $questions = $filters;
        $answers = AssessmentAnswer::all();
        return view('admin.assessment.create', compact('questions','answers','workforce','actions'));
    }

    public function information()
    {
        $information = AssessmentQuestion::with(['answer', 'parent', 'answercode'])->where('workforce_group_id', 1)->where('site_id', 1)->where('type', 'Informasi')->orderBy('order', 'asc')->get();
        return response()->json([
            'status'    => true,
            'information'  => $information,
        ], 200);
    }
    
    public function question(Request $request)
    {
        $limit = $request->limit;
        $questions = AssessmentQuestion::with(['answer', 'parent', 'answercode'])->where('workforce_group_id', 1)->where('site_id', 1)->limit($limit)->where('type', 'Pertanyaan')->orderBy('order', 'asc')->get();
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
        DB::beginTransaction();
        $workforce = Auth::user()->workforce;
        $workforce_group_id = $workforce->workforce_group_id;
        $site_id = $workforce->site_id;
        $questions = AssessmentQuestion::select('assessment_questions.*')
                            ->leftJoin('assessment_question_workforce_groups','assessment_question_workforce_groups.assessment_question_id','=','assessment_questions.id')
                            ->leftJoin('assessment_question_sites','assessment_question_sites.assessment_question_id','=','assessment_questions.id')
                            ->where('workforce_group_id',$workforce_group_id)
                            ->where('site_id',$site_id)
                            ->orderBy('order','asc')
                            ->get();
        foreach($questions as $question){
            switch($question->answer_type){
                case 'checkbox':
                    if($request->input('answer_choice_'.$question->id)){
                        foreach($request->input('answer_choice_'.$question->id) as $choice){
                            $assessmentanswer = AssessmentAnswer::find($choice);
                            if($assessmentanswer){
                                $assessment = Assessment::create([
                                    'assessment_date'           => date('Y-m-d'),
                                    'assessment_question_id'    => $question->id,
                                    'assessment_answer_id'      => $assessmentanswer->id,
                                    'rating'                    => $assessmentanswer->rating,
                                    'description'               => '',
                                    'updated_by'                => Auth::id(),
                                    'workforce_id'              => Auth::id()
                                ]);
                                if (!$assessment) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'        => false,
                                        'message'       => $assessment
                                    ], 400);
                                } 
                                $assessmentlog = AssessmentLog::create([
                                    'workforce_id'          => $workforce->id,
                                    'assessment_id'         => $assessment->id,
                                    'date'                  => date('Y-m-d'),
                                    'assessment_answer_id'  => $assessmentanswer->id,
                                    'status'                => 'Create',
                                    'updated_by'            => Auth::id(),
                                ]);
                                if (!$assessmentlog) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'        => false,
                                        'message'       => $assessmentlog
                                    ], 400);
                                }
                            }
                        }
                    }
                break;
                case 'radio':
                    if($request->input('answer_choice_'.$question->id)){
                        $choice = $request->input('answer_choice_'.$question->id);
                        $assessmentanswer = AssessmentAnswer::find($choice);
                        if($assessmentanswer){
                            $assessment = Assessment::create([
                                'assessment_date'           => date('Y-m-d'),
                                'assessment_question_id'    => $question->id,
                                'assessment_answer_id'      => $assessmentanswer->id,
                                'rating'                    => $assessmentanswer->rating,
                                'description'               => '',
                                'updated_by'                => Auth::id(),
                                'workforce_id'              => Auth::id()
                            ]);
                            if (!$assessment) {
                                DB::rollBack();
                                return response()->json([
                                    'status'        => false,
                                    'message'       => $assessment
                                ], 400);
                            } 
                            $assessmentlog = AssessmentLog::create([
                                'workforce_id'          => $workforce->id,
                                'assessment_id'         => $assessment->id,
                                'date'                  => date('Y-m-d'),
                                'assessment_answer_id'  => $assessmentanswer->id,
                                'status'                => 'Create',
                                'updated_by'            => Auth::id(),
                            ]);
                            if (!$assessmentlog) {
                                DB::rollBack();
                                return response()->json([
                                    'status'        => false,
                                    'message'       => $assessmentlog
                                ], 400);
                            }
                        }
                    }
                break;
                default:
                    if($request->input('answer_choice_'.$question->id)){
                        $choice = $request->input('answer_choice_'.$question->id);
                        $assessment = Assessment::create([
                            'assessment_date'           => date('Y-m-d'),
                            'assessment_question_id'    => $question->id,
                            'rating'                    => 0,
                            'description'               => $choice,
                            'updated_by'                => Auth::id(),
                            'workforce_id'              => $workforce->id
                        ]);
                        if (!$assessment) {
                            DB::rollBack();
                            return response()->json([
                                'status'        => false,
                                'message'       => $assessment
                            ], 400);
                        } 
                        $assessmentlog = AssessmentLog::create([
                            'workforce_id'          => $workforce->id,
                            'assessment_id'         => $assessment->id,
                            'date'                  => date('Y-m-d'),
                            'status'                => 'Create',
                            'updated_by'            => Auth::id(),
                        ]);
                        if (!$assessmentlog) {
                            DB::rollBack();
                            return response()->json([
                                'status'        => false,
                                'message'       => $assessmentlog
                            ], 400);
                        }
                    }
            }
        }
        $bobot = 0;
        $formula = Formula::first();
        $healthmeters = HealthMeter::where('site_id',$site_id)->where('workforce_group_id',$workforce_group_id)->get();
        if($formula){
            $calculate = $formula->calculate;
            $assessmentanswers = AssessmentAnswer::all();
            foreach($assessmentanswers as $assessmentanswer){
                if($assessmentanswer->question){
                    if($assessmentanswer->question->answer_type == 'checkbox'){
                        if($request->input('answer_choice_'.$assessmentanswer->question->id)){
                            foreach($request->input('answer_choice_'.$assessmentanswer->question->id) as $choice){
                                if($choice == $assessmentanswer->id){
                                    $calculate = str_replace('#'.$assessmentanswer->id.'#',$assessmentanswer->rating,$calculate);
                                }
                            }
                        }
                    }
                    else{
                        if($request->input('answer_choice_'.$assessmentanswer->question->id) == $assessmentanswer->id){
                            $calculate = str_replace('#'.$assessmentanswer->id.'#',$assessmentanswer->rating,$calculate);
                        }
                    } 
                    $calculate = str_replace('#'.$assessmentanswer->id.'#',0,$calculate);
                }
            }
        }
        $bobot = eval('return '.$calculate.';');
        $healthmeter_id = null;
        foreach($healthmeters as $healthmeter){
            if($bobot >= $healthmeter->min && $bobot <= $healthmeter->max){
                $healthmeter_id = $healthmeter->id;
            }
        }
        $assessmentresult = AssessmentResult::create([
            'date'              => date('Y-m-d'),
            'workforce_id'      => $workforce->id,
            'workforce_group_id'=> $workforce->workforce_group_id,
            'agency_id'         => $workforce->agency_id,
            'title_id'          => $workforce->title_id,
            'site_id'           => $workforce->site_id,
            'department_id'     => $workforce->department_id,
            'sub_department_id' => $workforce->sub_department_id,
            'health_meter_id'   => $healthmeter_id,
            'value_total'       => $bobot,
            'updated_by'        => Auth::id()
        ]);
        if (!$assessmentresult) {
            DB::rollBack();
            return response()->json([
                'status'        => false,
                'message'       => $assessmentresult
            ], 400);
        }
        $assessmentbot = AssessmentBot::create([
            'assessment_result_id' => $assessmentresult->id,
            'description'          => $request->record,
        ]);
        if (!$assessmentbot) {
            DB::rollBack();
            return response()->json([
                'status'        => false,
                'message'       => $assessmentbot
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('assessment.index'),
        ], 200);
    }

    public function check(Request $request)
    {
        $workforce = Auth::user()->workforce;
        $workforce_group_id = $workforce->workforce_group_id;
        $site_id = $workforce->site_id;
        $bobot = 0;
        $formula = Formula::first();
        $healthmeters = HealthMeter::where('site_id',$site_id)->where('workforce_group_id',$workforce_group_id)->get();
        if($formula){
            $calculate = $formula->calculate;
            $assessmentanswers = AssessmentAnswer::all();
            foreach($assessmentanswers as $assessmentanswer){
                if($assessmentanswer->question){
                    if($assessmentanswer->question->answer_type == 'checkbox'){
                        if($request->input('answer_choice_'.$assessmentanswer->question->id)){
                            foreach($request->input('answer_choice_'.$assessmentanswer->question->id) as $choice){
                                if($choice == $assessmentanswer->id){
                                    $calculate = str_replace('#'.$assessmentanswer->id.'#',$assessmentanswer->rating,$calculate);
                                }
                            }
                        }
                    }
                    else{
                        if($request->input('answer_choice_'.$assessmentanswer->question->id) == $assessmentanswer->id){
                            $calculate = str_replace('#'.$assessmentanswer->id.'#',$assessmentanswer->rating,$calculate);
                        }
                    } 
                    $calculate = str_replace('#'.$assessmentanswer->id.'#',0,$calculate);
                }
            }
        }
        $bobot = eval('return '.$calculate.';');
        $message = 'Hasil assessment anda tidak ada dalam kategori.</br>
        Simpan data Assessment Kesehatan?';
        foreach($healthmeters as $healthmeter){
            if($bobot >= $healthmeter->min && $bobot <= $healthmeter->max){
                $color = $healthmeter->color;
                $message = 'Hasil assessment anda termasuk dalam kategori <b style="color:'.$color.'">'.$healthmeter->name.'</b>. </br> Info tindak lanjut <b>'.$healthmeter->recomendation.'</b>..</br>Simpan data Assessment Kesehatan?';
            }
        }
        return response()->json([
            'status'    => true,
            'message'   => $message,
        ], 200); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bot = AssessmentBot::where('assessment_result_id',$id)->orderBy('created_at','desc')->first();
        if ($bot) {
            return view('admin.assessment.detail', compact('bot'));
        } else {
            abort(404);
        }
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