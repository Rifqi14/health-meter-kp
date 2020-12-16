<?php

namespace App\Http\Controllers\Admin;

use App\Models\Formula;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $operation = [
            'add'       => 'Penjumlahan',
            'multiply'  => 'Pengali'
        ];

        $result = [
            'normal'      => 'Normal',
            'percentage'  => 'Persentasi'
        ];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('formulas');
        $query->select('formulas.*');
        $query->whereRaw("upper(formulas.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('formulas');
        $query->select('formulas.*');
        $query->whereRaw("upper(formulas.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $formulas = $query->get();

        $data = [];
        foreach($formulas as $formula){
            $formula->no = ++$start;
            $formula->operation = $operation[$formula->operation];
            $formula->result = $result[$formula->result];
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
            'operation' => 'required',
            'result' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $formula = Formula::create([
            'name' 	    => $request->name,
			'operation' => $request->operation,
            'result' 	=> $request->result
        ]);
        if (!$formula) {
            return response()->json([
                'status' => false,
                'message' 	=> $formula
            ], 400);
        }
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
        $operation = [
            'add'       => 'Penjumlahan',
            'multiply'  => 'Pengali'
        ];

        $result = [
            'normal'      => 'Normal',
            'percentage'  => 'Persentasi'
        ];
        $formula = Formula::find($id);
        if($formula){
            return view('admin.formula.detail',compact('formula','operation','result'));
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
            'operation' => 'required',
            'result' 	=> 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $formula = Formula::find($id);
        $formula->name = $request->name;
        $formula->operation = $request->operation;
        $formula->result = $request->result;
        $formula->save();
        if (!$formula) {
            return response()->json([
                'status' => false,
                'message' 	=> $formula
            ], 400);
        }
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
                'message'     => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
