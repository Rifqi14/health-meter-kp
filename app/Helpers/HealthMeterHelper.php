<?php
if (!function_exists('healthMeter')) {
   
    function healthMeter($value)
    {
        $color = '#000000';
        $healthmeters = App\Models\HealthMeter::all();
        foreach ($healthmeters as $healthmeter) {
            if($healthmeter->min <= $value && $healthmeter->max >= $value){
                $color = $healthmeter->color;
            }
        }
        return $color;
    }
}

if (!function_exists('healthRecomendation')) {
   
    function healthRecomendation($value)
    {
        $recomendation = '';
        $healthmeters = App\Models\HealthMeter::all();
        foreach ($healthmeters as $healthmeter) {
            if($healthmeter->min <= $value && $healthmeter->max >= $value){
                $recomendation = $healthmeter->recomendation;
            }
        }
        return $recomendation;
    }
}