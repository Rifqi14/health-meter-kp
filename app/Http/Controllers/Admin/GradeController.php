<?php

namespace App\Http\Controllers\Admin;

use App\Models\Grade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'grade'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.grade.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $code = strtoupper($request->code);
        $category = $request->category;

        //Count Data
        $query = Grade::with(['user'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(code) like '%$code%'");
        if ($category) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Grade::with(['user'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(code) like '%$code%'");
        if ($category) {
            $query->onlyTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $grades = $query->get();

        $data = [];
        foreach($grades as $grade){
            $grade->no = ++$start;
			$data[] = $grade;
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
        $query = Grade::select('grades.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Grade::select('grades.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start*$length);
        $query->limit($length);
        $grades = $query->get();

        $data = [];
        foreach($grades as $grade){
            $grade->no = ++$start;
			$data[] = $grade;
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
    public function create(Request $request)
    {
        if(in_array('create',$request->actionmenu)){
            return view('admin.grade.create');
        }
        else{
            abort(403);
        }
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
            'code'      => 'required|unique:grades',
            'name'      => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $grade = Grade::create([
            'code' 	    => strtoupper($request->code),
            'name' 	    => $request->name,
            'updated_by'=> Auth::id()
        ]);
        if (!$grade) {
            return response()->json([
                'status' => false,
                'message' 	=> $grade
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('grade.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if(in_array('read',$request->actionmenu)){
            $grade = Grade::find($id);
            if($grade){
                return view('admin.grade.detail',compact('grade'));
            }
            else{
                abort(404);
            }
        }
        else{
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if(in_array('update',$request->actionmenu)){
            $grade = Grade::find($id);
            if($grade){
                return view('admin.grade.edit',compact('grade'));
            }
            else{
                abort(404);
            }
        }
        else{
            abort(403);
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
            'code'      => 'required|unique:grades,code,'.$id,
            'name'      => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $grade = Grade::find($id);
        $grade->code = strtoupper($request->code);
        $grade->name = $request->name;
        $grade->updated_by = Auth::id();
        $grade->save();

        if (!$grade) {
            return response()->json([
                'status' => false,
                'message' 	=> $grade
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('grade.index'),
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
            $grade = Grade::find($id);
            $grade->delete();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error archive data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success archive data'
        ], 200);
    }

    public function restore($id)
    {
        try {
            $grade = Grade::onlyTrashed()->find($id);
            $grade->restore();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error restore data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success restore data'
        ], 200);
    }

    public function delete($id)
    {
        try {
            $grade = Grade::onlyTrashed()->find($id);
            $grade->forceDelete();
        } catch (QueryException $th) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error delete data ' . $th->errorInfo[2]
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete data'
        ], 200);
    }
    public function import(Request $request)
    {
        if(in_array('import',$request->actionmenu)){
            return view('admin.grade.import');
        }
        else{
            abort(403);
        }
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
            $code = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            $name = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $status = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            if($code){
                $error = [];
                $data[] = array(
                    'index'=>$no,
                    'code' => trim($code),
                    'name' => $name,
                    'status'=>$status=='Y'?1:0,
                    'error'=>implode($error,'<br>'),
                    'is_import'=>count($error) == 0?1:0
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
            'grades' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        DB::beginTransaction();
        $grades = json_decode($request->grades);
        foreach($grades as $grade){
            $cek = Grade::whereRaw("upper(code) = '$grade->code'")->withTrashed()->first();
            if(!$cek){
                $insert = Grade::create([
                    'code' 	        => strtoupper($grade->code),
                    'name'          => $grade->name,
                    'updated_by'    => Auth::id()
                ]);
                if (!$insert) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'     => $insert
                    ], 400);
                }
                $insert->deleted_at = $grade->status?null:date('Y-m-d H:i:s');
                $insert->save();
            }
            else{
                $cek->code      = strtoupper($grade->code);
                $cek->name      = $grade->name;
                $cek->deleted_at= $grade->status?null:date('Y-m-d H:i:s');
                $cek->updated_by= Auth::id();
                $cek->save();
                if (!$cek) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'     => $cek
                    ], 400);
                }
            }
        }
        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('grade.index'),
        ], 200);
    }
    public function sync(Request $request)
    {
        DB::beginTransaction();
        $host = 'https://webcontent.ptpjb.com/api/data/hr/health_meter/jenjangjabatan/?apikey=539581c464b44701a297a04a782ce4a9';
        $curl = curl_init($host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        switch(curl_getinfo($curl, CURLINFO_HTTP_CODE)){
            case 200 :
                $response = json_decode($response);
                if(isset($response->returned_object) && count($response->returned_object) > 0){
                    Grade::query()->update([
                        'deleted_at'=>date('Y-m-d H:i:s')
                    ]);
                    foreach($response->returned_object as $grade){
                        $cek = Grade::whereRaw("upper(code) = '$grade->KODE'")->withTrashed()->first();
                        if(!$cek){
                            $insert = Grade::create([
                                'code' 	        => strtoupper($grade->KODE),
                                'name'          => $grade->DESKRIPSI,
                                'updated_by'    => Auth::id()
                            ]);
                            if (!$insert) {
                                DB::rollback();
                                return response()->json([
                                    'status' => false,
                                    'message'     => $insert
                                ], 400);
                            }
                            $insert->deleted_at = $grade->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s');
                            $insert->save();
                        }
                        else{
                            $cek->code      = strtoupper($grade->KODE);
                            $cek->name      = $grade->DESKRIPSI;
                            $cek->deleted_at= $grade->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s');
                            $cek->updated_by= Auth::id();
                            $cek->save();
                            if (!$cek) {
                                DB::rollback();
                                return response()->json([
                                    'status' => false,
                                    'message'     => $cek
                                ], 400);
                            }
                        }  
                    }
                    curl_close($curl);
                    DB::commit();
                    return response()->json([
                        'status' 	=> true,
                        'message'   => 'Success syncronize data grade'
                    ], 200);
                }
                else{
                    curl_close($curl);
                    DB::commit();
                    return response()->json([
                        'status' 	=> false,
                        'message'   => 'Row data not found'
                    ], 200);
                }
                
                break;
            default:
                curl_close($curl);
                DB::commit();
                return response()->json([
                    'status' 	=> false,
                    'message'   => 'Error connection'
                ], 200);
        }
    }
}