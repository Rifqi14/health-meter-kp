<?php

namespace App\Http\Controllers\Admin;

use App\Models\CoveringLetter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class CoveringLetterController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/coveringletter'));
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $decription = strtoupper($request->decription);
        $arsip = $request->category;

        //Count Data
        $query = CoveringLetter::with([
            'patient',
            'doctor',
            'referraldoctor',
            'consultation',
            'doctorsite',
            'medicine',
            'partner',
            'patientsite',
            'referralpartner',
            'speciality',
            'referralspeciality',
            'usingrule',
            'workforce'
        ]);
        // $query->whereRaw("upper(decription) like '%$decription%'");
        // if ($arsip) {
        //     $query->onlyTrashed();
        // }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = CoveringLetter::with([
            'patient',
            'doctor',
            'referraldoctor',
            'consultation',
            'doctorsite',
            'medicine',
            'partner',
            'patientsite',
            'referralpartner',
            'speciality',
            'referralspeciality',
            'usingrule',
            'workforce'
        ]);
        //     $query->onlyTrashed();
        // }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $coveringletters = $query->get();

        $data = [];
        foreach ($coveringletters as $coveringletter) {
            $coveringletter->no = ++$start;
            $data[] = $coveringletter;
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
        return view('admin.coveringletter.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.coveringletter.create');
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
            'workforce_id'          => 'required',
            'letter_date'           => 'required',
            'type' 	                => 'required',
            'number'                => 'required',
            'patient_id'            => 'required',
            'patient_site_id'       => 'required',
            'doctor_id'             => 'required',
            'doctor_site_id'        => 'required',
            'partner_id'            => 'required',
            'speciality_id'         => 'required'
           
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'    => $validator->errors()->first()
            ], 400);
        }
        try {
            $coveringletter = CoveringLetter::create([
                'type'                   => $request->type,
                'number'                 => $request->number,
                'letter_date'            => $request->letter_date,
                'workforce_id'           => $request->workforce_id,
                'patient_id'             => $request->patient_id,
                'patient_site_id'        => $request->patient_site_id != 'null' ? $request->patient_site_id:null,
                'doctor_id'              => $request->doctor_id,
                'doctor_site_id'         => $request->doctor_site_id != 'null' ? $request->doctor_site_id:null,
                'partner_id'             => $request->partner_id != 'null' ? $request->partner_id:null,
                'speciality_id'          => $request->speciality_id != 'null' ? $request->speciality_id:null,
                'referral_doctor_id'     => $request->referral_doctor_id,
                'referral_partner_id'    => $request->referral_partner_id != 'null' ? $request->referral_partner_id:null,
                'referral_speciality_id' => $request->referral_speciality_id != 'null' ? $request->referral_speciality_id:null,
                'consultation_id'        => $request->consultation_id,
                'medicine_id'            => $request->medicine_id,
                'using_rule_id'          => $request->using_rule_id,
                'amount'                 => $request->amount,
                'print_status'           => 0,
                'status'                 =>'Draft',
                'description'            => $request->description,
                'updated_by'             => Auth::id(),
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status'      => false,
                'message'     => $ex->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('coveringletter.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CoveringLetter  $coveringLetter
     * @return \Illuminate\Http\Response
     */
    public function show(CoveringLetter $coveringLetter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CoveringLetter  $coveringLetter
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $coveringletter = CoveringLetter::find($id);
        if($coveringletter)
        {
            return view('admin.coveringletter.edit', compact('coveringletter'));
        }else{
            abort(404);
        }
    }
    public function print($id)
    {

        $coveringletter = CoveringLetter::with([
                                'patient',
                                'doctor',
                                'referraldoctor',
                                'consultation',
                                'doctorsite',
                                'medicine',
                                'partner',
                                'patientsite',
                                'referralpartner',
                                'speciality',
                                'referralspeciality',
                                'usingrule',
                                'workforce'
        ])->find($id);

        if ($coveringletter) {
            $coveringletter->print_status = 1;
            $coveringletter->save();
            return view('admin.coveringletter.print', compact('coveringletter'));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CoveringLetter  $coveringLetter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'workforce_id'          => 'required',
            'letter_date'           => 'required',
            'type'                  => 'required',
            'number'                => 'required',
            'patient_id'            => 'required',
            'patient_site_id'       => 'required',
            'doctor_id'             => 'required',
            'doctor_site_id'        => 'required',
            'partner_id'            => 'required',
            'speciality_id'         => 'required'
           
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'    => $validator->errors()->first()
            ], 400);
        }

        $coveringletter = CoveringLetter::find($id);
        $coveringletter->type                   = $request->type;
        $coveringletter->number                 = $request->number;
        $coveringletter->letter_date            = $request->letter_date;
        $coveringletter->workforce_id           = $request->workforce_id;
        $coveringletter->patient_id             = $request->patient_id;
        $coveringletter->patient_site_id        = $request->patient_site_id != 'null' ? $request->patient_site_id : null;
        $coveringletter->doctor_id              = $request->doctor_id;
        $coveringletter->doctor_site_id         = $request->doctor_site_id != 'null' ? $request->doctor_site_id : null;
        $coveringletter->partner_id             = $request->partner_id != 'null' ? $request->partner_id : null;
        $coveringletter->speciality_id          = $request->speciality_id != 'null' ? $request->speciality_id : null;
        $coveringletter->referral_doctor_id     = $request->referral_doctor_id;
        $coveringletter->referral_partner_id    = $request->referral_partner_id != 'null' ? $request->referral_partner_id : null;
        $coveringletter->referral_speciality_id = $request->referral_speciality_id != 'null' ? $request->referral_speciality_id : null;
        $coveringletter->consultation_id        = $request->consultation_id;
        $coveringletter->medicine_id            = $request->medicine_id;
        $coveringletter->using_rule_id          = $request->using_rule_id;
        $coveringletter->amount                 = $request->amount;
        $coveringletter->description            = $request->description;
        $coveringletter->print_status           = 0;
        $coveringletter->status                 = 'Draft';
        $coveringletter->updated_by             = Auth::id();
        $coveringletter->save();

        if (!$coveringletter) {
            return response()->json([
                'status'    => false,
                'message'     => $coveringletter
            ], 400);
        }

        return response()->json([
            'status'     => true,
            'results'     => route('coveringletter.index'),
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CoveringLetter  $coveringLetter
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $coveringletter = CoveringLetter::find($id);
            $coveringletter->forceDelete();
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
