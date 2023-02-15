<?php

namespace App\modules\HRM\Models;

use App\models\User;
use Illuminate\Database\Eloquent\Model;

class NidRequestLog extends Model
{
    protected $connection = 'hrm';
    protected $table='tbl_nid_request_log';

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'action_by','id');
    }
	
	
}
