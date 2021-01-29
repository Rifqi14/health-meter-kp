<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Site;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

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
        $site = $request->site;
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;
        //Count Data
        $query = Agency::with(['user', 'site'])->whereRaw("upper(name) like '%$name%'");
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
        $query = Agency::with(['user', 'site'])->whereRaw("upper(name) like '%$name%'");
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
        $query = Agency::whereRaw("upper(name) like '%$name%'");
        if($site_id){
            $query->where('site_id',$site_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Agency::whereRaw("upper(name) like '%$name%'");
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
    public function create()
    {
        return view('admin.agency.create');
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
            'site_id'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $site = Site::find($request->site_id);
        $agency = Agency::create([
            'code'          => $site->code.strtoupper($request->code),
            'name'          => $request->name,
            'authentication'=> $request->authentication,
            'host'          => $request->host,
            'port'          => $request->port,
            'updated_by'    => Auth::id(),
            'site_id'       => $request->site_id,
        ]);
        if (!$agency) {
            return response()->json([
                'status' => false,
                'message' 	=> $agency
            ], 400);
        }
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
            return view('admin.agency.detail', compact('agency'));
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
        if ($agency) {
            return view('admin.agency.edit', compact('agency'));
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
        //$agency->code           = $request->code;
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
}