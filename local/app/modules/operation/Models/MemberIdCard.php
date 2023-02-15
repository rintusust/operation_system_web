<?php

namespace App\modules\operation\Models;

use Illuminate\Database\Eloquent\Model;

class MemberIdCard extends Model
{
    //
    protected $connection = 'operation';
    protected $table = 'tbl_member_id_card';
    protected $fillable = ['member_id','geo_id','issue_date','expire_date','status'];
}
