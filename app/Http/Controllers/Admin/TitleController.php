<?php

namespace App\Http\Controllers\Admin;

use App\Models\Title;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class TitleController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'title'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.title.index');
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
        $query = DB::table('titles');
        $query->select('titles.*');
        $query->whereRaw("upper(titles.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('titles');
        $query->select('titles.*','departments.name as department_name','parent.name as parent_name','grades.name as grade_name');
        $query->leftJoin('departments','titles.department_id','=','departments.id');
        $query->leftJoin('grades','titles.grade_id','=','grades.id');
        $query->leftJoin('titles as parent','parent.id','=','titles.parent_id');
        $query->whereRaw("upper(titles.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $titles = $query->get();

        $data = [];
        foreach($titles as $title){
            $title->no = ++$start;
			$data[] = $title;
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
        $query = DB::table('titles');
        $query->select('titles.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('titles');
        $query->select('titles.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $titles = $query->get();

        $data = [];
        foreach($titles as $title){
            $title->no = ++$start;
			$data[] = $title;
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
        return view('admin.title.create');
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
            'department_id'  => 'required',
            'code'      => 'required|unique:titles',
            'name'      => 'required',
            'grade_id'  => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $title = Title::create([
            'department_id'  => $request->department_id,
            'parent_id' 	    => $request->parent_id?$request->parent_id:0,
            'code' 	    => $request->code,
            'name' 	    => $request->name,
            'grade_id' 	    => $request->grade_id,
            
        ]);
        if (!$title) {
            return response()->json([
                'status' => false,
                'message' 	=> $title
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('title.index'),
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
        $title = Title::find($id);
        // dd($title);
        if($title){

            return view('admin.title.detail',compact('title'));
        }
        else{
            abort(404);
        }
    }

    public function employee(Request $request)
    {
        // dd("tes");

        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $title_id = $request->title_id;

        //Count Data
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->leftJoin('employee_movements','employees.id','=','employee_movements.employee_id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->where('employee_movements.title_id', $title_id);
        $query->whereNull('employee_movements.finish');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->leftJoin('employee_movements','employees.id','=','employee_movements.employee_id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->where('employee_movements.title_id', $title_id);
        $query->whereNull('employee_movements.finish');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employeemovements = $query->get();

        $data = [];
        foreach($employeemovements as $employeemovement){
            $employeemovement->no = ++$start;
			$data[] = $employeemovement;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = Title::with('department','grade')->find($id);
        if($title){
            return view('admin.title.edit',compact('title'));
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
            'department_id'  => 'required',
            'code'      => 'required|unique:titles,code,'.$id,
            'name'      => 'required',
            'grade_id'  => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $title = Title::find($id);
        $title->department_id = $request->department_id;
        $title->parent_id = $request->parent_id;
        $title->code = $request->code;
        $title->name = $request->name;
        $title->grade_id = $request->grade_id;
        $title->save();

        if (!$title) {
            return response()->json([
                'status' => false,
                'message' 	=> $title
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('title.index'),
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
            $title = Title::find($id);
            $title->delete();
            $this->destroychild($title->id);
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
        $titles= Title::where('parent_id','=',$parent_id)->get();
		foreach($titles as $title){
            try {
                Title::find($title->id)->delete();
                $this->destroychild($title->id);
            } catch (\Illuminate\Database\QueryException $e) {
                
            }
		}
		
    }
    public function import()
    {
        return view('admin.title.import');
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
            $department_code = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $department = Department::whereRaw("upper(code) = '$department_code'")->first();
            if($code){
                $data[] = array(
                    'index'=>$no,
                    'department_id'=>$department?$department->id:0,
                    'department_name' => $department?$department->name:'',
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
            'titles' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $titles = json_decode($request->titles);
        foreach($titles as $title){
            $cek = Title::whereRaw("upper(code) = '$title->code'")->first();
            if(!$cek){
                $title = Title::create([
                    'department_id' => $title->department_id,
                    'parent_id' => 0,
                    'code' 	=> strtoupper($title->code),
                    'name' => $title->name
                ]);
            }
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('title.index'),
        ], 200);
    }
}
