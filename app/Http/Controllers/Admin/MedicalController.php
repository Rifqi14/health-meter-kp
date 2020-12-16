<?php

namespace App\Http\Controllers\Admin;

use App\Models\Medical;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class MedicalController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'medical'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.medical.index');
    }

    public function read(Request $request)
    {
        $type = [
            'history'     => 'Riwayat',
            'physical'    => 'Fisik' ,
            'laboratory'  => 'Laboraturium' ,
            'nonlaboratury'    => 'Non Laboraturium' ,
        ];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('medicals');
        $query->select('medicals.*');
        $query->whereRaw("upper(medicals.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('medicals');
        $query->select('medicals.*');
        $query->whereRaw("upper(medicals.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $medicals = $query->get();

        $data = [];
        foreach($medicals as $medical){
            $medical->no = ++$start;
            $medical->type = $type[$medical->type];
			$data[] = $medical;
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
        return view('admin.medical.create');
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
            'type' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $medical = Medical::create([
            'name' 	    => $request->name,
			'type' 	    => $request->type
        ]);
        if (!$medical) {
            return response()->json([
                'status' => false,
                'message' 	=> $medical
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('medical.index'),
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
        $type = [
            'history'     => 'Riwayat',
            'physical'    => 'Fisik' ,
            'laboratory'  => 'Laboraturium' ,
            'nonlaboratury'    => 'Non Laboraturium' ,
        ];
        $medical = Medical::find($id);
        if($medical){
            return view('admin.medical.detail',compact('medical','type'));
        }
        else{
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
        $medical = Medical::find($id);
        if($medical){
            return view('admin.medical.edit',compact('medical'));
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
            'name' 	    => 'required',
            'type' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $medical = Medical::find($id);
        $medical->name = $request->name;
        $medical->type = $request->type;
        $medical->save();

        if (!$medical) {
            return response()->json([
                'status' => false,
                'message' 	=> $medical
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('medical.index'),
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
            $medical = Medical::find($id);
            $medical->delete();
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
