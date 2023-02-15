<?php

namespace App\modules\operation\Models;

use App\models\User;
use App\modules\operation\Models\Blood;
use App\modules\operation\Models\District;
use App\modules\operation\Models\Division;
use App\modules\operation\Models\Thana;
use App\modules\HRM\Models\Unions;
use App\modules\operation\Models\VdpDesignation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VDPAnsarInfo extends Model
{
    //
    protected $table = "tbl_vdp_ansar_info";
    protected $connection = "operation";
    protected $guarded = ['id'];


    public function status(){
        return $this->hasOne(VDPAnsarStatusInfo::class,'vdp_ansar_info_id');
    }

    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function unit(){
        return $this->belongsTo(District::class,'unit_id');
    }
    public function thana(){
        return $this->belongsTo(Thana::class,'thana_id');
    }

    public function bloodGroup(){
        return $this->belongsTo(Blood::class,'blood_group_id');
    }

    public function designationData(){
        return $this->belongsTo(VdpDesignation::class,'designation');
    }
    public function setDateOfBirthAttribute($value){
        $this->attributes['date_of_birth'] = $value?Carbon::parse($value)->format('Y-m-d'):'';
    }
    public function getDateOfBirthAttribute($value){
        if($value){
            return Carbon::parse($value)->format('d-M-Y');
        }
        return '';
    }
    public function log(){
        return $this->hasMany(UserActionLog::class,'action_id','id');
    }
    public function scopeUserQuery($query,$id){
        if($id){
            $user = User::find($id);
            if($user->usertype->type_name=="Dataentry"){
                return $query->whereHas('log',function ($q) use ($user){
                    $q->where('action_user_id',$user->id);
                    $q->where('action_type','Entry');
                });
            }
        }
        return $query;
    }
    public function scopeSearchQuery($query,$search){
        if($search){
            return $query->where(function ($q) use ($search){
                $q->where('ansar_name_bng','like',"%$search%");
                $q->orWhere('ansar_name_eng','like',"%$search%");
                $q->orWhere('father_name_bng','like',"%$search%");
                $q->orWhere('designation','like',"%$search%");
                $q->orWhere('mobile_no_self','like',"%$search%");
                $q->orWhere('geo_id','=',"$search");
                if(strtotime($search)){
                    $d = Carbon::parse($search)->format('Y-m-d');
                    $q->orWhere('date_of_birth',$d);
                }
            });
        }
        return $query;
    }

    public function age(){

        $now = Carbon::now();
        $age = false;

        if($this->date_of_birth&&strtotime($this->date_of_birth)){
            try {
                $age = Carbon::parse($this->date_of_birth)->diffInYears($now, true);
            }catch (\Exception $e){
                $age = false;
            }
        }
        return $age===false?"Invalid date of birth":$age;

    }
}
