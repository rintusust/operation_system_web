<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLoginUser extends Model
{
    //
    protected $table = 'tbl_request_log_in_user';
    protected $connection = 'hrm';
    protected $guarded = [];
}
