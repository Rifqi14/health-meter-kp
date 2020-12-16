<?php

namespace App\Http\Controllers\Admin;

use App\Models\MedicalDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MedicalDetailController extends Controller
{
    public function read(Request $request)
    {
        $input = ['numeric'=>'Angka','string'=>'Text'];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $medical_id = $request->medical_id;

        //Count Data
        $query = DB::table('medical_details');
        $query->select('medical_details.*');
        $query->where('medical_id',$medical_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('medical_details');
        $query->select('medical_details.*');
        $query->where('medical_id',$medical_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $medicaldetails = $query->get();

        $data = [];
        foreach($medicaldetails as $medicaldetail){
            $medicaldetail->no = ++$start;
            $medicaldetail->input = @$input[$medicaldetail->input];
			$data[] = $medicaldetail;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
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
            'input'          => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $medicaldetail = MedicalDetail::create([
            'medical_id'   => $request->medical_id,
			'name'          => $request->name,
			'input'          => $request->input
        ]);
        if (!$medicaldetail) {
            return response()->json([
                'status' => false,
                'message' 	=> $medicaldetail
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
        $medicaldetail = MedicalDetail::find($id);
        return response()->json([
            'status' 	=> true,
            'data' => $medicaldetail
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

        $medicaldetail = MedicalDetail::find($id);
        $medicaldetail->name  = $request->name;
        $medicaldetail->input  = $request->input;
        $medicaldetail->save();
        if (!$medicaldetail) {
            return response()->json([
                'status' => false,
                'message' 	=> $medicaldetail
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
            $medicaldetail = MedicalDetail::find($id);
            $medicaldetail->delete();
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
