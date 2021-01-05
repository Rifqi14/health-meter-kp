<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ExaminationType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExaminationTypeController extends Controller
{
    public function read(Request $request)
    {
        $input = ['numeric'=>'Angka','string'=>'Text'];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $examination_id = $request->examination_id;

        //Count Data
        $query = ExaminationType::where('examination_id',$examination_id);
        $query->withTrashed();
        $recordsTotal = $query->count();

        //Select Pagination
        $query = ExaminationType::where('examination_id',$examination_id);
        $query->withTrashed();
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $examination_types = $query->get();

        $data = [];
        foreach($examination_types as $examination_type){
            $examination_type->no = ++$start;
            $examination_type->input = @$input[$examination_type->input];
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name'          => 'required',
            'input'         => 'required',
            'status'        => 'required'
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
            'input'             => $request->input,
            'status'            => $request->status,
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
            'message' 	=> 'Success Create Data'
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
        $examination = ExaminationType::withTrashed()->find($id);
        return response()->json([
            'status'    => true,
            'data'      => $examination
        ], 200);
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
            'name'          => 'required',
            'input'         => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $examinationtype = ExaminationType::withTrashed()->find($id);
        $examinationtype->name  = $request->name;
        $examinationtype->input  = $request->input;
        $examinationtype->status = $request->status;
        $examinationtype->updated_by = Auth::id();
        $examinationtype->save();
        if ($examinationtype->status == 0) {
            $examinationtype->delete();
        } else {
            $examinationtype->restore();
        }
        if (!$examinationtype) {
            return response()->json([
                'status' => false,
                'message' 	=> $examinationtype
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'message' 	=> 'Success Update Data'
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
            $examinationtype->status = 0;
            $examinationtype->save();
            $examinationtype->delete();
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