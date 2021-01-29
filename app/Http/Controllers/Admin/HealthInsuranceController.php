<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AuthorizedOfficial;
use App\Models\Doctor;
use App\Models\HealthInsurance;
use App\Models\Patient;
use App\Models\Workforce;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class HealthInsuranceController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/healthinsurance'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.healthinsurance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.healthinsurance.create');
    }

    public function read(Request $request)
    {
        $start                  = $request->start;
        $length                 = $request->length;
        $query                  = $request->search['value'];
        $sort                   = $request->columns[$request->order[0]['column']]['data'];
        $dir                    = $request->order[0]['dir'];
        $cover_letter_type      = strtoupper($request->cover_letter_type);
        $workforce_id           = $request->workforce_id;
        $patient_id             = $request->patient_id;
        $letter_maker_id        = $request->letter_maker_id;
        $authorized_official_id = $request->authorized_official_id;
        $site_id                = $request->site_id;
        $date                   = $request->date;

        // Count Data
        $query                  = HealthInsurance::with(['workforce', 'workforce.site', 'patient', 'patient.site', 'lettermaker', 'lettermaker.site', 'authorizer', 'authorizer.site', 'authorizer.title', 'inpatient', 'updatedby']);
        if ($cover_letter_type) {
            $query->whereRaw("upper(cover_letter_type) like '%$cover_letter_type%'");
        }
        if ($workforce_id) {
            $query->where('workforce_id', $workforce_id);
        }
        if ($patient_id) {
            $query->where('patient_id', $patient_id);
        }
        if ($letter_maker_id) {
            $query->where('letter_maker_id', $letter_maker_id);
        }
        if ($authorized_official_id) {
            $query->where('authorized_official_id', $authorized_official_id);
        }
        if ($site_id) {
            $query->whereRaw("patient_site_id = $site_id OR letter_maker_site_id = $site_id");
        }
        if ($date) {
            $query->whereRaw("date_in = '$date' OR date = '$date'");
        }
        $recordsTotal           = $query->count();

        // Select Pagination
        $query                  = HealthInsurance::with(['workforce', 'workforce.site', 'patient', 'patient.site', 'lettermaker', 'lettermaker.site', 'authorizer', 'authorizer.site', 'authorizer.title', 'inpatient', 'updatedby']);
        if ($cover_letter_type) {
            $query->whereRaw("upper(cover_letter_type) like '%$cover_letter_type%'");
        }
        if ($workforce_id) {
            $query->where('workforce_id', $workforce_id);
        }
        if ($patient_id) {
            $query->where('patient_id', $patient_id);
        }
        if ($letter_maker_id) {
            $query->where('letter_maker_id', $letter_maker_id);
        }
        if ($authorized_official_id) {
            $query->where('authorized_official_id', $authorized_official_id);
        }
        if ($site_id) {
            $query->whereRaw("patient_site_id = $site_id OR letter_maker_site_id = $site_id");
        }
        if ($date) {
            $query->whereRaw("date_in = '$date' OR date = '$date'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $results                = $query->get();

        $data                   = [];
        foreach ($results as $key => $value) {
            $value->no          = ++$start;
            $data[]             = $value;
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

        // Count Data
        $query          = HealthInsurance::whereRaw("upper(cover_letter_type) like '$name' OR upper(letter_number) like '$name'");
        $recordsTotal   = $query->count();

        // Select Pagination
        $query          = HealthInsurance::with(['workforce', 'patient'])->whereRaw("upper(cover_letter_type) like '$name' OR upper(letter_number) like '$name'");
        $query->offset($start);
        $query->limit($length);
        $results        = $query->get();

        $data           = [];
        foreach ($results as $key => $value) {
            $value->no      = ++$start;
            $value->prod    = ["<span>{$value->patient->name}<br>{$value->cover_letter_type} - {$value->letter_number}</span><span style='float:right'><i>{$value->date}</i></span>"];
            $data[]         = $value;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows'  => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator          = Validator::make($request->all(), [
            'cover_letter_type'                     => 'required',
            'letter_number'                         => 'required|unique:health_insurances',
            'workforce_id'                          => 'required',
            'patient_id'                            => 'required',
            'inpatient_id'                          => 'required',
            'date_in'                               => 'required',
            'date'                                  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        try {
            $patient        = Patient::find($request->patient_id);
            $doctor         = Doctor::find($request->patient_id);
            $letter_maker   = Workforce::find(Auth::user()->workforce->id);
            $authorizer     = AuthorizedOfficial::where('site_id', $letter_maker->site_id)->whereRaw("upper(authority) like '%".strtoupper($request->cover_letter_type)."%'")->first();
            $healthinsurance                = HealthInsurance::create([
               'cover_letter_type'              => $request->cover_letter_type,
               'letter_number'                  => $request->letter_number,
               'workforce_id'                   => $request->workforce_id,
               'patient_id'                     => $request->patient_id,
               'patient_site_id'                => $patient ? $patient->site_id : null,
               'letter_maker_id'                => $letter_maker ? $letter_maker->id : null,
               'letter_maker_site_id'           => $letter_maker && $letter_maker->site_id ? $letter_maker->site_id : null,
               'authorized_official_id'         => $authorizer ? $authorizer->id : null,
               'guarantor_id'                   => $letter_maker && $letter_maker->guarantor_id ? $letter_maker->guarantor_id : null,
               'reference_id'                   => $request->reference_id,
               'partner_id'                     => $request->partner_id,
               'doctor_id'                      => $request->doctor_id,
               'inpatient_id'                   => $request->inpatient_id,
               'description'                    => $request->description,
               'date_in'                        => $request->date_in,
               'date'                           => $request->date,
               'print_status'                   => 0,
               'updated_by'                     => Auth::id(),
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status'    => false,
                'message'   => $ex->errorInfo[2],
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('healthinsurance.index')
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
        $insurance          = HealthInsurance::find($id);
        if ($insurance) {
            return view('admin.healthinsurance.detail', compact('insurance'));
        } else {
            abort(404);
        }
    }
    public function print($id)
    {
        $insurance = HealthInsurance::with([
            'workforce',
            'workforce.site',
            'patient',
            'patient.site',
            'lettermaker',
            'lettermaker.site',
            'authorizer',
            'authorizer.site',
            'authorizer.title',
            'inpatient',
            'updatedby',
            'guarantor'])->find($id);
        if ($insurance) {
            $insurance->print_status = 1;
            $insurance->save();
            return view('admin.healthinsurance.print', compact('insurance'));
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
        $insurance          = HealthInsurance::find($id);
        if ($insurance) {
            return view('admin.healthinsurance.edit', compact('insurance'));
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
        $validator          = Validator::make($request->all(), [
            'cover_letter_type'                     => 'required',
            'letter_number'                         => 'required|unique:health_insurances',
            'workforce_id'                          => 'required',
            'patient_id'                            => 'required',
            'inpatient_id'                          => 'required',
            'date_in'                               => 'required',
            'date'                                  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $patient        = Patient::find($request->patient_id);
        $doctor         = Doctor::find($request->patient_id);
        $letter_maker   = Workforce::find(Auth::user()->workforce->id);
        $authorizer     = AuthorizedOfficial::where('site_id', $letter_maker->site_id)->whereRaw("upper(authority) like '%".strtoupper($request->cover_letter_type)."%'")->first();
        $insurance                          = HealthInsurance::find($id);
        $insurance->cover_letter_type       = $request->cover_letter_type;
        $insurance->letter_number           = $request->letter_number;
        $insurance->workforce_id            = $request->workforce_id;
        $insurance->patient_id              = $request->inpatient_id;
        $insurance->patient_site_id         = $patient ? $patient->site_id : null;
        $insurance->letter_maker_id         = $letter_maker ? $letter_maker->id : null;
        $insurance->letter_maker_site_id    = $letter_maker ? $letter_maker->site_id : null;
        $insurance->authorized_official_id  = $authorizer ? $authorizer->id : null;
        $insurance->guarantor_id            = $letter_maker && $letter_maker->guarantor_id ? $letter_maker->guarantor_id : null;
        $insurance->reference_id            = $request->reference_id;
        $insurance->partner_id              = $request->partner_id;
        $insurance->doctor_id               = $request->doctor_id;
        $insurance->inpatient_id            = $request->inpatient_id;
        $insurance->description             = $request->description;
        $insurance->date_in                 = $request->date_in;
        $insurance->date                    = $request->date;
        $insurance->updated_by              = Auth::id();
        $insurance->save();
        if (!$insurance) {
            return response()->json([
                'status'    => false,
                'message'   => $insurance
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('healthinsurance.index')
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
            $insurance      = HealthInsurance::find($id);
            $insurance->forceDelete();
        } catch (QueryException $ex) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error delete data ' . $ex->errorInfo[2],
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete data'
        ], 200);
    }
}