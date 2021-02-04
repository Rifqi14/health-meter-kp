<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\AgencySite;
use App\Models\Site;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

use function Ramsey\Uuid\v1;

class AgencyController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/agency'));
        $this->middleware('accessmenu', ['except'   => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.agency.index');
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
        $query = Agency::with(['user'])->whereRaw("upper(name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Agency::with(['user'])->whereRaw("upper(name) like '%$name%'");
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
        $query = Agency::select('agencies.*')->whereRaw("upper(name) like '%$name%'");
        if($site_id){
            $query->leftJoin('agency_sites','agency_sites.agency_id','=','agencies.id');
            $query->where('agency_sites.site_id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Agency::select('agencies.*')->whereRaw("upper(name) like '%$name%'");
        if($site_id){
            $query->leftJoin('agency_sites','agency_sites.agency_id','=','agencies.id');
            $query->where('agency_sites.site_id',$site_id);
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
    public function create()
    {
        $sites = Site::orderBy('sites.name', 'asc')->get();
        return view('admin.agency.create', compact('sites'));
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
            'code'          => 'required|unique:agencies',
            'name'          => 'required',
            'authentication'=> 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $agency = Agency::create([
            'code'          => strtoupper($request->code),
            'name'          => $request->name,
            'authentication'=> $request->authentication,
            'host'          => $request->host,
            'port'          => $request->port,
            'updated_by'    => Auth::id(),
        ]);
        if (!$agency) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' 	=> $agency
            ], 400);
        }
        if ($request->site) {
            foreach ($request->site as $key => $value) {
                if (isset($request->site_status[$value])) {
                    $site = AgencySite::create([
                        'agency_id' => $agency->id,
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
            'results'   => route('agency.index'),
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
        $agency = Agency::withTrashed()->find($id);
        if ($agency) {
            $sites = Site::select('sites.*', 'agency_sites.id as agency_site_id')->leftJoin('agency_sites', function ($join) use ($id) {
                $join->on('agency_sites.site_id', '=', 'sites.id')
                     ->where('agency_id', '=', $id);
            })->orderBy('sites.name', 'asc')->get();
            return view('admin.agency.detail', compact('agency','sites'));
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
        $agency = Agency::withTrashed()->find($id);
        $sites = Site::select('sites.*', 'agency_sites.id as agency_site_id')->leftJoin('agency_sites', function ($join) use ($id) {
            $join->on('agency_sites.site_id', '=', 'sites.id')
                 ->where('agency_id', '=', $id);
        })->orderBy('sites.name', 'asc')->get();
        if ($agency) {
            return view('admin.agency.edit', compact('agency', 'sites'));
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
            'code'              => 'required|unique:agencies,code,'.$id,
            'name' 	            => 'required',
            'authentication'    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $agency = Agency::find($id);
        $agency->code           = $request->code;
        $agency->name           = $request->name;
        $agency->authentication = $request->authentication;
        $agency->host           = $request->host;
        $agency->port           = $request->port;
        $agency->updated_by      = Auth::id();
        $agency->save();

        if (!$agency) {
            return response()->json([
                'status' => false,
                'message' 	=> $agency
            ], 400);
        }
        
        if ($request->site) {
            $exception = [];
            foreach ($request->site as $key => $value) {
                if (isset($request->site_status[$value])) {
                    array_push($exception, $value);
                    $check = AgencySite::where('agency_id', $agency->id)->where('site_id', $value)->first();
                    if (!$check) {
                        $agencySite = AgencySite::create([
                            'agency_id'     => $agency->id,
                            'site_id'       => $value,
                            'updated_by'    => Auth::id(),
                        ]);
                        if (!$agencySite) {
                            DB::rollBack();
                            return response()->json([
                                'status'    => false,
                                'message'   => $agencySite
                            ], 400);
                        }
                    }
                }
                $agencySite = AgencySite::whereNotIn('site_id', $exception)->where('agency_id', $agency->id)->forceDelete();
            }
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('agency.index'),
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
            $agency = Agency::find($id);
            $agency->delete();
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
            $agency = Agency::onlyTrashed()->find($id);
            $agency->restore();
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
            $agency = Agency::onlyTrashed()->find($id);
            $agency->forceDelete();
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
        if (in_array('import', $request->actionmenu)) {
            return view('admin.agency.import');
        } else {
            abort(403);
        }
    }

    /**
     * Function to give preview from file to import to database
     *
     * @param Request $request
     * @return void
     */
    public function preview(Request $request)
    {
        $authMethod = [
            'ldap'  => 'LDAP',
            'local' => 'Local',
            'web'   => 'WEB (API)'
        ];
        $validator = Validator::make($request->all(), [
            'file'      => 'required|mimes:xlsx'
        ]);
        $file = $request->file('file');
        try {
            $filetype = \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch (Exception $ex) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME).'": '.$ex->getMessage());
        }
        $data = [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row=2; $row <= $highestRow; $row++) { 
            $code = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            $name = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $authentication = strtolower($sheet->getCellByColumnAndRow(2, $row)->getValue());
            $host = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $port = $sheet->getCellByColumnAndRow(4, $row)->getValue();
            $status = $sheet->getCellByColumnAndRow(5, $row)->getValue();
            if ($code) {
                $error = [];
                if (!$site) {
                    array_push($error, 'Distrik tidak ditemukan');
                }
                $data[] = array(
                    'index' => $no,
                    'code'  => $site ? trim($site->code . $code) : null,
                    'name'  => $name,
                    'autentikasi' => array_key_exists($authentication, $authMethod) ? $authMethod[$authentication] : null,
                    'host'      => $host,
                    'port'      => $port,
                    'status'    => $status == 'Y'?1:0,
                    'error'     => implode('<br>', $error),
                    'is_import' => count($error) == 0 ? 1 : 0,
                );
            }
        }
        return response()->json([
            'status'    => true,
            'data'      => $data
        ], 200);
    }

    /**
     * Storemass data from preview to database
     *
     * @param Request $request
     * @return void
     */
    public function storeMass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agency'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $agencies = json_decode($request->agency);
        foreach ($agencies as $key => $agency) {
            $existCode = Agency::whereRaw("upper(code) = '$agency->code'")->first();
            if (!$existCode) {
                $insert = Agency::create([
                    'code'          => strtoupper($agency->code),
                    'name'          => $agency->name,
                    'authentication'=> $agency->autentikasi,
                    'updated_by'    => Auth::id(),
                    'host'          => $agency->host,
                    'port'          => $agency->port,
                    'deleted_at'    => $agency->status ? null : date('Y-m-d H:i:s'),
                ]);
                if (!$insert) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => $insert
                    ], 400);
                }
            } else {
                $existCode->code    = strtoupper($agency->code);
                $existCode->name    = $agency->name;
                $existCode->authentication  = $agency->autentikasi;
                $existCode->updated_by      = Auth::id();
                $existCode->host            = $agency->host;
                $existCode->port            = $agency->port;
                $existCode->deleted_at      = $agency->status ? null : date('Y-m-d H:i:s');
                $existCode->save();
                if (!$existCode) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => $existCode
                    ], 400);
                }
            }
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('agency.index'),
        ], 200);
    }
}