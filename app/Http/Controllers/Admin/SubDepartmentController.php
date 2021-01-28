<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SubDepartment;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class SubDepartmentController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/subdepartment'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.subdepartment.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $arsip = $request->category;
        $site = $request->site;
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;
        //Count Data
        $query = SubDepartment::with(['user','site'])->whereRaw("upper(sub_departments.name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        if ($site) {
            $query->where('site_id', $site);
        }
        if($data_manager){
            $query->where('site_id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = SubDepartment::with(['user','site'])->select('sub_departments.*')->whereRaw("upper(sub_departments.name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        if ($site) {
            $query->where('site_id', $site);
        }
        if($data_manager){
            $query->where('site_id',$site_id);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $data[] = $result;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $site_id = $request->site_id;
        //Count Data
        $query = SubDepartment::whereRaw("upper(name) like '%$name%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = SubDepartment::whereRaw("upper(name) like '%$name%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        $query->offset($start*$length);
        $query->limit($length);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $data[] = $result;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
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
            return view('admin.subdepartment.create');
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
            'code'          => 'required',
            'name'          => 'required',
            'site_id'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $exist = SubDepartment::whereRaw("upper(code) = '$request->code'")->where('site_id',$request->site_id)->first();
        if($exist){
            return response()->json([
                'status' 	=> false,
        		'message' 	=> 'The code has already been taken.'
        	], 400);  
        }
        $subdepartment = SubDepartment::create([
            'site_id'       => $request->site_id,
            'code'          => strtoupper($request->code),
            'name'          => $request->name,
            'updated_by'    => Auth::id(),
        ]);
        if (!$subdepartment) {
            return response()->json([
                'status' => false,
                'message' 	=> $subdepartment
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('subdepartment.index'),
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
            $subdepartment = SubDepartment::withTrashed()->find($id);
            if($subdepartment){
                return view('admin.subdepartment.detail',compact('subdepartment'));
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
            $subdepartment = SubDepartment::withTrashed()->find($id);
            if ($subdepartment) {
                return view('admin.subdepartment.edit', compact('subdepartment'));
            } else {
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
            'code'          => 'required',
            'site_id'       => 'required',
            'name'          => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $exist = SubDepartment::whereRaw("upper(code) = '$request->code'")->where('site_id',$request->site_id)->where('id','<>',$id)->first();
        if($exist){
            return response()->json([
                'status' 	=> false,
        		'message' 	=> 'The code has already been taken.'
        	], 400);  
        }
        $subdepartment = SubDepartment::withTrashed()->find($id);
        $subdepartment->site_id = $request->site_id;
        $subdepartment->code = strtoupper($request->code);
        $subdepartment->name = $request->name;
        $subdepartment->updated_by = Auth::id();
        $subdepartment->save();
        if (!$subdepartment) {
            return response()->json([
                'status' => false,
                'message' 	=> $subdepartment
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'results'   => route('subdepartment.index')
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
            $subdepartment = SubDepartment::find($id);
            $subdepartment->delete();
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
            $subdepartment = SubDepartment::onlyTrashed()->find($id);
            $subdepartment->restore();
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
            $subdepartment = SubDepartment::onlyTrashed()->find($id);
            $subdepartment->forceDelete();
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
            return view('admin.subdepartment.import');
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
            $site_code = strtoupper($sheet->getCellByColumnAndRow(2, $row)->getValue());
            $status = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $site = Site::whereRaw("upper(code) = '$site_code'")->first();
            if($code){
                $error = [];
                if(!$site){
                    array_push($error,'Distrik Tidak Ditemukan');
                }
                $data[] = array(
                    'index'=>$no,
                    'code' => trim($code),
                    'name' => $name,
                    'site_name'=>$site?$site->name:null,
                    'site_id'=>$site?$site->id:null,
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
            'subdepartments' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        DB::beginTransaction();
        $subdepartments = json_decode($request->subdepartments);
        foreach($subdepartments as $subdepartment){
            $cek = SubDepartment::whereRaw("upper(code) = '$subdepartment->code'")->where('site_id',$subdepartment->site_id)->withTrashed()->first();
            if(!$cek){
                $insert = SubDepartment::create([
                    'code' 	        => strtoupper($subdepartment->code),
                    'name'          => $subdepartment->name,
                    'site_id'       => $subdepartment->site_id,
                    'updated_by'    => Auth::id()
                ]);
                if (!$insert) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'     => $insert
                    ], 400);
                }
                $subdepartment->deleted_at = $subdepartment->status?null:date('Y-m-d H:i:s');
                $subdepartment->save();
            }
            else{
                $cek->code      = strtoupper($subdepartment->code);
                $cek->name      = $subdepartment->name;
                $cek->site_id   = $subdepartment->site_id;
                $cek->deleted_at= $subdepartment->status?null:date('Y-m-d H:i:s');
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
        	'results' 	=> route('subdepartment.index'),
        ], 200);
    }
    public function sync(Request $request)
    {
        DB::beginTransaction();
        $host = 'https://webcontent.ptpjb.com/api/data/hr/health_meter/subdivbid/?apikey=539581c464b44701a297a04a782ce4a9';
        $curl = curl_init($host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        switch(curl_getinfo($curl, CURLINFO_HTTP_CODE)){
            case 200 :
                $response = json_decode($response);
                if(isset($response->returned_object) && count($response->returned_object) > 0){
                    SubDepartment::query()->update([
                        'deleted_at'=>date('Y-m-d H:i:s')
                    ]);
                    foreach($response->returned_object as $subdepartment){
                        $cek = SubDepartment::whereRaw("upper(code) = '$subdepartment->KODE'")->withTrashed()->get();
                        if(!$cek->count()){
                            $insert = SubDepartment::create([
                                'code' 	        => strtoupper($subdepartment->KODE),
                                'name'          => $subdepartment->DESKRIPSI,
                                'updated_by'    => Auth::id()
                            ]);
                            if (!$insert) {
                                DB::rollback();
                                return response()->json([
                                    'status'    => false,
                                    'message'   => $insert
                                ], 400);
                            }
                            $insert->deleted_at = $department->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s');
                            $insert->save();
                        }
                        else{
                            SubDepartment::whereRaw("upper(code) = '$subdepartment->KODE'")->update([
                                'name'          => $subdepartment->DESKRIPSI,
                                'deleted_at'    => $subdepartment->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s'),
                                'update_by'     => Auth::id()
                            ]);
                        }  
                    }
                    curl_close($curl);
                    DB::commit();
                    return response()->json([
                        'status' 	=> true,
                        'message'   => 'Success syncronize data sub department'
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