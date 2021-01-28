<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'site'));
        $this->middleware('accessmenu', ['except' => ['select', 'set']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $code = strtoupper($request->code);
        $name = strtoupper($request->name);
        $category = $request->category;

        //Count Data
        $query = Site::select('sites.*');
        $query->select('sites.*');
        $query->whereRaw("upper(code) like '%$code%'");
        $query->whereRaw("upper(name) like '%$name%'");
        if ($category) {
            $query->onlyTrashed();
        } 
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Site::with('user')->select('sites.*');
        $query->whereRaw("upper(code) like '%$code%'");
        $query->whereRaw("upper(name) like '%$name%'");
        if ($category) {
            $query->onlyTrashed();
        } 
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $sites = $query->get();

        $data = [];
        foreach ($sites as $site) {
            $site->no = ++$start;
            $data[] = $site;
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
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;

        //Count Data
        $query = Site::whereRaw("upper(name) like '%$name%'");
        if($data_manager){
            $query->where('id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Site::whereRaw("upper(name) like '%$name%'");
        if($data_manager){
            $query->where('id',$site_id);
        }
        $query->orderBy('id', 'asc');
        $query->offset($start);
        $query->limit($length);
        $sites = $query->get();

        $data = [];
        foreach ($sites as $site) {
            $site->no = ++$start;
            $data[] = $site;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }
    public function index()
    {
        return view('admin.site.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(in_array('create',$request->actionmenu)){
            return view('admin.site.create');
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
            'code'              => 'required|unique:sites',
            'name'              => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $site = Site::create([
            'code'                  => $request->code,
            'name'                  => $request->name,
            'updated_by'            => Auth::id()
        ]);
        if (!$site) {
            return response()->json([
                'status'    => false,
                'message'   => $site
            ], 400);
        }
        $logo = $request->file('logo');
        if ($logo) {
            $path = 'assets/site/';
            $logo->move($path, $site->code . '.' . $logo->getClientOriginalExtension());
            $filename = $path . $site->code . '.' . $logo->getClientOriginalExtension();
            $site->logo = $filename ? $filename : '';
            $site->save();
        }
        return response()->json([
            'status'     => true,
            'results'     => route('site.index'),
        ], 200);
    }
    public function show(Request $request,$id)
    {
        if(in_array('read',$request->actionmenu)){
            $site = Site::withTrashed()->find($id);
            if ($site) {
                return view('admin.site.detail', compact('site'));
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
            $site = Site::withTrashed()->find($id);
            if ($site) {
                return view('admin.site.edit', compact('site'));
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
            'code'              => 'required|unique:sites,code,' . $id,
            'name'              => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $site = Site::find($id);
        //$site->code = $request->code;
        $site->name = $request->name;
        $site->updated_by = Auth::id();
        $site->save();
        if (!$site) {
            return response()->json([
                'status' => false,
                'message'     => $site
            ], 400);
        }
        $logo = $request->file('logo');
        if ($logo) {
            if (file_exists($site->logo)) {
                unlink($site->logo);
            }
            $path = 'assets/site/';
            $logo->move($path, $site->code . '.' . $logo->getClientOriginalExtension());
            $filename = $path . $site->code . '.' . $logo->getClientOriginalExtension();
            $site->logo = $filename ? $filename : '';
            $site->save();
        }

        return response()->json([
            'status'     => true,
            'results'     => route('site.index'),
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
            $site = Site::find($id);
            $site->delete();
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
            $site = Site::onlyTrashed()->find($id);
            $site->restore();
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
            $site = Site::onlyTrashed()->find($id);
            $site->forceDelete();
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

    public function set(Request $request)
    {
        $request->session()->put('role_id', $request->id);
        return redirect()->back();
    }
    public function import(Request $request)
    {
        if(in_array('import',$request->actionmenu)){
            return view('admin.site.import');
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
            'sites' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        DB::beginTransaction();
        $sites = json_decode($request->sites);
        foreach($sites as $site){
            $cek = Site::whereRaw("upper(code) = '$site->code'")->withTrashed()->first();
            if(!$cek){
                $department = Site::create([
                    'code' 	        => strtoupper($site->code),
                    'name'          => $site->name,
                    'updated_by'    => Auth::id()
                ]);
                if (!$site) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'     => $site
                    ], 400);
                }
                $site->deleted_at = $site->status?null:date('Y-m-d H:i:s');
                $site->save();
            }
            else{
                $cek->code      = strtoupper($site->code);
                $cek->name      = $site->name;
                $cek->deleted_at= $site->status?null:date('Y-m-d H:i:s');
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
        	'results' 	=> route('site.index'),
        ], 200);
    }
    public function sync(Request $request)
    {
        DB::beginTransaction();
        $host = 'https://webcontent.ptpjb.com/api/data/hr/health_meter/distrik/?apikey=539581c464b44701a297a04a782ce4a9';
        $curl = curl_init($host);
        $response = curl_exec($curl);
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> $response,
        ], 200);
        curl_close($curl);	
        // $sites = json_decode($request->sites);
        // foreach($sites as $site){
        //     $cek = Site::whereRaw("upper(code) = '$site->code'")->withTrashed()->first();
        //     if(!$cek){
        //         $department = Site::create([
        //             'code' 	        => strtoupper($site->code),
        //             'name'          => $site->name,
        //             'updated_by'    => Auth::id()
        //         ]);
        //         if (!$site) {
        //             DB::rollback();
        //             return response()->json([
        //                 'status' => false,
        //                 'message'     => $site
        //             ], 400);
        //         }
        //         $site->deleted_at = $site->status?null:date('Y-m-d H:i:s');
        //         $site->save();
        //     }
        //     else{
        //         $cek->code      = strtoupper($site->code);
        //         $cek->name      = $site->name;
        //         $cek->deleted_at= $site->status?null:date('Y-m-d H:i:s');
        //         $cek->updated_by= Auth::id();
        //         $cek->save();
        //         if (!$cek) {
        //             DB::rollback();
        //             return response()->json([
        //                 'status' => false,
        //                 'message'     => $cek
        //             ], 400);
        //         }
        //     }
        // }
        // DB::commit();
        // return response()->json([
        // 	'status' 	=> true,
        // 	'results' 	=> route('site.index'),
        // ], 200);
    }
}