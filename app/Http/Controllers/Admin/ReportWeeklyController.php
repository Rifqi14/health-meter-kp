<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AssessmentResult;
use App\Models\HealthMeter;
use App\Models\Workforce;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ReportWeeklyController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/reportweekly'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.reportweekly.index');
    }

    public function personnel(Request $request)
    {
        $date = $request->date;
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $department_id = $request->department_id;
        $to = $request->date;
        $from = Carbon::parse($to)->subWeek();
        
        // Count data
        $query = Workforce::select(
                                'workforces.*',
                                DB::raw("(SELECT count(ar.id) FROM assessment_results ar LEFT JOIN health_meters hm ON ar.health_meter_id = hm.id WHERE workforce_id = workforces.id and date BETWEEN '$from' and '$to' and upper(hm.name) like '%TIDAK SEHAT%') as total")
                            )->with(['department', 'title']);
        $recordsTotal = $query->count();

        // Select pagination
        $query = Workforce::select(
                                'workforces.*',
                                DB::raw("(SELECT count(ar.id) FROM assessment_results ar LEFT JOIN health_meters hm ON ar.health_meter_id = hm.id WHERE workforce_id = workforces.id and date BETWEEN '$from' and '$to' and upper(hm.name) like '%TIDAK SEHAT%') as total")
                            )->with(['department', 'title']);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $workforces = $query->get();
        $data = [];
        foreach ($workforces as $key => $workforce) {
            $workforce->no = ++$start;
            $workforce->start_date = $from->toDateString();
            $workforce->finish_date = $to;
            $data[] = $workforce;
        }
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function totalpersonnel(Request $request)
    {
        $department_id = $request->department_id;
        $query = Workforce::all();
        if ($department_id) {
            $query->where('department_id', $department_id);
        }

        return $query->count();
    }

    public function lastweekpersonnel(Request $request)
    {
        $date = $request->date;
        $department_id = $request->department_id;
        $query = Workforce::select(DB::raw('coalesce(assessment_results.total,0) as total'))
                            ->leftJoin(DB::raw("(select workforce_id,count(id) as total from assessment_results where date = '$date' group by workforce_id) as assessment_results"), 'assessment_results.workforce_id', '=', 'workforces.id')
                            ->where('total', '>', 0);
        if ($department_id) {
            $query->where('department_id', $department_id);
        }
        return $query->count();
    }

    public function todaypersonnel(Request $request)
    {
        $date = $request->date;
        $department_id = $request->department_id;
        $query = Workforce::select(DB::raw('coalesce(reports.total,0) as total'))
                            ->leftJoin(DB::raw("(select workforce_id,count(id) as total from assessment_results where date = '$date' group by workforce_id) as assessment_results"), 'assessment_results.workforce_id', '=', 'workforces.id')
                            ->whereNull('total');
        if ($department_id) {
            $query->where('department_id', $department_id);
        }
        return $query->count();
    }

    public function chartpersonnel(Request $request)
    {
        $to = $request->date;
        $from = Carbon::parse($to)->subWeek();
        $query = Workforce::select(
            'workforces.name',
            DB::raw("(SELECT count(ar.id) FROM assessment_results ar LEFT JOIN health_meters hm ON ar.health_meter_id = hm.id WHERE workforce_id = workforces.id and date BETWEEN '$from' and '$to' and upper(hm.name) like '%TIDAK SEHAT%') as total")
        );
        $query->orderBy('total', 'desc');
        $query->limit(10);
        $category = $query->get();

        $categories = [];
        $series = [];
        foreach ($category as $key => $value) {
            $categories[]   = $value->name;
            $series[]       = intval($value->total);
        }

        return response()->json([
            'title'     => Carbon::parse($from)->format('d-m-Y') . ' s/d ' . Carbon::parse($to)->format('d-m-Y'),
            'series'    => $series,
            'categories'=> $categories,
        ], 200);
    }

    public function export(Request $request)
    {
        $to = $request->date;
        $from = Carbon::parse($request->date)->subWeek();
        $object 	= new \PHPExcel();
        $object->getProperties()->setCreator('Health Meter KP');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $query = Workforce::select(
            'workforces.*',
            DB::raw("(SELECT count(ar.id) FROM assessment_results ar LEFT JOIN health_meters hm ON ar.health_meter_id = hm.id WHERE workforce_id = workforces.id and date BETWEEN '$from' and '$to' and upper(hm.name) like '%TIDAK SEHAT%') as total")
        )->with(['department', 'title']);
        $query->orderBy('total', 'desc');
        $category = $query->get();

        // Header Column Excel
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Bidang');
        $sheet->setCellValue('D1', 'Jabatan');
        $sheet->setCellValue('E1', 'Resiko Tinggi 7 Hari Terakhir');

        $row_number = 2;
        // Content Data
        foreach ($category as $key => $value) {
            $sheet->setCellValue('A'.$row_number, $from->toDateString() . ' s/d ' . $to);
            $sheet->setCellValue('B'.$row_number, $value->name);
            $sheet->setCellValue('C'.$row_number, $value->department ? $value->department->name : '');
            $sheet->setCellValue('D'.$row_number, $value->title ? $value->title->name : '');
            $sheet->setCellValue('E'.$row_number, $value->total . ' kali');
            $row_number++;
        }

        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
		$objWriter->save('php://output');
		$export = ob_get_contents();
		ob_end_clean();
		header('Content-Type: application/json');
		if($category->count() > 0){
            return response()->json([
                'status' 	=> true,
                'name'		=> 'data-kategori-tinggi-'.date('d-m-Y').'.xlsx',
                'message'	=> "Berhasil Download Data Participant",
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