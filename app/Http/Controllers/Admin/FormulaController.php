<?php

namespace App\Http\Controllers\Admin;

use App\Models\Formula;
use App\Models\FormulaDetail;
use App\Models\AssessmentAnswer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class FormulaController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'formula'));
        $this->middleware('accessmenu');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.formula.index');
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
        $query = Formula::with('user');
        $query->whereRaw("upper(formulas.name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        } else {
            $query->withTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Formula::with('user');
        $query->whereRaw("upper(formulas.name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        } else {
            $query->withTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $formulas = $query->get();

        $data = [];
        foreach($formulas as $formula){
            $formula->no = ++$start;
			$data[] = $formula;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('formulas');
        $query->select('formulas.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('formulas');
        $query->select('formulas.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $formulas = $query->get();

        $data = [];
        foreach($formulas as $formula){
            $formula->no = ++$start;
			$data[] = $formula;
		}
        return response()->json([
			'total'=>$recordsTotal,
			'rows'=>$data
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.formula.create');
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
            'name'      => 'required',
            'calculate' => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $calculate = $request->calculate;
        $assessmentanswers = AssessmentAnswer::all();
        foreach($assessmentanswers as $assessmentanswer){
            $calculate = str_replace('#'.$assessmentanswer->id.'#',$assessmentanswer->rating,$calculate);
        }
        try{
            eval('return '.$calculate.';');
        }
        catch(\ParseError $e){
            return response()->json([
                'status' 	=> false,
                'message' 	=> 'Formula Error'
            ], 400);
        }
        DB::beginTransaction();
        $formula = Formula::create([
            'name' 	    => $request->name,
            'calculate' => $request->calculate,
            'updated_by'=> Auth::id(),
        ]);
        if (!$formula) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' 	=> $formula
            ], 400);
        }
        $order = 0;
        foreach($request->formula as $value){
            $formuladetail = FormulaDetail::create([
                'formula_id'            => $formula->id,
                'assessment_answer_id'  => $request->input('answer_'.$value)?$request->input('answer_'.$value):null,
                'order'                 => ++$order,
                'value'                 => $request->input('value_'.$value),
                'operation_before'      => $request->input('operation_before_'.$value),
                'operation'             => $request->input('operation_'.$value),
                'updated_by'            => Auth::id()
            ]);
            if (!$formuladetail) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' 	=> $formuladetail
                ], 400);
            }
        }
        Formula::where('id','<>',$formula->id)->update([
            'deleted_at'=>now()
        ]);
        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('formula.index'),
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
        $formula = Formula::find($id);
        if($formula){
            return view('admin.formula.detail',compact('formula'));
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
        $formula = Formula::find($id);
        if($formula){
            return view('admin.formula.edit',compact('formula'));
        }
        else{
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
            'name' 	    => 'required',
            'calculate' => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $calculate = $request->calculate;
        $assessmentanswers = AssessmentAnswer::all();
        foreach($assessmentanswers as $assessmentanswer){
            $calculate = str_replace('#'.$assessmentanswer->id.'#',$assessmentanswer->rating,$calculate);
        }
        try{
            eval('return '.$calculate.';');
        }
        catch(\ParseError $e){
            return response()->json([
                'status' 	=> false,
                'message' 	=> 'Formula Error'
            ], 400);
        }
        DB::beginTransaction();
        $formula = Formula::find($id);
        $formula->name = $request->name;
        $formula->calculate = $request->calculate;
        $formula->updated_by = Auth::id();
        $formula->save();
        if (!$formula) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' 	=> $formula
            ], 400);
        }
        $order = 0;
        $ids = [];
        foreach($request->formula as $value){
            if(!$request->input('update_'.$value)){
                $formuladetail = FormulaDetail::create([
                    'formula_id'            => $formula->id,
                    'assessment_answer_id'  => $request->input('answer_'.$value)?$request->input('answer_'.$value):null,
                    'order'                 => ++$order,
                    'value'                 => $request->input('value_'.$value),
                    'operation_before'      => $request->input('operation_before_'.$value),
                    'operation'             => $request->input('operation_'.$value),
                    'updated_by'            => Auth::id()
                ]);
                if (!$formuladetail) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' 	=> $formuladetail
                    ], 400);
                }
                array_push($ids,$formuladetail->id);
            }
            else{
                array_push($ids,$request->input('update_'.$value));
                $formuladetail = FormulaDetail::find($request->input('update_'.$value));
                $formuladetail->order = ++$order;
                $formuladetail->operation_before = $request->input('operation_before_'.$value);
                $formuladetail->value = $request->input('value_'.$value);
                $formuladetail->operation = $request->input('operation_'.$value);
                $formuladetail->updated_by = Auth::id();
                $formuladetail->save();
                if (!$formuladetail) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' 	=> $formuladetail
                    ], 400);
                }
            }
            
        }
        FormulaDetail::whereNotIn('id',$ids)->forceDelete();
        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('formula.index'),
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
            $formula = Formula::find($id);
            $formula->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error archive data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success archive data'
        ], 200);
    }

    public function restore($id)
    {
        try {
            $formula = Formula::onlyTrashed()->find($id);
            $formula->restore();
            Formula::where('id','<>',$id)->update([
                'deleted_at'=>now()
            ]);
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
            $formula = Formula::onlyTrashed()->find($id);
            $formula->forceDelete();
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