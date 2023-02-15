<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_user_log';
    protected $guarded=['id'];
    function user(){
        return $this->belongsTo('App\models\User');
    }
}
