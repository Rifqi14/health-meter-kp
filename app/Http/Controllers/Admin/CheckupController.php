<?php

namespace App\Http\Controllers\Admin;

use App\Models\Checkup;
use App\Models\CheckupDetail;
use App\Models\Medical;
use App\Models\MedicalDetail;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CheckupController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'checkup'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.checkup.index');
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $date_start = $request->date_start;
        $date_finish = $request->date_finish;

        //Count Data
        $query = Checkup::select('checkups.*');
        $query->leftJoin('employees','employees.id','=','checkups.employee_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        $query->whereBetween('checkup_date', [$date_start, $date_finish]);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Checkup::select('checkups.*','employees.name');
        $query->leftJoin('employees','employees.id','=','checkups.employee_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        $query->whereBetween('checkup_date', [$date_start, $date_finish]);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $checkups = $query->get();

        $data = [];
        foreach($checkups as $checkup){
            $checkup->no = ++$start;
			$data[] = $checkup;
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
        $medicals = Medical::orderBy('name','asc')->get();
        $medicaldetails = MedicalDetail::orderBy('name','asc')->get();
        return view('admin.checkup.create',compact('medicals','medicaldetails'));

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
            'employee_id' 	    => 'required',
            'code' 	            => 'required|unique:checkups',
            'checkup_date' 	    => 'required',
            'medicaldetail' 	=> 'required'
        ]);
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $checkup = Checkup::create([
            'employee_id' => $request->employee_id,
            'checkup_date' => $request->checkup_date,
            'code'      => $request->code,
        ]);
        foreach($request->medicaldetail as $id){
            $medicaldetail = MedicalDetail::find($id);
            $checkupdetail = CheckupDetail::create([
                'medical_detail_id' => $id,
                'checkup_id' 	    => $checkup->id,
                'value' 	        => $request->{"medicaldetail_".$id}
            ]);
        }

        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('checkup.index'),
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
        $checkup = Checkup::find($id);
        if($checkup){
            $medicals = Medical::orderBy('id','asc')->get();
            $medicaldetails = MedicalDetail::select('medical_details.*','checkup_details.value')
            ->leftJoin(DB::raw("(select * from checkup_details where checkup_id = $checkup->id) as checkup_details"),'checkup_details.medical_detail_id','=','medical_details.id')->
            orderBy('id','asc')->get();
            return view('admin.checkup.edit',compact('checkup','medicals','medicaldetails','checkupdetails'));
        }
        else{
            abort(404);
        }
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
        $validator = Validator::make($request->all(), [
            'checkup_date' 	    => 'required',
            'medicaldetail' 	=> 'required'
        ]);
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $checkup = Checkup::find($id);
        $checkup->checkup_date = $request->checkup_date;
        $checkup->save();
        foreach($request->medicaldetail as $medical_detail_id){
            $medicaldetail = MedicalDetail::find($medical_detail_id);
            $checkupdetail = CheckupDetail::where('checkup_id',$id)
                                        ->where('medical_detail_id',$medical_detail_id)
                                        ->get()->first();
            if($checkupdetail){
                $checkupdetail->value = $request->{"medicaldetail_".$medical_detail_id};
                $checkupdetail->save();
            }
            else{
                $checkupdetail = CheckupDetail::create([
                    'medical_detail_id' => $medical_detail_id,
                    'checkup_id' 	    => $checkup->id,
                    'value' 	        => $request->{"medicaldetail_".$medical_detail_id}
                ]);
            }

        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('checkup.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $checkup = Checkup::find($id);
            $checkup->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }

    public function export()
    {
        $i = 0;
        $types = [
            'history'=>'Riwayat',
            'laboratory'=>'Laboraturium',
            'nonlaboratury'=>'Non Laboraturium',
            'physical'=>'Fisik'
        ];
        $employees = Employee::select('employees.*','titles.name as title_name','regions.name as place_of_birth')
                            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                            ->leftJoin('regions','regions.id','=','employees.place_of_birth')
                            ->whereNull('finish')
                            ->get();

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
            foreach($employees as $employee){
                $sheet->setCellValue('B'.$row_number, $employee->nid);
                $sheet->setCellValue('C'.$row_number, $employee->name);
                $sheet->setCellValue('D'.$row_number, "'".date('Y-m-d'));
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

    public function import()
    {
        return view('admin.checkup.import');
    }

    public function preview(Request $request)
    {
        $nosheet = 0;
        $types = [
            'history'=>'Riwayat',
            'laboratory'=>'Laboraturium',
            'nonlaboratury'=>'Non Laboraturium',
            'physical'=>'Fisik'
        ];
        $validator = Validator::make($request->all(), [
            'file' 	    => 'required|mimes:xlsx'
        ]);
        $file = $request->file('file');
        try {
            $filetype 	= \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch(\Exception $e) {
            die('Error loading file "'.pathinfo($file,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        $data 	= [];
        $no = 1;
        foreach($types as $key => $value){
            $objPHPExcel->setActiveSheetIndex($nosheet);
            $sheet = $objPHPExcel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $medicals = Medical::orderBy('id','asc')->where('type',$key)->get();
            $medicaldetails = MedicalDetail::select('medical_details.*')
                            ->leftJoin('medicals','medicals.id','=','medical_details.medical_id')
                            ->where('type',$key)
                            ->orderBy('id','asc')->get();
            for ($row = 3; $row <= $highestRow; $row++){
                $code = $sheet->getCellByColumnAndRow(0, $row)->getValue();
                if($code){
                    $nid = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                    $name = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                    $checkup_date = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                    $checkupdetails = [];
                    $column = 4;
                    $employee = Employee::whereRaw("upper(nid) = '$nid'")->first();
                    foreach($medicaldetails as $medicaldetail){
                        $checkupdetails[] = array(
                            'medical_detail_id' => $medicaldetail->id,
                            'value' => $sheet->getCellByColumnAndRow($column, $row)->getValue()
                        );
                        $column++;
                    }
                    if($employee){
                        $data[] = array(
                            'index'=>$no,
                            'code'=>$code,
                            'employee_id'=>$employee->id,
                            'nid'=>$nid,
                            'name' => $name,
                            'checkup_date' => $checkup_date,
                            'checkupdetails' => $checkupdetails,
                        );
                        $no++;
                    }
                }
            }
            $nosheet++;
        }
        return response()->json([
            'status' 	=> true,
            'data' 	=> $data
        ], 200);
    }

    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkups' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $checkups = json_decode($request->checkups);
        foreach($checkups as $row){
            $checkup = Checkup::whereRaw("upper(code) = '$row->code'")->first();
            if(!$checkup){
                $checkup = Checkup::create([
                    'code' => $row->code,
                    'checkup_date' 	=> $row->checkup_date,
                    'employee_id' => $row->employee_id
                ]);
                foreach($row->checkupdetails as $data){
                    $checkupdetail = CheckupDetail::create([
                        'medical_detail_id' => $data->medical_detail_id,
                        'checkup_id' 	    => $checkup->id,
                        'value' 	        => $data->value
                    ]);
                }
            }
            else{
                foreach($row->checkupdetails as $data){
                    $checkupdetail = CheckupDetail::where('checkup_id',$checkup->id)
                                                    ->where('medical_detail_id',$data->medical_detail_id)
                                                    ->first();
                    if(!$checkupdetail){
                        $checkupdetail = CheckupDetail::create([
                            'medical_detail_id' => $data->medical_detail_id,
                            'checkup_id' 	    => $checkup->id,
                            'value' 	        => $data->value
                        ]);
                    }
                    else{
                        $checkupdetail->value = $data->value;
                        $checkupdetail->save();
                    }
                }
            }
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('checkup.index'),
        ], 200);
    }
}
