<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ExaminationType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class ExaminationTypeController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/examinationtype'));
        $this->middleware('accessmenu', ['excep' => 'select']);
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
        $query = ExaminationType::with('user','examination')->whereRaw("upper(name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = ExaminationType::with('user','examination')->whereRaw("upper(name) like '%$name%'");
        if ($arsip) {
            $query->onlyTrashed();
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $examination_types = $query->get();

        $data = [];
        foreach($examination_types as $examination_type){
            $examination_type->no = ++$start;
			$data[] = $examination_type;
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

        //Count Data
        $query = ExaminationType::Where('status', 1)->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = ExaminationType::Where('status', 1)->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $medicines = $query->get();

        $data = [];
        foreach ($medicines as $examination) {
            $examination->no = ++$start;
            $data[] = $examination;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.examinationtype.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.examinationtype.create');
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
            'examination_id'    => 'required',
            'name'              => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $examination = ExaminationType::create([
            'examination_id'    => $request->examination_id,
            'name'              => $request->name,
            'updated_by'        => Auth::id()
        ]);
        if (!$examination) {
            return response()->json([
                'status' => false,
                'message' 	=> $examination
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'results' 	=> route('examinationtype.index'),
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
        $examinationtype = ExaminationType::find($id);
        if ($examinationtype) {
            return view('admin.examinationtype.detail', compact('examinationtype'));
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
        $examinationtype = ExaminationType::find($id);
        if ($examinationtype) {
            return view('admin.examinationtype.edit', compact('examinationtype'));
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
            'examination_id'    => 'required',
            'name'              => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $examinationtype = ExaminationType::withTrashed()->find($id);
        $examinationtype->examination_id  = $request->examination_id;
        $examinationtype->name  = $request->name;
        $examinationtype->updated_by = Auth::id();
        $examinationtype->save();
        if (!$examinationtype) {
            return response()->json([
                'status' => false,
                'message' 	=> $examinationtype
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'results' 	=> route('examinationtype.index'),
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
            $examinationtype = ExaminationType::find($id);
            $examinationtype->delete();
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
            $examinationtype = ExaminationType::onlyTrashed()->find($id);
            $examinationtype->restore();
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
            $examinationtype = ExaminationType::onlyTrashed()->find($id);
            $examinationtype->forceDelete();
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