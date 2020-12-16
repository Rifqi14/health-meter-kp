<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'department'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.department.index');
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
        $query = DB::table('departments');
        $query->select('departments.*');
        $query->whereRaw("upper(departments.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('departments');
        $query->select('departments.*','parent.name as parent_name');
        $query->leftJoin('departments as parent','parent.id','=','departments.parent_id');
        $query->whereRaw("upper(departments.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $departments = $query->get();

        $data = [];
        foreach($departments as $department){
            $department->no = ++$start;
			$data[] = $department;
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
        $query = DB::table('departments');
        $query->select('departments.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('departments');
        $query->select('departments.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $departments = $query->get();

        $data = [];
        foreach($departments as $department){
            $department->no = ++$start;
			$data[] = $department;
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
        return view('admin.department.create');
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
            'code'      => 'required|unique:departments',
            'name'      => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $department = Department::create([
            'parent_id' => $request->parent_id?$request->parent_id:0,
            'code' 	    => $request->code,
            'name' 	    => $request->name,
            'is_show' 	    => $request->is_show?1:0,
        ]);
        if (!$department) {
            return response()->json([
                'status' => false,
                'message' 	=> $department
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('department.index'),
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
        $department = Department::with('parent')->find($id);
        if($department){
            return view('admin.department.edit',compact('department'));
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
            'code'      => 'required|unique:departments,code,'.$id,
            'name' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $department = Department::find($id);
        $department->code = $request->code;
        $department->name = $request->name;
        $department->parent_id = $request->parent_id?$request->parent_id:0;
        $department->is_show = $request->is_show?1:0;
        $department->save();

        if (!$department) {
            return response()->json([
                'status' => false,
                'message' 	=> $department
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('department.index'),
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
            $department = Department::find($id);
            $department->delete();
            $this->destroychild($department->id);
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

    function destroychild($parent_id){
        $departments= Department::where('parent_id','=',$parent_id)->get();
		foreach($departments as $department){
            try {
                Department::find($department->id)->delete();
                $this->destroychild($department->id);
            } catch (\Illuminate\Database\QueryException $e) {

            }
		}

    }

    public function import()
    {
        return view('admin.department.import');
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' 	    => 'required|mimes:xlsx'
        ]);
        $file = $request->file('file');
        try {
            $filetype 	= \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch(\Exception $e) {
            die('Error loading file "'.pathinfo($file,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        $data 	= [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++){
            $parent_code = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            $code = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $name = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $parent = Department::whereRaw("upper(code) = '$parent_code'")->first();
            if($code){
                $data[] = array(
                    'index'=>$no,
                    'parent_id'=>$parent?$parent->id:0,
                    'parent_name' => $parent?$parent->name:'',
                    'code' => $code,
                    'name' => $name,
                );
                $no++;
            }
        }
        return response()->json([
            'status' 	=> true,
            'data' 	=> $data
        ], 200);
    }

    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'departments' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $departments = json_decode($request->departments);
        foreach($departments as $department){
            $cek = Department::whereRaw("upper(code) = '$department->code'")->first();
            if(!$cek){
                $department = Department::create([
                    'parent_id' => $department->parent_id,
                    'code' 	=> strtoupper($department->code),
                    'name' => $department->name
                ]);
            }
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('department.index'),
        ], 200);
    }
}
