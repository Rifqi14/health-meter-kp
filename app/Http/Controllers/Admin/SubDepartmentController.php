<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SubDepartment;
use App\Models\SubDepartmentSite;
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
        //Count Data
        $query = SubDepartment::with(['user','site'])->whereRaw("upper(sub_departments.name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = SubDepartment::with(['user','site'])->select('sub_departments.*')->whereRaw("upper(sub_departments.name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
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
        $query = SubDepartment::select('sub_departments.*')->whereRaw("upper(name) like '%$name%'");
        if($site_id){
            $query->leftJoin('sub_department_sites','sub_department_sites.sub_department_id','=','sub_departments.id');
            $query->where('sub_department_sites.site_id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = SubDepartment::select('sub_departments.*')->whereRaw("upper(name) like '%$name%'");
        if($site_id){
            $query->leftJoin('sub_department_sites','sub_department_sites.sub_department_id','=','sub_departments.id');
            $query->where('sub_department_sites.site_id',$site_id);
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
            $sites = Site::orderBy('sites.name', 'asc')->get();
            return view('admin.subdepartment.create', compact('sites'));
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
            'code'          => 'required|unique:sub_departments',
            'name'          => 'required',
            'site_id'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $subdepartment = SubDepartment::create([
            'code'          => strtoupper($request->code),
            'name'          => $request->name,
            'updated_by'    => Auth::id(),
        ]);
        if (!$subdepartment) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' 	=> $subdepartment
            ], 400);
        }
        if ($request->site) {
            foreach ($request->site as $key => $value) {
                if (isset($request->site_status[$value])) {
                    $site = SubDepartmentSite::create([
                        'sub_department_id' => $subdepartment->id,
                        'site_id'   => $value,
                        'updated_by'=> Auth::id(),
                    ]);
                    if (!$site) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $site
                        ], 400);
                    }
                }
            }
        }
        DB::commit();
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
                $sites = Site::select('sites.*', 'sub_department_sites.id as sub_department_site_id')->leftJoin('sub_department_sites', function ($join) use ($id) {
                    $join->on('sub_department_sites.site_id', '=', 'sites.id')
                         ->where('sub_department_id', '=', $id);
                })->orderBy('sites.name', 'asc')->get();
                return view('admin.subdepartment.detail',compact('subdepartment','sites'));
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
                $sites = Site::select('sites.*', 'sub_department_sites.id as sub_department_site_id')->leftJoin('sub_department_sites', function ($join) use ($id) {
                    $join->on('sub_department_sites.site_id', '=', 'sites.id')
                         ->where('sub_department_id', '=', $id);
                })->orderBy('sites.name', 'asc')->get();
                return view('admin.subdepartment.edit', compact('subdepartment','sites'));
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
            'code'          => 'required|unique:sub_departments,code,'.$id,
            'name'          => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $subdepartment = SubDepartment::withTrashed()->find($id);
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
        if ($request->site) {
            $exception = [];
            foreach ($request->site as $key => $value) {
                if (isset($request->site_status[$value])) {
                    array_push($exception, $value);
                    $check = SubDepartmentSite::where('sub_department_id', $subdepartment->id)->where('site_id', $value)->first();
                    if (!$check) {
                        $subdepartmentsite = SubDepartmentSite::create([
                            'sub_department_id'     => $subdepartment->id,
                            'site_id'       => $value,
                            'updated_by'    => Auth::id(),
                        ]);
                        if (!$subdepartmentsite) {
                            DB::rollBack();
                            return response()->json([
                                'status'    => false,
                                'message'   => $subdepartmentsite
                            ], 400);
                        }
                    }
                }
                $subdepartmentsite = SubDepartmentSite::whereNotIn('site_id', $exception)->where('sub_department_id', $subdepartment->id)->forceDelete();
            }
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
            $cek = SubDepartment::whereRaw("upper(code) = '$subdepartment->code'")->withTrashed()->first();
            if(!$cek){
                $insert = SubDepartment::create([
                    'code' 	        => strtoupper($subdepartment->code),
                    'name'          => $subdepartment->name,
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
                        $cek = SubDepartment::whereRaw("upper(code) = '$subdepartment->KODE'")->withTrashed()->first();
                        if(!$cek){
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
                            $insert->deleted_at = $subdepartment->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s');
                            $insert->save();
                        }
                        else{
                            $cek->code      = strtoupper($site->KODE);
                            $cek->name      = $site->DESKRIPSI;
                            $cek->deleted_at= $site->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s');
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
    public function export()
    {
        // dd('aaaaaaa');
        $object = new \PHPExcel();
        $object->getProperties()->setCreator('PJB');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $query = SubDepartment::select('sub_departments.*', 'sites.code as site_code');
        $query->leftJoin('sites', 'sites.id', '=', 'sub_departments.site_id');
        $subdepartments = $query->get();

        // Header Columne Excel
        $sheet->setCellValue('A1', 'Kode');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Kode Distrik');
        $sheet->setCellValue('D1', 'Status');

        $row_number = 2;

        foreach ($subdepartments as $subdepartment) {

            $sheet->setCellValue('A' . $row_number, $subdepartment->code);
            $sheet->setCellValue('B' . $row_number, $subdepartment->name);
            $sheet->setCellValue('C' . $row_number, $subdepartment->site_code);
            $sheet->setCellValue('D' . $row_number, $subdepartment->deleted_at ? 'N' : 'Y');

            $row_number++;
        }
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($subdepartments->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'       => 'data-sub-department' . date('d-m-Y') . '.xlsx',
                'message'    => "Sukses Download Data Sub Bidang",
                'file'       => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
            ], 200);
        } else {
            return response()->json([
                'status'     => false,
                'message'    => "Data not found",
            ], 400);
        }
    }
}