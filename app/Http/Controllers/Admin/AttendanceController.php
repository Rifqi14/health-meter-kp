<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AttendanceController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/attendance'));
        $this->middleware('accessmenu', ['except'=>'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workforce = Auth::user()->workforce;
        $assessment = Assessment::where('assessment_date', date('Y-m-d'))->where('workforce_id', $workforce->id)->get()->count();
        $attendance = Attendance::where('date', date('Y-m-d'))->where('workforce_id', $workforce->id)->get()->count();
        return view('admin.attendance.index', compact('assessment', 'attendance'));
    }

    public function read(Request $request)
    {
        $start                      = $request->start;
        $length                     = $request->length;
        $query                      = $request->search['value'];
        $sort                       = $request->columns[$request->order[0]['column']]['data'];
        $dir                        = $request->order[0]['dir'];
        $attendance_description_id  = $request->attendance_description_id;
        $date                       = $request->date;
        $workforce_id               = $request->workforce_id;

        // Count Data
        $query                      = Attendance::with(['workforce', 'description'])->where('workforce_id', $workforce_id);
        if ($attendance_description_id) {
            $query->where('attendance_description_id', $attendance_description_id);
        }
        if ($date) {
            $query->where('date', $date);
        }
        $recordsTotal               = $query->count();

        // Select Pagination
        $query                      = Attendance::with(['workforce', 'description'])->where('workforce_id', $workforce_id);
        if ($attendance_description_id) {
            $query->where('attendance_description_id', $attendance_description_id);
        }
        if ($date) {
            $query->where('date', $date);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $workforceAttendances = $query->get();

        $data = [];
        foreach ($workforceAttendances as $result) {
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $workforce = Auth::user()->workforce;
        $attendance = Attendance::where('date', date('Y-m-d'))->where('workforce_id', $workforce->id)->get()->count();

        if (!$attendance) {
            return view('admin.attendance.create', compact('workforce'));
        } else {
            return Redirect::back()->withErrors(['msg' => 'Anda telah mengisi kehadiran untuk hari ini. Anda tidak dapat melakukan perubahan maupun penambahan data kehadiran.']);
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
        $validator  = Validator::make($request->all(), [
            'attendance_description_id'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        try {
            $attendance = Attendance::create([
                'workforce_id'              => $request->workforce_id,
                'date'                      => Carbon::parse($request->date)->toDate(),
                'attendance_description_id' => $request->attendance_description_id
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status'    => false,
                'message'   => $ex->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('attendance.index'),
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
        $attendance         = Attendance::find($id);
        if ($attendance) {
            return view('admin.attendance.detail', compact('attendance'));
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
        $attendance         = Attendance::find($id);
        if ($attendance) {
            return view('admin.attendance.detail', compact('attendance'));
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
        try {
            $attendance = Attendance::find($id);
            $attendance->forceDelete();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => "Error delete data " . $th->errorInfo[2]
            ], 400);
        }
    }
}