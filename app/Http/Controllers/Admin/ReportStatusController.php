<?php

namespace App\Http\Controllers\Admin;

use App\Models\StatusUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class ReportStatusController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'reportstatus'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.reportstatus.index');
    }
    public function read(Request $request)
    {
        $employeetype = [
            'permanent'   => 'Pegawai Tetap',
            'internship'  => 'Alih Daya',
            'pensiun'  => 'Pensiun',
            'other'  => 'Lainya',
        ];
        $description = [
            'wfo'   => 'WFO',
            'wfh'  => 'WFH',
            'izin'  => 'Izin',
            'dinas'  => 'Dinas Luar Kota',
            'libur'  => 'Libur'
        ];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $type = $request->type;
        $status = strtoupper($request->status);
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        $name = strtoupper($request->name);
        // Count Data
        $query = StatusUser::select('status_users.*','users.name','titles.name as title_name','employees.nid','employees.type');
        $query->leftJoin('users','users.id','=','status_users.user_id');
        $query->leftJoin('employees','employees.nid','=','users.username');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->whereNull('finish');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if($type){
            $query->where('employees.type',$type);
        }
        if($status){
            $query->whereRaw("upper(description) like '%$status%'");
        }
        if($date_start){
            $query->whereBetween('status_date', [$date_start, $date_finish]);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = StatusUser::select('status_users.*','users.name','titles.name as title_name','employees.nid','employees.type');
        $query->leftJoin('users','users.id','=','status_users.user_id');
        $query->leftJoin('employees','employees.nid','=','users.username');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->whereNull('finish');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if($type){
            $query->where('employees.type',$type);
        }
        if($status){
            $query->whereRaw("upper(description) like '%$status%'");
        }
        if($date_start){
            $query->whereBetween('status_date', [$date_start, $date_finish]);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $statususers = $query->get();
        $data = [];
        foreach($statususers as $statususer){
            $statususer->no = ++$start;
            $statususer->description = $description[$statususer->description];
            $statususer->type = $employeetype[$statususer->type];
			$data[] = $statususer;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function total()
    {
        $total = StatusUser::select('status_users.id')
                                ->where('status_users.description','wfo')
                                ->get()
                                ->count();
        return $total;
    }
    public function lastweek()
    {
		$start = date('Y-m-d', strtotime(date('Y-m-d') . ' -6 day'));
        $finish = date('Y-m-d');
        $total = StatusUser::select('status_users.id')
                                ->where('status_users.description','wfo')
                                ->whereBetween('status_date',[$start,$finish])
                                ->get()
                                ->count();
        return $total;
    }
    public function today()
    {
        $total = StatusUser::select('status_users.id')
                            ->where('status_users.description','wfo')
                            ->where('status_date',date('Y-m-d'))
                            ->get()
                            ->count();
        return $total;
    }

    public function chart(Request $request)
    {
        $query = StatusUser::select('status_users.description',DB::raw('count(status_users.id) as total'));
        $query->orderBy('total','desc');
        $query->limit(10);
        $query->groupBy('description');
        $statususers = $query->get();
        $series = [];
		$categories = [];
        foreach($statususers as $statususer){
            $categories[] = $statususer->description;
			$series[] = intval($statususer->total);
        }
        return response()->json([
            'date' => '01/09/2020 - 30/09/2020',
			'series' => $series,
			'categories' => $categories
        ], 200);
    }

    public function export(Request $request){
        $object 	= new \PHPExcel();
        $object->getProperties()->setCreator('Perki Surabaya');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();
        $employeetype = [
            'permanent'   => 'Pegawai Tetap',
            'internship'  => 'Alih Daya',
            'pensiun'  => 'Pensiun',
            'other'  => 'Lainya',
        ];
        $description = [
            'wfo'   => 'WFO',
            'wfh'  => 'WFH',
            'izin'  => 'Izin',
            'dinas'  => 'Dinas Luar Kota',
            'libur'  => 'Libur'
        ];
        $name = strtoupper($request->name);
        $status = strtoupper($request->status);
        $type = $request->type;
        $date = explode(' - ',$request->date);
        $date_start = date('Y-m-d',strtotime(str_replace('/','-',$date[0])));
        $date_finish = date('Y-m-d',strtotime(str_replace('/','-',$date[1])));

        // dd($partner_id);

        $query = StatusUser::select('status_users.*','users.name as user_name','employees.nid','employees.type','employees.name as title_name');
        $query->leftJoin('users','users.id','=','status_users.user_id');
        $query->leftJoin('employees','employees.nid','=','users.username');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->whereNull('finish');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if($type){
            $query->where('employees.type',$type);
        }
        if($status){
            $query->whereRaw("upper(description) like '%$status%'");
        }
        if($date_start){
            $query->whereBetween('status_date', [$date_start, $date_finish]);
        }
        $medicalrecords = $query->get();
        // dd($medicalrecords);
        //Header Column Excel
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'NID');
        $sheet->setCellValue('D1', 'Nama');
        $sheet->setCellValue('E1', 'Jabatan');
        $sheet->setCellValue('E1', 'Tipe');
        $sheet->setCellValue('F1', 'Status');

        $row_number = 2;
        $no = 1;
        //Content Data
		foreach ($medicalrecords as $medicalrecord) {
            $sheet->setCellValue('A'.$row_number, $no);
            $sheet->setCellValue('B'.$row_number, $medicalrecord->status_date);
            $sheet->setCellValue('C'.$row_number, $medicalrecord->nid);
            $sheet->setCellValue('D'.$row_number, $medicalrecord->user_name);
            $sheet->setCellValue('E'.$row_number, $medicalrecord->title_name);
            $sheet->setCellValue('E'.$row_number, $employeetype[$medicalrecord->type]);
            $sheet->setCellValue('F'.$row_number, $description[$medicalrecord->description]);
            $row_number++;
            $no++;
        }
        foreach (range('A', 'G')as $column)
        {
            $sheet->getColumnDimension($column)
            ->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
		$objWriter->save('php://output');
		$export = ob_get_contents();
		ob_end_clean();
		header('Content-Type: application/json');
		if($medicalrecords->count() > 0){
            return response()->json([
                'status' 	=> true,
                'name'		=> 'data-reportstatus-'.date('d-m-Y').'.xlsx',
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
}
