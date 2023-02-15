<?php
if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::group(['prefix' => 'recruitment', 'middleware' => ['recruitment'],'namespace' => '\App\modules\recruitment\Controllers'], function () {
    Route::get('/confirmPayment/{txID}', ['uses'=>'ApplicantScreeningController@webhook']);
});

Route::group(['prefix' => 'recruitment', 'middleware' => ['recruitment'],'namespace' => '\App\modules\recruitment\Controllers'], function () {
//Route::get('/confirmPayment-ipn/{txID}', ['uses'=>'ApplicantScreeningController@crn_webhook']);
//Route::get('/confirmPayment-ipn-v2', ['uses'=>'ApplicantScreeningController@crn_webhook_v2']);
//Route::get('/confirmPayment-ipn-v3', ['uses'=>'ApplicantScreeningController@crn_webhook_v3']);

});

Route::group(['prefix' => 'recruitment','middleware' => ['throttle:20,1'],'namespace' => '\App\modules\recruitment\Controllers'], function () {
    // Route::get('/confirmPayment-ipn/{txID}', ['uses'=>'ApplicantScreeningController@crn_webhook']);
    Route::any('/confirmPayment-ipn-v2', ['uses'=>'ApplicantScreeningController@crn_webhook_v2']);
    //Route::any('/confirmPayment-ipn-v3', ['uses'=>'ApplicantScreeningController@crn_webhook_v3']);//

    // Consolidate With Shurjopay Success Order List
    Route::any('/manualFire', ['uses'=>'ApplicantScreeningController@manualFire']);
});


