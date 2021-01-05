<?php

namespace App\Http\Controllers\Admin;

use App\Models\Template;
use App\Models\MedicalAction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class MedicalActionController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'medicalaction'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.medicalaction.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = MedicalAction::with('examination')->select('medical_actions.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = MedicalAction::with('examination')->select('medical_actions.*','templates.name as template_name');
        $query->leftJoin('templates','templates.id','=','medical_actions.template_id');
        $query->whereRaw("upper(medical_actions.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $medicalactions = $query->get();

        $data = [];
        foreach($medicalactions as $medicalaction){
            $medicalaction->no = ++$start;
            $medicalaction->examination_type = $medicalaction->examination_type_id ? $medicalaction->examination->name : '-';
			$data[] = $medicalaction;
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
        $templates = Template::all();
        return view('admin.medicalaction.create',compact('templates'));
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
            'code'          => 'required|unique:medical_actions',
            'name' 	        => 'required|unique:medical_actions',
            'template_id' 	=> 'required',
            'description'   => 'required',
            'examination'   => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $medicalaction = MedicalAction::create([
            'code'                  => $request->code,
            'name'                  => $request->name,
            'template_id'           => $request->template_id,
            'description'           => $request->description,
            'examination_type_id'   => $request->examination
        ]);
        if (!$medicalaction) {
            return response()->json([
                'status' => false,
                'message' 	=> $medicalaction
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('medicalaction.index'),
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
        $medicalaction = MedicalAction::find($id);
        if($medicalaction){
            return view('admin.medicalaction.show',compact('medicalaction'));
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
        $templates = Template::all();
        $medicalaction = MedicalAction::find($id);
        if($medicalaction){
            return view('admin.medicalaction.edit',compact('medicalaction','templates'));
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
            'code'  	    => 'required|unique:medical_actions,code,'.$id,
            'name' 	        => 'required|unique:medical_actions,name,'.$id,
            'template_id'   => 'required',
            'description'   => 'required',
            'examination'   => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $medicalaction = MedicalAction::find($id);
        $code = $medicalaction->code;
        $medicalaction->code = $request->code;
        $medicalaction->name = $request->name;
        $medicalaction->template_id = $request->template_id;
        $medicalaction->description = $request->description;
        $medicalaction->examination_type_id = $request->examination;
        $medicalaction->save();
        if (!$medicalaction) {
            return response()->json([
                'status' => false,
                'message' 	=> $medicalaction
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('medicalaction.index'),
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
            $medicalaction = MedicalAction::find($id);
            $medicalaction->delete();
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