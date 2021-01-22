<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\MedicalTreatment;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MedicalTreatmentController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'medicaltreatment'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];

        //Count Data
        $query = DB::table('medical_treatments');
        $query->select('medical_treatments.*', 'doctors.name as doctor_name', 'patients.name as patient_name', 'health_consultations.complaint as complaint', 'medical_actions.description as desc');
        $query->leftJoin('doctors','doctors.id','=','medical_treatments.doctor_id');
        $query->leftJoin('patients','patients.id','=','medical_treatments.patient_id');
        $query->leftJoin('health_consultations','health_consultations.id','=','medical_treatments.consultation_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_treatments.medical_action_id');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('medical_treatments');
        $query->select('medical_treatments.*', 'doctors.name as doctor_name', 'patients.name as patient_name', 'health_consultations.complaint as complaint', 'medical_actions.description as desc');
        $query->leftJoin('doctors','doctors.id','=','medical_treatments.doctor_id');
        $query->leftJoin('patients','patients.id','=','medical_treatments.patient_id');
        $query->leftJoin('health_consultations','health_consultations.id','=','medical_treatments.consultation_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_treatments.medical_action_id');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $medicaltreatments = $query->get();

        $data = [];
        foreach ($medicaltreatments as $medicaltreatment) {
            $medicaltreatment->no = ++$start;
            $data[] = $medicaltreatment;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.medicaltreatment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.medicaltreatment.create');
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
            'date'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'        => false,
                'message'       => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $medicaltreatment = MedicalTreatment::create([
            'patient_id'  => $request->patient,
            'date'       => $request->date,
            'doctor_id'          => $request->doctor,
            'consultation_id'         => $request->consultation,
            'medical_action_id'    => $request->medical_treatment,
            'description' => $request->description,
            'updated_by'    => Auth::id()
        ]);
        if (!$medicaltreatment) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $medicaltreatment
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'        => true,
            'results'       => route('medicaltreatment.index'),
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
        $medicaltreatment = MedicalTreatment::find($id);
        if($medicaltreatment){
            return view('admin.medicaltreatment.edit',compact('medicaltreatment'));
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
            'date'      => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $medicaltreatment = MedicalTreatment::find($id);
        $medicaltreatment->patient_id = $request->patient;
        $medicaltreatment->date = $request->date;
        $medicaltreatment->doctor_id = $request->doctor;
        $medicaltreatment->consultation_id = $request->consultation;
        $medicaltreatment->medical_action_id = $request->medical_treatment;
        $medicaltreatment->description = $request->description;
        $medicaltreatment->updated_by = Auth::id();
        $medicaltreatment->save();

        if (!$medicaltreatment) {
            return response()->json([
                'status' => false,
                'message' 	=> $medicaltreatment
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('medicaltreatment.index'),
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
            $medicaltreatment = MedicalTreatment::find($id);
            $medicaltreatment->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     =>  'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
