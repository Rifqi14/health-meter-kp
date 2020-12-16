<?php

namespace App\Http\Controllers\Admin;

use App\Models\Grade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'grade'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.grade.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = Grade::select('grades.*');
        $query->whereRaw("upper(grades.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Grade::select('grades.*');
        $query->whereRaw("upper(grades.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $grades = $query->get();

        $data = [];
        foreach($grades as $grade){
            $grade->no = ++$start;
			$data[] = $grade;
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
        $query = Grade::select('grades.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Grade::select('grades.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $grades = $query->get();

        $data = [];
        foreach($grades as $grade){
            $grade->no = ++$start;
			$data[] = $grade;
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
        return view('admin.grade.create');
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
            'inpatient_id'  => 'required',
            'code'      => 'required|unique:grades',
            'name'      => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $grade = Grade::create([
            'inpatient_id'  => $request->inpatient_id,
            'code' 	    => $request->code,
            'name' 	    => $request->name,
        ]);
        if (!$grade) {
            return response()->json([
                'status' => false,
                'message' 	=> $grade
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('grade.index'),
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
        $grade = Grade::find($id);
        if($grade){
            return view('admin.grade.edit',compact('grade'));
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
            'inpatient_id'  => 'required',
            'code'      => 'required|unique:grades,code,'.$id,
            'name'      => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $grade = Grade::find($id);
        $grade->inpatient_id = $request->inpatient_id;
        $grade->code = $request->code;
        $grade->name = $request->name;
        $grade->save();

        if (!$grade) {
            return response()->json([
                'status' => false,
                'message' 	=> $grade
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('grade.index'),
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
            $grade = Grade::find($id);
            $grade->delete();
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
