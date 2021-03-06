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

    public function selectcategory(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $nid = strtoupper($request->nid);
        $site_id = $request->site_id;
        $workforce_group_id = $request->workforce_group_id;

        //Count Data
        $query = HealthMeter::whereRaw("upper(name) like '%$name%'")->where('site_id', $site_id)->where('workforce_group_id', $workforce_group_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = HealthMeter::whereRaw("upper(name) like '%$name%'")->where('site_id', $site_id)->where('workforce_group_id', $workforce_group_id);
        $query->orderBy('name', 'asc');
        $query->offset($start);
        $query->limit($length);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $data[] = $result;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function personnel(Request $request)
    {
        $start_date = date('Y-m-d', strtotime('-7 days'));
        $finish_date = date('Y-m-d');
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $site_id = $request->site_id ? explode(',', $request->site_id) : null;
        $health_meter_id = $request->health_meter_id ? $request->health_meter_id : -1;
        $workforce_group_id = $request->workforce_group_id ? explode(',', $request->workforce_group_id) : null;
        
        // Count data
        $query = Workforce::select(
                                'workforces.*',
                                DB::raw("(SELECT count(ar.id) FROM assessment_results ar WHERE workforce_id = workforces.id and ar.date >= '$start_date' and ar.date <= '$finish_date' and ar.health_meter_id = '$health_meter_id') as total")
                            )->with(['department', 'title']);
        if ($site_id) {
            $query->whereIn('site_id', $site_id);
        }
        if ($workforce_group_id) {
            $query->whereIn('workforce_group_id', $workforce_group_id);
        }
        $recordsTotal = $query->count();

        // Select pagination
        $query = Workforce::select(
                                'workforces.*',
                                DB::raw("(SELECT count(ar.id) FROM assessment_results ar WHERE workforce_id = workforces.id and ar.date >= '$start_date' and ar.date <= '$finish_date' and ar.health_meter_id = '$health_meter_id') as total")
                            )->with(['department', 'title']);
        if ($site_id) {
            $query->whereIn('site_id', $site_id);
        }
        if ($workforce_group_id) {
            $query->whereIn('workforce_group_id', $workforce_group_id);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $workforces = $query->get();
        $data = [];
        foreach ($workforces as $key => $workforce) {
            $workforce->no = ++$start;
            $workforce->total = $workforce->total.' Kali'; 
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
        $start_date = date('Y-m-d', strtotime('-7 days'));
        $finish_date = date('Y-m-d');
        $site_id = $request->site_id ? explode(',', $request->site_id) : null;
        $health_meter_id = $request->health_meter_id ? $request->health_meter_id : -1;
        $workforce_group_id = $request->workforce_group_id ? explode(',', $request->workforce_group_id) : null;
        $query = Workforce::select(
            'workforces.name',
            DB::raw("(SELECT count(ar.id) FROM assessment_results ar WHERE workforce_id = workforces.id and ar.date >= '$start_date' and ar.date <= '$finish_date' and ar.health_meter_id = '$health_meter_id') as total")
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
        $series = [];
        foreach ($category as $key => $value) {
            $categories[]   = $value->name;
            $series[]       = intval($value->total);
        }

        return response()->json([
            'title'     => $title?'Kategori ' . $title->name:'Belum Ada Kategori Yang Dipilih',
            'subtitle'  => date('d F Y',strtotime($start_date)).' sd '.date('d F Y',strtotime($finish_date)),
            'series'    => $series,
            'categories'=> $categories,
        ], 200);
    }

    public function export(Request $request)
    {
        $start_date = date('Y-m-d', strtotime('-7 days'));
        $finish_date = date('Y-m-d');
        $site_id = $request->site_id ? explode(',', $request->site_id) : null;
        $health_meter_id = $request->health_meter_id ? $request->health_meter_id : -1;
        $workforce_group_id = $request->workforce_group_id ? explode(',', $request->workforce_group_id) : null;
        $category_title = HealthMeter::find($health_meter_id);
        $from = Carbon::parse($request->date)->subWeek();
        $object 	= new \PHPExcel();
        $object->getProperties()->setCreator('Health Meter KP');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $query = Workforce::select(
            'workforces.*',
            DB::raw("(SELECT count(ar.id) FROM assessment_results ar WHERE workforce_id = workforces.id and ar.date >= '$start_date' and ar.date <= '$finish_date' and ar.health_meter_id = '$health_meter_id') as total")
        )->with(['department', 'title']);
        if ($site_id) {
            $query->whereIn('site_id', $site_id);
        }
        if ($workforce_group_id) {
            $query->whereIn('workforce_group_id', $workforce_group_id);
        }
        $query->orderBy('total', 'desc');
        $category = $query->get();

        // Header Column Excel
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Bidang');
        $sheet->setCellValue('C1', 'Jabatan');
        $sheet->setCellValue('D1', 'Kategori Resiko ' . @$category_title->name);

        $row_number = 2;
        // Content Data
        foreach ($category as $key => $value) {
            $sheet->setCellValue('A'.$row_number, $value->name);
            $sheet->setCellValue('B'.$row_number, $value->department ? $value->department->name : '');
            $sheet->setCellValue('C'.$row_number, $value->title ? $value->title->name : '');
            $sheet->setCellValue('D'.$row_number, $value->total . ' kali');
            $row_number++;
        }

        foreach (range('A', 'D') as $column) {
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
                'name'		=> 'data-kategori-'.@$category_title->name.'-'.date('d-m-Y').'.xlsx',
                'message'	=> "Berhasil Download Data Self Assessment",
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