<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
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
        $site = $request->site;
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;
        //Count Data
        $query = Department::with(['user','site'])->whereRaw("upper(departments.name) like '%$name%'");
        if ($category) {
            $query->onlyTrashed();
        }
        if ($site) {
            $query->where('site_id', $site);
        }
        if($data_manager){
            $query->where('site_id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Department::with(['user','site'])->whereRaw("upper(departments.name) like '%$name%'");
        if ($category) {
            $query->onlyTrashed();
        }
        if ($site) {
            $query->where('site_id', $site);
        }
        if($data_manager){
            $query->where('site_id',$site_id);
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
        $site_id = $request->site_id;

        //Count Data
        $query = Department::with(['user'])->whereRaw("upper(departments.name) like '%$name%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Department::with(['user'])->whereRaw("upper(departments.name) like '%$name%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        $query->offset($start*$length);
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
    public function create(Request $request)
    {
        if(in_array('create',$request->actionmenu)){
            return view('admin.department.create');
        }
        else{
            abort(403);
        }
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
            'code'      => 'required',
            'name'      => 'required',
            'site_id'      => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $exist = Department::whereRaw("upper(code) = '$request->code'")->where('site_id',$request->site_id)->first();
        if($exist){
            return response()->json([
                'status' 	=> false,
        		'message' 	=> 'The code has already been taken.'
        	], 400);  
        }
        $department = Department::create([
            'code' 	    => strtoupper($request->code),
            'name' 	    => $request->name,
            'site_id' 	=> $request->site_id,
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
    public function show(Request $request,$id)
    {
        if(in_array('read',$request->actionmenu)){
            $department = Department::find($id);
            if($department){
                return view('admin.department.detail',compact('department'));
            }
            else{
                abort(404);
            }
        }
        else{
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if(in_array('update',$request->actionmenu)){
            $department = Department::find($id);
            if($department){
                return view('admin.department.edit',compact('department'));
            }
            else{
                abort(404);
            }
        }
        else{
            abort(403);
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
            'code'      => 'required',
            'name' 	    => 'required',
            'site_id' 	    => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $exist = Department::whereRaw("upper(code) = '$request->code'")->where('site_id',$request->site_id)->where('id','<>',$id)->first();
        if($exist){
            return response()->json([
                'status' 	=> false,
        		'message' 	=> 'The code has already been taken.'
        	], 400);  
        }
        $department = Department::find($id);
        $department->site_id    = $request->site_id;
        $department->code       = $request->code;
        $department->name       = $request->name;
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

    public function import(Request $request)
    {
        if(in_array('import',$request->actionmenu)){
            return view('admin.department.import');
        }
        else{
            abort(403);
        }
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
            $site_code = strtoupper($sheet->getCellByColumnAndRow(2, $row)->getValue());
            $status = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $site = Site::whereRaw("upper(code) = '$site_code'")->first();
            if($code){
                $error = [];
                if(!$site){
                    array_push($error,'Distrik Tidak Ditemukan');
                }
                $data[] = array(
                    'index'=>$no,
                    'code' => trim($code),
                    'name' => $name,
                    'site_name'=>$site?$site->name:null,
                    'site_id'=>$site?$site->id:null,
                    'status'=>$status=='Y'?1:0,
                    'error'=>implode($error,'<br>'),
                    'is_import'=>count($error) == 0?1:0
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
        DB::beginTransaction();
        $departments = json_decode($request->departments);
        foreach($departments as $department){
            $cek = Department::whereRaw("upper(code) = '$department->code'")->withTrashed()->first();
            if(!$cek){
                $department = Department::create([
                    'code' 	        => strtoupper($department->code),
                    'name'          => $department->name,
                    'site_id'       => $department->site_id,
                    'updated_by'    => Auth::id()
                ]);
                if (!$department) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'     => $department
                    ], 400);
                }
                $department->deleted_at = $department->status?null:date('Y-m-d H:i:s');
                $department->save();
            }
            else{
                $cek->code      = strtoupper($department->code);
                $cek->name      = $department->name;
                $cek->site_id   = $department->site_id;
                $cek->deleted_at= $department->status?null:date('Y-m-d H:i:s');
                $cek->updated_by= Auth::id();
                $cek->save();
                if (!$cek) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'     => $cek
                    ], 400);
                }
            }
        }
        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('department.index'),
        ], 200);
    }
}