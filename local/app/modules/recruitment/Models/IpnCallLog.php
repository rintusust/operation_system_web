<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class IpnCallLog extends Model
{
    //
    protected $table = 'tbl_ipn_call_log';
    protected $guarded = ['id'];
    protected $connection = 'recruitment';

}

