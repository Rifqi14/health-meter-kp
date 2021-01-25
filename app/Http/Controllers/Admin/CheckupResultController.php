<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CheckupResult;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class CheckupResultController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/checkupresult'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.checkupresult.index');
    }

    public function read(Request $request)
    {
        $start          = $request->start;
        $length         = $request->length;
        $query          = $request->search['value'];
        $sort           = $request->columns[$request->order[0]['column']]['data'];
        $dir            = $request->order[0]['dir'];
        $workforce_id   = $request->workforce_id;
        $patient_id     = $request->patient_id;
        $partner_id     = $request->partner_id;
        $date           = $request->date;

        // Count Data
        $query          = CheckupResult::with(['workforce', 'patient', 'partner', 'updatedby']);
        if ($workforce_id) {
            $query->where('workforce_id', $workforce_id);
        }
        if ($patient_id) {
            $query->where('patient_id', $patient_id);
        }
        if ($partner_id) {
            $query->where('partner_id', $partner_id);
        }
        if ($date) {
            $query->where('date', $date);
        }
        $recordsTotal   = $query->count();

        // Select Pagination
        $query          = CheckupResult::with(['workforce', 'patient', 'partner', 'updatedby']);
        if ($workforce_id) {
            $query->where('workforce_id', $workforce_id);
        }
        if ($patient_id) {
            $query->where('patient_id', $patient_id);
        }
        if ($partner_id) {
            $query->where('partner_id', $partner_id);
        }
        if ($date) {
            $query->where('date', $date);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $results        = $query->get();

        $data           = [];
        foreach ($results as $key => $value) {
            $value->no  = ++$start;
            $data[]     = $value;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data    
        ], 200);
    }
    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = CheckupResult::with(['patient' => function ($q) use ($name) {
            $q->whereRaw("upper(name) like '%$name%'");
        }]);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = CheckupResult::with(['patient' => function ($q) use ($name) {
            $q->whereRaw("upper(name) like '%$name%'");
        }]);
        $query->offset($start);
        $query->limit($length);
        $checkupresults = $query->get();

        $data = [];
        foreach ($checkupresults as $checkupresult) {
            $checkupresult->no = ++$start;
            $checkupresult->patientname = $checkupresult->patient->name;
            $checkupresult->custom = ["<span>$checkupresult->result</span>
                                <br>
                                <span><b>$checkupresult->patientname</b></span><span style='float:right'><i> $checkupresult->date</i></span>"];
            $data[] = $checkupresult;
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
        return view('admin.checkupresult.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator      = Validator::make($request->all(), [
            'workforce_id'                      => 'required',
            'patient_id'                        => 'required',
            'date'                              => 'required',
            'partner_id'                        => 'required',
            'examination_type_id'               => 'required',
            'examination_evaluation_id'         => 'required',
            'examination_evaluation_level_id'   => 'required',
            'result'                            => 'required',
            'normal_limit'                      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        try {
            $patient                    = Patient::find($request->patient_id);
            $doctor                     = Doctor::find($request->doctor_id);
            $checkupResult              = CheckupResult::create([
                'workforce_id'                      => $request->workforce_id,
                'patient_id'                        => $request->patient_id,
                'patient_site_id'                   => $patient ? $patient->site_id : null,
                'date'                              => $request->date,
                'reference_id'                      => $request->reference_id,
                'checkup_schedule_id'               => $request->checkup_schedule_id,
                'partner_id'                        => $request->partner_id,
                'examination_type_id'               => $request->examination_type_id,
                'result'                            => $request->result,
                'normal_limit'                      => $request->normal_limit,
                'examination_evaluation_id'         => $request->examination_evaluation_id,
                'examination_evaluation_level_id'   => $request->examination_evaluation_level_id,
                'doctor_id'                         => $request->doctor_id,
                'doctor_site_id'                    => $doctor ? $doctor->site_id : null,
                'description'                       => $request->description,
                'updated_by'                        => Auth::id()
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status'    => false,
                'message'   => $ex->errorInfo[2],
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('checkupresult.index')
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
        $result     = CheckupResult::find($id);
        if ($result) {
            return view('admin.checkupresult.detail', compact('result'));
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
        $result     = CheckupResult::find($id);
        if ($result) {
            return view('admin.checkupresult.edit', compact('result'));
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
        $validator      = Validator::make($request->all(), [
            'workforce_id'                      => 'required',
            'patient_id'                        => 'required',
            'date'                              => 'required',
            'partner_id'                        => 'required',
            'examination_type_id'               => 'required',
            'examination_evaluation_id'         => 'required',
            'examination_evaluation_level_id'   => 'required',
            'result'                            => 'required',
            'normal_limit'                      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $patient                                        = Patient::find($request->patient_id);
        $doctor                                         = Doctor::find($request->doctor_id);
        $checkupResult                                  = CheckupResult::find($id);
        $checkupResult->workforce_id                    = $request->workforce_id;
        $checkupResult->patient_id                      = $request->patient_id;
        $checkupResult->patient_site_id                 = $patient ? $patient->site_id : null;
        $checkupResult->date                            = $request->date;
        $checkupResult->reference_id                    = $request->reference_id;
        $checkupResult->checkup_schedule_id             = $request->checkup_schedule_id;
        $checkupResult->partner_id                      = $request->partner_id;
        $checkupResult->examination_type_id             = $request->examination_type_id;
        $checkupResult->result                          = $request->result;
        $checkupResult->normal_limit                    = $request->normal_limit;
        $checkupResult->examination_evaluation_id       = $request->examination_evaluation_id;
        $checkupResult->examination_evaluation_level_id = $request->examination_evaluation_level_id;
        $checkupResult->doctor_id                       = $request->doctor_id;
        $checkupResult->doctor_site_id                  = $doctor ? $doctor->site_id : null;
        $checkupResult->description                     = $request->description;
        $checkupResult->updated_by                      = Auth::id();
        $checkupResult->save();
        if (!$checkupResult) {
            return response()->json([
                'status'    => false,
                'message'   => $checkupResult
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('checkupresult.index')
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
            $checkupresult = CheckupResult::find($id);
            $checkupresult->forceDelete();
        } catch (QueryException $ex) {
            return response()->json([
                'status'        => false,
                'message'       => 'Error delete data ' . $ex->errorInfo[2],
            ], 400);
        }
        return response()->json([
            'status'        => true,
            'message'       => 'Success delete data'
        ], 200);
    }
}