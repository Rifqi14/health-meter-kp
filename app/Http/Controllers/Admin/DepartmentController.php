<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
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
        $category = $request->category;

        //Count Data
        $query = Department::with(['user', 'subdepartment'])->whereRaw("upper(departments.name) like '%$name%'");
        if ($category) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Department::with(['user', 'subdepartment'])->whereRaw("upper(departments.name) like '%$name%'");
        if ($category) {
            $query->onlyTrashed();
        }
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
        $query = Department::with(['user', 'subdepartment'])->whereRaw("upper(departments.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Department::with(['user', 'subdepartment'])->whereRaw("upper(departments.name) like '%$name%'");
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
            'code' 	    => $request->code,
            'name' 	    => $request->name,
            'updated_by'=> Auth::id()
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
        $department = Department::find($id);
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
        $department->updated_by = Auth::id();
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
            $department = Department::onlyTrashed()->find($id);
            $department->restore();
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
            $department = Department::onlyTrashed()->find($id);
            $department->forceDelete();
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
            $code = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            $name = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            if($code){
                $data[] = array(
                    'index'=>$no,
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
                    'code' 	=> strtoupper($department->code),
                    'name' => $department->name,
                    'updated_by' => Auth::id()
                ]);
            }
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('department.index'),
        ], 200);
    }
}