<?php

namespace App\Http\Controllers\Admin;

use App\Models\FormulaDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FormulaDetailController extends Controller
{

    public function read(Request $request)
    {
        $operation = [
            'percentage'  => 'Presentasi',
            'divide'  => 'Pembagian',
            'origin'  => 'Asli'
        ];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $formula_id = $request->formula_id;

        //Count Data
        $query = DB::table('formula_details');
        $query->select('formula_details.*');
        $query->leftJoin('categories','categories.id','=','formula_details.category_id');
        $query->leftJoin('formulas','formulas.id','=','formula_details.reference_id');
        $query->where('formula_details.formula_id',$formula_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('formula_details');
        $query->select('formula_details.*','categories.name as category_name','formulas.name as formula_name');
        $query->leftJoin('categories','categories.id','=','formula_details.category_id');
        $query->leftJoin('formulas','formulas.id','=','formula_details.reference_id');
        $query->where('formula_details.formula_id',$formula_id);
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
            'pick'          => 'required',
            'operation'          => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $formuladetail = FormulaDetail::create([
            'formula_id'   => $request->formula_id,
			'pick'          => $request->pick,
            'category_id' 	=> $request->pick=='category'?$request->category_id:null,
            'reference_id' 	=> $request->pick=='formula'?$request->reference_id:null,
            'operation'     => $request->operation,
            'value'         => $request->value
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
        $formuladetail = FormulaDetail::select('formula_details.*','categories.name as category_name','formulas.name as formula_name')
                            ->leftJoin('categories','categories.id','=','formula_details.category_id')
                            ->leftJoin('formulas','formulas.id','=','formula_details.reference_id')
                            ->where('formula_details.id',$id)
                            ->get()
                            ->first();
        return response()->json([
            'status' 	=> true,
            'data' => $formuladetail
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'pick'          => 'required',
            'operation'     => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $formuladetail = FormulaDetail::find($id);
        $formuladetail->pick = $request->pick;
        $formuladetail->category_id  = $request->pick=='category'?$request->category_id:null;
        $formuladetail->reference_id  = $request->pick=='formula'?$request->reference_id:null;
        $formuladetail->operation  = $request->operation;
        $formuladetail->value  = $request->value;
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
