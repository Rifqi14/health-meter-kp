<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Workforce;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class PatientController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/patient'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.patient.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $status = strtoupper($request->status);
        $workforce_id = $request->workforce_id;
        $site_id = $request->site_id;
        $arsip = $request->category;

        //Count Data
        $query = Patient::with(['updatedby', 'site', 'workforce', 'inpatient'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(status) like '%$status%'");
        if ($site_id) {
            $query->where('site_id', $site_id);
        }
        if ($arsip) {
            $query->onlyTrashed();
        }
        if ($workforce_id) {
            $query->where('workforce_id', $workforce_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Patient::with(['updatedby', 'site', 'workforce', 'inpatient'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(status) like '%$status%'");
        if ($site_id) {
            $query->where('site_id', $site_id);
        }
        if ($arsip) {
            $query->onlyTrashed();
        }
        if ($workforce_id) {
            $query->where('workforce_id', $workforce_id);
        }
        $query->orderBy('updated_at', 'asc');
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

        //Count Data
        $query = Patient::whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Patient::whereRaw("upper(name) like '%$name%'");
        $query->orderBy('name', 'asc');
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
        return view('admin.patient.create');
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
            'site_id'           => 'required',
            'workforce_id'      => 'required',
            'name'              => 'required',
            'status'            => 'required',
            'birth_date'        => 'required',
            'inpatient_id'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        try {
            $patient = Patient::create([
                'site_id'           => $request->site_id,
                'workforce_id'      => $request->workforce_id,
                'name'              => $request->name,
                'status'            => $request->status,
                'birth_date'        => $request->birth_date,
                'inpatient_id'      => $request->inpatient_id,
                'updated_by'        => Auth::id()
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status'      => false,
                'message'     => $ex->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('patient.index'),
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
        $patient = Patient::withTrashed()->find($id);
        if ($patient) {
            return view('admin.patient.edit', compact('patient'));
        } else {
            # code...
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
            'code'              => 'required|unique:patients,code,'.$id,
            'nid'               => 'required|unique:patients,nid,'.$id,
            'name'              => 'required',
            'status'            => 'required',
            'birth_date'        => 'required',
            'site_id'           => 'required',
            'department_id'     => 'required',
            'sub_department_id' => 'required',
            'inpatient_id'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $patient = Patient::withTrashed()->find($id);
        $patient->workforce_id      = $request->workforce_id;
        $patient->code              = strtoupper($request->code);
        $patient->name              = $request->name;
        $patient->nid               = strtoupper($request->nid);
        $patient->status            = $request->status;
        $patient->birth_date        = $request->birth_date;
        $patient->site_id           = $request->site_id;
        $patient->department_id     = $request->department_id;
        $patient->sub_department_id = $request->sub_department_id;
        $patient->inpatient_id      = $request->inpatient_id;
        $patient->updated_by        = Auth::id();
        $patient->save();
        if (!$patient) {
            return response()->json([
                'status' => false,
                'message' 	=> $patient
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'results'   => route('patient.index')
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
            $patient = Patient::find($id);
            $patient->delete();
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
            $patient = Patient::onlyTrashed()->find($id);
            $patient->restore();
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
            $patient = Patient::onlyTrashed()->find($id);
            $patient->forceDelete();
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