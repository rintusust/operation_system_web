<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class EmbodimentLogModel extends Model
{
    protected $connection = 'hrm';
    protected $table = "tbl_embodiment_log";
    protected $guarded = [];

    function ansar()
    {
        return $this->belongsTo(PersonalInfo::class, 'ansar_id', 'ansar_id');
    }

    function restData()
    {
        return $this->hasOne(RestInfoModel::class, "old_embodiment_id","old_embodiment_id");
    }

    function restLogData()
    {
        return $this->hasOne(RestInfoLogModel::class, "old_embodiment_id",'old_embodiment_id');
    }

    function kpi()
    {
        return $this->belongsTo(KpiGeneralModel::class, 'kpi_id');
    }

    public function disembodimentReason()
    {
        return $this->hasOne(DisembodimentReason::class, "id","disembodiment_reason_id");
    }


}
