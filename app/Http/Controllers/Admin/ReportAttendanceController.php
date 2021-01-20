<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\HealthMeter;
use App\Models\Workforce;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ReportAttendanceController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/reportattendance'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.reportattendance.index');
    }

    public function assessment(Request $request)
    {
        $start           = $request->start;
        $length          = $request->length;
        $query           = $request->search['value'];
        $sort            = $request->columns[$request->order[0]['column']]['data'];
        $dir             = $request->order[0]['dir'];
        $date            = $request->date;
        $site_id         = $request->site_id;
        $health_meter_id = $request->health_meter_id;

        // Count Data
        $query           = AssessmentResult::with(['category', 'workforce', 'workforce.site', 'workforce.agency', 'workforce.department', 'workforce.subdepartment', 'workforce.title'])->where('date', $date);
        if ($site_id) {
            $query->where('site_id', $site_id);
        }
        if ($health_meter_id) {
            $query->where('health_meter_id', $health_meter_id);
        }
        $recordsTotal    = $query->count();

        // Select Pagination
        $query           = AssessmentResult::with(['category', 'workforce', 'workforce.site', 'workforce.agency', 'workforce.department', 'workforce.subdepartment', 'workforce.title'])->where('date', $date);
        if ($site_id) {
            $query->where('site_id', $site_id);
        }
        if ($health_meter_id) {
            $query->where('health_meter_id', $health_meter_id);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $assessments = $query->get();

        $data = [];
        foreach ($assessments as $key => $assessment) {
            $assessment->no = ++$start;
            $data[]         = $assessment;
        }
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function totalassessment(Request $request)
    {
        $date = $request->date;
        $site_id = $request->site_id;
        $health_meter_id = $request->health_meter_id;

        $query = AssessmentResult::where('date', $date);
        if ($site_id) {
            $query->where('site_id', $site_id);
        }
        if ($health_meter_id) {
            $query->where('health_meter_id', $health_meter_id);
        }

        return $query->count();
    }

    public function lowriskassessment(Request $request)
    {
        $date           = $request->date;
        $site_id        = $request->site_id;

        $query          = AssessmentResult::whereHas('category', function ($q) {
                                                $q->whereRaw("upper(name) not like '%TIDAK SEHAT%'");
                                            })->where('date', $date);
        if ($site_id) {
            $query->where('site_id', $site_id);
        }
        
        return $query->count();
    }
    
    public function highriskassessment(Request $request)
    {
        $date           = $request->date;
        $site_id        = $request->site_id;
        
        $query          = AssessmentResult::whereHas('category', function ($q) {
                                                $q->whereRaw("upper(name) like '%TIDAK SEHAT%'");
                                            })->where('date', $date);
        if ($site_id) {
            $query->where('site_id', $site_id);
        }

        return $query->count();
    }

    public function chartassessment(Request $request)
    {
        // $date               = $request->date;
        $date               = Carbon::today();
        // $site_id            = $request->site_id;
        $site_id            = 16;
        $health_meter_id    = $request->health_meter_id;
        $yaxis              = [];
        $series             = [];
        $xaxis              = [];


        $query             = Workforce::with(['assessmentresult' => function ($q) use ($date) {
            $q->whereMonth('date', $date);
        }]);

        $query_yaxis    = HealthMeter::where('site_id', $site_id)->get();
        foreach ($query_yaxis as $key => $value) {
            $yaxis[]    = $value->name;
        }
        dd($request->site);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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