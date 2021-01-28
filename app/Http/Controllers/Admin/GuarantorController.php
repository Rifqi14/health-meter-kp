<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Guarantor;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
    public function show($id)
    {
        $guarantor = Guarantor::with(['site','user'])->withTrashed()->find($id);
        if ($guarantor) {
            return view('admin.guarantor.detail', compact('guarantor'));
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
        $guarantor = Guarantor::withTrashed()->find($id);
        if ($guarantor) {
            return view('admin.guarantor.edit', compact('guarantor'));
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
            'position_code'     => 'required|unique:guarantors,position_code,'.$id,
            'nid' 	            => 'required',
            'site_id'           => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $guarantor = Guarantor::find($id);
        $guarantor->position_code   = $request->position_code;
        $guarantor->nid             = $request->nid;
        $guarantor->site_id         = $request->site_id;
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
}