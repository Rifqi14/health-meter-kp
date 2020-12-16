<?php

namespace App\Http\Controllers\Admin;

use App\Models\Config;
use App\Models\Employee;
use App\Models\Report;
use App\Models\MedicalAction;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordDiagnosis;
use App\Models\MedicalRecordPresciption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class MedicalRecordController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'medicalrecord'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.medicalrecord.index');
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $partner_id = $request->partner_id;
        $employee_name = strtoupper($request->employee_name);
        $status = $request->status;
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        // Count Data
        $query = DB::table('medical_records');
        $query->select('medical_records.*');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        $query->leftJoin('partners','partners.id','=','medical_records.partner_id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->where('status',$status);
        }
        if($employee_name){
            $query->whereRaw("upper(employees.name) like '%$employee_name%'");
        }
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('medical_records');
        $query->select('medical_records.*',
                        'employees.name as employee_name',
                        'medical_actions.name as medical_action_name');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->where('status',$status);
        }
        if($employee_name){
            $query->whereRaw("upper(employees.name) like '%$employee_name%'");
        }
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $medicalrecords = $query->get();
        $data = [];
        foreach($medicalrecords as $medicalrecord){
            $medicalrecord->no = ++$start;
            $medicalrecord->medical_action_name = $medicalrecord->medical_action_name?$medicalrecord->medical_action_name:'Belum Ada Tindakan';
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
    public function create(Request $request)
    {
        $employee = null;
        $report = Report::find($request->report_id);
        if($report){
            $employee = Employee::find($report->employee_id);
        }
        $medicalactions = MedicalAction::all();
        return view('admin.medicalrecord.create',compact('medicalactions','employee','report'));
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
        $record_no = 0;
        $medicalaction = MedicalAction::find($request->medical_action_id);
        $status = 'Request';
        if($medicalaction->code == 'resepdokter' || $medicalaction->code == 'rawatjalan' || $medicalaction->code == 'isolasi' || $medicalaction->code == 'istirahatmandiri'){
            $status = 'Closed';
        }
        if($medicalaction->code != 'resepdokter'){
            $record_no = config('configs.surat_pengantar')?config('configs.surat_pengantar'):0;
        }
        if($medicalaction->code == 'istirahatmandiri'){
            $record_no = 0;
        }
        if($medicalaction->code == 'resepdokter'){
            $record_no = config('configs.resep_dokter')?config('configs.resep_dokter'):0;
        }
        $medicalrecord = MedicalRecord::create([
            'record_no' => $record_no,
            'report_id' => $request->report_id?$request->report_id:null,
            'date' 	    => $request->date,
            'employee_id' => $request->employee_id,
			'complaint' => $request->complaint,
            'employee_family_id' 	=> $request->employee_family_id?$request->employee_family_id:null,
            'partner_id'=>$request->partner_id,
            'medical_action_id'=>$request->medical_action_id,
            'status'=>$status,
            'status_invoice'=>0,
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
        if($medicalaction->code == 'opnamepegawai' || $medicalaction->code == 'melahirkan' || $medicalaction->code == 'rawatjalan'  || $medicalaction->code == 'swab' || $medicalaction->code == 'isolasi' || $medicalaction->code == 'hemodalisa' || $medicalaction->code == 'opnamepensiunan' || $medicalaction->code == 'laboraturium'){
            $config = Config::where('option','surat_pengantar')->first();
            if($config){
                $config->value = ++$record_no;
                $config->save();
                if (!$config) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' 	=> $config
                    ], 400);
                }
            }
        }

        if($medicalaction->code == 'resepdokter'){
            $config = Config::where('option','resep_dokter')->first();
            if($config){
                $config->value = ++$record_no;
                $config->save();
                if (!$config) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' 	=> $config
                    ], 400);
                }
            }
        }
        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('medicalrecord.show',['id'=>$medicalrecord]),
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
        $medicalrecord = MedicalRecord::with('medicalaction','partner','employee')->find($id);
        if($medicalrecord){
            return view('admin.medicalrecord.detail',compact('medicalrecord'));
        }
        else{
            abort(404);
        }
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

    public function total()
    {
        $total = MedicalRecordDiagnosis::select('diagnosis_id')
                                        //->distinct()
                                        ->get()
                                        ->count();
        return $total;
    }
    public function lastweek()
    {
		$start = date('Y-m-d', strtotime(date('Y-m-d') . ' -6 day'));
        $finish = date('Y-m-d');
        $total = MedicalRecordDiagnosis::select('diagnosis_id')
                                ->leftJoin('medical_records','medical_records.id','=','medical_record_diagnoses.medical_record_id')
                                ->whereBetween('medical_records.date',[$start,$finish])
                                ->get()
                                ->count();
        return $total;
    }
    public function today()
    {
        $total = MedicalRecordDiagnosis::select('diagnosis_id')
                                ->leftJoin('medical_records','medical_records.id','=','medical_record_diagnoses.medical_record_id')
                                ->where('date',date('Y-m-d'))
                                ->get()
                                ->count();
        return $total;
    }
    public function reportread(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $diagnose_id = $request->diagnose_id;
        $status = $request->status;
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        $name = strtoupper($request->name);
        $pasien = strtoupper($request->pasien);

        // Count Data
        $query = MedicalRecordDiagnosis::select('medical_record_diagnoses.*');
        $query->leftJoin('medical_records','medical_records.id','=','medical_record_diagnoses.medical_record_id');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('employee_families','employee_families.id','=','medical_records.employee_family_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        $query->leftJoin('diagnoses','diagnoses.id','=','medical_record_diagnoses.diagnosis_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if($diagnose_id){
            $query->where('diagnoses.id',$diagnose_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        $query->whereRaw("upper(coalesce(employee_families.name,'')) like '%$pasien%'");
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = MedicalRecordDiagnosis::select('medical_records.*',
                        'employees.name as employee_name',
                        'medical_actions.name as medical_action_name',
                        'diagnoses.name as diagnose_name',
                        'partners.name as partner_name','employee_families.name as patient_name');
        $query->leftJoin('medical_records','medical_records.id','=','medical_record_diagnoses.medical_record_id');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('employee_families','employee_families.id','=','medical_records.employee_family_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        $query->leftJoin('diagnoses','diagnoses.id','=','medical_record_diagnoses.diagnosis_id');
        $query->leftJoin('partners','partners.id','=','medical_records.partner_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if($diagnose_id){
            $query->where('diagnoses.id',$diagnose_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        $query->whereRaw("upper(coalesce(employee_families.name,'')) like '%$pasien%'");
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
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

    public function chart(Request $request)
    {
        $query = MedicalRecordDiagnosis::select('diagnoses.name',DB::raw('count(diagnoses.id) as total'));
        $query->leftJoin('diagnoses','diagnoses.id','=','medical_record_diagnoses.diagnosis_id');
        $query->orderBy('total','desc');
        $query->limit(10);
        $query->groupBy('diagnoses.name');
        $diagnoses = $query->get();
        $series = [];
		$categories = [];
        foreach($diagnoses as $diagnosis){
            $categories[] = $diagnosis->name;
			$series[] = intval($diagnosis->total);
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

        $diagnose_id = $request->diagnose_id;
        $status = strtoupper($request->status);
        $name = strtoupper($request->name);
        $pasien = strtoupper($request->pasien);
        // $order_date = $request->order_date;
        $order_date = explode(' - ',$request->order_date);
        $date_start = date('Y-m-d',strtotime(str_replace('/','-',$order_date[0])));
        $date_finish = date('Y-m-d',strtotime(str_replace('/','-',$order_date[1])));
        
        $query = MedicalRecordDiagnosis::select('medical_records.*',
                        'employees.name as employee_name',
                        'medical_actions.name as medical_action_name',
                        'diagnoses.name as diagnose_name',
                        'partners.name as partner_name','employee_families.name as patient_name');
        $query->leftJoin('medical_records','medical_records.id','=','medical_record_diagnoses.medical_record_id');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('employee_families','employee_families.id','=','medical_records.employee_family_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        $query->leftJoin('diagnoses','diagnoses.id','=','medical_record_diagnoses.diagnosis_id');
        $query->leftJoin('partners','partners.id','=','medical_records.partner_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        $query->whereRaw("upper(coalesce(employee_families.name,'')) like '%$pasien%'");
        if($diagnose_id){
            $query->where('diagnoses.id',$diagnose_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        if($order_date){
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        }
        $healthmeters = $query->get();
        // dd($healthmeters);
        //Header Column Excel
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Pasien');
        $sheet->setCellValue('D1', 'Diagnosis');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Dibuat');

        $row_number = 2;
        //Content Data
		foreach ($healthmeters as $healthmeter) {
            $sheet->setCellValue('A'.$row_number, $healthmeter->date);
            $sheet->setCellValue('B'.$row_number, $healthmeter->employee_name);
            $sheet->setCellValue('C'.$row_number, $healthmeter->patient_name);
            $sheet->setCellValue('D'.$row_number, $healthmeter->diagnose_name);
            $sheet->setCellValue('E'.$row_number, $healthmeter->status);
            $sheet->setCellValue('F'.$row_number, $healthmeter->created_at);
            $row_number++;
        }
        foreach (range('A', 'E')as $column)
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
		if($healthmeters->count() > 0){
            return response()->json([
                'status' 	=> true,
                'name'		=> 'data-Laporan-Diagnosa-'.date('d-m-Y').'.xlsx',
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
