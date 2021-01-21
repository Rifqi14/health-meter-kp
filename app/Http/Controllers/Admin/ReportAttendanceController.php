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
use Illuminate\Support\Facades\DB;
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

    public function chartassessment(Request $request)
    {
        $date               = $request->date;
        $site_id            = $request->site_id ? $request->site_id : -1;
        $health_meter_id    = $request->health_meter_id ? $request->health_meter_id : -1;
        $workforce_group_id = $request->workforce_group_id ? $request->workforce_group_id : -1;

        $query = Workforce::select(
            'workforces.*',
            DB::raw("(SELECT count(ar.id) FROM assessment_results ar WHERE workforce_id = workforces.id and ar.date = '$date' and ar.health_meter_id = '$health_meter_id') as total")
        );
        $query->where('site_id', $site_id)->where('workforce_group_id', $workforce_group_id);
        $query->orderBy('total', 'desc');
        $query->limit(10);
        $category = $query->get();

        $title = HealthMeter::find($request->health_meter_id);

        $categories = [];
        $cat = [];
        $series = [];
        $colors = [];
        foreach ($category as $key => $value) {
            $cat[] = $value->name;
            $categories[]   = $value;
        }
        $risks          = HealthMeter::where('site_id', $site_id)->where('workforce_group_id', $workforce_group_id)->orderBy('max', 'asc')->get();
        foreach ($risks as $key => $risk) {
            $series[]['name'] = $risk->name;
            $colors[] = $risk->color;
        }
        foreach ($series as $key => $name) {
            foreach ($categories as $k => $value) {
                $value->total = AssessmentResult::whereHas('category', function ($q) use ($name, $site_id, $workforce_group_id)
                {
                    $q->where('name', $name['name'])->where('site_id', $site_id)->where('workforce_group_id', $workforce_group_id);
                })->where('workforce_id', $value->id)->count();
                $series[$key]['data'][] = $value->total;
            }
        }

        return response()->json([
            'title'     => 'Laporan Bulanan',
            'subtitle'  => 'Periode ' . date('F', strtotime($date)),
            'series'    => $series,
            'categories'=> $cat,
            'colors'    => $colors,
        ], 200);
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