<?php

namespace App\modules\recruitment\Models;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use Illuminate\Database\Eloquent\Model;

class JobApplicantQuota extends Model
{
    //
    protected $table = 'job_applicant_quota';
    protected $guarded = ['id'];
    protected $connection = 'recruitment';

    public function unit(){
        return $this->belongsTo(District::class,'district_id');
    }
    public function range(){
        return $this->belongsTo(Division::class,'range_id');
    }
}
