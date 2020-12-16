<?php

namespace App\Http\Controllers\Admin;

use App\Models\Config;
use App\Models\MedicalRecord;
use App\Models\Employee;
use App\Models\MedicalAction;
use App\Models\MedicalRecordPresciption;
use App\Models\MedicalRecordDiagnosis;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class CoverLetterController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'coverletter'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    public function index()
    {
        return view('admin.coverletter.index');
    }
    public function create(Request $request)
    {
        $employee = Employee::find($request->employee_id);
        $medicalactions = MedicalAction::all();
        return view('admin.coverletter.create',compact('medicalactions','employee'));
    }
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
            'date' 	    => $request->date,
            'employee_id' => $request->employee_id,
			'complaint' => $request->complaint,
            'employee_family_id' 	=> $request->employee_family_id?$request->employee_family_id:null,
            'partner_id'=>$request->partner_id,
            'medical_action_id'=>$request->medical_action_id,
            'status'=>$status,
            'print_status' 	=> 0,
            'status_invoice' 	=> 0
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
        	'results' 	=> route('coverletter.show',['id'=>$medicalrecord]),
        ], 200);
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
        $patient_name = strtoupper($request->patient_name);
        $status = $request->status;
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;

        // Count Data
        $query = DB::table('medical_records');
        $query->select('medical_records.*', 'partners.name as partner_name');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('employee_families','employee_families.id','=','medical_records.employee_family_id');
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
        if($patient_name){
            $query->whereRaw("upper(coalesce(employee_families.name,'')) like '%$patient_name%'");
        }
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('medical_records');
        $query->select('medical_records.*',
                        'employees.name as employee_name',
                        'medical_actions.name as medical_action_name',
                        'partners.name as partner_name','employee_families.name as patient_name');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('employee_families','employee_families.id','=','medical_records.employee_family_id');
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
        if($patient_name){
            $query->whereRaw("upper(coalesce(employee_families.name,'')) like '%$patient_name%'");
        }
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $medicalrecords = $query->get();
        $data = [];
        foreach($medicalrecords as $medicalrecord){
            $medicalrecord->no = ++$start;
			$data[] = $medicalrecord;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function total(Request $request)
    {
        $partner_id = $request->partner_id;
        $status = $request->status;
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        $query = MedicalRecord::select('id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        $total = $query->count();
        return $total;
    }
    public function closed(Request $request)
    {
		$partner_id = $request->partner_id;
        $status = $request->status;
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        $query = MedicalRecord::select('id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        $query->where('status','Closed');
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        $total = $query->count();
        return $total;
    }
    public function request(Request $request)
    {
        $partner_id = $request->partner_id;
        $status = $request->status;
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        $query = MedicalRecord::select('id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        $query->where('status','Request');
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        $total = $query->count();
        return $total;
    }
    public function edit($id)
    {
        $medicalrecord = MedicalRecord::with(['medicalaction','employeefamily','partner','employee','medicalrecorddiadnosis'=>function($q){
           $q->with('diagnosis');
        }])->find($id);
        $medicalactions = MedicalAction::all();
        if($medicalrecord){
            return view('admin.coverletter.edit',compact('medicalrecord','medicalactions'));
        }
        else{
            abort(404);
        }
    }
    public function update(Request $request, $id)
    {
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
        $medicalrecord = MedicalRecord::find($id);
        $medicalrecord->record_no = $request->record_no!=''?$request->record_no:$record_no;
        $medicalrecord->status = $request->status;
        $medicalrecord->partner_id = $request->partner_id;
        $medicalrecord->employee_family_id = $request->employee_family_id;
        $medicalrecord->complaint = $request->complaint;
        $medicalrecord->medical_action_id = $request->medical_action_id;
        $medicalrecord->save();
        if (!$medicalrecord) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $medicalrecord
            ], 400);
        }
        if($request->prescription_item){
            foreach($request->prescription_item as $key => $value){
                if($value){
                    $medicalrecordpresciption = MedicalRecordPresciption::find($value);
                    $medicalrecordpresciption->prescribed = $request->prescribed[$key];
                    $medicalrecordpresciption->instruction = $request->instruction[$key];
                    $medicalrecordpresciption->save();
                    if (!$medicalrecordpresciption) {
                        DB::rollback();
                        return response()->json([
                            'status' => false,
                            'message' 	=> $medicalrecordpresciption
                        ], 400);
                    }
                }
                else{
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
        }
        if($request->record_no != ''){
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
        }
        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('coverletter.show',['id'=>$id]),
        ], 200);

    }
    public function show($id)
    {
        $medicalrecord = MedicalRecord::with(['medicalaction','employeefamily','partner','employee','medicalrecorddiadnosis'=>function($q){
            $q->with('diagnosis');
         }])->find($id);
         $medicalactions = MedicalAction::all();
         if($medicalrecord){
             return view('admin.coverletter.detail',compact('medicalrecord','medicalactions'));
         }
         else{
             abort(404);
         }
    }

    public function print($id)
    {
        $medicalrecord = MedicalRecord::with(['medicalaction','employeefamily','partner','employee'=>function($q){
            $q->with(['movement'=>function($q){
                $q->with(['title'=>function($q){
                    $q->with('grade');
                }])->whereNull('finish')->first();
            }]);
        }])->find($id);
        if($medicalrecord){

            $medicalrecord->print_status = 1;
            $medicalrecord->save();
            return view('admin.coverletter.print',compact('medicalrecord'));
        }
        else{
            abort(404);
        }
    }

    public function chart(Request $request)
    {
        $partner_id = $request->partner_id;
        $status = $request->status;
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;
        $query = MedicalRecord::select('medical_actions.name',DB::raw('count(medical_actions.id) as total'));
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        if($partner_id){
            $query->where('partner_id',$partner_id);
        }
        if($status){
            $query->where('status',$status);
        }
        $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        $query->orderBy('total','desc');
        $query->limit(10);
        $query->groupBy('medical_actions.name');
        $medicalactions = $query->get();
        $series = [];
		$categories = [];
        foreach($medicalactions as $medicalaction){
            $categories[] = $medicalaction->name;
			$series[] = intval($medicalaction->total);
        }
        return response()->json([
            'title' =>  Carbon::parse($request->date_start)->format('d/m/Y').' - '.Carbon::parse($request->date_finish)->format('d/m/Y'),
			'series' => $series,
			'categories' => $categories
        ], 200);
    }

    public function export(Request $request){
        $object 	= new \PHPExcel();
        $object->getProperties()->setCreator('Perki Surabaya');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $partner_id = $request->partner_id;
        $status = strtoupper($request->status);
        $date = explode(' - ',$request->date);
        $date_start = date('Y-m-d',strtotime(str_replace('/','-',$date[0])));
        $date_finish = date('Y-m-d',strtotime(str_replace('/','-',$date[1])));

        // dd($partner_id);

        $query = MedicalRecord::select('medical_records.*',
                        'employees.name as employee_name',
                        'partners.name as partner_name',
                        'medical_actions.name as medical_action_name','employee_families.name as patient_name');
        $query->leftJoin('employees','employees.id','=','medical_records.employee_id');
        $query->leftJoin('employee_families','employee_families.id','=','medical_records.employee_family_id');
        $query->leftJoin('partners','partners.id','=','medical_records.partner_id');
        $query->leftJoin('medical_actions','medical_actions.id','=','medical_records.medical_action_id');
        if($partner_id){
            $query->where('partner_id', $partner_id);
        }
        if($status){
            $query->whereRaw("upper(status) like '%$status%'");
        }
        if($date_start){
            $query->whereBetween('medical_records.date', [$date_start, $date_finish]);
        }
        $query->orderBy('id','asc');
        $medicalrecords = $query->get();
        // dd($medicalrecords);
        //Header Column Excel
        $sheet->setCellValue('A1', 'Id');
        $sheet->setCellValue('B1', 'No Surat');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Nama');
        $sheet->setCellValue('E1', 'Pasien');
        $sheet->setCellValue('F1', 'Partner');
        $sheet->setCellValue('G1', 'Tindakan');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Dibuat');

        $row_number = 2;
        //Content Data
		foreach ($medicalrecords as $medicalrecord) {
            $sheet->setCellValue('A'.$row_number, $medicalrecord->id);
            $sheet->setCellValue('B'.$row_number, $medicalrecord->record_no);
            $sheet->setCellValue('C'.$row_number, $medicalrecord->date);
            $sheet->setCellValue('D'.$row_number, $medicalrecord->employee_name);
            $sheet->setCellValue('E'.$row_number, $medicalrecord->patient_name);
            $sheet->setCellValue('F'.$row_number, $medicalrecord->partner_name);
            $sheet->setCellValue('G'.$row_number, $medicalrecord->medical_action_name);
            $sheet->setCellValue('H'.$row_number, $medicalrecord->status);
            $sheet->setCellValue('I'.$row_number, $medicalrecord->created_at);
            $row_number++;
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
                'name'		=> 'data-medicalrecord-'.date('d-m-Y').'.xlsx',
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
