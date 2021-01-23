<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
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
                    $filters[] = $question;
                    break;
                case 'tahunan':
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
                    $filters[] = $question;
                    break;
            }
        }
        $questions = $filters;
        $answers = AssessmentAnswer::all();
        return view('admin.assessment.create', compact('questions','answers','workforce'));
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
        $validator = Validator::make($request->all(), [
            'answer_choice'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'        => false,
                'message'       => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        foreach ($request->answer_choice as $key => $values) {
            $value = (object) $values;
            $question = AssessmentQuestion::find($value->question_id);
            if ($value->answer_type != 'freetext') {
                switch ($value->answer_type) {
                    case 'checkbox':
                        foreach ($value->answer_id as $key => $answer) {
                            $answers = AssessmentAnswer::find($answer);
                            $assessment = Assessment::create([
                                'assessment_date'           => date('Y-m-d'),
                                'assessment_question_id'    => $question->id,
                                'assessment_answer_id'      => $answers->id,
                                'rating'                    => $answers->rating,
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
                            } else {
                                $log = AssessmentLog::create([
                                    'workforce_id'          => Auth::id(),
                                    'assessment_id'         => $assessment->id,
                                    'date'                  => date('Y-m-d'),
                                    'assessment_answer_id'  => $answers->id,
                                    'status'                => 'Create',
                                    'updated_by'            => Auth::id(),
                                ]);
                                if (!$log) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'        => false,
                                        'message'       => $log
                                    ], 400);
                                }
                            }
                        }
                        break;

                    case 'radio':
                        $answers = AssessmentAnswer::find($value->answer_id);
                        $assessment = Assessment::create([
                            'assessment_date'           => date('Y-m-d'),
                            'assessment_question_id'    => $question->id,
                            'assessment_answer_id'      => $answers->id,
                            'rating'                    => $answers->rating,
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
                        } else {
                            $log = AssessmentLog::create([
                                'workforce_id'          => Auth::id(),
                                'assessment_id'         => $assessment->id,
                                'date'                  => date('Y-m-d'),
                                'assessment_answer_id'  => $answers->id,
                                'status'                => 'Create',
                                'updated_by'            => Auth::id(),
                            ]);
                            if (!$log) {
                                DB::rollBack();
                                return response()->json([
                                    'status'        => false,
                                    'message'       => $log
                                ], 400);
                            }
                        }
                        break;
                    default:
                        $answers = AssessmentAnswer::find($value->answer_id);
                        $assessment = Assessment::create([
                            'assessment_date'           => date('Y-m-d'),
                            'assessment_question_id'    => $question->id,
                            'assessment_answer_id'      => $answers->id,
                            'rating'                    => $answers->rating,
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
                        } else {
                            $log = AssessmentLog::create([
                                'workforce_id'          => Auth::id(),
                                'assessment_id'         => $assessment->id,
                                'date'                  => date('Y-m-d'),
                                'assessment_answer_id'  => $answers->id,
                                'status'                => 'Create',
                                'updated_by'            => Auth::id(),
                            ]);
                            if (!$log) {
                                DB::rollBack();
                                return response()->json([
                                    'status'        => false,
                                    'message'       => $log
                                ], 400);
                            }
                        }
                        break;
                }
            } else {
                $assessment = Assessment::create([
                    'assessment_date'           => date('Y-m-d'),
                    'assessment_question_id'    => $question->id,
                    'rating'                    => 0,
                    'description'               => $value->answer_id,
                    'updated_by'                => Auth::id(),
                    'workforce_id'              => Auth::id()
                ]);
                if (!$assessment) {
                    DB::rollBack();
                    return response()->json([
                        'status'        => false,
                        'message'       => $assessment
                    ], 400);
                } else {
                    $log = AssessmentLog::create([
                        'workforce_id'          => Auth::id(),
                        'assessment_id'         => $assessment->id,
                        'date'                  => date('Y-m-d'),
                        'status'                => 'Create',
                        'updated_by'            => Auth::id(),
                    ]);
                    if (!$log) {
                        DB::rollBack();
                        return response()->json([
                            'status'        => false,
                            'message'       => $log
                        ], 400);
                    }
                }
            }
        }
        $calculation = $this->calculation();
        if (!$calculation) {
            DB::rollBack();
            return response()->json([
                'status'        => false,
                'message'       => 'Error calculate assessment',
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
        $formula = Formula::with(['detail'])->first();
        $healthmeters = HealthMeter::where('site_id',$site_id)->where('workforce_group_id',$workforce_group_id)->get();
        if($formula){
            foreach($formula->detail as $formuladetail){
                if($formuladetail->question->answer_type == 'checkbox'){
                    if($request->input('answer_choice_'.$formuladetail->question->id)){
                        foreach($request->input('answer_choice_'.$formuladetail->question->id) as $choice){
                            if($choice == $formuladetail->answer->id){
                                $bobot += $formuladetail->answer->rating;
                            }  
                        }
                    }
                }   
                else{
                    if($request->input('answer_choice_'.$formuladetail->question->id) == $formuladetail->answer->id){
                        $bobot += $formuladetail->answer->rating;
                    }
                } 
            }
        }
        $message = 'Hai '.$workforce->name.' , menurut bot assessment anda tidak termasuk dalam kategori resiko manapun. Apakah anda setuju data akan dikirim ke server? Pilih (ya) jika menyetujui';
        foreach($healthmeters as $healthmeter){
            if($bobot >= $healthmeter->min && $bobot <= $healthmeter->max){
                $message = 'Hai '.$workforce->name.' , menurut bot assessment andatermasuk dalam kategori resiko '.$healthmeter->name.'. Apakah anda setuju data akan dikirim ke server? Pilih (ya) jika menyetujui';
            }
        }
        return response()->json([
            'status'    => true,
            'message'   => $message,
        ], 200); 
    }

    public function calculation()
    {
        $assessments = Assessment::where('assessment_date', date('Y-m-d'))->where('workforce_id', Auth::id())->get();
        $workforce = Workforce::find(Auth::id());
        $question = AssessmentQuestion::where('type', 'Pertanyaan')->count();
        $value_total = 0;
        if ($assessments) {
            foreach ($assessments as $key => $value) {
                $formula = Formula::with(['detail' => function($q) use ($value){
                    $q->where('assessment_question_id', $value->assessment_question_id);
                    $q->where('assessment_answer_id', $value->assessment_answer_id);
                }])->first();
                if ($formula) {
                    switch ($formula->operation) {
                        case 'add':
                            $value_total = $value_total + $value->rating;
                            break;

                        case 'subtract':
                            $value_total = $value_total - $value->rating;
                            break;
                            
                        case 'multiplay':
                            $value_total = $value_total * $value->rating;
                            break;
                            
                        case 'divide':
                            $value_total = $value_total / $value->rating;
                            break;

                        case 'percentage':
                            $value_total = $value_total * ($value->rating / 100);
                            break;
                        
                        default:
                            $value_total = $value_total + $value->rating;
                            break;
                    }
                } else {
                    $value_total = $value_total + $value->rating;
                }
            }
        } else {
            return false;
        }
        $value_total = $value_total / $question;
        $health_meter = HealthMeter::where('min', '<=', $value_total)->where('max', '>=', $value_total)->first();
        $result = AssessmentResult::create([
            'date'              => date('Y-m-d'),
            'workforce_id'      => $workforce->id,
            'workforce_group_id'=> $workforce->workforce_group_id,
            'agency_id'         => $workforce->agency_id,
            'title_id'          => $workforce->title_id,
            'site_id'           => $workforce->site_id,
            'department_id'     => $workforce->department_id,
            'sub_department_id' => $workforce->sub_department_id,
            'health_meter_id'   => $health_meter->id,
            'value_total'       => $value_total,
            'updated_by'        => Auth::id()
        ]);
        if ($result) {
            return true;
        } else {
            return false;
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
        $assessment_result = AssessmentResult::find($id);
        $assessment = Assessment::with(['question', 'answer'])->where('assessment_date', $assessment_result->date)->where('workforce_id', $assessment_result->workforce_id)->get();
        if ($assessment) {
            return view('admin.assessment.detail', compact('assessment'));
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