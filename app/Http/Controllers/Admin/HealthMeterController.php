<?php

namespace App\Http\Controllers\Admin;

use App\Models\HealthMeter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class HealthMeterController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'healthmeter'));
        $this->middleware('accessmenu');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.healthmeter.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $site = $request->site;
        $data_manager = $request->data_manager;
        $site_id = $request->site_id;
        $arsip = $request->category;

        //Count Data
        $query = HealthMeter::with(['user'])->whereRaw("upper(name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = HealthMeter::with(['user'])->whereRaw("upper(name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $healthmeters = $query->get();

        $data = [];
        foreach($healthmeters as $healthmeter){
            $healthmeter->no = ++$start;
			$data[] = $healthmeter;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $nid = strtoupper($request->nid);
        // $site_id = $request->site_id;

        //Count Data
        $query = HealthMeter::whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = HealthMeter::whereRaw("upper(name) like '%$name%'");
        $query->orderBy('name', 'asc');
        $query->offset($start);
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
            return view('admin.healthmeter.create');
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
            'name'              => 'required',
            'min'               => 'required',
            'max'               => 'required',
            'color'     => 'required',
            'recomendation'     => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $healthmeter = HealthMeter::create([
            'name' 	            => $request->name,
            'min' 	            => $request->min,
			'max'               => $request->max,
            'color' 	        => $request->color,
            'recomendation'     => $request->recomendation,
            'updated_by'        => Auth::id()
        ]);
        if (!$healthmeter) {
            return response()->json([
                'status' => false,
                'message' 	=> $healthmeter
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('healthmeter.index'),
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
            $healthmeter = HealthMeter::with(['site', 'workforcegroup'])->find($id);
            if($healthmeter){
                return view('admin.healthmeter.detail',compact('healthmeter'));
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
            $healthmeter = HealthMeter::with(['site', 'workforcegroup'])->find($id);
            if($healthmeter){
                return view('admin.healthmeter.edit',compact('healthmeter'));
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
            'name'              => 'required',
            'min'               => 'required',
            'max'               => 'required',
            'color'             => 'required',
            'recomendation'     => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $healthmeter = HealthMeter::find($id);
        $healthmeter->name          = $request->name;
        $healthmeter->min           = $request->min;
        $healthmeter->max           = $request->max;
        $healthmeter->color         = $request->color;
        $healthmeter->recomendation = $request->recomendation;
        $healthmeter->save();

        if (!$healthmeter) {
            return response()->json([
                'status' => false,
                'message' 	=> $healthmeter
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('healthmeter.index'),
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
            $healthmeter = HealthMeter::find($id);
            $healthmeter->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error archive data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success archive data'
        ], 200);
    }

    public function restore(Request $request)
    {
        try {
            $healthmeter = HealthMeter::onlyTrashed()->find($request->id);
            $healthmeter->restore();
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

    public function delete(Request $request)
    {
        try {
            $healthmeter = HealthMeter::onlyTrashed()->find($request->id);
            $healthmeter->forceDelete();
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