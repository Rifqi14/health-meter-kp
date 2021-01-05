<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ExaminationEvaluationLevel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class ExaminationEvaluationLevelController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/evaluationlevel'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.evaluationlevel.index');
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
        $query = ExaminationEvaluationLevel::with('user')->whereRaw("upper(examination_level) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = ExaminationEvaluationLevel::with('user')->whereRaw("upper(examination_level) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $levels = $query->get();

        $data = [];
        foreach ($levels as $level) {
            $level->no = ++$start;
            $level->evaluation_name = $level->evaluation->result_categories;
            $data[] = $level;
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
        $arsip = $request->category;

        //Count Data
        $query = ExaminationEvaluationLevel::Where('status', 1)->whereRaw("upper(examination_level) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = ExaminationEvaluationLevel::Where('status', 1)->whereRaw("upper(examination_level) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $levels = $query->get();

        $data = [];
        foreach ($levels as $level) {
            $level->no = ++$start;
            $data[] = $level;
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
        return view('admin.evaluationlevel.create');
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
            'evaluation'=> 'required',
            'level'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        try {
            $evaluation = ExaminationEvaluationLevel::create([
                'examination_evaluation_id' => $request->evaluation,
                'examination_level'         => $request->level,
                'status'                    => $request->status ? 1 : 0,
                'updated_by'                => Auth::id(),
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status'      => false,
                'message'     => $ex->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('evaluationlevel.index'),
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
        $evaluation = ExaminationEvaluationLevel::find($id);
        if ($evaluation) {
            return view('admin.evaluationlevel.edit', compact('evaluation'));
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
            'evaluation'    => 'required',
            'level'         => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $evaluation = ExaminationEvaluationLevel::withTrashed()->find($id);
        $evaluation->examination_evaluation_id = $request->evaluation;
        $evaluation->examination_level = $request->level;
        $evaluation->status = $request->status ? 1 : 0;
        $evaluation->updated_by = Auth::id();
        $evaluation->save();
        if ($evaluation->status == 0) {
            $evaluation->delete();
        } else {
            $evaluation->restore();
        }
        if (!$evaluation) {
            return response()->json([
                'status' => false,
                'message' 	=> $evaluation
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'results'   => route('evaluationlevel.index')
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
            $evaluation = ExaminationEvaluationLevel::find($id);
            $evaluation->status = 0;
            $evaluation->save();
            $evaluation->delete();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error archive data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success archive data'
        ], 200);
    }

    public function restore($id)
    {
        try {
            $evaluation = ExaminationEvaluationLevel::onlyTrashed()->find($id);
            $evaluation->status = 1;
            $evaluation->save();
            $evaluation->restore();
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
    
    public function delete($id)
    {
        try {
            $evaluation = ExaminationEvaluationLevel::onlyTrashed()->find($id);
            $evaluation->forceDelete();
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