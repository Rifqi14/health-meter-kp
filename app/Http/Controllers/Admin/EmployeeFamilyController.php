<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeFamily;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeFamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function read(Request $request)
    {
        $type = [
            'couple'  => 'Pasangan',
            'child'  => 'Anak'
        ];
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;

        //Count Data
        $query = EmployeeFamily::select('employee_families.*');
        $query->where('employee_families.employee_id',$employee_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = EmployeeFamily::select('employee_families.*');
        $query->where('employee_families.employee_id',$employee_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employeefamilies = $query->get();
        // dd($formuladetails);

        $data = [];
        foreach($employeefamilies as $employeefamily){
            $employeefamily->no = ++$start;
            $employeefamily->type = $type[$employeefamily->type];
            $employeefamily->birth_date = Carbon::parse($employeefamily->birth_date)->format('d F Y');
			$data[] = $employeefamily;
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
        $employee_id = $request->employee_id;

        //Count Data
        $query = DB::table('employee_families');
        $query->select('employee_families.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->where("employee_id",$employee_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_families');
        $query->select('employee_families.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->where("employee_id",$employee_id);
        $query->offset($start);
        $query->limit($length);
        $employeefamilies = $query->get();

        $data = [];
        foreach($employeefamilies as $employeefamily){
            $employeefamily->no = ++$start;
			$data[] = $employeefamily;
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
            'type'          => 'required',
            'name'          => 'required',
            'birth_date'    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $employeefamily = EmployeeFamily::create([
            'employee_id'   => $request->employee_id,
			'type'          => $request->type,
			'name'          => $request->name,
			'birth_date'    => $request->birth_date
        ]);
        if (!$employeefamily) {
            return response()->json([
                'status' => false,
                'message' 	=> $employeefamily
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
        $employeefamily = EmployeeFamily::find($id);
        return response()->json([
            'status' 	=> true,
            'data' => $employeefamily
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
            'type'          => 'required',
            'birth_date'    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $employeefamily = EmployeeFamily::find($id);
        $employeefamily->type  = $request->type;
        $employeefamily->name  = $request->name;
        $employeefamily->birth_date  = $request->birth_date;
        $employeefamily->save();
        if (!$employeefamily) {
            return response()->json([
                'status' => false,
                'message' 	=> $employeefamily
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
            $employeefamily = EmployeeFamily::find($id);
            $employeefamily->delete();
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