Route::group(['prefix' => 'recruitment', 'middleware' => ['recruitment'], 'namespace' => '\App\modules\recruitment\Controllers'], function () {
    Route::get('/admitcard', 'ApplicantReportsController@admitcard');
    Route::group(['middleware' => ['auth', 'manageDatabase', 'checkUserType', 'permission']], function () {
        Route::get('/', ['as' => 'recruitment', 'uses' => 'RecruitmentController@index']);
        Route::get('/educations', ['as' => 'educations', 'uses' => 'RecruitmentController@educationList']);
        Route::get('/getRecruitmentSummary', ['uses' => 'RecruitmentController@getRecruitmentSummary']);

        //job category route
        Route::resource('category', 'JobCategoryController', ['except' => ['destroy', 'show']]);
        Route::resource('circular', 'JobCircularController', ['except' => ['destroy', 'show']]);
        Route::get('/circular/quota_list/{id}', ['as' => 'recruitment.circular.quota_list', 'uses' => 'JobCircularController@quotaList']);
        Route::get('circular/constraint/{id}', ['as' => 'recruitment.circular.constraint', 'uses' => 'JobCircularController@constraint']);
        Route::resource('marks', 'JobApplicantMarksController', ['except' => ['show']]);
        //applicant management
        Route::any('/applicant', ['as' => 'recruitment.applicant.index', 'uses' => 'ApplicantScreeningController@index']);
        Route::any('/applicant/sms', ['as' => 'recruitment.applicant.sms', 'uses' => 'SMSController@index']);
        Route::post('/applicant/sms', ['as' => 'recruitment.applicant.sms_send', 'uses' => 'SMSController@sendSMSToApplicant']);
        Route::post('/applicant/sms_upload_file', ['as' => 'recruitment.applicant.sms_send_file', 'uses' => 'SMSController@sendSMSToApplicantByUploadFile']);
        Route::get('/applicant/detail/view/{id}', ['as' => 'recruitment.applicant.detail_view', 'uses' => 'ApplicantScreeningController@applicantDetailView']);
        Route::get('/applicant/detail/{id}', ['as' => 'recruitment.applicant.detail', 'uses' => 'ApplicantScreeningController@getApplicantData']);
        Route::post('/applicant/update', ['as' => 'recruitment.applicant.update', 'uses' => 'ApplicantScreeningController@updateApplicantData']);
        Route::post('/applicant/confirm_selection_or_rejection', ['as' => 'recruitment.applicant.confirm_selection_or_rejection', 'uses' => 'ApplicantScreeningController@confirmSelectionOrRejection']);
        Route::post('/applicant/confirm_accepted', ['as' => 'recruitment.applicant.confirm_accepted', 'uses' => 'ApplicantScreeningController@confirmAccepted']);
        Route::post('/applicant/confirm_accepted_by_uploading_file', ['as' => 'recruitment.applicant.confirm_accepted_by_uploading_file', 'uses' => 'ApplicantScreeningController@acceptApplicantByFile']);
        Route::post('/applicant/confirm_accepted_bn_candidate', ['as' => 'recruitment.applicant.confirm_accepted_if_bn_candidate', 'uses' => 'ApplicantScreeningController@confirmAcceptedIfBncandidate']);
        Route::post('/applicant/confirm_accepted_special_candidate', ['as' => 'recruitment.applicant.confirm_accepted_special_candidate', 'uses' => 'ApplicantScreeningController@confirmAcceptedIfSpecialCandidate']);
        Route::get('/applicant/search', ['as' => 'recruitment.applicant.search', 'uses' => 'ApplicantScreeningController@searchApplicant']);
        Route::post('/applicant/search', ['as' => 'recruitment.applicant.search_result', 'uses' => 'ApplicantScreeningController@loadApplicants']);
        Route::any('/applicant/info', ['as' => 'recruitment.applicant.info', 'uses' => 'ApplicantScreeningController@loadApplicantsByStatus']);
        Route::any('/applicant/revert', ['as' => 'recruitment.applicant.revert', 'uses' => 'ApplicantScreeningController@loadApplicantsForRevert']);
        Route::post('/applicant/revert_status', ['as' => 'recruitment.applicant.revert_status', 'uses' => 'ApplicantScreeningController@revertApplicantStatus']);
        Route::post('/applicant/detail/selected_applicant', ['as' => 'recruitment.applicant.selected_applicant', 'uses' => 'ApplicantScreeningController@loadSelectedApplicant']);
        Route::get('/applicant/editfield', ['as' => 'recruitment.applicant.editfield', 'uses' => 'ApplicantScreeningController@applicantEditField']);
        Route::post('/applicant/editfield', ['as' => 'recruitment.applicant.editfieldstore', 'uses' => 'ApplicantScreeningController@saveApplicantEditField']);
        Route::get('/applicant/geteditfield', ['as' => 'recruitment.applicant.getfieldstore', 'uses' => 'ApplicantScreeningController@loadApplicantEditField']);
        Route::get('/applicant/final_list', ['as' => 'recruitment.applicant.final_list', 'uses' => 'ApplicantScreeningController@acceptedApplicantView']);
        Route::post('/applicant/final_list/load', ['as' => 'recruitment.applicant.final_list_load', 'uses' => 'ApplicantScreeningController@loadApplicantByQuota']);

        Route::get('applicant_list', ['as' => 'recruitment.applicant_list', 'uses' => 'ApplicantScreeningController@getApplicantList']);
        Route::get('/applicant/list/{type?}', ['as' => 'recruitment.applicant.list', 'uses' => 'ApplicantScreeningController@applicantListSupport']);
        Route::get('/applicants/list/{circular_id}/{type?}', ['as' => 'recruitment.applicants.list', 'uses' => 'ApplicantScreeningController@applicantList']);
        //Test Total Successful
        Route::get('/applicants/successlist', ['as' => 'recruitment.applicants.successlist', 'uses' => 'ApplicantScreeningController@SuccessTransactionList']);
        Route::get('/applicants/nid_input_list', ['as' => 'recruitment.applicants.nid_input_list', 'uses' => 'ApplicantScreeningController@processNIDData']);


        Route::get('/applicant/mark_as_paid/{type}/{id}/{circular_id}', ['as' => 'recruitment.applicant.mark_as_paid', 'uses' => 'ApplicantScreeningController@markAsPaid']);
        Route::get('/applicant/send_msg/{id}/{circular_id}', ['as' => 'recruitment.applicant.send_msg', 'uses' => 'ApplicantScreeningController@sendMsg']);
        Route::post('/applicant/mark_as_paid/{id}', ['as' => 'recruitment.applicant.update_as_paid', 'uses' => 'ApplicantScreeningController@updateAsPaid']);
        Route::any('/applicant/update_as_paid_by_file', ['as' => 'recruitment.applicant.update_as_paid_by_file', 'uses' => 'ApplicantScreeningController@updateAsPaidByFile']);
        Route::any('/applicant/move_to_hrm', ['as' => 'recruitment.move_to_hrm', 'uses' => 'ApplicantScreeningController@moveApplicantToHRM']);
        Route::any('/applicant/edit_for_hrm', ['as' => 'recruitment.edit_for_hrm', 'uses' => 'ApplicantScreeningController@editApplicantForHRM']);
        Route::any('/applicant/applicant_edit_for_hrm/{type}/{id}', ['as' => 'recruitment.applicant_edit_for_hrm', 'uses' => 'ApplicantScreeningController@applicantEditForHRM']);
        Route::post('/applicant/store_hrm_detail', ['as' => 'recruitment.store_hrm_detail', 'uses' => 'ApplicantScreeningController@storeApplicantHRmDetail']);
        Route::any('/applicant/generate_roll_no', ['as' => 'recruitment.applicant.generate_roll_no', 'uses' => 'ApplicantScreeningController@generateApplicantRoll']);


        Route::any('/applicant/hrm', ['as' => 'recruitment.hrm.index', 'uses' => 'ApplicantHRMController@index']);
        Route::get('/applicant/hrm/{type}/{circular_id}/{id}', ['as' => 'recruitment.hrm.view_download', 'uses' => 'ApplicantHRMController@applicantEditForHRM']);
        Route::post('/applicant/hrm/move/{id}', ['as' => 'recruitment.hrm.move', 'uses' => 'ApplicantHRMController@moveApplicantToHRM']);
        Route::post('/applicant/hrm/bulk_move', ['as' => 'recruitment.hrm.bulk_move', 'uses' => 'ApplicantHRMController@moveBulkApplicantToHRM']);

        Route::any('/applicant/hrm/card_print', ['as' => 'recruitment.hrm.card_print', 'uses' => 'ApplicantHRMController@print_card']);

        //settings
        //quota
        Route::any('/settings/applicant_quota', ['as' => 'recruitment.quota.index', 'uses' => 'JobApplicantQuotaController@index']);
        Route::any('/settings/applicant_quota/edit', ['as' => 'recruitment.quota.edit', 'uses' => 'JobApplicantQuotaController@edit']);
        Route::post('/settings/applicant_quota/update', ['as' => 'recruitment.quota.update', 'uses' => 'JobApplicantQuotaController@update']);
        //point table
        Route::resource('marks_rules', 'ApplicantMarksRuleController');

        //support
        Route::any('/supports/feedback', ['as' => 'supports.feedback', 'uses' => 'SupportController@problemReport']);
        Route::post('/supports/feedback/{id}', ['as' => 'supports.feedback.submit', 'uses' => 'SupportController@replyProblem']);
        Route::post('/supports/feedback/delete/{id}', ['as' => 'supports.feedback.delete', 'uses' => 'SupportController@replyProblemDelete']);


        Route::any('/reports/applicat_status', ['as' => 'report.applicants.status', 'uses' => 'ApplicantReportsController@applicantStatusReport']);
        Route::any('/reports/applicat_accepted_list', ['as' => 'report.applicants.applicat_accepted_list', 'uses' => 'ApplicantReportsController@applicantAcceptedListReport']);
        Route::any('/reports/applicat_marks_list', ['as' => 'report.applicants.applicat_marks_list', 'uses' => 'ApplicantReportsController@applicantMarksReport']);
        Route::post('/reports/applicat_status/export', ['as' => 'report.applicants.status_export', 'uses' => 'ApplicantReportsController@exportData']);
        Route::post('/reports/applicat_status/export_pdf', ['as' => 'report.applicants.status_export_pdf', 'uses' => 'ApplicantReportsController@exportDataAsPdf']);
        Route::get('/reports/applicant_details/', ['as' => 'report.applicant_details', 'uses' => 'ApplicantReportsController@applicantDetailsReport']);
        Route::post('/reports/applicant_details/export', ['as' => 'report.applicant_details.export', 'uses' => 'ApplicantReportsController@exportApplicantDetailReport']);
        Route::get('/reports/download', ['as' => 'report.download', 'uses' => 'ApplicantReportsController@download']);
        Route::any('/reports/applicant_form_download', ['as' => 'report.form.download', 'uses' => 'ApplicantReportsController@applicantFormDownload']);

//
        Route::get('/setting/instruction', ['as' => 'recruitment.instruction', 'uses' => 'RecruitmentController@aplicationInstruction']);
        Route::any('/setting/instruction/create', ['as' => 'recruitment.instruction.create', 'uses' => 'RecruitmentController@createApplicationInstruction']);
        Route::any('/setting/instruction/edit/{id}', ['as' => 'recruitment.instruction.edit', 'uses' => 'RecruitmentController@editApplicationInstruction']);

        Route::resource('exam-center', 'ApplicantExamCenter');
        Route::resource('mark_distribution', 'JobCircularMarkDistributionController');
        Route::resource('training', 'JobApplicantTrainingDateController');
        //load image
        Route::get('/profile_image', ['as' => 'profile_image', 'uses' => 'ApplicantScreeningController@loadImage']);
        Route::get('/test', function () {
            $data = \Illuminate\Support\Facades\DB::table('job_applicant')
                ->join('job_circular','job_circular.id','=','job_applicant.job_circular_id')->join('db_amis.tbl_division','db_amis.tbl_division.id','=','job_applicant.present_division_id')
                ->where('job_applicant.status','selected')
                ->whereIn('job_circular.id',[48])
                ->select('job_applicant.applicant_id','job_applicant.present_unit_id','job_circular.id as cid','job_applicant.applicant_name_bng','job_applicant.roll_no','job_circular.circular_name','job_applicant.applicant_password',
                    'division_name_bng','mobile_no_self')
                ->get();
//            return $data;
            $datas = [];
//            array_push($datas,['mobile_no_self','sms_body']);
            foreach ($data as $d){
                $exam_center = \App\modules\recruitment\Models\JobApplicantExamCenter::where('job_circular_id',$d->cid)
                    ->whereHas('examUnits',function($q) use($d){
                        $q->where('unit_id',$d->present_unit_id);
                    })->first();
                $bang = ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯'];
                $date_array = str_split("13/09/2019");
                $time_array = str_split("10:00 am");
                $roll_array = str_split($d->roll_no);
                $ri = intval($d->roll_no);

                $date = "";
                $time = "";
                $time_a = "";
                $roll_no = "";
                $exam_place = "";
                if($exam_center){
                    $exam_place_roll_wise = $exam_center->exam_place_roll_wise?json_decode($exam_center->exam_place_roll_wise,true):'';
                    if($exam_place_roll_wise){
                        foreach ($exam_place_roll_wise as $ex){
                            if($ri>=intval($ex['min_roll'])&&$ri<=intval($ex['max_roll'])){
                                $exam_place = $ex['exam_place'];
                                break;
                            }
                        }
                    }
                }
                foreach ($date_array as $da){
                    if(isset($bang[$da])){
                        $date .= $bang[$da];
                    }else{
                        $date .= $da;
                    }
                }
                foreach ($time_array as $da){
                    if(isset($bang[$da])){
                        $time_a .= $bang[$da];
                    }else{
                        $time_a .= $da;
                    }
                }
                foreach ($roll_array as $da){
                    if(isset($bang[$da])){
                        $roll_no .= $bang[$da];
                    }else{
                        $roll_no .= $da;
                    }
                }
                $rr = explode(" ",$time_a);
                if(!strcasecmp($rr[1],'am')){
                    $time = "সকাল $rr[0]";
                }else{
                    $time = "বিকাল $rr[0]";
                }
                if(!$exam_place){
                    array_push($datas,[$d->mobile_no_self,"নামঃ ".$d->applicant_name_bng.",  আইডিঃ ".$d->applicant_id.", পাসওয়ার্ডঃ ".$d->applicant_password.", রোল নংঃ $roll_no , পদবীঃ ".explode("|",$d->circular_name)[0]." , পরীক্ষার তারিখঃ $date,  সময়ঃ $time, পরীক্ষার স্থান/ জেলাঃ ".$d->division_name_bng." শহর । আসন বিন্যাস, প্রবেশপত্র ও বিস্তারিত  তথ্যের জন্য ভিজিট করুনঃ  www.ansarvdp.gov.bd"]);
                }else{
                    array_push($datas,[$d->mobile_no_self,"নামঃ ".$d->applicant_name_bng.",  আইডিঃ ".$d->applicant_id.", পাসওয়ার্ডঃ ".$d->applicant_password.", রোল নংঃ $roll_no , পদবীঃ ".explode("|",$d->circular_name)[0]." , পরীক্ষার তারিখঃ $date,  সময়ঃ $time, পরীক্ষার স্থান/ জেলাঃ ".$d->division_name_bng." শহর, পরীক্ষার কেন্দ্র: $exam_place । কেন্দ্রের নাম সহ প্রবেশপত্র ডাউনলোডের জন্য ভিজিট করুনঃ  www.ansarvdp.gov.bd"]);
                }
            }
            return \Maatwebsite\Excel\Facades\Excel::create('sms_file_download',function($excel) use($datas){
                $excel->sheet('Sheet1', function($sheet) use($datas) {

                    $sheet->fromArray($datas);

                });
            })->export('xls');
        });
        Route::get('/test1', function () {
            $applicants = \App\modules\recruitment\Models\JobAppliciant::whereHas('selectedApplicant',function(){

            })->where('status','paid')->get();
            foreach ($applicants as $applicant){
                $applicant->update('status','selected');
            }

        });
        Route::get('/gr1', function () {
            $divisions = [
                "8"=>41,
                "5"=>42,
                "3"=>43,
                "4"=>44,
                "6"=>45,
                "1"=>46,
                "2"=>47,
                "12"=>48,
                "11"=>49,
            ];
            $applicant = \App\modules\recruitment\Models\JobAppliciant::where('job_circular_id',52)->where('status','selected')
                ->select('applicant_id','roll_no','present_division_id')->get();
            $datas = collect($applicant)->groupBy('present_division_id',true);
            foreach ($datas as $k=>$data){
                $count = 1;
                foreach ($data as $d){
                    echo($k."--->".sprintf($divisions[$k]."%05d",$count)."<br>");
                    $a = \App\modules\recruitment\Models\JobAppliciant::where('applicant_id',$d->applicant_id)->where('job_circular_id',52)->first();
                    $a->roll_no = sprintf($divisions[$k]."%05d",$count);
                    $a->save();
                    $count++;
                }
            }

        });
        Route::get('/gr2', function () {
            $divisions = [
                "8"=>51,
                "5"=>52,
                "3"=>53,
                "4"=>54,
                "6"=>55,
                "1"=>56,
                "2"=>57,
                "12"=>58,
                "11"=>59,
            ];
            $applicant = \App\modules\recruitment\Models\JobAppliciant::where('job_circular_id',51)->where('status','selected')
                ->select('applicant_id','roll_no','present_division_id')->get();
            $datas = collect($applicant)->groupBy('present_division_id',true);
            foreach ($datas as $k=>$data){
                $count = 1;
                foreach ($data as $d){
                    echo($k."--->".sprintf($divisions[$k]."%05d",$count)."<br>");
                    $a = \App\modules\recruitment\Models\JobAppliciant::where('applicant_id',$d->applicant_id)
                        ->where('job_circular_id',51)->first();
                    $a->roll_no = sprintf($divisions[$k]."%05d",$count);
                    $a->save();
                    $count++;
                }
            }
        });
        Route::get('/gr3', function () {
            $divisions = [
                "8"=>61,
                "5"=>62,
                "3"=>63,
                "4"=>64,
                "6"=>65,
                "1"=>66,
                "2"=>67,
                "12"=>68,
                "11"=>69,
            ];
            $applicant = \App\modules\recruitment\Models\JobAppliciant::where('job_circular_id',48)->where('status','selected')
                ->select('applicant_id','roll_no','present_division_id')->get();
            $datas = collect($applicant)->groupBy('present_division_id',true);
            foreach ($datas as $k=>$data){
                $count = 1;
                foreach ($data as $d){
                    echo($k."--->".sprintf($divisions[$k]."%05d",$count)."<br>");
                    $a = \App\modules\recruitment\Models\JobAppliciant::where('applicant_id',$d->applicant_id)
                        ->where('job_circular_id',48)->first();
                    $a->roll_no = sprintf($divisions[$k]."%05d",$count);
                    $a->save();
                    $count++;
                }
            }
        });
        Route::get('/send_sms_paid', ['as' => 'send_sms_paid', 'uses' => 'SupportController@sendUserNamePassword']);

        Route::resource('quota_type','JobCircularApplicantQuota');
    });


});