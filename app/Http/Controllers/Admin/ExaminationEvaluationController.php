<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ExaminationEvaluation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class ExaminationEvaluationController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/examinationevaluation'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.examinationevaluation.index');
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
        $query = ExaminationEvaluation::with('user')->whereRaw("upper(result_categories) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = ExaminationEvaluation::with('user')->whereRaw("upper(result_categories) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $evaluations = $query->get();

        $data = [];
        foreach ($evaluations as $evaluation) {
            $evaluation->no = ++$start;
            $evaluation->type_name = $evaluation->type->name;
            $data[] = $evaluation;
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
        $examination_type_id = $request->examination_type_id;

        //Count Data
        $query = ExaminationEvaluation::Where('examination_type_id', $examination_type_id)->whereRaw("upper(result_categories) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = ExaminationEvaluation::Where('examination_type_id', $examination_type_id)->whereRaw("upper(result_categories) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $medicines = $query->get();

        $data = [];
        foreach ($medicines as $examination) {
            $examination->no = ++$start;
            $data[] = $examination;
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
        return view('admin.examinationevaluation.create');
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
            'examination_type'  => 'required',
            'result'            => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        try {
            $evaluation = ExaminationEvaluation::create([
                'examination_type_id'   => $request->examination_type,
                'result_categories'     => $request->result,
                'updated_by'            => Auth::id(),
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status'      => false,
                'message'     => $ex->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('examinationevaluation.index'),
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
        $evaluation = ExaminationEvaluation::find($id);
        if ($evaluation) {
            return view('admin.examinationevaluation.detail', compact('evaluation'));
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
        $evaluation = ExaminationEvaluation::find($id);
        if ($evaluation) {
            return view('admin.examinationevaluation.edit', compact('evaluation'));
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
            'examination_type'=> 'required',
            'result'          => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $evaluation = ExaminationEvaluation::withTrashed()->find($id);
        $evaluation->examination_type_id = $request->examination_type;
        $evaluation->result_categories = $request->result;
        $evaluation->updated_by = Auth::id();
        $evaluation->save();
        if (!$evaluation) {
            return response()->json([
                'status' => false,
                'message' 	=> $evaluation
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'results'   => route('examinationevaluation.index')
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
            $evaluation = ExaminationEvaluation::find($id);
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
            $evaluation = ExaminationEvaluation::onlyTrashed()->find($id);
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
            $evaluation = ExaminationEvaluation::onlyTrashed()->find($id);
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