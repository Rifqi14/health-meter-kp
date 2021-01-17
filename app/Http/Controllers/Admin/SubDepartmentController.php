<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\SubDepartment;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class SubDepartmentController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/subdepartment'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.subdepartment.index');
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
        $site = $request->site;
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;
        //Count Data
        $query = SubDepartment::with(['user', 'department'])->whereRaw("upper(sub_departments.name) like '%$name%'");
        $query->leftJoin('departments','departments.id','=','sub_departments.department_id');
        if ($arsip) {
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
        $query = SubDepartment::with(['user', 'department'])->select('sub_departments.*')->whereRaw("upper(sub_departments.name) like '%$name%'");
        $query->leftJoin('departments','departments.id','=','sub_departments.department_id');
        if ($arsip) {
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
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $result->site_name = $result->department->site->name;
            $data[] = $result;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = SubDepartment::whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = SubDepartment::whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $data[] = $result;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.subdepartment.create');
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
            'code'          => 'required|unique:sub_departments',
            'department_id' => 'required',
            'name'          => 'required',
            'site_id'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $department = Department::find($request->department_id);
        $subdepartment = SubDepartment::create([
            'department_id' => $request->department_id,
            'code'          => $department->code.strtoupper($request->code),
            'name'          => $request->name,
            'updated_by'    => Auth::id(),
        ]);
        if (!$subdepartment) {
            return response()->json([
                'status' => false,
                'message' 	=> $subdepartment
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('subdepartment.index'),
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
        $subdepartment = SubDepartment::with('department')->withTrashed()->find($id);
        if($subdepartment){
            return view('admin.subdepartment.detail',compact('subdepartment'));
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
        $subdepartment = SubDepartment::with('department')->withTrashed()->find($id);
        if ($subdepartment) {
            return view('admin.subdepartment.edit', compact('subdepartment'));
        } else {
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
            'code'          => 'required|unique:sub_departments,code,'.$id,
            'department_id' => 'required',
            'name'          => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $subdepartment = SubDepartment::withTrashed()->find($id);
        $subdepartment->department_id = $request->department_id;
        //$subdepartment->code = strtoupper($request->code);
        $subdepartment->name = $request->name;
        $subdepartment->updated_by = Auth::id();
        $subdepartment->save();
        if (!$subdepartment) {
            return response()->json([
                'status' => false,
                'message' 	=> $subdepartment
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'results'   => route('subdepartment.index')
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
            $subdepartment = SubDepartment::find($id);
            $subdepartment->delete();
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
            $subdepartment = SubDepartment::onlyTrashed()->find($id);
            $subdepartment->restore();
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
            $subdepartment = SubDepartment::onlyTrashed()->find($id);
            $subdepartment->forceDelete();
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