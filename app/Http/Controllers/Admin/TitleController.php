<?php

namespace App\Http\Controllers\Admin;

use App\Models\Title;
use App\Role;
use App\Models\Workforce;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
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
        $code = strtoupper($request->code);
        $shortname = strtoupper($request->shortname);
        $category = $request->category;

        //Count Data
        $query = Title::with(['user'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(code) like '%$code%'")->whereRaw("upper(shortname) like '%$shortname%'");
        if ($category) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Title::with(['user'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(code) like '%$code%'")->whereRaw("upper(shortname) like '%$shortname%'");
        if ($category) {
            $query->onlyTrashed();
        }
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
    public function readrole(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $title_id = $request->title_id;

        //Count Data
        $query = DB::table('role_titles');
        $query->select('roles.*');
        $query->leftJoin('roles', 'roles.id', '=', 'role_titles.role_id');
        $query->where('title_id', $title_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('role_titles');
        $query->select('roles.*');
        $query->leftJoin('roles', 'roles.id', '=', 'role_titles.role_id');
        $query->where('title_id', $title_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $roles = $query->get();

        $data = [];
        foreach ($roles as $role) {
            $role->no = ++$start;
            $data[] = $role;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    public function assignrole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $title = Title::find($request->title_role_id);
        $role = Role::find($request->role_id);
        $title->attachRole($role);
        if (!$title) {
            return response()->json([
                'status' => false,
                'message'     => $title
            ], 400);
        }
        return response()->json([
            'status' => true,
            'message'     => 'Role has been added'
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
            'code'      => 'required|unique:titles',
            'name'      => 'required',
            'shortname' => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $title = Title::create([
            'name'      => $request->name,
            'code'      => strtoupper($request->code),
            'shortname' => $request->shortname,
            'updated_by'=> Auth::id()
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
        $title = Title::with(['user'])->find($id);
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
        $query = Workforce::select('workforces.*');
        $query->where('workforces.title_id', $title_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Workforce::with(['workforcegroup', 'agency'])->select('workforces.*');
        $query->where('workforces.title_id', $title_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $workforces = $query->get();

        $data = [];
        foreach($workforces as $workforce){
            $workforce->no = ++$start;
			$data[] = $workforce;
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
        $title = Title::with(['user'])->withTrashed()->find($id);
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
            'code'      => 'required|unique:titles,code,'.$id,
            'name'      => 'required',
            'shortname' => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $title = Title::withTrashed()->find($id);
        $title->code = $request->code;
        $title->name = $request->name;
        $title->shortname = $request->shortname;
        $title->updated_by = Auth::id();
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
            $title = Title::onlyTrashed()->find($id);
            $title->restore();
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
            $title = Title::onlyTrashed()->find($id);
            $title->forceDelete();
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
            $shortname = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            if($code){
                $data[] = array(
                    'index'=>$no,
                    'code' => $code,
                    'name' => $name,
                    'shortname' => $shortname,
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
                    'code' 	=> strtoupper($title->code),
                    'name' => $title->name,
                    'shortname' => $title->shortname,
                    'updated_by'=> Auth::id()
                ]);
            }
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('title.index'),
        ], 200);
    }
    public function deleterole(Request $request)
    {
        $role_id = $request->role_id;
        $title_id = $request->title_id;
        try {
            $title = Title::find($title_id);
            $role = Role::find($role_id);
            $title->detachRole($role);
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