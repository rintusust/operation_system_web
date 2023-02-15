<?php

namespace App\modules\recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class JobCircular extends Model
{
    //
    protected $table = 'job_circular';
    protected $connection = 'recruitment';
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function appliciant()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id');
    }
    public function examCenter(){
        return $this->hasMany(JobApplicantExamCenter::class,'job_circular_id');
    }

    public function appliciantMale()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('gender', 'Male');
    }

    public function appliciantFemale()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('gender', 'Female');
    }

    public function appliciantPaid()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')
            ->where(function ($q){
                $q->where('job_applicant.status', 'applied');
                $q->orWhere('job_applicant.status', 'selected');
                $q->orWhere('job_applicant.status', 'accepted');
                $q->orWhere('job_applicant.status', 'rejected');
            });
    }

    public function appliciantNotPaid()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('status', 'pending');
    }

    public function appliciantInitial()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('status', 'initial');
    }

    public function appliciantPaidNotApply()
    {
        return $this->hasMany(JobAppliciant::class, 'job_circular_id')->where('status', 'paid');
    }

    public function constraint()
    {
        return $this->hasOne(JobCircularConstraint::class, 'job_circular_id');
    }
    public function markDistribution()
    {
        return $this->hasOne(JobCircularMarkDistribution::class, 'job_circular_id');
    }
    public function trainingDate()
    {
        return $this->hasOne(JobApplicantTrainingDate::class, 'job_circular_id');
    }
    public function point()
    {
        return $this->hasOne(JobApplicantPoints::class, 'job_circular_id');
    }
    public function applicantQuotaRelation()
    {
        return $this->belongsToMany(CircularApplicantQuota::class,'job_circular_applicant_quota_relation','job_circular_id','job_circular_applicant_quota_id');
    }
}
