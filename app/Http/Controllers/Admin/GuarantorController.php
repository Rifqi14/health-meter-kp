<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Guarantor;
use App\Models\Site;
use App\Models\Title;
use App\Models\Workforce;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class GuarantorController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/guarantor'));
        $this->middleware('accessmenu', ['except'   => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.guarantor.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $site = $request->site;
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;
        $arsip = $request->category;

        //Count Data
        $query = Guarantor::with(['user', 'site','title']);
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
        $query = Guarantor::with(['user', 'site','title']);
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
        $query = Guarantor::select('guarantors.id','titles.name as title_name','titles.code', 'sites.name as site_name')->whereRaw("upper(titles.name) like '%$name%'");
        $query->leftJoin('titles','titles.id','=','guarantors.title_id');
        $query->leftJoin('sites', 'sites.id', '=', 'guarantors.site_id');
        if($site_id){
            $query->where('guarantors.site_id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Guarantor::select('guarantors.id','titles.name as title_name','titles.code', 'sites.name as site_name')->whereRaw("upper(titles.name) like '%$name%'");
        $query->leftJoin('titles','titles.id','=','guarantors.title_id');
        $query->leftJoin('sites', 'sites.id', '=', 'guarantors.site_id');
        if($site_id){
            $query->where('guarantors.site_id',$site_id);
        }
        $query->offset($start);
        $query->limit($length);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $result->title = $result->title_name;
            $result->site = $result->site_name;
            $result->custom = ["<span>$result->title</span>
                                <br>
                                <span><i>$result->site</i></span>"];
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
            return view('admin.guarantor.create');
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
            'title_id'  => 'required',
            'site_id'   => 'required',
            'workforce_id'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $guarantor = Guarantor::create([
            'site_id'       => $request->site_id,
            'title_id'      => $request->title_id,
            'workforce_id'      => $request->workforce_id,
            'executor'     => $request->executor?1:0,
            'updated_by'    => Auth::id()
        ]);
        if (!$guarantor) {
            return response()->json([
                'status' => false,
                'message' 	=> $guarantor
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('guarantor.index'),
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
            $guarantor = Guarantor::with(['site','user'])->withTrashed()->find($id);
            if ($guarantor) {
                return view('admin.guarantor.detail', compact('guarantor'));
            } else {
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
            $guarantor = Guarantor::withTrashed()->find($id);
            if ($guarantor) {
                return view('admin.guarantor.edit', compact('guarantor'));
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
            'title_id'  => 'required',
            'site_id'   => 'required',
            'workforce_id'   => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $guarantor = Guarantor::find($id);
        $guarantor->title_id        = $request->title_id;
        $guarantor->site_id         = $request->site_id;
        $guarantor->workforce_id    = $request->workforce_id;
        $guarantor->executor    = $request->executor?1:0;
        $guarantor->updated_by      = Auth::id();
        $guarantor->save();

        if (!$guarantor) {
            return response()->json([
                'status' => false,
                'message' 	=> $guarantor
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('guarantor.index'),
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
            $guarantor = Guarantor::find($id);
            $guarantor->delete();
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
            $guarantor = Guarantor::onlyTrashed()->find($id);
            $guarantor->restore();
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
            $guarantor = Guarantor::onlyTrashed()->find($id);
            $guarantor->forceDelete();
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
            return view('admin.guarantor.import');
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
            $site_code = strtoupper($sheet->getCellByColumnAndRow(0, $row)->getValue());
            $title_code = strtoupper($sheet->getCellByColumnAndRow(1, $row)->getValue());
            $executor = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $workforce_nid = strtoupper($sheet->getCellByColumnAndRow(3, $row)->getValue());
            $status = $sheet->getCellByColumnAndRow(4, $row)->getValue();
            $site = Site::whereRaw("upper(code) = '$site_code'")->first();
            $title = Title::whereRaw("upper(code) = '$title_code'")->first();
            $workforce = Workforce::whereRaw("upper(nid) = '$workforce_nid'")->first();
            if($code){
                $error = [];
                if(!$site){
                    array_push($error,'Distrik Tidak Ditemukan');
                }
                if(!$title){
                    array_push($error,'Jabatan Tidak Ditemukan');
                }
                $data[] = array(
                    'index'=>$no,
                    'site_name'=>$site?$site->name:null,
                    'site_id'=>$site?$site->id:null,
                    'title_name'=>$title?$title->name:null,
                    'title_id'=>$title?$title->id:null,
                    'workforce_name'=>$workforce?$workforce->name:null,
                    'workforce_id'=>$workforce?$workforce->id:null,
                    'executor'=>$executor?1:0,
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
            'guarantors' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        DB::beginTransaction();
        $guarantors = json_decode($request->guarantors);
        foreach($guarantors as $guarantor){
            $cek = Guarantor::withTrashed()->where('site_id',$guarantor->site_id)->where('workforce_id',$guarantor->workforce_id)->where('title_id',$guarantor->title_id)->first();
            if(!$cek){
                $insert = Guarantor::create([
                    'site_id'       => $guarantor->site_id,
                    'title_id'      => $guarantor->title_id,
                    'workforce_id'  => $guarantor->workforce_id,
                    'executor'      => $guarantor->executor,
                    'updated_by'    => Auth::id()
                ]);
                if (!$insert) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'     => $insert
                    ], 400);
                }
                $insert->deleted_at = $guarantor->status?null:date('Y-m-d H:i:s');
                $insert->save();
            }
            else{
                $cek->deleted_at= $guarantor->status?null:date('Y-m-d H:i:s');
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
        	'results' 	=> route('guarantor.index'),
        ], 200);
    }
    public function sync(Request $request)
    {
        DB::beginTransaction();
        $host = 'https://webcontent.ptpjb.com/api/data/hr/health_meter/atasan/?apikey=539581c464b44701a297a04a782ce4a9';
        $curl = curl_init($host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        switch(curl_getinfo($curl, CURLINFO_HTTP_CODE)){
            case 200 :
                $response = json_decode($response);
                if(isset($response->returned_object) && count($response->returned_object) > 0){
                    Guarantor::query()->update([
                        'deleted_at'=>date('Y-m-d H:i:s')
                    ]);
                    foreach($response->returned_object as $guarantor){
                        $site_code = trim($guarantor->DISTRIK_KODE);
                        $title_code = trim($guarantor->POSITION_ID);
                        $workforce_nid = trim($guarantor->NID);
                        $site = Site::whereRaw("upper(code) = '$site_code'")->first();
                        $title = Title::whereRaw("upper(code) = '$title_code'")->first();
                        $workforce = Workforce::whereRaw("upper(nid) = '$workforce_nid'")->first();
                        if($site && $title){
                            $cek = Guarantor::withTrashed()->where('site_id',$site->id)->where('title_id',$title->id)->first();
                            if(!$cek){
                                $insert = Guarantor::create([
                                    'site_id'       => $site->id,
                                    'title_id'      => $title->id,
                                    'workforce_id'  => $workforce?$workforce->id:null,
                                    'executor'      => $guarantor->STATUS_JABATAN?1:0,
                                    'updated_by'    => Auth::id()
                                ]);
                                if (!$insert) {
                                    DB::rollback();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $insert
                                    ], 400);
                                }
                                $insert->deleted_at = $guarantor->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s');
                                $insert->save();
                            }
                            else{
                                $cek->workforce_id= $workforce?$workforce->id:null;
                                $cek->deleted_at= $guarantor->STATUS_AKTIF?null:date('Y-m-d H:i:s');
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
                    }
                    curl_close($curl);
                    DB::commit();
                    return response()->json([
                        'status' 	=> true,
                        'message'   => 'Success syncronize data department'
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

        $query = Guarantor::select('guarantors.*', 'sites.code as site_code', 'titles.name as title_name');
        $query->leftJoin('sites', 'sites.id', '=', 'guarantors.site_id');
        $query->leftJoin('titles', 'titles.id', '=', 'guarantors.site_id');
        $guarantors = $query->get();

        // Header Columne Excel
        $sheet->setCellValue('A1', 'DISTRIK_KODE');
        $sheet->setCellValue('B1', 'POSITION');
        $sheet->setCellValue('C1', 'STATUS Jabatan');
        $sheet->setCellValue('D1', 'NID');
        $sheet->setCellValue('E1', 'STATUS AKTIF');

        $row_number = 2;

        foreach ($guarantors as $guarantor) {

            $sheet->setCellValue('A' . $row_number, $guarantor->site_code);
            $sheet->setCellValue('B' . $row_number, $guarantor->title_name);
            $sheet->setCellValue('C' . $row_number, '-');
            $sheet->setCellValue('D' . $row_number, '-');
            $sheet->setCellValue('E' . $row_number, $guarantor->deleted_at ? 'N' : 'Y');

            $row_number++;
        }
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($guarantors->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'       => 'data-guarantors-' . date('d-m-Y') . '.xlsx',
                'message'    => "Sukses Download Data Penanggung Jawab",
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