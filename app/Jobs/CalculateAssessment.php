<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CalculateAssessment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $assessment_question, $assessment_answer, $assessment_date;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($assessment_question, $assessment_answer, $assessment_date)
    {
        $this->assessment_question = $assessment_question;
        $this->assessment_answer = $assessment_answer;
        $this->assessment_date = $assessment_date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}