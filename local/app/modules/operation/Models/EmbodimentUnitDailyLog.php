<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class EmbodimentUnitDailyLog extends Model
{
    protected $connection = 'hrm';
    protected $table = 'db_amis.tbl_embodiment_daily_unit_count_log';
    protected $guarded = [];

    
}
