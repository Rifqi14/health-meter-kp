<?php

namespace App\Http\Controllers\Admin;

use App\Models\Inpatient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class InpatientController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'inpatient'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.inpatient.index');
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];

        //Count Data
        $query = Inpatient::with('user')->select('inpatients.*');
        $query->withTrashed();
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Inpatient::with('user')->select('inpatients.*');
        $query->withTrashed();
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $inpatients = $query->get();

        $data = [];
        foreach($inpatients as $inpatient){
            $inpatient->no = ++$start;
            $inpatient->price = number_format($inpatient->price,0,',','.');
			$data[] = $inpatient;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = Inpatient::select('inpatients.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Inpatient::select('inpatients.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $inpatients = $query->get();

        $data = [];
        foreach($inpatients as $inpatient){
            $inpatient->no = ++$start;
			$data[] = $inpatient;
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
    public function create()
    {
        return view('admin.inpatient.create');
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
            'price'     => 'required|numeric'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $inpatient = Inpatient::create([
            'name' 	    => $request->name,
            'price' 	=> $request->price,
            'note'      => $request->note,
            'updated_by'=> Auth::id()
        ]);

        if (!$inpatient) {
            return response()->json([
                'status' => false,
                'message' 	=> $inpatient
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('inpatient.index'),
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
        $inpatient = Inpatient::find($id);
        if($inpatient){
            return view('admin.inpatient.detail',compact('inpatient'));
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
        $inpatient = Inpatient::find($id);
        if($inpatient){
            return view('admin.inpatient.edit',compact('inpatient'));
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
            'price'     => 'required|numeric'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $inpatient = Inpatient::find($id);
        $inpatient->name        = $request->name;
        $inpatient->price       = $request->price;
        $inpatient->note        = $request->note;
        $inpatient->updated_by  = Auth::id();
        $inpatient->save();

        if (!$inpatient) {
            return response()->json([
                'status' => false,
                'message' 	=> $inpatient
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('inpatient.index'),
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
            $inpatient = Inpatient::find($id);
            $inpatient->delete();
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
            $inpatient = Inpatient::onlyTrashed()->find($id);
            $inpatient->restore();
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
            $inpatient = Inpatient::onlyTrashed()->find($id);
            $inpatient->forceDelete();
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