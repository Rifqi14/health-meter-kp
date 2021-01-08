<?php

namespace App\Http\Controllers\Admin;

use App\Models\FormulaDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Formula;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FormulaDetailController extends Controller
{

    public function read(Request $request)
    {
        $operation = [
            'percentage'    => 'Persentasi',
            'add'           => 'Penambahan',
            'subtract'      => 'Pengurangan',
            'multiplay'     => 'Pengkalian',
            'divide'        => 'Pembagian',
            'origin'        => 'Asli'
        ];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $formula_id = $request->formula_id;

        //Count Data
        $query = FormulaDetail::with(['question', 'answer']);
        $query->where('formula_id',$formula_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = FormulaDetail::with(['question', 'answer']);
        $query->where('formula_id',$formula_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $formuladetails = $query->get();
        // dd($formuladetails);

        $data = [];
        foreach($formuladetails as $formuladetail){
            $formuladetail->no = ++$start;
            $formuladetail->operation = $operation[$formuladetail->operation];
			$data[] = $formuladetail;
        }
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formula_id'    => 'required',
            'question_id'   => 'required',
            'answer_id'     => 'required',
            'operation'     => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $order = FormulaDetail::max('order');
        $formuladetail = FormulaDetail::create([
            'formula_id'            => $request->formula_id,
			'assessment_question_id'=> $request->question_id,
            'assessment_answer_id'  => $request->answer_id,
            'order'                 => ++$order,
            'operation'             => $request->operation,
            'updated_by'            => Auth::id()
        ]);
        if (!$formuladetail) {
            return response()->json([
                'status' => false,
                'message' 	=> $formuladetail
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'message' 	=> 'Success Create Data'
        ], 200);
    }
    public function edit($id){
        $formuladetail = FormulaDetail::with(['question', 'answer'])->find($id);
        return response()->json([
            'status' 	=> true,
            'data' => $formuladetail
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question_id'   => 'required',
            'answer_id'     => 'required',
            'operation'     => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $formuladetail = FormulaDetail::find($id);
        $formuladetail->assessment_question_id  = $request->question_id;
        $formuladetail->assessment_answer_id    = $request->answer_id;
        $formuladetail->operation               = $request->operation;
        $formuladetail->updated_by              = Auth::id();
        $formuladetail->save();
        if (!$formuladetail) {
            return response()->json([
                'status' => false,
                'message' 	=> $formuladetail
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'message' 	=> 'Success Update Data'
        ], 200);
    }
    public function destroy($id)
    {
        try {
            $formuladetail = FormulaDetail::find($id);
            $formuladetail->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}