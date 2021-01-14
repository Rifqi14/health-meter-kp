<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoleDashboard;
use App\Models\Dashboard;
use App\Models\Report;
use App\Models\Category;
use App\Models\MedicalRecord;
use App\Models\SubCategory;
use App\Models\Employee;
use App\Models\Formula;
use App\Models\FormulaDetail;
use App\Models\FormulaReport;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'dashboard'));
    }
    public function index()
    {
        $role = Auth::guard('admin')->user()->roles()->first();
        $roledashboards = RoleDashboard::where('role_id',$role->id)->where('role_access',1)->get();
        $dashboards = [];
        foreach($roledashboards as $roledashboard){
            array_push($dashboards,$roledashboard->dashboard->dashboard_name);
        }
        $categories     = Category::orderBy('id','asc')->get();
        $subcategories  = SubCategory::orderBy('id','asc')->get();
        //Bidang
        $nid            = Auth::guard()->user()->username;
        $employee       = Employee::select('employees.*','titles.department_id')
                                    ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                                    ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                                    ->whereNull('finish') 
                                    ->where('nid',$nid)
                                    ->get()
                                    ->first();
        $employees = [];
        $reports = [];
        $formulas = [];
        $department_id = 0;
        if($employee){
            $employees  = Employee::select('employees.*')
                                    ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                                    ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                                    ->whereNull('finish') 
                                    ->where('titles.department_id',$employee->department_id)
                                    ->get();
            
            $reports = DB::table(DB::raw('(select reports.id,
                                reports.sub_category_id,
                                case when employee_id is null then supervisor_id else employee_id end as employee_id,
                                reports.value,
                                reports.report_date,
                                reports.created_at,
                                reports.updated_at
                                from reports) as reports'))
                            ->select('reports.*','sub_categories.type','categories.id as category_id','sub_categories.name as category_name')
                            ->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id')    
                            ->leftJoin('categories','categories.id','=','sub_categories.category_id')
                            ->leftJoin('employees','employees.id','=','reports.employee_id')
                            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                            ->whereNull('finish') 
                            ->where('titles.department_id',$employee->department_id)
                            ->where('report_date',date('Y-m-d'))
                            ->get();
            
            $formulas = Formula::select('formulas.*','formula_reports.value')
                                ->leftJoin('formula_reports','formulas.id','=','formula_reports.formula_id')
                                ->where('formula_reports.department_id',$employee->department_id)
                                ->where('formula_reports.report_date',date('Y-m-d'))
                                ->get();
            $department_id = $employee->department_id;                
        }
        //All
        $departmentall = Department::orderBy('name','asc')->get();
        $departments = [];


        $formulaall = Formula::orderBy('id','asc')->get();
        foreach($departmentall as $department){
            $count = Employee::select('employees.*','titles.department_id')
                                    ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                                    ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                                    ->whereNull('finish') 
                                    ->where('titles.department_id',$department->id)
                                    ->get()->count();
            if($count > 0){
                array_push($departments,$department);
            }
        }
        $unwells = DB::table(DB::raw('(select reports.id,
                    reports.sub_category_id,
                    case when employee_id is null then supervisor_id else employee_id end as employee_id,
                    reports.value,
                    reports.report_date,
                    reports.created_at,
                    reports.updated_at
                    from reports) as reports'))
                    ->select('reports.id','employees.name','reports.value','employees.nid','titles.name as title_name','departments.name as department_name','reports.employee_id','medical_records.id as medical_record_id')
                    ->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id')    
                    ->leftJoin('categories','categories.id','=','sub_categories.category_id')
                    ->leftJoin('employees','employees.id','=','reports.employee_id')
                    ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                    ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                    ->leftJoin('departments','departments.id','=','titles.department_id')
                    ->leftJoin('medical_records','medical_records.report_id','=','reports.id')
                    ->whereNull('finish') 
                    ->where('report_date',date('Y-m-d'))
                    ->where('sub_categories.name','Apakah Sehat?')
                    ->orderBy('reports.id','asc')
                    ->get();  
        $temperatures = DB::table(DB::raw('(select reports.id,
                    reports.sub_category_id,
                    case when employee_id is null then supervisor_id else employee_id end as employee_id,
                    reports.value,
                    reports.report_date,
                    reports.created_at,
                    reports.updated_at
                    from reports) as reports'))
                    ->select('employees.id','employees.name','reports.value')
                    ->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id')    
                    ->leftJoin('categories','categories.id','=','sub_categories.category_id')
                    ->leftJoin('employees','employees.id','=','reports.employee_id')
                    ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                    ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                    ->whereNull('finish') 
                    ->where('report_date',date('Y-m-d'))
                    ->where('sub_categories.name','Suhu Badan')
                    ->orderBy('employees.id','asc')
                    ->get(); 
        $reportall =  FormulaReport::select('formula_reports.*')
        ->where('formula_reports.report_date',date('Y-m-d'))
        ->get();
        
        return view('admin.dashboard',compact('categories','subcategories','employees','reports','formulas','departments','reportall','formulaall','unwells','temperatures','dashboards','department_id'));
    }
    
    function formula($formula_id,$value){
        $categories = Category::all();
        $employees  = Employee::all()->count();
        foreach($categories as $key => $category){
            $subcategory = SubCategory::where('category_id',$category->id)->count();
            if($category->type == 'summary'){
                $cvalue = Report::select('reports.*')
                ->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id')
                ->leftJoin('categories','categories.id','=','sub_categories.category_id')
                ->where('categories.id',$category->id)
                ->get()->sum('value');
            }
            
            if($category->type == 'filled'){
                $cvalue = Report::select('reports.*')
                ->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id')
                ->leftJoin('categories','categories.id','=','sub_categories.category_id')
                ->where('categories.id',$category->id)
                ->get()->count();
            }
            if($category->parameter == 'employee'){
                $categories[$key]->value = $cvalue/$employees;
            }
            if($category->parameter == 'subcategory'){
                $categories[$key]->value = $cvalue/$subcategory;
            }
            
        }
        $formula = Formula::find($formula_id);
        if($formula->operation == 'add'){
            $formuladetails = FormulaDetail::where('formula_id',$formula->id)->get();
            foreach($formuladetails as $formuladetail){
                if($formuladetail->pick == 'category'){
                    foreach($categories as $category){
                        if($formuladetail->category_id == $category->id){
                            if($formuladetail->operation == 'percentage'){
                                $value = $value + ($category->value * $formuladetail->value/100);
                            }
                            if($formuladetail->operation == 'divide'){
                                $value =  $value + ($category->value / $formuladetail->value);
                            }

                            if($formuladetail->operation == 'origin'){
                                $value =  $value + ($category->value);
                            }
                        }
                    }
                }
                if($formuladetail->pick == 'formula'){
                    if($formuladetail->operation == 'percentage'){
                        $value = $value + ($this->formula($formuladetail->reference_id,$value) * $formuladetail->value/100);
                    }
                    if($formuladetail->operation == 'divide'){
                        $value =  $value + ($this->formula($formuladetail->reference_id,$value) / $formuladetail->value);
                    }

                    if($formuladetail->operation == 'origin'){
                        $value =  $value + ($this->formula($formuladetail->reference_id,$value));
                    }
                    
                }
            }
        }
        
        if($formula->operation == 'multiply'){
            if($value == 0){
                $value = 1;
            }
            $formuladetails = FormulaDetail::where('formula_id',$formula->id)->get();
            foreach($formuladetails as $formuladetail){
                if($formuladetail->pick == 'category'){
                    foreach($categories as $category){
                        if($formuladetail->category_id == $category->id){
                            if($formuladetail->operation == 'percentage'){
                                $value = $value * ($category->value * $formuladetail->value/100);
                            }
                            if($formuladetail->operation == 'divide'){
                                $value =  $value * ($category->value / $formuladetail->value);
                            }

                            if($formuladetail->operation == 'origin'){
                                $value =  $value * ($category->value);
                            }
                        }
                    }
                }
                if($formuladetail->pick == 'formula'){
                    if($formuladetail->operation == 'percentage'){
                        $value = $value * ($this->formula($formuladetail->reference_id,$value) * $formuladetail->value/100);
                    }
                    if($formuladetail->operation == 'divide'){
                        $value =  $value * ($this->formula($formuladetail->reference_id,$value) / $formuladetail->value);
                    }

                    if($formuladetail->operation == 'origin'){
                        $value =  $value * ($this->formula($formuladetail->reference_id,$value));
                    }
                }
            }
        }
        return $value;
    }

    public function chart(Request $request)
    {
        $start = date('Y-m-d', strtotime(date('Y-m-d') . ' -6 day'));
        $finish = date('Y-m-d');
        $query = FormulaReport::select('formula_reports.report_date','formula_reports.value');
        $query->leftJoin('formulas','formulas.id','=','formula_reports.formula_id');
        $query->whereBetween('formula_reports.report_date',[$start,$finish]);
        $query->where('formulas.name','Peta Kesehatan');
        $query->where('formula_reports.department_id',$request->department_id);
        $query->orderBy('report_date','asc');
        $reports = $query->get();
        $series = [];
		$categories = [];
        foreach($reports as $report){
            $categories[] = $report->report_date;
			$series[] = intval($report->value);
        }
        return response()->json([
            'date' => date('d/m/Y', strtotime(date('Y-m-d') . ' -6 day')).' - '.date('d/m/Y'),
			'series' => $series,
			'categories' => $categories
        ], 200);
    }

    public function medicalrecord($id)
    {
        $medicalrecord = MedicalRecord::with('medicalaction','partner','employee')->find($id);
        if($medicalrecord){
            return view('admin.medicalrecord',compact('medicalrecord'));
        }
        else{
            abort(404);
        }
    }

    public function healthmeter($id)
    {
        $department = Department::find($id);
        if($department){
            $categories     = Category::orderBy('id','asc')->get();
            $subcategories  = SubCategory::orderBy('id','asc')->get();
            $employees  = Employee::select('employees.*')
                                    ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                                    ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                                    ->whereNull('finish') 
                                    ->where('titles.department_id',$id)
                                    ->get();
            $reports = DB::table(DB::raw('(select reports.id,
                                    reports.sub_category_id,
                                    case when employee_id is null then supervisor_id else employee_id end as employee_id,
                                    reports.value,
                                    reports.report_date,
                                    reports.created_at,
                                    reports.updated_at
                                    from reports) as reports'))
                                ->select('reports.*','sub_categories.type','categories.id as category_id','sub_categories.name as category_name')
                                ->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id')    
                                ->leftJoin('categories','categories.id','=','sub_categories.category_id')
                                ->leftJoin('employees','employees.id','=','reports.employee_id')
                                ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                                ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                                ->whereNull('finish') 
                                ->where('titles.department_id',$id)
                                ->where('report_date',date('Y-m-d'))
                                ->get();
                
            $formulas = Formula::select('formulas.*','formula_reports.value')
                                ->leftJoin('formula_reports','formulas.id','=','formula_reports.formula_id')
                                ->where('formula_reports.department_id',$id)
                                ->where('formula_reports.report_date',date('Y-m-d'))
                                ->get();
            $department_id = $id;
            return view('admin.healthmeter',compact('categories','subcategories','employees','reports','formulas','department_id'));
        }
        else{
            abort(404);
        }
    }
}