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
        $date            = explode(' - ', $request->date);
        $to              = Carbon::parse($date[0])->toDateString();
        $from            = Carbon::parse($date[1])->toDateString();
        $site_id         = $request->site_id ? explode(',', $request->site_id) : null;
        $workforce_group_id = $request->workforce_group_id ? explode(',', $request->workforce_group_id) : null;
        $health_meter_id = $request->health_meter_id;

        // Count Data
        $query           = AssessmentResult::with(['category', 'workforce', 'workforce.site', 'workforce.agency', 'workforce.department', 'workforce.subdepartment', 'workforce.title'])->whereBetween('date', [$to, $from]);
        if ($site_id) {
            $query->whereIn('site_id', $site_id);
        }
        if ($health_meter_id) {
            $query->where('health_meter_id', $health_meter_id);
        }
        if ($workforce_group_id) {
            $query->whereIn('workforce_group_id', $workforce_group_id);
        }
        $recordsTotal    = $query->count();

        // Select Pagination
        $query           = AssessmentResult::with(['category', 'workforce', 'workforce.site', 'workforce.agency', 'workforce.department', 'workforce.subdepartment', 'workforce.title'])->whereBetween('date', [$to, $from]);
        if ($site_id) {
            $query->whereIn('site_id', $site_id);
        }
        if ($health_meter_id) {
            $query->where('health_meter_id', $health_meter_id);
        }
        if ($workforce_group_id) {
            $query->whereIn('workforce_group_id', $workforce_group_id);
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
        $date               = explode(' - ', $request->date);
        $to                 = Carbon::parse($date[0])->toDateString();
        $from               = Carbon::parse($date[1])->toDateString();
        $site_id            = $request->site_id ? explode(',', $request->site_id) : null;
        $health_meter_id    = $request->health_meter_id ? $request->health_meter_id : -1;
        $workforce_group_id = $request->workforce_group_id ? explode(',', $request->workforce_group_id) : null;

        $query = Workforce::select(
            'workforces.*',
            DB::raw("(SELECT count(ar.id) FROM assessment_results ar WHERE workforce_id = workforces.id and ar.date BETWEEN '$from' and '$to' and ar.health_meter_id = '$health_meter_id') as total")
        );
        if ($site_id) {
            $query->whereIn('site_id', $site_id);
        }
        if ($workforce_group_id) {
            $query->whereIn('workforce_group_id', $workforce_group_id);
        }
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
        $risks          = HealthMeter::orderBy('max', 'asc')->get();
        foreach ($risks as $key => $risk) {
            $series[]['name'] = $risk->name;
            $colors[] = $risk->color;
        }
        foreach ($series as $key => $name) {
            foreach ($categories as $k => $value) {
                $value->total = AssessmentResult::whereHas('category', function ($q) use ($name)
                {
                    $q->where('name', $name['name']);
                })->where('workforce_id', $value->id)->count();
                $series[$key]['data'][] = $value->total;
            }
        }

        return response()->json([
            'title'     => 'Laporan Bulanan',
            'subtitle'  => "Periode $from s/d $to",
            'series'    => $series,
            'categories'=> $cat,
            'colors'    => $colors,
        ], 200);
    }

    /**
     * Export data selft assessment
     *
     * @param Request $request
     * @return void
     */
    public function export(Request $request)
    {
        $date               = explode(' - ', $request->date);
        $to                 = Carbon::parse($date[0])->toDateString();
        $from               = Carbon::parse($date[1])->toDateString();

        $query           = AssessmentResult::with(['category', 'workforce', 'workforce.site', 'workforce.agency', 'workforce.department', 'workforce.subdepartment', 'workforce.title'])->whereBetween('date', [$to, $from]);
        $selfAsessments  = $query->get();

        $object         = new \PHPExcel();
        $object->getProperties()->setCreator('Health Meter KP');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        // Header Column Excel
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'NID Workforce');
        $sheet->setCellValue('C1', 'Nama Workforce');
        $sheet->setCellValue('D1', 'Distrik Workforce');
        $sheet->setCellValue('E1', 'Instansi');
        $sheet->setCellValue('F1', 'Divisi Bidang');
        $sheet->setCellValue('G1', 'Sub Divisi Bidang');
        $sheet->setCellValue('H1', 'Jabatan');
        $sheet->setCellValue('I1', 'Total bobot');
        $sheet->setCellValue('J1', 'Kategori Resiko');

        $row_number = 2;

        // Content Data
        foreach ($selfAsessments as $key => $value) {
            $sheet->setCellValue('A'.$row_number, @$value->date);
            $sheet->setCellValue('B'.$row_number, @$value->workforce->nid);
            $sheet->setCellValue('C'.$row_number, @$value->workforce->name);
            $sheet->setCellValue('D'.$row_number, @$value->workforce->site->name);
            $sheet->setCellValue('E'.$row_number, @$value->workforce->agency->name);
            $sheet->setCellValue('F'.$row_number, @$value->workforce->department->name);
            $sheet->setCellValue('G'.$row_number, @$value->workforce->subdepartment->name);
            $sheet->setCellValue('H'.$row_number, @$value->workforce->title->name);
            $sheet->setCellValue('I'.$row_number, @$value->value_total);
            $sheet->setCellValue('J'.$row_number, @$value->category->name);

            $row_number++;
        }

        foreach (range('A', 'J') as $key => $value) {
            $sheet->getColumnDimension($value)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
		ob_end_clean();
		header('Content-Type: application/json');
		if($selfAsessments->count() > 0){
            return response()->json([
                'status' 	=> true,
                'name'		=> 'data-laporan-self-assessment-'.date('d-m-Y').'.xlsx',
                'message'	=> "Berhasil Download Data Laporan",
                'file' 		=> "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($export)
            ], 200);
		} else {
            return response()->json([
                'status' 	=> false,
                'message'	=> "Data tidak ditemukan",
            ], 400);
		}
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