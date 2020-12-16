<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class ReportHealthEmployeeController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'reporthealthemployee'));
    }
    public function index()
    {
        return view('admin.reporthealthemployee.index');
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $type = $request->type;
        $department_id = $request->department_id;
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        // Count Data
        $query = Report::select('reports.report_date',
                                'employees.name as employee_name',
                                'employees.nid as employee_nid',
                                'titles.name as title_name',
                                'departments.name as department_name',
                                'reports.value as sehat');
        $query->leftJoin('employees','employees.id','=','reports.employee_id');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id');
        $query->whereNull('finish');
        $query->where('sub_categories.name', 'Apakah Sehat?');
        $query->where('value', 0);
        if($department_id){
            $query->where('department_id',$department_id);
        }
        if($type){
            $query->where('employees.type',$type);
        }
        if($date_start){
            $query->whereBetween('report_date', [$date_start, $date_finish]);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Report::select('reports.report_date',
                                'employees.name as employee_name',
                                'employees.nid as employee_nid',
                                'titles.name as title_name',
                                'departments.name as department_name',
                                'reports.value as sehat');
        $query->leftJoin('employees','employees.id','=','reports.employee_id');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id');
        $query->where('sub_categories.name', 'Apakah Sehat?');
        $query->where('value', 0);
        $query->whereNull('finish');
        if($department_id){
            $query->where('department_id',$department_id);
        }
        if($type){
            $query->where('employees.type',$type);
        }
        if($date_start){
            $query->whereBetween('report_date', [$date_start, $date_finish]);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $reports = $query->get();
        $data = [];
        foreach($reports as $report){
            $report->no = ++$start;
            $report->sehat = $report->sehat?'Ya':'Tidak';
			$data[] = $report;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function export(Request $request){
        $object 	= new \PHPExcel();
        $object->getProperties()->setCreator('Perki Surabaya');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();
        $department_id = $request->department_id;
        $type = $request->type;
        $date = explode(' - ',$request->date);
        $date_start = date('Y-m-d',strtotime(str_replace('/','-',$date[0])));
        $date_finish = date('Y-m-d',strtotime(str_replace('/','-',$date[1])));

        // dd($partner_id);

        $query = Report::select('reports.report_date',
                                'employees.name as employee_name',
                                'employees.nid as employee_nid',
                                'titles.name as title_name',
                                'departments.name as department_name',
                                DB::raw("max(case when sub_categories.name = 'Apakah Sehat?' then value else 0 end) as sehat"));
        $query->leftJoin('employees','employees.id','=','reports.employee_id');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id');
        $query->where('sub_categories.name', 'Apakah Sehat?');
        $query->where('value', 0);
        $query->groupBy('reports.report_date', 'employees.name', 'employees.nid', 'titles.name', 'departments.name');
        if($department_id){
            $query->where('department_id',$department_id);
        }
        if($type){
            $query->where('employees.type',$type);
        }
        if($date_start){
            $query->whereBetween('report_date', [$date_start, $date_finish]);
        }
        $medicalrecords = $query->get();
        // dd($medicalrecords);
        //Header Column Excel
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Nama Pegawai');
        $sheet->setCellValue('C1', 'NID Pegawai');
        $sheet->setCellValue('D1', 'Jabatan');
        $sheet->setCellValue('E1', 'Bidang');
        $sheet->setCellValue('F1', 'Apakah Sehat?');

        $row_number = 2;
        //Content Data
		foreach ($medicalrecords as $medicalrecord) {
            $sheet->setCellValue('A'.$row_number, $medicalrecord->report_date);
            $sheet->setCellValue('B'.$row_number, $medicalrecord->employee_name);
            $sheet->setCellValue('C'.$row_number, $medicalrecord->employee_nid);
            $sheet->setCellValue('D'.$row_number, $medicalrecord->title_name);
            $sheet->setCellValue('E'.$row_number, $medicalrecord->department_name);
            $sheet->setCellValue('F'.$row_number, $medicalrecord->sehat == 0?'Tidak':'Ya');
            $row_number++;
        }
        foreach (range('A', 'F')as $column)
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
                'name'		=> 'data-Kesehatan-Karyawan-'.date('d-m-Y').'.xlsx',
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
