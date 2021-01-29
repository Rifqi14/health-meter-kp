<?php

namespace App\Http\Controllers\Admin;

use App\Models\Title;
use App\Role;
use App\Models\Site;
use App\Models\Workforce;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class TitleController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'title'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.title.index');
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
        $shortname = strtoupper($request->shortname);
        $category = $request->category;
        $site = $request->site;
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;
        //Count Data
        $query = Title::with(['user','site'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(code) like '%$code%'")->whereRaw("upper(shortname) like '%$shortname%'");
        if ($category) {
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
        $query = Title::with(['user','site'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(code) like '%$code%'")->whereRaw("upper(shortname) like '%$shortname%'");
        if ($category) {
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
        $titles = $query->get();

        $data = [];
        foreach($titles as $title){
            $title->no = ++$start;
			$data[] = $title;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function readrole(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $title_id = $request->title_id;

        //Count Data
        $query = DB::table('role_titles');
        $query->select('roles.*');
        $query->leftJoin('roles', 'roles.id', '=', 'role_titles.role_id');
        $query->where('title_id', $title_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('role_titles');
        $query->select('roles.*');
        $query->leftJoin('roles', 'roles.id', '=', 'role_titles.role_id');
        $query->where('title_id', $title_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $roles = $query->get();

        $data = [];
        foreach ($roles as $role) {
            $role->no = ++$start;
            $data[] = $role;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    public function assignrole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $title = Title::find($request->title_role_id);
        $role = Role::find($request->role_id);
        $title->attachRole($role);
        if (!$title) {
            return response()->json([
                'status' => false,
                'message'     => $title
            ], 400);
        }
        return response()->json([
            'status' => true,
            'message'     => 'Role has been added'
        ], 200);
    }
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $site_id = $request->site_id;
        //Count Data
        $query = Title::select('titles.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Title::select('titles.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        $query->offset($start*$length);
        $query->limit($length);
        $titles = $query->get();

        $data = [];
        foreach($titles as $title){
            $title->no = ++$start;
			$data[] = $title;
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
            return view('admin.title.create');
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
            'code'      => 'required|unique:titles',
            'site_id'   => 'required',
            'name'      => 'required',
            'shortname' => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $title = Title::create([
            'site_id'   => $request->site_id,
            'name'      => $request->name,
            'code'      => strtoupper($request->code),
            'shortname' => $request->shortname,
            'updated_by'=> Auth::id()
        ]);
        if (!$title) {
            return response()->json([
                'status' => false,
                'message' 	=> $title
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('title.index'),
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
        $title = Title::with(['user'])->find($id);
        // dd($title);
        if($title){
            return view('admin.title.detail',compact('title'));
        }
        else{
            abort(404);
        }
    }

    public function employee(Request $request)
    {
        // dd("tes");

        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $title_id = $request->title_id;

        //Count Data
        $query = Workforce::select('workforces.*');
        $query->where('workforces.title_id', $title_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Workforce::with(['workforcegroup', 'agency'])->select('workforces.*');
        $query->where('workforces.title_id', $title_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $workforces = $query->get();

        $data = [];
        foreach($workforces as $workforce){
            $workforce->no = ++$start;
			$data[] = $workforce;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
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
            $title = Title::with(['user','site'])->withTrashed()->find($id);
            if($title){
                return view('admin.title.edit',compact('title'));
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
            'code'      => 'required|unique:titles,code,'.$id,
            'site_id'   => 'required',
            'name'      => 'required',
            'shortname' => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $title = Title::withTrashed()->find($id);
        $title->site_id = $request->site_id;
        $title->code = $request->code;
        $title->name = $request->name;
        $title->shortname = $request->shortname;
        $title->updated_by = Auth::id();
        $title->save();

        if (!$title) {
            return response()->json([
                'status' => false,
                'message' 	=> $title
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('title.index'),
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
            $title = Title::find($id);
            $title->delete();
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
            $title = Title::onlyTrashed()->find($id);
            $title->restore();
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
            $title = Title::onlyTrashed()->find($id);
            $title->forceDelete();
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
            return view('admin.title.import');
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
            $shortname = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $site_code = strtoupper($sheet->getCellByColumnAndRow(3, $row)->getValue());
            $status = $sheet->getCellByColumnAndRow(4, $row)->getValue();
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
                    'shortname' => $shortname,
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
            'titles' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        DB::beginTransaction();
        $titles = json_decode($request->titles);
        foreach($titles as $title){
            $cek = Title::whereRaw("upper(code) = '$title->code'")->withTrashed()->first();
            if(!$cek){
                $insert = Title::create([
                    'code' 	    => strtoupper($title->code),
                    'name'      => $title->name,
                    'shortname' => $title->shortname,
                    'site_id'   => $title->site_id,
                    'updated_by'=> Auth::id()
                ]);
                if (!$insert) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'     => $insert
                    ], 400);
                }
                $insert->deleted_at = $title->status?null:date('Y-m-d H:i:s');
                $insert->save();
            }
            else{
                $cek->code      = strtoupper($title->code);
                $cek->name      = $title->name;
                $cek->shortname = $title->shortname;
                $cek->site_id   = $title->site_id;
                $cek->deleted_at= $title->status?null:date('Y-m-d H:i:s');
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
        	'results' 	=> route('title.index'),
        ], 200);
    }
    public function deleterole(Request $request)
    {
        $role_id = $request->role_id;
        $title_id = $request->title_id;
        try {
            $title = Title::find($title_id);
            $role = Role::find($role_id);
            $title->detachRole($role);
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

    public function sync(Request $request)
    {
        DB::beginTransaction();
        $host = 'https://webcontent.ptpjb.com/api/data/hr/health_meter/jabatan/?apikey=539581c464b44701a297a04a782ce4a9';
        $curl = curl_init($host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        switch(curl_getinfo($curl, CURLINFO_HTTP_CODE)){
            case 200 :
                $response = json_decode($response);
                if(isset($response->returned_object) && count($response->returned_object) > 0){
                    Title::query()->update([
                        'deleted_at'=>date('Y-m-d H:i:s')
                    ]);
                    foreach($response->returned_object as $title){
                        $code = trim($title->POSITION_ID);
                        $site_code = trim($title->DISTRIK_KODE);
                        $site = Site::whereRaw("upper(code) = '$site_code'")->first();
                        if($site){
                            $cek = Title::whereRaw("upper(code) = '$code'")->withTrashed()->first();
                            if(!$cek){
                                $insert = Title::create([
                                    'code' 	    => strtoupper($code),
                                    'name'      => trim($title->DESKRIPSI?$title->DESKRIPSI:'-'),
                                    'shortname' => trim($title->SHORT_DESKRIPSI),
                                    'site_id'   => $site->id,
                                    'updated_by'=> Auth::id()
                                ]);
                                if (!$insert) {
                                    DB::rollback();
                                    return response()->json([
                                        'status' => false,
                                        'message'     => $insert
                                    ], 400);
                                }
                                $insert->deleted_at = $title->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s');
                                $insert->save();
                            }
                            else{
                                $cek->code      = strtoupper($code);
                                $cek->name      = trim($title->DESKRIPSI?$title->DESKRIPSI:'-');
                                $cek->shortname = trim($title->SHORT_DESKRIPSI);
                                $cek->site_id   = $site->id;
                                $cek->deleted_at= $title->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s');
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
                        'message'   => 'Success syncronize data title'
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