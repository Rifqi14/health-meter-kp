<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Checkup;
use App\Models\CheckupDetail;
use App\Models\Medical;
use App\Models\MedicalDetail;
use App\Models\StatusUser;
use App\Models\Employee;
use App\Models\Report;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class PersonnelController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'personnel'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    public function index()
    {
        $nid = Auth::guard('admin')->user()->username;
        $employee = Employee::where('nid',$nid)->get()->first();
        $report = Report::where('report_date',date('Y-m-d'))->where('employee_id',$employee->id)->get()->count();
        return view('admin.personnel.index',compact('report'));
    }
    public function read(Request $request)
    {
        $nid = Auth::guard('admin')->user()->username;
        $employee = Employee::where('nid',$nid)->get()->first();
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('reports');
        $query->select('reports.*');
        $query->where('employee_id',$employee->id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('reports');
        $query->select('reports.*','sub_categories.name as category_name','sub_categories.type as category_type');
        $query->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id');
        $query->where('employee_id',$employee->id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $reports = $query->get();

        $data = [];
        foreach($reports as $report){
            $report->no = ++$start;
            if($report->category_type == 'yesno'){
                $report->value = $report->value?'Yes':'No';
            }
			$data[] = $report;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function create()
    {
        
        $nid = Auth::guard('admin')->user()->username;
        $employee = Employee::where('nid',$nid)->get()->first();
        //$report = Report::where('report_date',date('Y-m-d'))->where('employee_id',$employee->id)->get()->count();
        //if(!$report){
            $categories = Category::where('input','personil')->get();
            $subcategories = SubCategory::select('sub_categories.*')
                            ->leftJoin('categories','categories.id','=','sub_categories.category_id')
                            ->where('input','personil')->get();
            return view('admin.personnel.create',compact('categories','subcategories'));
        //}
        //return redirect()->back();
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' 	     => 'required',
            'report_date'=> 'required',
        ]);
        $nid      = Auth::guard('admin')->user()->username;
        $employee = Employee::select('employees.*','titles.department_id')
                        ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                        ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                        ->whereNull('finish') 
                        ->where('nid',$nid)->get()->first();
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        if($request->report_date > date('Y-m-d')){
            return response()->json([
        		'status' 	=> false,
        		'message' 	=> 'Tidak Boleh Melbihi Tanggal Sekarang'
        	], 400);
        }
        $report = Report::where('report_date',$request->report_date)->where('employee_id',$employee->id)->first();
        if($report){
            return response()->json([
        		'status' 	=> false,
        		'message' 	=> 'Laporan Sudah Dibuat'
        	], 400);
        }
        $statususer = StatusUser::create([
            'user_id' 	=> Auth::guard('admin')->user()->id,
            'description' 	=> $request->status,
            'status_date' 	=> $request->report_date
        ]);
        foreach($request->id as $id){
            $subcategory = SubCategory::find($id);
            $report_date = $request->report_date;
            $report = Report::create([
                'sub_category_id' 	=> $id,
                'employee_id' 	=> $employee->id,
                'supervisor_id' => null,
                'value' 	=> $request->{"subcategory_".$id},
                'report_date' 	=> $report_date
            ]);
            dispatch(new \App\Jobs\CalculateCategory($subcategory->category_id,$employee->department_id,$report_date));
        }
        
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('personnel.index'),
        ], 200);
    }

    public function info(){
        View::share('menu_active', url('admin/'.'personnel/info'));
        $nid      = Auth::guard('admin')->user()->username;
        $employee = Employee::select('employees.*','titles.department_id')
                        ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                        ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                        ->whereNull('finish') 
                        ->where('nid',$nid)->get()->first();
        $type = [
            'permanent'   => 'Pegawai Tetap',
            'internship'  => 'Alih Daya',
            'pensiun'  => 'Pensiun',
            'other' => 'Lainya',
        ];
        $employee = Employee::with(['region','movement'=>function($q){
                                    $q->with(['title'=>function($q){
                                        $q->with('grade');
                                    }])->whereNull('finish')->first();
                            }])
                            ->select('employees.*')
                            ->find($employee->id);
        if($employee){
            $employee->type = $type[$employee->type];
            return view('admin.personnel.info',compact('employee'));
        }
        else{
            abort(404);
        }
    }
    public function medis(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;

        //Count Data
        $query = DB::table('checkup_details');
        $query->select('employees.*');
        $query->leftJoin('checkups','checkups.id','=','checkup_details.checkup_id');
        $query->leftJoin('medical_details','medical_details.id','=','checkup_details.medical_detail_id');
        $query->where("checkups.employee_id",$employee_id);
        $query->where("checkup_details.value",'<>','Tidak');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('checkup_details');
        $query->select('checkup_details.*','checkups.code','medical_details.name');
        $query->leftJoin('checkups','checkups.id','=','checkup_details.checkup_id');
        $query->leftJoin('medical_details','medical_details.id','=','checkup_details.medical_detail_id');
        $query->where("checkups.employee_id",$employee_id);
        $query->where("checkup_details.value",'<>','Tidak');
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

    public function history(Request $request)
    {
        $employee_id = $request->employee_id;
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        // Count Data
        $query = Report::select('reports.report_date',DB::raw("max(case when sub_categories.name = 'Suhu Badan' then value else 0 end) as suhu_badan"),DB::raw("max(case when sub_categories.name = 'Apakah Sehat?' then value else 0 end) as sehat"),DB::raw("max(case when sub_categories.name = 'Saturasi Oksigen' then value else 0 end) as saturasi"),DB::raw("max(case when sub_categories.name = 'Hasil Profil Resiko Covid-19 termasuk tinggi' then value else 0 end) as resiko"));
        $query->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id');
        $query->where("reports.employee_id",$employee_id);
        $query->groupBy('reports.report_date');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Report::select('reports.report_date',DB::raw("max(case when sub_categories.name = 'Suhu Badan' then value else 0 end) as suhu_badan"),DB::raw("max(case when sub_categories.name = 'Apakah Sehat?' then value else 0 end) as sehat"),DB::raw("max(case when sub_categories.name = 'Saturasi Oksigen' then value else 0 end) as saturasi"),DB::raw("max(case when sub_categories.name = 'Hasil Profil Resiko Covid-19 termasuk tinggi' then value else 0 end) as resiko"));
        $query->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id');
        $query->where("reports.employee_id",$employee_id);
        $query->groupBy('reports.report_date');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $reports = $query->get();
        $data = [];
        foreach($reports as $report){
            $report->no = ++$start;
            $report->sehat = $report->sehat?'Ya':'Tidak';
            $report->suhu_badan = $report->suhu_badan.' °C' ;
            $report->saturasi = $report->saturasi.' %' ;
            $report->resiko = $report->resiko?'Ya':'Tidak';
			$data[] = $report;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function temperature(Request $request)
    {
        $employee_id = $request->employee_id;
        $start = date('Y-m-d', strtotime(date('Y-m-d') . ' -6 day'));
        $finish = date('Y-m-d');
        $query = Report::select('reports.report_date','reports.value');
        $query->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id');
        $query->whereBetween('reports.report_date',[$start,$finish]);
        $query->where('sub_categories.name','Suhu Badan');
        $query->where('reports.employee_id',$employee_id);
        $query->orderBy('report_date','asc');
        $reports = $query->get();
        $series = [];
		$categories = [];
        foreach($reports as $report){
            $categories[] = $report->report_date;
			$series[] = intval($report->value);
        }
        return response()->json([
            'date' => date('d/m/Y', strtotime(date('Y-m-d') . ' -6 day')).' - '.date('d/m/Y'),
			'series' => $series,
			'categories' => $categories
        ], 200);
    }

    public function saturasi(Request $request)
    {
        $employee_id = $request->employee_id;
        $start = date('Y-m-d', strtotime(date('Y-m-d') . ' -6 day'));
        $finish = date('Y-m-d');
        $query = Report::select('reports.report_date','reports.value');
        $query->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id');
        $query->whereBetween('reports.report_date',[$start,$finish]);
        $query->where('sub_categories.name','Saturasi Oksigen');
        $query->where('reports.employee_id',$employee_id);
        $query->orderBy('report_date','asc');
        $reports = $query->get();
        $series = [];
		$categories = [];
        foreach($reports as $report){
            $categories[] = $report->report_date;
			$series[] = intval($report->value);
        }
        return response()->json([
            'date' => date('d/m/Y', strtotime(date('Y-m-d') . ' -6 day')).' - '.date('d/m/Y'),
			'series' => $series,
			'categories' => $categories
        ], 200);
    }
    public function exportmedis(Request $request)
    {
        $i = 0;
        $types = [
            'history'=>'Riwayat',
            'laboratory'=>'Laboraturium',
            'nonlaboratury'=>'Non Laboraturium',
            'physical'=>'Fisik'
        ];
        $employee_id = $request->employee_id;
        $checkups = Checkup::where('employee_id',$employee_id)->get();

        $object 	= new \PHPExcel();
        $object->getProperties()->setCreator('PT PJB UNIT PEMBANGKITAN GRESIK');
        foreach($types as $key => $value){
            $medicals = Medical::orderBy('id','asc')->where('type',$key)->get();
            $medicaldetails = MedicalDetail::select('medical_details.*')
                            ->leftJoin('medicals','medicals.id','=','medical_details.medical_id')
                            ->where('type',$key)
                            ->orderBy('id','asc')->get();
			if($i > 0){
				$object->createSheet();
				$object->setActiveSheetIndex($i);
				$sheet = $object->getActiveSheet();
				$sheet->setTitle($value);
			}
			else{
				$object->setActiveSheetIndex(0);
				$sheet = $object->getActiveSheet();
				$sheet->setTitle($value);
            }
            $sheet->setCellValue('A1', 'No Dokumen');
            $sheet->setCellValue('B1', 'NID');
            $sheet->setCellValue('C1', 'Nama');
            $sheet->setCellValue('D1', 'Tanggal');
            $column = 4;
            foreach($medicals as $medical){
                $sheet->setCellValueByColumnAndRow($column,1,$medical->name);
                $start = \PHPExcel_Cell::stringFromColumnIndex($column);
                foreach($medicaldetails as $medicaldetail){
                    if($medical->id == $medicaldetail->medical_id){
                        $sheet->setCellValueByColumnAndRow($column,2,$medicaldetail->name);
                        $column++;
                    }
                }
                $end = \PHPExcel_Cell::stringFromColumnIndex($column-1);
                $counter = 1;
                $merge = "$start{$counter}:$end{$counter}";
                $object->getActiveSheet()->getStyle("$start{$counter}:$end{$counter}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('f4ff81');
                $object->getActiveSheet()->mergeCells($merge);
            }
            $object->getActiveSheet()->mergeCells('A1:A2');
            $object->getActiveSheet()->mergeCells('B1:B2');
            $object->getActiveSheet()->mergeCells('C1:C2');
            $object->getActiveSheet()->mergeCells('D1:D2');
            $row_number = 3;
            $no = 1;
            foreach($checkups as $checkup){
                $sheet->setCellValue('A'.$row_number, $checkup->code);
                $sheet->setCellValue('B'.$row_number, $checkup->employee->nid);
                $sheet->setCellValue('C'.$row_number, $checkup->employee->name);
                $sheet->setCellValue('D'.$row_number, "'".$checkup->checkup_date);
                $column = 4;
                foreach($medicals as $medical){
                    foreach($medicaldetails as $medicaldetail){
                        if($medical->id == $medicaldetail->medical_id){
                            $checkupvalue = CheckupDetail::where('medical_detail_id',$medicaldetail->id)
                                                        ->where('checkup_id',$checkup->id)
                                                        ->first();
                            $sheet->setCellValueByColumnAndRow($column,$row_number,$checkupvalue->value);
                            $column++;
                        }
                    }
                }

               
                $row_number++;
            }
            
            foreach (range('A', $object->getActiveSheet()->getHighestColumn()) as $column)
            {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
		    $object->getActiveSheet()->freezePane('E3');
            $i++;
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
		$objWriter->save('php://output');
		$export = ob_get_contents();
		ob_end_clean();
		header('Content-Type: application/json');
        return response()->json([
            'status' 	=> true,
            'name'		=> 'data-checkup-'.date('d-m-Y').'.xlsx',
            'message'	=> "Berhasil Download Data Checkup",
            'file' 		=> "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($export)
        ], 200);
    }

}
