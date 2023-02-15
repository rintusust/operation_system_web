<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class CorrectionLog extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_correction_log';
    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'ansar_id');
    }
}
