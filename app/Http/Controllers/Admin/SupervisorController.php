<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Employee;
use App\Models\Report;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SupervisorController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'supervisor'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }
    public function index()
    {
        $nid = Auth::guard('admin')->user()->username;
        $employee = Employee::where('nid',$nid)->get()->first();
        $report = Report::where('report_date',date('Y-m-d'))->where('supervisor_id',$employee->id)->get()->count();
        return view('admin.supervisor.index',compact('report'));
    }
    public function read(Request $request)
    {
        $nid = Auth::guard('admin')->user()->username;
        $employee = Employee::where('nid',$nid)->get()->first();
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('reports');
        $query->select('reports.*');
        $query->where('supervisor_id',$employee->id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('reports');
        $query->select('reports.*','sub_categories.name as category_name','sub_categories.type as category_type');
        $query->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id');
        $query->where('supervisor_id',$employee->id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $reports = $query->get();

        $data = [];
        foreach($reports as $report){
            $report->no = ++$start;
            if($report->category_type == 'yesno'){
                $report->value = $report->value?'Yes':'No';
            }
			$data[] = $report;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function create()
    {
        $nid = Auth::guard('admin')->user()->username;
        $employee = Employee::where('nid',$nid)->get()->first();
        $report = Report::where('report_date',date('Y-m-d'))->where('supervisor_id',$employee->id)->get()->count();
        //if(!$report){
            $categories = Category::where('input','supervisor')->get();
            $subcategories = SubCategory::select('sub_categories.*')
                            ->leftJoin('categories','categories.id','=','sub_categories.category_id')
                            ->where('input','supervisor')->get();
            return view('admin.supervisor.create',compact('categories','subcategories'));
        //}
        //return redirect()->back();
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' 	    => 'required',
            'report_date' => 'required'
        ]);
        $nid = Auth::guard('admin')->user()->username;
        $employee = Employee::select('employees.*','titles.department_id')
        ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
        ->leftJoin('titles','titles.id','=','employee_movements.title_id')
        ->whereNull('finish') 
        ->where('nid',$nid)->get()->first();
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        if($request->report_date > date('Y-m-d')){
            return response()->json([
        		'status' 	=> false,
        		'message' 	=> 'Tidak Boleh Melbihi Tanggal Sekarang'
        	], 400);
        }
        $report = Report::where('report_date',$request->report_date)->where('supervisor_id',$employee->id)->first();
        if($report){
            return response()->json([
        		'status' 	=> false,
        		'message' 	=> 'Laporan Sudah Dibuat'
        	], 400);
        }
        foreach($request->id as $id){
            $subcategory = SubCategory::find($id);
            $report_date = $request->report_date;
            $report = Report::create([
                'sub_category_id' 	=> $id,
                'employee_id' 	=> null,
                'supervisor_id' 	=> $employee->id,
                'value' 	=> $request->{"subcategory_".$id},
                'report_date' 	=> $request->report_date
            ]);
            dispatch(new \App\Jobs\CalculateCategory($subcategory->category_id,$employee->department_id,$report_date));
        }
        
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('supervisor.index'),
        ], 200);
    }
}
