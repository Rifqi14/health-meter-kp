<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\MedicalAction;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordDiagnosis;
use App\Models\MedicalRecordPresciption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class MedicalPersonnelController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'medicalpersonnel'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.medicalpersonnel.index');
    }
    public function read(Request $request)
    {
        $nid = Auth::guard('admin')->user()->username;
        $employee = Employee::where('nid',$nid)->get()->first();
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];

        //Count Data
        $query = DB::table('medical_records');
        $query->select('medical_records.*');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->where('employee_id',$employee->id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('medical_records');
        $query->select('medical_records.*','employees.name as employee_name');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->where('employee_id',$employee->id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $healthmeters = $query->get();

        $data = [];
        foreach($healthmeters as $healthmeter){
            $healthmeter->no = ++$start;
			$data[] = $healthmeter;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $nid = Auth::guard('admin')->user()->username;
        $employee = Employee::where('nid',$nid)->get()->first();
        $medicalactions = MedicalAction::all();
        return view('admin.medicalpersonnel.create',compact('medicalactions','employee'));
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
            'date'      => 'required',
            'employee_id' => 'required',
            'complaint' => 'required'
        ]);
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        } 
        DB::beginTransaction();
        $medicalrecord = MedicalRecord::create([
            'date' 	    => $request->date,
            'employee_id' => $request->employee_id,
			'complaint' => $request->complaint,
            'employee_family_id' 	=> $request->employee_family_id?$request->employee_family_id:null,
            'status'=>'Request',
            'status_invoice'=>0,
            'print_status' 	=> 0
        ]);
        if (!$medicalrecord) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $medicalrecord
            ], 400);
        }
        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('medicalpersonnel.index'),
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
        $nid = Auth::guard('admin')->user()->username;
        $employee = Employee::where('nid',$nid)->get()->first();
        $medicalrecord = MedicalRecord::with('medicalaction','partner','employee')
                                    ->where('id',$id)
                                    ->where('employee_id',$employee->id)->first();
        if($medicalrecord){
            return view('admin.medicalpersonnel.detail',compact('medicalrecord'));
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
