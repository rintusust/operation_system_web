<?php

namespace App\modules\operation\Models;


use Illuminate\Database\Eloquent\Model;

class UserActionLog extends Model
{
    //
    protected $table = "operation_user_action_log";
    protected $connection = "operation";
    protected $guarded = ['id'];

}
