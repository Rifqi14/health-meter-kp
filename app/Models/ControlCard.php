<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ControlCard extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function updatedby()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function authorizedofficial()
    {
        return $this->belongsTo(AuthorizedOfficial::class, 'authorized_official_id');
    }
    public function checkup_examinationevaluation()
    {
        return $this->belongsTo(ExaminationEvaluation::class, 'checkup_examination_evaluation_id');
    }
    public function checkup_examinationevaluationlevel()
    {
        return $this->belongsTo(ExaminationEvaluationLevel::class, 'checkup_examination_evaluation_level_id');
    }
    public function checkupresult()
    {
        return $this->belongsTo(CheckupResult::class, 'checkup_result_id');
    }
    public function examinationevaluation()
    {
        return $this->belongsTo(ExaminationEvaluation::class, 'examination_evaluation_id');
    }
    public function examinationevaluationlevel()
    {
        return $this->belongsTo(ExaminationEvaluationLevel::class, 'examination_evaluation_level_id');
    }
    public function guarantor()
    {
        return $this->belongsTo(Guarantor::class, 'guarantor_id');
    }
    public function nid()
    {
        return $this->belongsTo(Workforce::class, 'nid');
    }
    public function nidmaker()
    {
        return $this->belongsTo(User::class, 'nid_maker');
    }
    public function sitemaker()
    {
        return $this->belongsTo(Site::class, 'site_maker_id');
    }
}
