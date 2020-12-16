<?php

namespace App\Http\Controllers\Admin;

use App\Models\FormulaReport;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class ReportHealthController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'reporthealth'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.reporthealth.index');
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $department_id = $request->department_id;
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;

        // Count Data
        $query = FormulaReport::select('formula_reports.report_date','departments.name as department_name',DB::raw("max(case when formulas.name = 'Total Nilai Bidang' then value else 0 end) as total_bidang"),DB::raw("max(case when formulas.name = 'Peta Kesehatan' then value else 0 end) as peta_kesehatan"));
        $query->leftJoin('departments','departments.id','=','formula_reports.department_id');
        $query->leftJoin('formulas','formulas.id','=','formula_reports.formula_id');
        $query->groupBy('formula_reports.report_date','departments.name');
        if($department_id){
            $query->where('department_id', $department_id);
        }
        if($date_start){
            $query->whereBetween('report_date', [$date_start, $date_finish]);
        }
        $recordsTotal = $query->get()->count();

        //Select Pagination
        $query = FormulaReport::select('formula_reports.report_date','departments.name as department_name',DB::raw("max(case when formulas.name = 'Total Nilai Bidang' then value else 0 end) as total_bidang"),DB::raw("max(case when formulas.name = 'Peta Kesehatan' then value else 0 end) as peta_kesehatan"));
        $query->leftJoin('departments','departments.id','=','formula_reports.department_id');
        $query->leftJoin('formulas','formulas.id','=','formula_reports.formula_id');
        $query->groupBy('formula_reports.report_date','departments.name');
        if($department_id){
            $query->where('department_id', $department_id);
        }
        if($date_start){
            $query->whereBetween('report_date', [$date_start, $date_finish]);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $medicalrecords = $query->get();
        $data = [];
        foreach($medicalrecords as $medicalrecord){
            $medicalrecord->no = ++$start;
            $medicalrecord->color = healthMeter($medicalrecord->peta_kesehatan);
            $medicalrecord->peta_kesehatan = round($medicalrecord->peta_kesehatan,2);
			$data[] = $medicalrecord;
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
    public function create()
    {
        $medicalactions = MedicalAction::all();
        return view('admin.medicalrecord.create',compact('medicalactions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date'      => 'required',
            'employee_id' => 'required',
            'complaint' => 'required',
            'diagnosis_id'=> 'required',
            'medical_action_id'=>'required'
        ]);
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        DB::beginTransaction();
        $medicalrecord = MedicalRecord::create([
            'date' 	    => $request->date,
            'employee_id' => $request->employee_id,
			'complaint' => $request->complaint,
            'employee_family_id' 	=> $request->employee_family_id?$request->employee_family_id:null,
            'partner_id'=>$request->partner_id,
            'medical_action_id'=>$request->medical_action_id,
            'status'=>'Request',
            'print_status' 	=> 0
        ]);
        if (!$medicalrecord) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $medicalrecord
            ], 400);
        }
        foreach(explode(',',$request->diagnosis_id) as $diagnosis_id){
            $medicalrecorddiagnosis = MedicalRecordDiagnosis::create([
                'medical_record_id' => $medicalrecord->id,
                'diagnosis_id'  => $diagnosis_id
            ]);
            if (!$medicalrecorddiagnosis) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' 	=> $medicalrecorddiagnosis
                ], 400);
            }
        }
        if($request->prescription_item){
            foreach($request->prescription_item as $key => $value){
                $medicalrecordpresciption = MedicalRecordPresciption::create([
                    'medical_record_id' => $medicalrecord->id,
                    'instruction' => $request->instruction[$key],
                    'prescribed' => $request->prescribed[$key]
                ]);
                if (!$medicalrecordpresciption) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' 	=> $medicalrecordpresciption
                    ], 400);
                }
            }
        }
        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('medicalrecord.index'),
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

    public function report()
    {
        View::share('menu_active', url('admin/reportdiagnoses'));
        return view('admin.medicalrecord.report');
    }


    public function reportread(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;

        // Count Data
        $query = MedicalRecordDiagnosis::select('medical_record_diagnoses.*');
        $query->leftJoin('medical_records','medical_records.id','=','medical_record_diagnoses.medical_record_id');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        $query->leftJoin('diagnoses','diagnoses.id','=','medical_record_diagnoses.diagnosis_id');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = MedicalRecordDiagnosis::select('medical_records.*',
                        'employees.name as employee_name',
                        'medical_actions.name as medical_action_name',
                        'diagnoses.name as diagnose_name');
        $query->leftJoin('medical_records','medical_records.id','=','medical_record_diagnoses.medical_record_id');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        $query->leftJoin('diagnoses','diagnoses.id','=','medical_record_diagnoses.diagnosis_id');
        //$query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $healthmeters = $query->get();
        $data = [];
        foreach($healthmeters as $healthmeter){
            $healthmeter->no = ++$start;
			$data[] = $healthmeter;
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
        $date = explode(' - ',$request->date);
        $date_start = date('Y-m-d',strtotime(str_replace('/','-',$date[0])));
        $date_finish = date('Y-m-d',strtotime(str_replace('/','-',$date[1])));

        // dd($partner_id);

        $query = FormulaReport::select('formula_reports.report_date','departments.name as department_name',DB::raw("max(case when formulas.name = 'Total Nilai Bidang' then value else 0 end) as total_bidang"),DB::raw("max(case when formulas.name = 'Peta Kesehatan' then value else 0 end) as peta_kesehatan"));
        $query->leftJoin('departments','departments.id','=','formula_reports.department_id');
        $query->leftJoin('formulas','formulas.id','=','formula_reports.formula_id');
        $query->groupBy('formula_reports.report_date','departments.name');
        if($department_id){
            $query->where('department_id', $department_id);
        }
        if($date_start){
            $query->whereBetween('report_date', [$date_start, $date_finish]);
        }
        $medicalrecords = $query->get();
        // dd($medicalrecords);
        //Header Column Excel
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Bidang');
        $sheet->setCellValue('C1', 'Total Nilai Bidang');
        $sheet->setCellValue('D1', 'Peta KEsehatan');

        $row_number = 2;
        //Content Data
		foreach ($medicalrecords as $medicalrecord) {
            $sheet->setCellValue('A'.$row_number, $medicalrecord->report_date);
            $sheet->setCellValue('B'.$row_number, $medicalrecord->department_name);
            $sheet->setCellValue('C'.$row_number, $medicalrecord->total_bidang);
            $sheet->setCellValue('D'.$row_number, round($medicalrecord->peta_kesehatan,2));
            $row_number++;
        }
        foreach (range('A', 'D')as $column)
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
                'name'		=> 'data-Kesehatan-'.date('d-m-Y').'.xlsx',
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
