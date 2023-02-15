<?php

namespace App\modules\HRM\Models;

use Illuminate\Database\Eloquent\Model;

class  UserLoginSuccessLog extends Model
{
    //
    protected $table = 'tbl_user_login_success_log';
    protected $connection = 'hrm';
    protected $guarded = [];
}
