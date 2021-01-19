<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class ReportDailyController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'reportdaily'));
    }
    public function index()
    {
        return view('admin.reportdaily.index');
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

        // dd($department_id);

        // Count Data
        $query = Employee::select('employees.*','titles.name as title_name','regions.name as place_of_birth','departments.name as department_name',DB::raw("'$date' as report_date"));
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('regions','regions.id','=','employees.place_of_birth');
        $query->leftJoin('users','users.username','=','employees.nid');
        $query->leftJoin('role_user','role_user.user_id','=','users.id');
        $query->leftJoin('roles','roles.id','=','role_user.role_id');
        $query->whereIn('roles.name',['personil','supervisor','os','koos']);
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        $query->whereNull('finish');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Employee::select('employees.*','titles.name as title_name','regions.name as place_of_birth','departments.name as department_name',DB::raw("'$date' as report_date"),DB::raw('coalesce(reports.total,0) as total'));
        $query->leftJoin(DB::raw("(select employee_id,count(id) as total from reports where report_date ='$date' group by employee_id) as reports"),'reports.employee_id','=','employees.id');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('regions','regions.id','=','employees.place_of_birth');
        $query->leftJoin('users','users.username','=','employees.nid');
        $query->leftJoin('role_user','role_user.user_id','=','users.id');
        $query->leftJoin('roles','roles.id','=','role_user.role_id');
        $query->whereIn('roles.name',['personil','supervisor','os','koos']);
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        $query->whereNull('finish');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employees = $query->get();
        $data = [];
        foreach($employees as $employee){
            $employee->no = ++$start;
			$data[] = $employee;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function supervisor(Request $request)
    {
        $date = $request->date;
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $department_id = $request->department_id;

        // Count Data
        $query = Employee::select('employees.*','titles.name as title_name','regions.name as place_of_birth','departments.name as department_name',DB::raw("'$date' as report_date"));
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('regions','regions.id','=','employees.place_of_birth');
        $query->leftJoin('users','users.username','=','employees.nid');
        $query->leftJoin('role_user','role_user.user_id','=','users.id');
        $query->leftJoin('roles','roles.id','=','role_user.role_id');
        $query->whereIn('roles.name',['supervisor','koos']);
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        $query->whereNull('finish');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Employee::select('employees.*','titles.name as title_name','regions.name as place_of_birth','departments.name as department_name',DB::raw("'$date' as report_date"),DB::raw('coalesce(reports.total,0) as total'));
        $query->leftJoin(DB::raw("(select supervisor_id as employee_id,count(id) as total from reports where report_date ='$date' group by supervisor_id) as reports"),'reports.employee_id','=','employees.id');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('regions','regions.id','=','employees.place_of_birth');
        $query->leftJoin('users','users.username','=','employees.nid');
        $query->leftJoin('role_user','role_user.user_id','=','users.id');
        $query->leftJoin('roles','roles.id','=','role_user.role_id');
        $query->whereIn('roles.name',['supervisor','koos']);
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        $query->whereNull('finish');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employees = $query->get();
        // dd($employees);
        $data = [];
        foreach($employees as $employee){
            $employee->no = ++$start;
			$data[] = $employee;
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
        $query = Employee::select('employees.id')
                                ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                                ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                                ->leftJoin('departments','departments.id','=','titles.department_id')
                                ->leftJoin('regions','regions.id','=','employees.place_of_birth')
                                ->leftJoin('users','users.username','=','employees.nid')
                                ->leftJoin('role_user','role_user.user_id','=','users.id')
                                ->leftJoin('roles','roles.id','=','role_user.role_id')
                                ->whereIn('roles.name',['personil','supervisor','os','koos'])
                                ->whereNull('finish');
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        return $query->count();
    }

    public function lastweekpersonnel(Request $request)
    {
        $date = $request->date;
        $department_id = $request->department_id;
        $query = Employee::select(DB::raw('coalesce(reports.total,0) as total'))
                            ->leftJoin(DB::raw("(select employee_id,count(id) as total from reports where report_date ='$date' group by employee_id) as reports"),'reports.employee_id','=','employees.id')
                            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                            ->leftJoin('departments','departments.id','=','titles.department_id')
                            ->leftJoin('regions','regions.id','=','employees.place_of_birth')
                            ->leftJoin('users','users.username','=','employees.nid')
                            ->leftJoin('role_user','role_user.user_id','=','users.id')
                            ->leftJoin('roles','roles.id','=','role_user.role_id')
                            ->whereIn('roles.name',['personil','supervisor','os','koos'])
                            ->where('total','>',0)
                            ->whereNull('finish');
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        return $query->count();
    }

    public function todaypersonnel(Request $request)
    {
        $date = $request->date;
        $department_id = $request->department_id;
        $query = Employee::select(DB::raw('coalesce(reports.total,0) as total'))
                            ->leftJoin(DB::raw("(select employee_id,count(id) as total from reports where report_date ='$date' group by employee_id) as reports"),'reports.employee_id','=','employees.id')
                            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                            ->leftJoin('departments','departments.id','=','titles.department_id')
                            ->leftJoin('regions','regions.id','=','employees.place_of_birth')
                            ->leftJoin('users','users.username','=','employees.nid')
                            ->leftJoin('role_user','role_user.user_id','=','users.id')
                            ->leftJoin('roles','roles.id','=','role_user.role_id')
                            ->whereIn('roles.name',['personil','supervisor','os','koos'])
                            ->whereNull('total')
                            ->whereNull('finish');
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        return $query->count();
    }

    public function totalsupervisor(Request $request)
    {
        $department_id = $request->department_id;
        $query = Employee::select('employees.id')
                                ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                                ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                                ->leftJoin('departments','departments.id','=','titles.department_id')
                                ->leftJoin('regions','regions.id','=','employees.place_of_birth')
                                ->leftJoin('users','users.username','=','employees.nid')
                                ->leftJoin('role_user','role_user.user_id','=','users.id')
                                ->leftJoin('roles','roles.id','=','role_user.role_id')
                                ->whereIn('roles.name',['supervisor','koos'])
                                ->whereNull('finish');
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        return $query->count();
    }

    public function lastweeksupervisor(Request $request)
    {
        $date = $request->date;
        $department_id = $request->department_id;
        $query = Employee::select(DB::raw('coalesce(reports.total,0) as total'))
                            ->leftJoin(DB::raw("(select supervisor_id as employee_id,count(id) as total from reports where report_date ='$date' group by supervisor_id) as reports"),'reports.employee_id','=','employees.id')
                            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                            ->leftJoin('departments','departments.id','=','titles.department_id')
                            ->leftJoin('regions','regions.id','=','employees.place_of_birth')
                            ->leftJoin('users','users.username','=','employees.nid')
                            ->leftJoin('role_user','role_user.user_id','=','users.id')
                            ->leftJoin('roles','roles.id','=','role_user.role_id')
                            ->whereIn('roles.name',['supervisor','koos'])
                            ->whereRaw('coalesce(reports.total,0) > 0')
                            ->whereNull('finish');
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        return $query->count();
    }

    public function todaysupervisor(Request $request)
    {
        $date = $request->date;
        $department_id = $request->department_id;
        $query = Employee::select(DB::raw('coalesce(reports.total,0) as total'))
                            ->leftJoin(DB::raw("(select supervisor_id as employee_id,count(id) as total from reports where report_date ='$date' group by supervisor_id) as reports"),'reports.employee_id','=','employees.id')
                            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                            ->leftJoin('departments','departments.id','=','titles.department_id')
                            ->leftJoin('regions','regions.id','=','employees.place_of_birth')
                            ->leftJoin('users','users.username','=','employees.nid')
                            ->leftJoin('role_user','role_user.user_id','=','users.id')
                            ->leftJoin('roles','roles.id','=','role_user.role_id')
                            ->whereIn('roles.name',['supervisor','koos'])
                            ->whereRaw('coalesce(reports.total,0) = 0')
                            ->whereNull('finish');
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        return $query->count();
    }

    public function export(Request $request){
        $date = $request->date;
        $object 	= new \PHPExcel();
        $object->getProperties()->setCreator('Perki Surabaya');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();
        $department_id = $request->department_id;

        // dd($partner_id);

        $query = Employee::select('employees.*','titles.name as title_name','regions.name as place_of_birth','departments.name as department_name',DB::raw("'$date' as report_date"),DB::raw('coalesce(reports.total,0) as total'));
        $query->leftJoin(DB::raw("(select employee_id,count(id) as total from reports where report_date ='$date' group by employee_id) as reports"),'reports.employee_id','=','employees.id');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('regions','regions.id','=','employees.place_of_birth');
        $query->leftJoin('users','users.username','=','employees.nid');
        $query->leftJoin('role_user','role_user.user_id','=','users.id');
        $query->leftJoin('roles','roles.id','=','role_user.role_id');
        $query->whereIn('roles.name',['personil','supervisor']);
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        $query->whereNull('finish');
        $employees = $query->get();
        // dd($employees);
        //Header Column Excel
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Bidang');
        $sheet->setCellValue('D1', 'Jabatan');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Dibuat');

        $row_number = 2;
        //Content Data
		foreach ($employees as $employee) {
            $sheet->setCellValue('A'.$row_number, $employee->report_date);
            $sheet->setCellValue('B'.$row_number, $employee->name);
            $sheet->setCellValue('C'.$row_number, $employee->department_name);
            $sheet->setCellValue('D'.$row_number, $employee->title_name);
            $sheet->setCellValue('E'.$row_number, $employee->total > 0?'Yes':'No');
            $sheet->setCellValue('F'.$row_number, $employee->created_at);
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
		if($employees->count() > 0){
            return response()->json([
                'status' 	=> true,
                'name'		=> 'data-employee-'.date('d-m-Y').'.xlsx',
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

    public function exportsuper(Request $request){
        $date = $request->date;
        $object 	= new \PHPExcel();
        $object->getProperties()->setCreator('Perki Surabaya');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();
        $department_id = $request->department_id;

        // dd($partner_id);

        $query = Employee::select('employees.*','titles.name as title_name','regions.name as place_of_birth','departments.name as department_name',DB::raw("'$date' as report_date"),DB::raw('coalesce(reports.total,0) as total'));
        $query->leftJoin(DB::raw("(select employee_id,count(id) as total from reports where report_date ='$date' group by employee_id) as reports"),'reports.employee_id','=','employees.id');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('regions','regions.id','=','employees.place_of_birth');
        $query->leftJoin('users','users.username','=','employees.nid');
        $query->leftJoin('role_user','role_user.user_id','=','users.id');
        $query->leftJoin('roles','roles.id','=','role_user.role_id');
        $query->whereIn('roles.name',['supervisor']);
        if($department_id){
            $query->where('titles.department_id',$department_id);
        }
        // if($date){
        //     $query->whereRaw("report_date like '%$date%'");
        // }
        $employees = $query->get();
        // dd($employees);
        //Header Column Excel
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Bidang');
        $sheet->setCellValue('D1', 'Jabatan');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Dibuat');

        $row_number = 2;
        //Content Data
		foreach ($employees as $employee) {
            $sheet->setCellValue('A'.$row_number, $employee->report_date);
            $sheet->setCellValue('B'.$row_number, $employee->name);
            $sheet->setCellValue('C'.$row_number, $employee->department_name);
            $sheet->setCellValue('D'.$row_number, $employee->title_name);
            $sheet->setCellValue('E'.$row_number, $employee->total > 0?'Yes':'No');
            $sheet->setCellValue('F'.$row_number, $employee->created_at);
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
		if($employees->count() > 0){
            return response()->json([
                'status' 	=> true,
                'name'		=> 'data-employee-'.date('d-m-Y').'.xlsx',
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

    public function chartpersonnel(Request $request)
    {
        $date = $request->date;
        $query = Employee::select('departments.name',DB::raw('count(departments.id) as total'));
        $query->leftJoin(DB::raw("(select employee_id,count(id) as fill from reports where report_date ='$date' group by employee_id) as reports"),'reports.employee_id','=','employees.id');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('users','users.username','=','employees.nid');
        $query->leftJoin('role_user','role_user.user_id','=','users.id');
        $query->leftJoin('roles','roles.id','=','role_user.role_id');
        $query->whereIn('roles.name',['personil','supervisor']);
        $query->where('fill','>',0);
        $query->orderBy('total','desc');
        $query->limit(10);
        $query->groupBy('departments.name');
        $departments = $query->get();
        $series = [];
		$categories = [];
        foreach($departments as $department){
            $categories[] = $department->name;
			$series[] = intval($department->total);
        }
        return response()->json([
            'title' =>  Carbon::parse($request->date)->format('d/m/Y'),
			'series' => $series,
			'categories' => $categories
        ], 200);
    }

    public function chartsupervisor(Request $request)
    {
        $date = $request->date;
        $query = Employee::select('departments.name',DB::raw('count(departments.id) as total'));
        $query->leftJoin(DB::raw("(select supervisor_id as employee_id,count(id) as fill from reports where report_date ='$date' group by supervisor_id) as reports"),'reports.employee_id','=','employees.id');
        $query->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id');
        $query->leftJoin('titles','titles.id','=','employee_movements.title_id');
        $query->leftJoin('departments','departments.id','=','titles.department_id');
        $query->leftJoin('users','users.username','=','employees.nid');
        $query->leftJoin('role_user','role_user.user_id','=','users.id');
        $query->leftJoin('roles','roles.id','=','role_user.role_id');
        $query->whereIn('roles.name',['supervisor']);
        $query->where('fill','>',0);
        $query->orderBy('total','desc');
        $query->limit(10);
        $query->groupBy('departments.name');
        $departments = $query->get();
        $series = [];
		$categories = [];
        foreach($departments as $department){
            $categories[] = $department->name;
			$series[] = intval($department->total);
        }
        return response()->json([
            'title' =>  Carbon::parse($request->date)->format('d/m/Y'),
			'series' => $series,
			'categories' => $categories
        ], 200);
    }
}