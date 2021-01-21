<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\HealthConsultation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HealthConsultationController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'healthconsultation'));
        $this->middleware('accessmenu');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];

        //Count Data
        $query = DB::table('health_consultations');
        $query->select('health_consultations.*', 'doctors.name as doctor_name', 'patients.name as patient_name', 'sites.name as site', 'sites.name as site_doctor');
        $query->leftJoin('doctors','doctors.id','=','health_consultations.doctor_id');
        $query->leftJoin('patients','patients.id','=','health_consultations.patient_id');
        $query->leftJoin('sites','sites.id','=','health_consultations.site_patient_id');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('health_consultations');
        $query->select('health_consultations.*', 'doctors.name as doctor_name', 'patients.name as patient_name', 'sites.name as site', 'sites.name as site_doctor');
        $query->leftJoin('doctors','doctors.id','=','health_consultations.doctor_id');
        $query->leftJoin('patients','patients.id','=','health_consultations.patient_id');
        $query->leftJoin('sites','sites.id','=','health_consultations.site_patient_id');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $healthconsultations = $query->get();

        $data = [];
        foreach ($healthconsultations as $healthconsultation) {
            $healthconsultation->no = ++$start;
            $data[] = $healthconsultation;
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

        //Count Data
        $query = DB::table('health_consultations');
        $query->select('health_consultations.*');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('health_consultations');
        $query->select('health_consultations.*');
        // $query->orderBy('complaint', 'asc');
        $query->offset($start);
        $query->limit($length);
        $healthconsultations = $query->get();

        $data = [];
        foreach ($healthconsultations as $healthconsultation) {
            $healthconsultation->no = ++$start;
            $data[] = $healthconsultation;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }
    public function index()
    {
        return view('admin.healthconsultation.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.healthconsultation.create');
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
            'tanggal'      => 'required',
            'complaint'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'        => false,
                'message'       => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $healthconsultation = HealthConsultation::create([
            'tanggal'  => $request->tanggal,
            'patient_id'       => $request->patient,
            'site_patient_id'          => $request->site_id,
            'doctor_id'         => $request->doctor,
            'site_doctor_id'    => $request->distrik_id,
            'complaint' => $request->complaint,
            'diagnose_id' => $request->diagnose,
            'note' => $request->note,
            'updated_by'    => Auth::id()
        ]);
        if (!$healthconsultation) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $healthconsultation
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'        => true,
            'results'       => route('healthconsultation.index'),
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
        $healthconsultation = HealthConsultation::find($id);
        if($healthconsultation){
            return view('admin.healthconsultation.edit',compact('healthconsultation'));
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
            'tanggal'      => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $healthconsultation = HealthConsultation::find($id);
        $healthconsultation->patient_id = $request->patient;
        $healthconsultation->tanggal = $request->tanggal;
        $healthconsultation->site_patient_id = $request->site_id;
        $healthconsultation->doctor_id = $request->doctor;
        $healthconsultation->site_doctor_id = $request->distrik_id;
        $healthconsultation->complaint = $request->complaint;
        $healthconsultation->diagnose_id = $request->diagnose;
        $healthconsultation->note = $request->note;
        $healthconsultation->updated_by = Auth::id();
        $healthconsultation->save();

        if (!$healthconsultation) {
            return response()->json([
                'status' => false,
                'message' 	=> $healthconsultation
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('healthconsultation.index'),
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
            $healthconsultation = HealthConsultation::find($id);
            $healthconsultation->delete();
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
