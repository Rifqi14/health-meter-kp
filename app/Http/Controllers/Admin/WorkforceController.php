<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SiteUser;
use App\Models\Workforce;
use App\Models\Patient;
use App\Role;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class WorkforceController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/workforce'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.workforce.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $nid = strtoupper($request->nid);
        $workforce_group_id = $request->workforce_group_id;
        $agency_id = $request->agency_id;
        $site = $request->site;
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;
        $arsip = $request->category;

        //Count Data
        $query = Workforce::with(['updatedby', 'workforcegroup', 'agency', 'title', 'site', 'department', 'subdepartment', 'guarantor'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(nid) like '%$nid%'");
        if ($workforce_group_id) {
            $query->where('workforce_group_id', $workforce_group_id);
        }
        if ($agency_id) {
            $query->where('agency_id', $agency_id);
        }
        if ($site) {
            $query->where('site_id', $site);
        }
        if($data_manager){
            $query->where('site_id',$site_id);
        }
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Workforce::with(['updatedby', 'workforcegroup', 'agency', 'title', 'site', 'department', 'subdepartment', 'guarantor'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(nid) like '%$nid%'");
        if ($workforce_group_id) {
            $query->where('workforce_group_id', $workforce_group_id);
        }
        if ($agency_id) {
            $query->where('agency_id', $agency_id);
        }
        if ($site) {
            $query->where('site_id', $site);
        }
        if($data_manager){
            $query->where('site_id',$site_id);
        }
        if ($arsip) {
            $query->onlyTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
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
        $nid = strtoupper($request->nid);
        $site_id = $request->site_id;
        $title_id = $request->title_id;

        //Count Data
        $query = Workforce::whereRaw("(upper(name) like '%$name%' or upper(nid) like '%$name%')")->whereRaw("upper(nid) like '%$nid%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        if($title_id){
            $query->where('title_id',$title_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Workforce::whereRaw("(upper(name) like '%$name%' or upper(nid) like '%$name%')")->whereRaw("upper(nid) like '%$nid%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        if($title_id){
            $query->where('title_id',$title_id);
        }
        $query->orderBy('name', 'asc');
        $query->offset($start);
        $query->limit($length);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $result->namenid = ["<span>$result->name<br><small>$result->nid</small></span>"];
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
    public function create(Request $request)
    {
        if(in_array('create',$request->actionmenu)){
            return view('admin.workforce.create');
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
            'nid'               => 'required|unique:workforces',
            'name'              => 'required',
            'workforce_group_id'=> 'required',
            'agency_id'         => 'required',
            'site_id'           => 'required',
            'department_id'     => 'required',
            'sub_department_id' => 'required',
            'email'             => 'required',
            'password'          => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        try {
            DB::beginTransaction();
            $workforce = Workforce::create([
                'nid'               => $request->nid,
                'name'              => $request->name,
                'workforce_group_id'=> $request->workforce_group_id,
                'agency_id'         => $request->agency_id,
                'grade_id'          => $request->grade_id,
                'title_id'          => $request->title_id,
                'site_id'           => $request->site_id,
                'department_id'     => $request->department_id,
                'sub_department_id' => $request->sub_department_id,
                'guarantor_id'      => $request->guarantor_id,
                'start_date'        => $request->start_date,
                'finish_date'       => $request->finish_date,
                'updated_by'        => Auth::id()
            ]);
            if ($workforce) {
                $user = User::create([
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'username'      => $workforce->nid,
                    'password'      => Hash::make($request->password),
                    'status'        => 1,
                    'workforce_id'  => $workforce->id
                ]);
                if (!$user) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => $user,
                    ], 400);
                }
                $patient = Patient::create([
                    'name'          => $request->name,
                    'status'        => 'Pegawai',
                    'birth_date'    => date('Y-m-d'),
                    'site_id'       => $request->site_id,
                    'updated_by'    => Auth::id(),
                    'workforce_id'  => $workforce->id
                ]);
                if (!$patient) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => $user,
                    ], 400);
                }
            }
        } catch (QueryException $ex) {
            return response()->json([
                'status'      => false,
                'message'     => $ex->errorInfo[2]
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('workforce.index'),
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
        $workforce = Workforce::withTrashed()->find($id);
        if ($workforce) {
            return view('admin.workforce.detail', compact('workforce'));
        } else {
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
        $workforce = Workforce::with(['updatedby', 'workforcegroup', 'agency', 'title', 'site', 'department', 'subdepartment', 'guarantor','user'])->find($id);
        if ($workforce) {
            return view('admin.workforce.edit', compact('workforce'));
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
            'nid'               => 'required|unique:workforces,nid,'.$id,
            'name'              => 'required',
            'workforce_group_id'=> 'required',
            'agency_id'         => 'required',
            'site_id'           => 'required',
            'department_id'     => 'required',
            'sub_department_id' => 'required',
            'email'             => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $workforce = Workforce::withTrashed()->find($id);
        if ($workforce->nid != $request->nid) {
            $user = User::where('workforce_id', $workforce->id)->first();
            $user->username = $request->nid;
            $user->save();
            if (!$user) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $user
                ], 400);
            }
        }
        $user = User::where('workforce_id', $workforce->id)->first();
        $user->email = $request->email;
        $user->save();
        if (!$user) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message'     => $user
            ], 400);
        }
        if($request->password){
            $user = User::where('workforce_id', $workforce->id)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            if (!$user) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $user
                ], 400);
            }
        }
        $workforce->nid                 = $request->nid;
        $workforce->name                = $request->name;
        $workforce->workforce_group_id  = $request->workforce_group_id;
        $workforce->agency_id           = $request->agency_id;
        $workforce->grade_id            = $request->grade_id;
        $workforce->title_id            = $request->title_id;
        $workforce->site_id             = $request->site_id;
        $workforce->department_id       = $request->department_id;
        $workforce->sub_department_id   = $request->sub_department_id;
        $workforce->guarantor_id        = $request->guarantor_id;
        $workforce->start_date          = $request->start_date;
        $workforce->finish_date         = $request->finish_date;
        $workforce->updated_by          = Auth::id();
        $workforce->save();
        if (!$workforce) {
            return response()->json([
                'status' => false,
                'message' 	=> $workforce
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'results'   => route('workforce.index')
        ], 200);
    }

    public function import()
    {
        return view('admin.workforce.import');
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'         => 'required|mimes:xlsx'
        ]);
        $file = $request->file('file');
        try {
            $filetype     = \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch (\Exception $e) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
        $data     = [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
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
            $workforce = Workforce::find($id);
            $workforce->delete();
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
            $workforce = Workforce::onlyTrashed()->find($id);
            $workforce->restore();
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
            $workforce = Workforce::onlyTrashed()->find($id);
            $workforce->forceDelete();
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