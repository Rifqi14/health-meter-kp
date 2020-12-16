<?php

namespace App\Http\Controllers\Admin;

use App\Models\Diagnosis;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;


class DiagnosisController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'diagnosis'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.diagnosis.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = Diagnosis::select('diagnoses.*');
        $query->select('diagnoses.*');
        $query->whereRaw("upper(diagnoses.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Diagnosis::select('diagnoses.*');
        $query->whereRaw("upper(diagnoses.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $diagnoses = $query->get();

        $data = [];
        foreach($diagnoses as $diagnosis){
            $diagnosis->no = ++$start;
			$data[] = $diagnosis;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('diagnoses');
        $query->select('diagnoses.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('diagnoses');
        $query->select('diagnoses.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $diagnoses = $query->get();

        $data = [];
        foreach($diagnoses as $diagnosis){
            $diagnosis->no = ++$start;
			$data[] = $diagnosis;
		}
        return response()->json([
			'total'=>$recordsTotal,
			'rows'=>$data
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.diagnosis.create');
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
            'code'      => 'required|unique:diagnoses',
            'name'      => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $diagnosis = Diagnosis::create([
            'code' 	    => $request->code,
            'name' 	    => $request->name,
        ]);
        if (!$diagnosis) {
            return response()->json([
                'status' => false,
                'message' 	=> $diagnosis
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('diagnosis.index'),
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
        $diagnosis = Diagnosis::find($id);
        if($diagnosis){
            return view('admin.diagnosis.edit',compact('diagnosis'));
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
            'code'      => 'required|unique:diagnoses,code,'.$id,
            'name' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $diagnosis = Diagnosis::find($id);
        $diagnosis->code = $request->code;
        $diagnosis->name = $request->name;
        $diagnosis->save();

        if (!$diagnosis) {
            return response()->json([
                'status' => false,
                'message' 	=> $diagnosis
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('diagnosis.index'),
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
            $diagnosis = Diagnosis::find($id);
            $diagnosis->delete();
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
    public function import()
    {
        return view('admin.diagnosis.import');
    }
    
    public function preview(Request $request)
    {
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
        $sheet = $objPHPExcel->getActiveSheet(0); 
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++){ 
            $code = $sheet->getCellByColumnAndRow(0, $row)->getCalculatedValue();
            $name = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            if($code){
                $data[] = array(
                    'index'=>$no,
                    'code' => $code,
                    'name' => $name
                );
                $no++; 
            }
        }
        return response()->json([
            'status' 	=> true,
            'data' 	=> $data
        ], 200);
    }

    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'diagnoses' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $diagnoses = json_decode($request->diagnoses);
        foreach($diagnoses as $diagnosis){
            $cek = Diagnosis::whereRaw("upper(code) = '$diagnosis->code'")->first();
            if(!$cek){
                $diagnosis = Diagnosis::create([
                    'code' 	=> strtoupper($diagnosis->code),
                    'name' => $diagnosis->name
                ]);
            }
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('diagnosis.index'),
        ], 200);
    }
}
