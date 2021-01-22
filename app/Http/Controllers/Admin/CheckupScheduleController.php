<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\CheckupSchedule;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class CheckupScheduleController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/checkupschedule'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = CheckupSchedule::with('patient')->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = CheckupSchedule::with('patient')->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $checkupschedules = $query->get();

        $data = [];
        foreach ($checkupschedules as $checkupschedule) {
            $checkupschedule->no = ++$start;
            $checkupschedule->prod = ["<span>$checkupschedule->patient->name</span><span style='float:right'><i> $checkupschedule->checkup_date</i></span>"];
            $data[] = $checkupschedule;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function index()
    {
        return view('admin.checkupschedule.index');
    }
    public function create()
    {
        return view('admin.checkupschedule.create');
    }
    public function store(Request $request)
    {
        // dd($request->patient_id);
        // $validator = Validator::make($request->all(), [
        //     'patient_id '                 => 'required',
        //     'examination_type_id'         => 'required',
        //     'checkup_date'                => 'required',
        //     'schedules_maker_id'          => 'required',
        //     'first_approval_id'           => 'required',
        //     'second_approval_id'          => 'required',
            
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status'     => false,
        //         'message'    => $validator->errors()->first()
        //     ], 400);
        // }
        
        try {
            $checkupschedule = CheckupSchedule::create([
                'patient_id'                  => $request->patient_id,
                'examination_type_id'         => $request->examination_type_id,
                'checkup_date'                => $request->checkup_date,
                'schedule_maker_id'           => $request->schedule_maker_id,
                'first_approval_id'           => $request->first_approval_id,
                'second_approval_id'          => $request->second_approval_id,
                'schedule_maker_title_id'     => $request->schedule_maker_title_id,
                'first_approval_title_id'     => $request->first_approval_title_id,
                'second_approval_title_id'    => $request->second_approval_title_id,
                'status'                      => 0,
                'first_approval_status'       => 0,
                'second_approval_status'      => 0,
                'description'                 => $request->description,
                'updated_by'                  => Auth::id(),
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status'      => false,
                'message'     => $ex->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('checkupschedule.index'),
        ], 200);
    }

    public function edit($id)
    {
        $checkupschedule = CheckupSchedule::find($id);
        if ($checkupschedule) {
            return view('admin.checkupschedule.edit', compact('checkupschedule'));
        } else {
            abort(404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'patient_id '                 => 'required',
            'examination_type_id'         => 'required',
            'checkup_date'                => 'required',
            'schedules_maker_id'          => 'required',
            'first_approval_id'           => 'required',
            'second_approval_id'          => 'required',
            'schedule_maker_title_id'     => 'required',
            'first_approval_title_id'     => 'required',
            'second_approval_title_id'    => 'required',
            'status'                      => 'required',
            'first_approval_status'       => 'required',
            'second_approval_status'      => 'required',
            'status_date'                 => 'required',
            'first_approval_status_date'  => 'required',
            'second_approval_status_date' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'    => $validator->errors()->first()
            ], 400);
        }

        $checkupschedule = CheckupSchedule::find($id);
        $checkupschedule->patient_id                  = $request->patient_id;
        $checkupschedule->examination_type_id         = $request->examination_type_id;
        $checkupschedule->checkup_date                = $request->checkup_date;
        $checkupschedule->schedules_maker_id          = $request->schedules_maker_id;
        $checkupschedule->first_approval_id           = $request->first_approval_id;
        $checkupschedule->second_approval_id          = $request->second_approval_id;
        $checkupschedule->schedule_maker_title_id     = $request->schedule_maker_title_id;
        $checkupschedule->first_approval_title_id     = $request->first_approval_title_id;
        $checkupschedule->second_approval_title_id    = $request->second_approval_title_id;
        $checkupschedule->status                      = $request->status?1:0;
        $checkupschedule->first_approval_status       = $request->first_approval_status;
        $checkupschedule->second_approval_status      = $request->second_approval_status;
        $checkupschedule->status_date                 = $request->status_date;
        $checkupschedule->first_approval_status_date  = $request->first_approval_status_date;
        $checkupschedule->second_approval_status_date = $request->second_approval_status_date;
        $checkupschedule->description                 = $request->description;
        $$checkupschedule->updated_by                 = Auth::id();
        $checkupschedule->save();

        if (!$checkupschedule) {
            return response()->json([
                'status'    => false,
                'message'     => $checkupschedule
            ], 400);
        }

        return response()->json([
            'status'     => true,
            'results'     => route('checkupschedule.index'),
        ], 200);

    }

    public function destroy($id)
    {
        try {
            $checkupschedule = CheckupSchedule::find($id);
            $checkupschedule->delete();
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
