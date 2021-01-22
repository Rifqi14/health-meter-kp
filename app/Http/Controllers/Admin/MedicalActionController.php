<?php

namespace App\Http\Controllers\Admin;

use App\Models\Template;
use App\Models\MedicalAction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        $category = $request->category;
        //Count Data
        $query = MedicalAction::with(['examination','user']);
        $query->whereRaw("upper(description) like '%$name%'");
        if ($category) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = MedicalAction::with(['examination','user']);
        $query->whereRaw("upper(description) like '%$name%'");
        if ($category) {
            $query->onlyTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $medicalactions = $query->get();

        $data = [];
        foreach($medicalactions as $medicalaction){
            $medicalaction->no = ++$start;
			$data[] = $medicalaction;
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

        //Count Data
        $query = DB::table('medical_actions');
        $query->select('medical_actions.*');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('medical_actions');
        $query->select('medical_actions.*');
        // $query->orderBy('complaint', 'asc');
        $query->offset($start);
        $query->limit($length);
        $medicalactions = $query->get();

        $data = [];
        foreach ($medicalactions as $medicalaction) {
            $medicalaction->no = ++$start;
            $data[] = $medicalaction;
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
            'examination_type_id'   => 'required',
            'description'           => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $medicalaction = MedicalAction::create([
            'examination_type_id'   => $request->examination_type_id,
            'description'           => $request->description,
            'updated_by'=> Auth::id()
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
            return view('admin.medicalaction.detail',compact('medicalaction'));
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
        $medicalaction = MedicalAction::find($id);
        if($medicalaction){
            return view('admin.medicalaction.edit',compact('medicalaction'));
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
            'examination_type_id'   => 'required',
            'description'           => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $medicalaction = MedicalAction::find($id);
        $medicalaction->examination_type_id = $request->examination_type_id;
        $medicalaction->description = $request->description;
        $medicalaction->updated_by = Auth::id();
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
    public function restore($id)
    {
        try {
            $medicalaction = MedicalAction::onlyTrashed()->find($id);
            $medicalaction->restore();
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
            $medicalaction = MedicalAction::onlyTrashed()->find($id);
            $medicalaction->forceDelete();
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