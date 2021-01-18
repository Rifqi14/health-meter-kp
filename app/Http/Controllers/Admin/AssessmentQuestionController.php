<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use App\Models\WorkforceGroup;
use App\Models\Site;
use App\Models\AssessmentQuestionWorkforceGroup;
use App\Models\AssessmentQuestionSite;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AssessmentQuestionController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/assessmentquestion'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.assessmentquestion.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $arsip = $request->category;

        //Count Data
        $query = AssessmentQuestion::with(['user', 'workforcegroup', 'site'])->whereRaw("upper(type) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = AssessmentQuestion::with(['user', 'workforcegroup', 'site'])->whereRaw("upper(type) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
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

        //Count Data
        $query = AssessmentQuestion::whereRaw("upper(type) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = AssessmentQuestion::whereRaw("upper(type) like '%$name%'");
        $query->orderBy('order', 'asc');
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
    public function create()
    {
        $workforcegroups = WorkforceGroup::whereNull('deleted_at')->get();
        $sites = Site::get();
        return view('admin.assessmentquestion.create',compact('workforcegroups','sites'));
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
            'order'                 => 'required|unique:assessment_questions',
            'type'                  => 'required',
            'description'           => 'required',
            'frequency'             => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
            $question = AssessmentQuestion::create([
                'order'                 => $request->order,
                'is_parent'             => $request->question_parent_code ? 1 : 0,
                'question_parent_code'  => $request->question_parent_code,
                'answer_parent_code'    => $request->answer_parent_code,
                'type'                  => $request->type,
                'description'           => $request->description,
                'frequency'             => $request->frequency,
                'start_date'            => $request->start_date,
                'finish_date'           => $request->finish_date,
                'updated_by'            => Auth::id()
            ]);
            if (!$question) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' 	=> $question
                ], 400);
            }
            if($request->workforcegroup){
                foreach($request->workforcegroup as $key => $value){
                    if(isset($request->workforcegroup_status[$value])){
                        $assessmentquestionworkforcegroup = AssessmentQuestionWorkforceGroup::create([
                            'assessment_question_id' => $question->id,
                            'workforce_group_id' => $value
                        ]);
                        if (!$assessmentquestionworkforcegroup) {
                            DB::rollback();
                            return response()->json([
                                'status' => false,
                                'message' 	=> $assessmentquestionworkforcegroup
                            ], 400);
                        }
                    }
                    
                }
            }
            if($request->site){
                foreach($request->site as $key => $value){
                    if(isset($request->site_status[$value])){
                        $assessmentquestionsite = AssessmentQuestionSite::create([
                            'assessment_question_id' => $question->id,
                            'site_id' => $value
                        ]);
                        if (!$assessmentquestionsite) {
                            DB::rollback();
                            return response()->json([
                                'status' => false,
                                'message' 	=> $assessmentquestionsite
                            ], 400);
                        }
                    }
                    
                }
            }
            DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('assessmentquestion.index'),
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
        $frequency = [
            'harian'        => 'Harian',
            'mingguan'      => 'Mingguan',
            'bulanan'       => 'Bulanan',
            'tahunan'       => 'Tahunan',
            'perkejadian'   => 'Perkejadian'
        ];
        $question = AssessmentQuestion::with(['workforcegroup', 'site'])->withTrashed()->find($id);
        if($question){
            return view('admin.assessmentquestion.detail',compact('question','frequency'));
        }
        else{
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
        $question = AssessmentQuestion::withTrashed()->find($id);
        if ($question) {
            return view('admin.assessmentquestion.edit', compact('question'));
        } else {
            abort(404);
        }
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
        $validator = Validator::make($request->all(), [
            'order'             => 'required|unique:assessment_questions,order,'.$id,
            'type'              => 'required',
            'description'       => 'required',
            'frequency'         => 'required',
            'workforce_group_id'=> 'required',
            'site_id'           => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $question = AssessmentQuestion::withTrashed()->find($id);
        $question->order                    = $request->order;
        $question->is_parent                = $request->question_parent_code ? 1 : 0;
        $question->question_parent_code     = $request->question_parent_code;
        $question->answer_parent_code       = $request->answer_parent_code;
        $question->type                     = $request->type;
        $question->description              = $request->description;
        $question->frequency                = $request->frequency;
        $question->start_date               = $request->start_date;
        $question->finish_date              = $request->finish_date;
        $question->workforce_group_id       = $request->workforce_group_id;
        $question->site_id                  = $request->site_id;
        $question->updated_by               = Auth::id();
        $question->save();

        if (!$question) {
            return response()->json([
                'status'    => false,
                'message'   => $question
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('assessmentquestion.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $delete = AssessmentQuestion::find($id);
            $delete->delete();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error archive data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'        => true,
            'message'       => 'Success archive data'
        ], 200);
    }

    public function restore(Request $request)
    {
        try {
            $restore = AssessmentQuestion::onlyTrashed()->find($request->id);
            $restore->restore();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error restore data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success restore data'
        ], 200);
    }

    public function delete(Request $request)
    {
        try {
            $delete = AssessmentQuestion::onlyTrashed()->find($request->id);
            $delete->forceDelete();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error delete data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete data'
        ], 200);
    }
}