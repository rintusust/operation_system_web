<?php

namespace App\modules\SD\Models;

use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\PersonalInfo;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    protected $connection = 'sd';
    protected $table = 'ansar_sd.tbl_attendance';
    protected $guarded = ['id'];

    public function kpi(){
        return $this->belongsTo(KpiGeneralModel::class,'kpi_id');
    }
    public function ansar(){
        return $this->belongsTo(PersonalInfo::class,'ansar_id','ansar_id');
    }
}
