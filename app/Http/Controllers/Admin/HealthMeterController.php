<?php

namespace App\Http\Controllers\Admin;

use App\Models\HealthMeter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        //Count Data
        $query = DB::table('health_meters');
        $query->select('health_meters.*');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('health_meters');
        $query->select('health_meters.*');
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.healthmeter.create');
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
            'name'      => 'required',
            'min'      => 'required',
            'max' => 'required',
            'color' 	=> 'required',
            'recomendation'=>'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $healthmeter = HealthMeter::create([
            'name' 	    => $request->name,
            'min' 	    => $request->min,
			'max' => $request->max,
            'color' 	=> $request->color,
            'recomendation'=>$request->recomendation
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $healthmeter = HealthMeter::find($id);
        if($healthmeter){
            return view('admin.healthmeter.edit',compact('healthmeter'));
        }
        else{
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
            'name'      => 'required',
            'min'      => 'required',
            'max' => 'required',
            'color' 	=> 'required',
            'recomendation' => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $healthmeter = HealthMeter::find($id);
        $healthmeter->name = $request->name;
        $healthmeter->min = $request->min;
        $healthmeter->max = $request->max;
        $healthmeter->color = $request->color;
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
                'message'     => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
