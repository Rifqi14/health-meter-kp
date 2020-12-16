<?php

namespace App\Jobs;

use App\Models\Formula;
use App\Models\FormulaDetail;
use App\Models\FormulaReport;
use App\Models\CategoryReport;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CalculateFormula implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $department_id,$report_date;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($department_id,$report_date)
    {
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
        $formulas = Formula::all();
        $categoryreports = CategoryReport::select('category_reports.*')
                                        ->where('department_id',$this->department_id)
                                        ->where('report_date',$this->report_date)
                                        ->get();
        foreach($formulas as $key => $formula){
            if($formula->operation == 'add'){
                $value = 0;
                $formuladetails = FormulaDetail::where('formula_id',$formula->id)->get();
                foreach($formuladetails as $formuladetail){
                    if($formuladetail->pick == 'category'){
                        foreach($categoryreports as $category){
                            if($formuladetail->category_id == $category->category_id){
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
                            $value = $value + ($this->formula($categoryreports,$formuladetail->reference_id,0) * $formuladetail->value/100);
                        }
                        if($formuladetail->operation == 'divide'){
                            $value =  $value + ($this->formula($categoryreports,$formuladetail->reference_id,0) / $formuladetail->value);
                        }

                        if($formuladetail->operation == 'origin'){
                            $value =  $value + ($this->formula($categoryreports,$formuladetail->reference_id,0));
                        }
                    }
                }
            }
            
            if($formula->operation == 'multiply'){
                $value = 1;
                $formuladetails = FormulaDetail::where('formula_id',$formula->id)->get();
                foreach($formuladetails as $formuladetail){
                    if($formuladetail->pick == 'category'){
                        foreach($categoryreports as $category){
                            if($formuladetail->category_id == $category->category_id){
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
                            $value = $value * ($this->formula($categoryreports,$formuladetail->reference_id,0) * $formuladetail->value/100);
                        }
                        if($formuladetail->operation == 'divide'){
                            $value =  $value * ($this->formula($categoryreports,$formuladetail->reference_id,0) / $formuladetail->value);
                        }

                        if($formuladetail->operation == 'origin'){
                            $value =  $value * ($this->formula($categoryreports,$formuladetail->reference_id,0));
                        }
                    }
                }
            }
            if($formula->result == 'percentage'){
                $value = $value * 100;
            }
            $formulareport = FormulaReport::select('formula_reports.id')
                                            ->where('formula_id',$formula->id)
                                            ->where('report_date',$this->report_date)
                                            ->where('department_id',$this->department_id)
                                            ->get()->first();
            if($formulareport){
                $formulareport->value = $value;
                $formulareport->save();
            }
            else{
                $formulareport = FormulaReport::create([
                    'formula_id'   => $formula->id,
                    'department_id' => $this->department_id,
                    'report_date'   => $this->report_date,
                    'value'         => $value
                ]);
            } 
        }
    }

    function formula($categoryreports,$formula_id,$value){
        $formula = Formula::find($formula_id);
        if($formula->operation == 'add'){
            $formuladetails = FormulaDetail::where('formula_id',$formula->id)->get();
            foreach($formuladetails as $formuladetail){
                if($formuladetail->pick == 'category'){
                    foreach($categoryreports as $category){
                        if($formuladetail->category_id == $category->category_id){
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
                    foreach($categoryreports as $category){
                        if($formuladetail->category_id == $category->category_id){
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
}
