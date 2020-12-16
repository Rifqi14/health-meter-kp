<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Report;
use App\Models\CategoryReport;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class CalculateCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $category_id,$department_id,$report_date;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($category_id,$department_id,$report_date)
    {
        $this->category_id   = $category_id;
        $this->department_id = $department_id;
        $this->report_date = $report_date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $employees = Employee::select('employees.*')
                            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                            ->whereNull('finish') 
                            ->where('titles.department_id',$this->department_id)
                            ->get()->count();
        $category = Category::find($this->category_id);
        if($category->type == 'summary'){
            $value = DB::table(DB::raw('(select reports.id,
            reports.sub_category_id,
            case when employee_id is null then supervisor_id else employee_id end as employee_id,
            reports.value,
            reports.report_date,
            reports.created_at,
            reports.updated_at
             from reports) as reports'))
            ->select('reports.*')
            ->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id')
            ->leftJoin('categories','categories.id','=','sub_categories.category_id')
            ->leftJoin('employees','employees.id','=','reports.employee_id')
            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
            ->whereNull('finish') 
            ->where('titles.department_id',$this->department_id)
            ->where('categories.id',$category->id)
            ->where('reports.report_date',$this->report_date)
            ->get()->sum('value');
        }
        
        if($category->type == 'filled'){
            $value = DB::table(DB::raw('(select reports.id,
            reports.sub_category_id,
            case when employee_id is null then supervisor_id else employee_id end as employee_id,
            reports.value,
            reports.report_date,
            reports.created_at,
            reports.updated_at
             from reports) as reports'))
            ->select('reports.*')
            ->leftJoin('sub_categories','sub_categories.id','=','reports.sub_category_id')
            ->leftJoin('categories','categories.id','=','sub_categories.category_id')
            ->leftJoin('employees','employees.id','=','reports.employee_id')
            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
            ->whereNull('finish') 
            ->where('titles.department_id',$this->department_id)
            ->where('categories.id',$category->id)
            ->where('reports.report_date',$this->report_date)
            ->get()->count();
        }
        $subcategory = SubCategory::where('category_id',$category->id)->count();
        if($category->parameter == 'employee'){
            $categoryreport = CategoryReport::where('report_date',$this->report_date)
                                            ->where('department_id',$this->department_id)
                                            ->where('category_id',$this->category_id)
                                            ->get()->first();
            if($categoryreport){
                $categoryreport->value = $value/$employees;
                $categoryreport->save();
                dispatch(new \App\Jobs\CalculateFormula($this->department_id,$this->report_date));
            }
            else{
                $categoryreport = CategoryReport::create([
                    'category_id'   => $this->category_id,
                    'department_id' => $this->department_id,
                    'report_date'   => $this->report_date,
                    'value'         => $value/$employees
                ]);
                dispatch(new \App\Jobs\CalculateFormula($this->department_id,$this->report_date));
            } 
        }
        if($category->parameter == 'subcategory'){
            $categoryreport = CategoryReport::where('report_date',$this->report_date)
                                            ->where('department_id',$this->department_id)
                                            ->where('category_id',$this->category_id)
                                            ->get()->first();
            if($categoryreport){
                $categoryreport->value = $value/$subcategory;
                $categoryreport->save();
                dispatch(new \App\Jobs\CalculateFormula($this->department_id,$this->report_date));
            }
            else{
                $categoryreport = CategoryReport::create([
                    'category_id'   => $this->category_id,
                    'department_id' => $this->department_id,
                    'report_date'   => $this->report_date,
                    'value'         => $value/$subcategory
                ]);
                dispatch(new \App\Jobs\CalculateFormula($this->department_id,$this->report_date));
            } 
        }
    }
}
