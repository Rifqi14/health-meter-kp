<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SiteUser;
use App\Models\Workforce;
use App\Models\Agency;
use App\Models\WorkforceGroup;
use App\Models\Patient;
use App\Models\Site;
use App\Models\Title;
use App\Models\Grade;
use App\Models\Department;
use App\Models\Guarantor;
use App\Models\Region;
use App\Models\SubDepartment;
use App\Models\TitleWorkforce;
use App\Role;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class WorkforceController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/workforce'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.workforce.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $nid = strtoupper($request->nid);
        $workforce_group_id = $request->workforce_group_id;
        $agency_id = $request->agency_id;
        $site = $request->site;
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;
        $arsip = $request->category;

        //Count Data
        $query = Workforce::with(['updatedby', 'workforcegroup', 'agency', 'title', 'site', 'department', 'subdepartment', 'guarantor'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(nid) like '%$nid%'");
        if ($workforce_group_id) {
            $query->where('workforce_group_id', $workforce_group_id);
        }
        if ($agency_id) {
            $query->where('agency_id', $agency_id);
        }
        if ($site) {
            $query->where('site_id', $site);
        }
        if($data_manager){
            $query->where('site_id',$site_id);
        }
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Workforce::with(['updatedby', 'workforcegroup', 'agency', 'title', 'site', 'department', 'subdepartment', 'guarantor'])->whereRaw("upper(name) like '%$name%'")->whereRaw("upper(nid) like '%$nid%'");
        if ($workforce_group_id) {
            $query->where('workforce_group_id', $workforce_group_id);
        }
        if ($agency_id) {
            $query->where('agency_id', $agency_id);
        }
        if ($site) {
            $query->where('site_id', $site);
        }
        if($data_manager){
            $query->where('site_id',$site_id);
        }
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
        $nid = strtoupper($request->nid);
        $site_id = $request->site_id;
        $title_id = $request->title_id;

        //Count Data
        $query = Workforce::whereRaw("(upper(name) like '%$name%' or upper(nid) like '%$name%')")->whereRaw("upper(nid) like '%$nid%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        if($title_id){
            $query->where('title_id',$title_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Workforce::whereRaw("(upper(name) like '%$name%' or upper(nid) like '%$name%')")->whereRaw("upper(nid) like '%$nid%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        if($title_id){
            $query->where('title_id',$title_id);
        }
        $query->orderBy('name', 'asc');
        $query->offset($start);
        $query->limit($length);
        $results = $query->get();

        $data = [];
        foreach ($results as $result) {
            $result->no = ++$start;
            $result->namenid = ["<span>$result->name<br><small>$result->nid</small></span>"];
            $data[] = $result;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    /**
     * Select secondary title hide already choosen title
     *
     * @param Request $request
     * @return void
     */
    public function selectSecondaryTitle(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $display_name = strtoupper($request->name);
        $id_except = [];
        if ($request->workforce_id) {
            $titleworkforce = DB::table('title_workforce')->where('workforce_id', $request->workforce_id)->get();
            foreach ($titleworkforce as $key => $value) {
                array_push($id_except, $value->title_id);
            }
        }

        // Count Data
        $query = Title::whereRaw("upper(name) like '%$display_name%'");
        if ($request->workforce_id) {
            $query->whereNotIn('id', $id_except);
        }
        $recordsTotal = $query->count();

        // Select Pagination
        $query = Title::whereRaw("upper(name) like '%$display_name%'");
        if ($request->workforce_id) {
            $query->whereNotIn('id', $id_except);
        }
        $query->orderBy('name', 'asc');
        $query->offset($start);
        $query->limit($length);
        $titles = $query->get();

        $data = [];
        foreach ($titles as $title) {
            $title->no = ++$start;
            $data[] = $title;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function secondaryTitle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_id'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $workforce = Workforce::find($request->workforce_title_id);
        $title = Title::find($request->title_id);
        $workforce->secondarytitle()->attach($title);
        if (!$workforce) {
            return response()->json([
                'status' => false,
                'message'     => $workforce
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'     => 'Title has been added'
        ], 200);
    }

    /**
     * Get secondary title by employee id
     *
     * @param Type $var
     * @return void
     */
    public function readSecondaryTitle(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $workforce_id = $request->workforce_id;

        //Count Data
        $query = TitleWorkforce::with(['title'])->where('workforce_id', $workforce_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = TitleWorkforce::with(['title'])->where('workforce_id', $workforce_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $titles = $query->get();

        $data = [];
        foreach ($titles as $title) {
            $title->no = ++$start;
            $data[] = $title;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    /**
     * Detach title from workforce
     *
     * @param Request $request
     * @return void
     */
    public function deleteSecondaryTitle(Request $request)
    {
        $workforce_id = $request->workforce_id;
        $title_id = $request->title_id;
        try {
            $workforce = Workforce::find($workforce_id);
            $title = Title::find($title_id);
            $workforce->secondarytitle()->detach($title);
        } catch (QueryException $th) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error delete data' . $th->getMessage()
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
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
            return view('admin.workforce.create');
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
            'site_id'           => 'required',
            'code'              => 'required',
            'nid'               => 'required',
            'id_card_number'    => 'required',
            'name'              => 'required',
            'address'           => 'required',
            'region_id'         => 'required',
            'phone'             => 'required',
            'workforce_group_id'=> 'required',
            'agency_id'         => 'required',
            'place_of_birth'    => 'required',
            'birth_date'        => 'required',
            'gender'            => 'required',
            'religion'          => 'required',
            'marriage_status'   => 'required',
            'last_education'    => 'required',
            'blood_type'        => 'required',
            'rhesus'            => 'required',
            'email'             => 'required|unique:users',
            'password'          => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $exist = Workforce::checkCode($request->code)->checkNID($request->nid)->first();
        if ($exist) {
            return response()->json([
                'status'    => false,
                'message'   => 'NID dan Employee ID telah digunakan'
            ], 400);
        }

        try {
            DB::beginTransaction();
            $workforce = Workforce::create([
                'site_id'                   => $request->site_id,
                'code'                      => strtoupper($request->code),
                'nid'                       => strtoupper($request->nid),
                'id_card_number'            => $request->id_card_number,
                'name'                      => $request->name,
                'address'                   => $request->address,
                'region_id'                 => $request->region_id,
                'phone'                     => $request->phone,
                'workforce_group_id'        => $request->workforce_group_id,
                'agency_id'                 => $request->agency_id,
                'start_date'                => $request->start_date,
                'finish_date'               => $request->finish_date,
                'place_of_birth'            => $request->place_of_birth,
                'birth_date'                => $request->birth_date,
                'gender'                    => $request->gender,
                'religion'                  => $request->religion,
                'marriage_status'           => $request->marriage_status,
                'last_education'            => $request->last_education,
                'blood_type'                => $request->blood_type,
                'rhesus'                    => $request->rhesus,
                'bpjs_employment_number'    => $request->bpjs_employment_number,
                'bpjs_health_number'        => $request->bpjs_health_number,
                'grade_id'                  => $request->grade_id,
                'title_id'                  => $request->title_id,
                'department_id'             => $request->department_id,
                'sub_department_id'         => $request->sub_department_id,
                'guarantor_id'              => $request->guarantor_id,
                'updated_by'                => Auth::id()
            ]);
            if ($workforce) {
                $user = User::create([
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'username'      => $workforce->nid,
                    'password'      => Hash::make($request->password),
                    'status'        => 1,
                    'workforce_id'  => $workforce->id
                ]);
                if (!$user) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => $user,
                    ], 400);
                }
                $patient = Patient::create([
                    'name'          => $request->name,
                    'status'        => 'Pegawai',
                    'birth_date'    => date('Y-m-d'),
                    'site_id'       => $request->site_id,
                    'updated_by'    => Auth::id(),
                    'workforce_id'  => $workforce->id
                ]);
                if (!$patient) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => $user,
                    ], 400);
                }
            }
        } catch (QueryException $ex) {
            return response()->json([
                'status'      => false,
                'message'     => $ex->errorInfo[2]
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('workforce.index'),
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
        $workforce = Workforce::withTrashed()->find($id);
        if ($workforce) {
            return view('admin.workforce.detail', compact('workforce'));
        } else {
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
        $workforce = Workforce::with(['updatedby', 'workforcegroup', 'agency', 'title', 'site', 'department', 'subdepartment', 'guarantor','user', 'region', 'placeofbirth', 'guarantor.title', 'guarantor.site'])->find($id);
        if ($workforce) {
            return view('admin.workforce.edit', compact('workforce'));
        } else {
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
            'nid'                   => 'required',
            'site_id'               => 'required',
            'code'                  => 'required',
            'id_card_number'        => 'required',
            'name'                  => 'required',
            'address'               => 'required',
            'region_id'             => 'required',
            'phone'                 => 'required',
            'workforce_group_id'    => 'required',
            'agency_id'             => 'required',
            'place_of_birth'        => 'required',
            'birth_date'            => 'required',
            'gender'                => 'required',
            'religion'              => 'required',
            'marriage_status'       => 'required',
            'last_education'        => 'required',
            'blood_type'            => 'required',
            'rhesus'                => 'required',
            'email'                 => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $exist = Workforce::checkCode($request->code)->checkNID($request->nid)->where('id', '<>', $id)->first();
        if ($exist) {
            return response()->json([
                'status'    => false,
                'message'   => 'NID dan Employee ID telah digunakan'
            ], 400);
        }

        $workforce = Workforce::withTrashed()->find($id);
        if ($workforce->nid != $request->nid) {
            $user = User::where('workforce_id', $workforce->id)->first();
            $user->username = $request->nid;
            $user->save();
            if (!$user) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $user
                ], 400);
            }
        }
        $user = User::where('workforce_id', $workforce->id)->first();
        $user->email = $request->email;
        $user->save();
        if (!$user) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message'     => $user
            ], 400);
        }
        if($request->password){
            $user = User::where('workforce_id', $workforce->id)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            if (!$user) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'     => $user
                ], 400);
            }
        }
        $workforce->site_id             = $request->site_id;
        $workforce->code                = strtoupper($request->code);
        $workforce->nid                 = strtoupper($request->nid);
        $workforce->id_card_number      = $request->id_card_number;
        $workforce->name                = $request->name;
        $workforce->address             = $request->address;
        $workforce->region_id           = $request->region_id;
        $workforce->phone               = $request->phone;
        $workforce->workforce_group_id  = $request->workforce_group_id;
        $workforce->agency_id           = $request->agency_id;
        $workforce->start_date          = $request->start_date;
        $workforce->finish_date         = $request->finish_date;
        $workforce->place_of_birth      = $request->place_of_birth;
        $workforce->birth_date          = $request->birth_date;
        $workforce->gender              = $request->gender;
        $workforce->religion            = $request->religion;
        $workforce->marriage_status     = $request->marriage_status;
        $workforce->last_education      = $request->last_education;
        $workforce->blood_type          = $request->blood_type;
        $workforce->rhesus              = $request->rhesus;
        $workforce->bpjs_employment_number = $request->bpjs_employment_number;
        $workforce->bpjs_health_number  = $request->bpjs_health_number;
        $workforce->grade_id            = $request->grade_id;
        $workforce->title_id            = $request->title_id;
        $workforce->department_id       = $request->department_id;
        $workforce->sub_department_id   = $request->sub_department_id;
        $workforce->guarantor_id        = $request->guarantor_id;
        $workforce->updated_by          = Auth::id();
        $workforce->save();
        if (!$workforce) {
            return response()->json([
                'status' => false,
                'message' 	=> $workforce
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'results'   => route('workforce.index')
        ], 200);
    }

    public function import(Request $request)
    {
        if (in_array('import', $request->actionmenu)) {
            return view('admin.workforce.import');
        } else {
            abort(403);
        }
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'         => 'required|mimes:xlsx'
        ]);
        $file = $request->file('file');
        try {
            $filetype     = \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch (\Exception $e) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
        $data     = [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row=2; $row <= $highestRow ; $row++) { 
            $code                   = strtoupper($sheet->getCellByColumnAndRow(0, $row)->getValue());
            $nid                    = strtoupper($sheet->getCellByColumnAndRow(1, $row)->getValue());
            $name                   = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $workforce_group_code   = strtoupper($sheet->getCellByColumnAndRow(3, $row)->getValue());
            $agency_code            = strtoupper($sheet->getCellByColumnAndRow(4, $row)->getValue());
            $title_code             = strtoupper($sheet->getCellByColumnAndRow(5, $row)->getValue());
            $start_date             = $sheet->getCellByColumnAndRow(6, $row)->getValue();
            $finish_date            = $sheet->getCellByColumnAndRow(7, $row)->getValue();
            $grade_code             = strtoupper($sheet->getCellByColumnAndRow(8, $row)->getValue());
            $site_code              = strtoupper($sheet->getCellByColumnAndRow(9, $row)->getValue());
            $department_code        = strtoupper($sheet->getCellByColumnAndRow(10, $row)->getValue());
            $sub_department_code    = strtoupper($sheet->getCellByColumnAndRow(11, $row)->getValue());
            $guarantor_code         = strtoupper($sheet->getCellByColumnAndRow(12, $row)->getValue());
            $status                 = $sheet->getCellByColumnAndRow(13, $row)->getValue();
            $workforce_group        = WorkforceGroup::whereRaw("upper(code) = '$workforce_group_code'")->first();
            $agency                 = Agency::whereRaw("upper(code) = '$agency_code'")->first();
            $title                  = Title::whereRaw("upper(code) = '$title_code'")->first();
            $grade                  = Grade::whereRaw("upper(code) = '$grade_code'")->first();
            $site                   = Site::whereRaw("upper(code) = '$site_code'")->first();
            $department             = Department::whereRaw("upper(code) = '$department_code'")->first();
            $sub_department         = SubDepartment::whereRaw("upper(code) = '$sub_department_code'")->first();
            $guarantor              = Guarantor::whereRaw("upper(code) = '$guarantor_code'")->first();
            if ($nid) {
                $error = [];
                if (!$workforce_group) {
                    array_push($error, 'Kelompok Workforce tidak ditemukan');
                }
                if (!$agency) {
                    array_push($error, 'Instansi tidak ditemukan');
                }
                if (!$title) {
                    array_push($error, 'Jabatan tidak ditemukan');
                }
                if (!$grade) {
                    array_push($error, 'Jenjang jabatan tidak ditemukan');
                }
                if (!$site) {
                    array_push($error, 'Distrik tidak ditemukan');
                }
                if (!$department) {
                    array_push($error, 'Divisi bidang tidak ditemukan');
                }
                if (!$sub_department) {
                    array_push($error, 'Sub divisi bidang tidak ditemukan');
                }
                if (!$guarantor) {
                    array_push($error, 'Penanggung jawab tidak ditemukan');
                }
            }
        }
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
            $workforce = Workforce::find($id);
            $workforce->delete();
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
            $workforce = Workforce::onlyTrashed()->find($id);
            $workforce->restore();
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
            $workforce = Workforce::onlyTrashed()->find($id);
            $workforce->forceDelete();
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
    public function sync(Request $request)
    {
        ini_set('max_execution_time', 0);
        DB::beginTransaction();
        $host = 'https://webcontent.ptpjb.com/api/data/hr/health_meter/workforce/?apikey=539581c464b44701a297a04a782ce4a9';
        $curl = curl_init($host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        switch(curl_getinfo($curl, CURLINFO_HTTP_CODE)){
            case 200 :
                $response = json_decode($response);
                if(isset($response->returned_object) && count($response->returned_object) > 0){
                    Workforce::where('nid','<>','WEBMASTER')->update([
                        'deleted_at'=>date('Y-m-d H:i:s')
                    ]);
                    foreach($response->returned_object as $workforce){
                        $site = Site::whereRaw("upper(code) = '$workforce->KODE_DISTRIK'")->first();
                        $title = Title::whereRaw("upper(code) = '$workforce->KODE_JABATAN'")->first();
                        $grade = Grade::whereRaw("upper(code) = '$workforce->KODE_JENJANGJABATAN'")->first();
                        $agency = Agency::whereRaw("upper(code) = '$workforce->ID_INSTANSI'")->first();
                        $pob = Region::whereRaw("upper(name) = '$workforce->TEMPAT_LAHIR'")->first();
                        $kota = str_replace("KOTA ", "", $workforce->KAB_KOTA);
                        $kab = str_replace("KAB. ", "", $workforce->KAB_KOTA);
                        $region = Region::whereRaw("upper(name) = '$kota' OR upper(name) = '$kab'")->first();
                        $workforcegroup = WorkforceGroup::whereRaw("upper(code) = '$workforce->ID_JENISWORKFORCE'")->first();
                        $department = Department::whereRaw("upper(code) = '$workforce->KODE_DIVBID'")->first();
                        $subdepartment = SubDepartment::whereRaw("upper(code) = '$workforce->KODE_SUBDIVBID'")->first();
                        if($site){
                            $cek = Workforce::whereRaw("upper(nid) = '$workforce->NID'")->withTrashed()->first();
                            if(!$cek){
                                $insert = Workforce::create([
                                    'code'                  => strtoupper($workforce->EMP_ID),
                                    'nid' 	                => strtoupper($workforce->NID),
                                    'name'                  => $workforce->NAMA,
                                    'place_of_birth'        => $pob?$pob->id:null,
                                    'birth_date'            => date('Y-m-d', strtotime($workforce->TANGGAL_LAHIR)),
                                    'gender'                => strtoupper($workforce->JENIS_KELAMIN) == "PRIA" ? "m" : "f",
                                    'religion'              => $workforce->AGAMA,
                                    'marriage_status'       => $workforce->STATUS_PERKAWINAN,
                                    'last_education'        => $workforce->PENDIDIKAN_TERAKHIR,
                                    'blood_type'            => $workforce->GOLONGAN_DARAH,
                                    'rhesus'                => $workforce->RHESUS == "-" ? "Negatif" : "Positif",
                                    'address'               => $workforce->ALAMAT,
                                    'region_id'             => $region?$region->id:null,
                                    'phone'                 => $workforce->NO_HP,
                                    'id_card_number'        => $workforce->NO_KTP,
                                    'bpjs_employment_number'=> $workforce->NO_BPJSKETENAGAKERJAAN,
                                    'bpjs_health_number'    => $workforce->NO_BPJSKESEHATAN,
                                    'site_id'               => $site->id,
                                    'workforce_group_id'    => $workforcegroup?$workforcegroup->id:null,
                                    'agency_id'             => $agency?$agency->id:null,
                                    'department_id'         => $department?$department->id:null,
                                    'sub_department_id'     => $subdepartment?$subdepartment->id:null,
                                    'title_id'              => $title?$title->id:null,
                                    'start_date'            => date('Y-m-d',strtotime($workforce->POS_STARTDATE)),
                                    'finish_date'           => date('Y-m-d',strtotime($workforce->POS_STOPDATE)),
                                    'updated_by'            => Auth::id()
                                ]);
                                if (!$insert) {
                                    DB::rollback();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $insert
                                    ], 400);
                                }
                                $insert->deleted_at = $workforce->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s');
                                $insert->save();
                                $user = User::create([
                                    'name'          => $workforce->NAMA,
                                    'email'         => $workforce->NID.'@ptpjb.com',
                                    'username'      => $workforce->NID,
                                    'password'      => Hash::make(123456),
                                    'status'        => 1,
                                    'workforce_id'  => $insert->id
                                ]);
                                $patient = Patient::create([
                                    'name'          => $workforce->NID,
                                    'status'        => 'Pegawai',
                                    'birth_date'    => date('Y-m-d'),
                                    'site_id'       => $site->id,
                                    'updated_by'    => Auth::id(),
                                    'workforce_id'  => $insert->id
                                ]);
                            }
                            else{
                                $cek->site_id       = $site->id;
                                $cek->name          = $workforce->NAMA;
                                $cek->code          = strtoupper($workforce->EMP_ID);
                                $cek->place_of_birth= $pob?$pob->id:null;
                                $cek->birth_date    = date('Y-m-d', strtotime($workforce->TANGGAL_LAHIR));
                                $cek->gender        = strtoupper($workforce->JENIS_KELAMIN) == 'PRIA' ? 'm' : 'f';
                                $cek->religion      = $workforce->AGAMA;
                                $cek->marriage_status = $workforce->STATUS_PERKAWINAN;
                                $cek->last_education= $workforce->PENDIDIKAN_TERAKHIR;
                                $cek->blood_type    = $workforce->GOLONGAN_DARAH;
                                $cek->rhesus        = $workforce->RHESUS == "-" ? "Negatif" : "Positif";
                                $cek->address       = $workforce->ALAMAT;
                                $cek->region_id     = $region?$region->id:null;
                                $cek->phone         = $workforce->NO_HP;
                                $cek->id_card_number= $workforce->NO_KTP;
                                $cek->bpjs_employment_number = $workforce->NO_BPJSKETENAGAKERJAAN;
                                $cek->bpjs_health_number = $workforce->NO_BPJSKESEHATAN;
                                $cek->start_date    = date('Y-m-d',strtotime($workforce->POS_STARTDATE));
                                $cek->finish_date   = date('Y-m-d',strtotime($workforce->POS_STOPDATE));
                                $cek->agency_id = $agency?$agency->id:null;
                                $cek->workforce_group_id = $workforcegroup?$workforcegroup->id:null;
                                $cek->department_id = $department?$department->id:null;
                                $cek->sub_department_id = $subdepartment?$subdepartment->id:null;
                                $cek->grade_id      = $grade?$grade->id:null;
                                $cek->title_id      = $title?$title->id:null;
                                $cek->deleted_at    = $workforce->STATUS_AKTIF=='Y'?null:date('Y-m-d H:i:s');
                                $cek->updated_by    = Auth::id();
                                $cek->save();
                                if (!$cek) {
                                    DB::rollback();
                                    return response()->json([
                                        'status' => false,
                                        'message'     => $cek
                                    ], 400);
                                }
                                $user = User::where('username',$cek->nid)->first();
                                $user->name = $workforce->NAMA;
                                $user->save();
                            }  
                        }
                    }
                    curl_close($curl);
                    DB::commit();
                    return response()->json([
                        'status' 	=> true,
                        'message'   => 'Success syncronize data workforce'
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