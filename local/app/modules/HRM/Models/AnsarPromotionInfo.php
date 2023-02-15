<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class AnsarPromotionInfo extends Model
{
    protected $connection = 'hrm';
    protected $table = 'tbl_ansar_promotion_info';
    protected $guarded = [];

    
    public function personalinfo(){
        return $this->hasOne(PersonalInfo::class,'ansar_id');
    }
}
