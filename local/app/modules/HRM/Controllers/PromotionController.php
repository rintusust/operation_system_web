<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\ExportData;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\DataExportStatus;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\ExportDataJob;
use App\modules\HRM\Models\GlobalParameter;
use App\modules\HRM\Models\SystemSetting;
use App\modules\HRM\Models\PersonalnfoLogModel;
use App\modules\HRM\Models\OfferZone;
use App\modules\HRM\Models\UnitCompany;
use App\modules\HRM\Models\UnitCompanyLog;
use App\modules\recruitment\Models\JobAppliciant;
use App\modules\recruitment\Models\JobCircularMarkDistribution;
use App\modules\recruitment\Models\JobCircularQuota;
use App\modules\recruitment\Models\JobCircular;
use App\modules\HRM\Models\AnsarFutureState;
use App\modules\HRM\Models\AnsarPromotion;
use App\modules\HRM\Models\AnsarPromotionInfo;
use App\modules\HRM\Models\AnsarPromotionStatusLog;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\MemorandumModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\RestInfoLogModel;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use App\modules\HRM\Models\FreezingInfoLog;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\OfferBlockedAnsar;
use App\Jobs\RearrangePanelPositionGlobal;
use App\Jobs\RearrangePanelPositionLocal;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\ExportDataToExcel;


class PromotionController extends Controller
{
    use ExportDataToExcel;

    function hrmDashboard()
    {
        $type = auth()->user()->type;
        if ($type == 22 || $type == 66) {
            return View::make('HRM::Dashboard.hrm-rc-dc');
        } else {
            return View::make('HRM::Dashboard.hrm');
        }
    }

    //Promotion

    public function promotionAnsarView()
    {
        return view('HRM::promotion.promotion_view');
    }

    public function BackToPreviousBatchUploadView()
    {
        return view('HRM::promotion.back_to_previous_batch_upload_view');
    }

    public function MakeVarifiedBatchUploadView()
    {
        return view('HRM::promotion.make_varified_batch_upload_view');
    }

    public function RankUpdateBatchUploadView()
    {
        return view('HRM::promotion.rank_update_batch_upload_view');
    }

    public function SendToPanelBatchUploadView()
    {
        return view('HRM::promotion.send_to_panel_batch_upload_view');
    }

    public function promotionList()
    {
        return view('HRM::promotion.promotion_list');
    }
    public function promotionLog()
    {
        return view('HRM::promotion.promotion_ansar_log');
    }
    public function getPromotionLog(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $sex = Input::get('gender');
        $q = Input::get('q');
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9,]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        $data = [];
        $user = Auth::user();
        $data = CustomQuery::getPromotionAnsarLog($offset, $limit, $unit, $division, $sex, $rank, $q);
        if ($request->exists('export')) {
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_view_excel', $type);
        }
        return Response::json($data);
    }

    public function  getCirculars()
    {
        $status = Input::get('status');
        $category = Input::get('category');
        $data = JobCircular::with('category')->where('circular_status', $status)->where('job_category_id', $category)->get();
        
        return response()->json($data);

    }

    public function confirmPromotion(Request $request)
    {

        $rules = [
            'range' => 'regex:/^[0-9]+$/',
            'unit' => 'regex:/^[0-9]+$/',
            'circular' => 'required|regex:/^[0-9]+$/',
        ];
        $this->validate($request, $rules);
        try {
            $written_pass_mark = 0;
            $viva_pass_mark = 0;
            $mark_distribution = JobCircularMarkDistribution::where('job_circular_id', $request->circular)->first();
            if ($mark_distribution) {
                $written_pass_mark = (floatval($mark_distribution->convert_written_mark) * floatval($mark_distribution->written_pass_mark)) / 100;
                $viva_pass_mark = (floatval($mark_distribution->viva) * floatval($mark_distribution->viva_pass_mark)) / 100;
            }
//        return $written_pass_mark." ".$viva_pass_mark;
            $job_quota = JobCircularQuota::where('job_circular_id', $request->circular)->first();
            if ($job_quota->type == "unit") {
                $quota = $job_quota->quota()->where('district_id', $request->unit)->first();
                $accepted = JobAppliciant::whereHas('accepted', function ($q) {
                })->where('status', 'accepted')->where('job_circular_id', $request->circular)->where('unit_id', $request->unit)->count();
            } else {
                $quota = $job_quota->quota()->where('range_id', $request->range)->first();
                $accepted = JobAppliciant::whereHas('accepted', function ($q) {
                })->where('status', 'accepted')->where('job_circular_id', $request->circular)->where('division_id', $request->range)->count();
            }
            if ($job_quota->type == "unit") {
                $applicant_male = JobApplicantMarks::with(['applicant' => function ($q) {
                    $q->with(['district', 'division', 'thana']);
                }])->whereHas('applicant', function ($q) use ($request) {
                    $q->whereHas('selectedApplicant', function () {
                    })->where('status', 'selected')->where('job_circular_id', $request->circular)->where('unit_id', $request->unit);
                })->select(DB::raw('DISTINCT *,(IFNULL(written,0)+IFNULL(viva,0)+IFNULL(physical,0)+IFNULL(edu_training,0)+IFNULL(edu_experience,0)+IFNULL(physical_age,0)) as total_mark'))->havingRaw('total_mark>0')->orderBy('total_mark', 'desc');
                $applicant_male->where('written', '>=', $written_pass_mark)->where('viva', '>=', $viva_pass_mark);
            } else {
                $applicant_male = JobApplicantMarks::with(['applicant' => function ($q) {
                    $q->with(['district', 'division', 'thana']);
                }])->whereHas('applicant', function ($q) use ($request) {
                    $q->where(DB::raw('height_feet*12+height_inch'), ">=", 65);
                    $q->whereHas('education', function ($q) {
                        $q->where('priority', '>=', 7);
                    });
                    $q->whereHas('selectedApplicant', function () {
                    })->where('status', 'selected')->where('job_circular_id', $request->circular)->where('division_id', $request->range);
                })->select(DB::raw('DISTINCT *,(IFNULL(written,0)+IFNULL(viva,0)+IFNULL(physical,0)+IFNULL(edu_training,0)+IFNULL(edu_experience,0)+IFNULL(physical_age,0)) as total_mark'))->havingRaw('total_mark>0')->orderBy('total_mark', 'desc');
                $applicant_male->where('written', '>=', $written_pass_mark)->where('viva', '>=', $viva_pass_mark);
            }
            if ($quota) {

                if (intval($quota->male) - $accepted > 0) $applicants = $applicant_male->limit(intval($quota->male) - $accepted)->get();
                else $applicants = [];
            } else $applicants = [];
            if (count($applicants)) {
                foreach ($applicants as $applicant) {
                    $applicant->applicant->update(['status' => 'accepted']);
                    if (!$applicant->applicant->accepted) {
                        $applicant->applicant->accepted()->create([
                            'action_user_id' => auth()->user()->id
                        ]);
                    }
                }
            } else {
                throw new \Exception('No applicants within quota available');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        return response()->json(['status' => 'success', 'message' => 'Applicant accepted successfully']);
    }

    public function acceptPromotionByFile(Request $request){
        DB::enableQueryLog();
        $file = $request->file("applicant_id_list");
        $promoted_rank = $request->promoted_rank;
        $selected_ansars = [];
        $applicant_ids = [];
        $circular_id = $request->circular;
        $comment = $request->comment;
        //echo $comment;exit;
        
        /*Excel::load($file,function ($reader) use(&$selected_ansars){
            $selected_ansars = array_flatten($reader->limitColumns(1)->first());
        });*/


        Excel::load($file, function ($reader) {

            foreach ($reader->toArray() as $row) {
                echo '<pre>';print_r($row);
            }
        });
        // (print_r($applicant_ids));
        //
        //
        exit;
        
        $applicants = JobAppliciant::where('job_circular_id',$request->circular)
        ->whereIn('job_applicant.ansar_id',$selected_ansars)
        //->get();
        // $applicants = JobAppliciant::join('db_amis.tbl_ansar_promotion', 'tbl_ansar_promotion.ansar_id', '=', 'job_applicant.ansar_id')
         ->leftjoin('db_amis.tbl_ansar_promotion', 'tbl_ansar_promotion.ansar_id', '=', 'job_applicant.ansar_id')
         ->WhereNull('tbl_ansar_promotion.circular_id')
         ->select ('job_applicant.ansar_id','job_applicant.job_circular_id')
         ->get();
        //echo "<pre>";print_r($applicants);exit;
        foreach($applicants as $applicant)
        {
            $selected_ansars[]= $applicant->ansar_id;
        }
       
        // $data = CustomQuery::checkPromotionAnsarEntryExistStatus($circular_id,$selected_ansars);
         //echo "<pre>";print_r($selected_ansars);exit;
        if(count($selected_ansars) == 0){
            
            $results = ['status' => false, 'message' => 'No Ansar is Eligible!'];
            return Response::json($results);
        }
        DB::beginTransaction();
        
        try{
            $i=1;
            foreach ($applicants as $applicant){
                $this->promotionEntry($applicant,$promoted_rank);
                //$this->promotionInfoEntry($applicant,$promoted_rank);
                $this->promotionEntryLog($applicant,$comment);
                $this->statusMakesNotVerified($applicant);
                $this->personalInfoMakesNotVerified($applicant);
                
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        return redirect()->back()->with("success_message","Applicants enlisted in the promotion list successfully");
    }

    public function BackToPreviousBatchUploadByFile(Request $request){
        DB::enableQueryLog();
        $file = $request->file("applicant_id_list");
        $date = Carbon::yesterday()->format('d-M-Y H:i:s');
        $applicant_ids = "";
        $selected_ansars = [];
        
        Excel::load($file,function ($reader) use(&$applicant_ids){
            $applicant_ids = array_flatten($reader->limitColumns(1)->first());
        });
        
        $applicants = AnsarPromotion::where('circular_id',$request->circular)->where('promoted_status',0)->whereIn('ansar_id',$applicant_ids)->get();
        $selected_ansars = $request->input('ansar_id');
        foreach($applicants as $applicant)
        {
            $selected_ansars[]= $applicant->ansar_id;
        }
        if(count($selected_ansars) == 0){
            
            $results = ['status' => false, 'message' => 'No Ansar is Eligible!'];
            return Response::json($results);
        }

        DB::beginTransaction();
        $user = [];
        try{
            if (!is_null($selected_ansars)) {
                for ($i = 0; $i < count($selected_ansars); $i++) {
                    // $row_id = $request->request_id;
                    // $results = [];  

                    $requestAnsarPromotionData = AnsarPromotion::where('ansar_id',$selected_ansars[$i])->where('circular_id', $request->circular)->firstOrFail();
                    $requestedAnsar = $requestAnsarPromotionData->ansar_id;
                    $ansarStatusLogData = AnsarPromotionStatusLog::where('ansar_id', $requestedAnsar)->where('circular_id', $requestAnsarPromotionData->circular)->firstOrFail();

                    
                    $ansarStatusData = AnsarStatusInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
                    $ansarStatusData->free_status = $ansarStatusLogData->free_status;
                    $ansarStatusData->pannel_status = $ansarStatusLogData->pannel_status;
                    $ansarStatusData->offer_sms_status = $ansarStatusLogData->offer_sms_status;
                    $ansarStatusData->offered_status = $ansarStatusLogData->offered_status;
                    $ansarStatusData->embodied_status = $ansarStatusLogData->embodied_status;
                    $ansarStatusData->offer_block_status = $ansarStatusLogData->offer_block_status;
                    $ansarStatusData->freezing_status = $ansarStatusLogData->freezing_status;
                    $ansarStatusData->early_retierment_status = $ansarStatusLogData->early_retierment_status;
                    $ansarStatusData->block_list_status = $ansarStatusLogData->block_list_status;
                    $ansarStatusData->black_list_status = $ansarStatusLogData->black_list_status;
                    $ansarStatusData->rest_status = $ansarStatusLogData->rest_status;
                    $ansarStatusData->retierment_status = $ansarStatusLogData->retierment_status;
                    $ansarStatusData->save();  
                    $requestAnsarPromotionData->delete(); 
                    $ansarStatusLogData->delete();
                }
            }
            DB::commit();
            // CustomQuery::addActionlog($user, true);
            // $this->dispatch(new RearrangePanelPositionGlobal());
            // $this->dispatch(new RearrangePanelPositionLocal());
        }catch(\Exception $e){
           //echo $selected_ansars[$i];exit;
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        return redirect()->back()->with("success_message","Applicants successfully Back to Previous...");
            
    }

    public function SendToPanelBatchUploadByFile(Request $request){
        DB::enableQueryLog();
        $file = $request->file("applicant_id_list");
        $date = Carbon::yesterday()->format('d-M-Y H:i:s');
        $applicant_ids = "";
        $selected_ansars = [];
        //echo $request->circular;exit;
        
        Excel::load($file,function ($reader) use(&$applicant_ids){
            $applicant_ids = array_flatten($reader->limitColumns(1)->first());
        });
        //print_r($applicant_ids);exit;
        $applicants = AnsarPromotion::where('circular_id',$request->circular)->where('promoted_status',1)->where('status','Completed')->whereIn('ansar_id',$applicant_ids)->get();
        $selected_ansars = $request->input('ansar_id');
        foreach($applicants as $applicant)
        {
            $selected_ansars[]= $applicant->ansar_id;
        }
        //print_r(count($selected_ansars));exit;
        if(count($selected_ansars) == 0){
            
            $results = ['status' => false, 'message' => 'No Ansar is Eligible!'];
            return Response::json($results);
        }
        
        $rules = [
            'memorandum_id' => 'required',
            'panel_date' => ["required", "after:{$date}","date_format:d-M-Y H:i:s"],
        ];
        // $valid = Validator::make($request->all(), $rules);
        // if ($valid->fails()) {
        //     $messages = $valid->messages();
        //     return Response::json(['status' => false, 'message' => 'Invalid request']);
            
        // }
        //echo $selected_ansars; exit;
        //print_r($selected_ansars); exit;
        DB::beginTransaction();
        $user = [];
        try{
            $n = Carbon::now();
            $mi = $request->input('memorandum_id');
            $pd = $request->input('panel_date');
            $modified_panel_date = Carbon::parse($pd)->format('Y-m-d H:i:s');
            $memorandum_entry = new MemorandumModel();
            $memorandum_entry->memorandum_id = $mi;
            $memorandum_entry->save();

            //print_r($selected_ansars); exit;
            if (!is_null($selected_ansars)) {
                for ($i = 0; $i < count($selected_ansars); $i++) {
                    $ansar = PersonalInfo::where('ansar_id', $selected_ansars[$i])->first();
                    if ($ansar && ($ansar->verified == 0 || $ansar->verified == 1)) {
                        $ansar->verified = 2;
                        $ansar->save();
                    }
                     
                        $ansar->deleteCount();
                        $ansar->deleteOfferStatus();
                        $panel_entry = new PanelModel;
                        $panel_entry->ansar_id = $selected_ansars[$i];
                        $panel_entry->come_from = "Promotion Process";
                        $panel_entry->panel_date = $modified_panel_date;
                        $panel_entry->re_panel_date = $modified_panel_date;
                        $panel_entry->memorandum_id = $mi;
                        $panel_entry->action_user_id = Auth::user()->id;
                        $panel_entry->save();

                        $this->getPreviousStatus($selected_ansars[$i],$request->circular, $mi);
                        
                        $ansarStatusLogData = AnsarPromotionStatusLog::where('ansar_id', $selected_ansars[$i])->where('circular_id',$request->circular)->firstOrFail();
                        //print_r($ansarStatusLogData);exit;
                        $ansarStatusLogData->delete();

                        AnsarStatusInfo::where('ansar_id', $selected_ansars[$i])->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 1, 'freezing_status' => 0]);
                        AnsarPromotion::where('ansar_id', $selected_ansars[$i])->delete();
                        array_push($user, ['ansar_id' => $selected_ansars[$i], 'action_type' => 'PANELED', 'from_state' => 'FREE', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                    
                            }
            }
            DB::commit();
            CustomQuery::addActionlog($user, true);
            $this->dispatch(new RearrangePanelPositionGlobal());
            $this->dispatch(new RearrangePanelPositionLocal());
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
        return redirect()->back()->with("success_message","Applicant/s successfully sent to panel...");

    }

    public function SendToPanelFromAnsarList(Request $request){
        
        DB::enableQueryLog();
        $date = Carbon::yesterday()->format('d-M-Y H:i:s');
        $row_id = $request->request_id;
        $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
        $selected_ansars[] = $requestAnsarPromotionData->ansar_id;

        $rules = [
            'memorandum_id' => 'required',
             'panel_date' => ["required", "after:{$date}"]
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            $messages = $valid->messages();
            return Response::json(['status' => false, 'message' => 'Invalid request']);
            
        }
        
        DB::beginTransaction();
        $user = [];
        try{
            $n = Carbon::now();
            $mi = $request->input('memorandum_id');
            $pd = $request->input('panel_date');
            $modified_panel_date = Carbon::parse($pd)->format('Y-m-d H:i:s');
            $memorandum_entry = new MemorandumModel();
            $memorandum_entry->memorandum_id = $mi;
            $memorandum_entry->save();
            
            
            
            if (!is_null($selected_ansars)) {
                for ($i = 0; $i < count($selected_ansars); $i++) {
                    $ansar = PersonalInfo::where('ansar_id', $selected_ansars[$i])->first();
                    if ($ansar && ($ansar->verified == 0 || $ansar->verified == 1)) {
                        $ansar->verified = 2;
                        $ansar->save();
                    }
                     
                        $ansar->deleteCount();
                        $ansar->deleteOfferStatus();
                        $panel_entry = new PanelModel;
                        $panel_entry->ansar_id = $selected_ansars[$i];
                        $panel_entry->come_from = "Promotion Process";
                        $panel_entry->panel_date = $modified_panel_date;
                        $panel_entry->re_panel_date = $modified_panel_date;
                        $panel_entry->memorandum_id = $mi;
                        $panel_entry->action_user_id = Auth::user()->id;
                        $panel_entry->save();

                        $this->getPreviousStatus($requestAnsarPromotionData->ansar_id,$requestAnsarPromotionData->circular_id, $mi);
                        
                        $ansarStatusLogData = AnsarPromotionStatusLog::where('ansar_id', $selected_ansars[$i])->where('circular_id',$requestAnsarPromotionData->circular_id)->firstOrFail();
                        $ansarStatusLogData->delete();
                        AnsarStatusInfo::where('ansar_id', $selected_ansars[$i])->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 1, 'freezing_status' => 0]);
                        //$previousStatus = $this->getPreviousStatus($selected_ansars[$i],$);
                        array_push($user, ['ansar_id' => $selected_ansars[$i], 'action_type' => 'PANELED', 'from_state' => 'FREE', 'to_state' => 'PANELED', 'action_by' => auth()->user()->id]);
                        AnsarPromotion::where('ansar_id', $selected_ansars[$i])->delete();
                    
                }
            }
            DB::commit();
            CustomQuery::addActionlog($user, true);
            $this->dispatch(new RearrangePanelPositionGlobal());
            $this->dispatch(new RearrangePanelPositionLocal());
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
        return Response::json(['status' => true, 'message' => "Applicant successfully sent to panel..."]);
        //return redirect()->back()->with("success_message","Applicant successfully sent to panel...");
        //return true;
    }

    public function processAnsarFreezeData($ansarId)
    {
          
                $frezeInfo = FreezingInfoModel::where('ansar_id', $ansarId)->first();
                $embodiment = $frezeInfo->embodiment;
                $freezed_ansar_embodiment_detail = $frezeInfo->freezedAnsarEmbodiment;


                DB::beginTransaction();

                try {
                    if (!$frezeInfo || !($embodiment || $freezed_ansar_embodiment_detail)) throw new \Exception("Invalid Request");
                    
                    // $m = new MemorandumModel;
                    // $m->memorandum_id = $request->memorandum;
                    // $m->save();

                    FreezingInfoLog::create([
                        'old_freez_id' => $frezeInfo->id,
                        'ansar_id' => $ansarId,
                        'freez_reason' => $frezeInfo->freez_reason,
                        'comment_on_freez' => $frezeInfo->comment_on_freez,
                        'move_frm_freez_date' => Carbon::now()->format('Y-m-d'),
                        'move_to' => 'panel',
                        'comment_on_move' => 'No Comment',
                    ]);

                    EmbodimentLogModel::create([
                        'old_embodiment_id' => $embodiment ? $embodiment->id : $freezed_ansar_embodiment_detail->embodiment_id,
                        'old_memorandum_id' => $embodiment ? $embodiment->memorandum_id : $freezed_ansar_embodiment_detail->em_mem_id,
                        'ansar_id' => $ansarId,
                        'reporting_date' => $embodiment ? $embodiment->reporting_date : $freezed_ansar_embodiment_detail->reporting_date,
                        'joining_date' => $embodiment ? $embodiment->joining_date : $freezed_ansar_embodiment_detail->embodied_date,
                        'kpi_id' => $embodiment ? $embodiment->kpi_id : $freezed_ansar_embodiment_detail->freezed_kpi_id,
                        'move_to' => 'panel',
                        'disembodiment_reason_id' => 0,
                        'release_date' => Carbon::now()->format('Y-m-d'),
                        'action_user_id' => Auth::id(),
                    ]);

                    $frezeInfo->delete();

                    if ($embodiment) $embodiment->delete();

                    if ($freezed_ansar_embodiment_detail) $freezed_ansar_embodiment_detail->delete();
                 
                    //CustomQuery::addActionlog(['ansar_id' => $ansarId, 'action_type' => 'DISEMBODIMENT', 'from_state' => 'FREEZE', 'to_state' => 'REST', 'action_by' => auth()->user()->id]);
                    DB::commit();

                } catch (\Exception $rollback) {
                    DB::rollback();
                    return false;
                }
        
            return true;
    }

    public function processAnsarEmbodiedData($ansar_id)
    {
   
        DB::beginTransaction();
        try {
            $embodiment_infos = EmbodimentModel::where('ansar_id', $ansar_id)->first();
            $joining_date = Carbon::parse($embodiment_infos->joining_date);
            $service_days = Carbon::now()->diffInDays($joining_date);
   
            $embodiment_infos->saveLog('Panel', Carbon::now()->format("Y-m-d"), 'Promotion Purpose', 8);
            $embodiment_infos->delete();
            DB::commit();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function processAnsarOfferedData($ansar_id)
    {
        
            DB::beginTransaction();
            try {
                $ansar = PersonalInfo::where('ansar_id', $ansar_id)->first();
                $panel_date = Carbon::now()->format("Y-m-d H:i:s");
                $offered_ansar = $ansar->offer_sms_info;
                $os = OfferSMSStatus::where('ansar_id', $ansar_id)->first();
                if (!$offered_ansar) $received_ansar = $ansar->receiveSMS;
                if ($offered_ansar && $offered_ansar->come_from == 'rest') {
                    $ansar->status()->update([
                        'offer_sms_status' => 0,
                        'rest_status' => 1,
                    ]);
                } else {
                    $pa = $ansar->panel;
                    if (!$pa) {
                        $panel_log = $ansar->panelLog()->first();
                        $ansar->panel()->save(new PanelModel([
                            'memorandum_id' => $panel_log->old_memorandum_id,
                            'panel_date' => $os && $os->isGlobalOfferRegion() ? $panel_date : $panel_log->panel_date,
                            're_panel_date' => $os && $os->isRegionalOfferRegion() ? $panel_date : $panel_log->re_panel_date,
                            'come_from' => 'OfferCancel',
                            'ansar_merit_list' => 1,
                            'action_user_id' => auth()->user()->id,
                        ]));

                    } else {
                        $pa->locked = 0;
                        $pa->come_from = 'OfferCancel';
                        if ($os && $os->isGlobalOfferRegion()) {
                            $pa->panel_date = $panel_date;
                        } elseif ($os && $os->isRegionalOfferRegion()) {
                            $pa->re_panel_date = $panel_date;
                        }
                        $pa->save();
                    }
                    $ansar->status()->update([
                        'offer_sms_status' => 0,
                        'pannel_status' => 1,
                    ]);
                }
                $ansar->offerCancel()->save(new OfferCancel([
                    'offer_cancel_date' => Carbon::now()
                ]));

                if ($os) {
                    $ot = explode(",", $os->offer_type);
                    $ou = explode(",", $os->last_offer_units);
                    $ot = array_slice($ot, 0, count($ot) - 1);
                    $ou = array_slice($ou, 0, count($ou) - 1);
                    $os->offer_type = implode(",", $ot);
                    $os->last_offer_units = implode(",", $ou);
                    $os->last_offer_unit = !count($ou) ? "" : $ou[count($ou) - 1];
                    $os->save();
                }
                if ($offered_ansar) {
                    $ansar->offerLog()->save(new OfferSmsLog([
                        'offered_date' => $offered_ansar->sms_send_datetime,
                        'action_date' => Carbon::now(),
                        'offered_district' => $offered_ansar->district_id,
                        'action_user_id' => auth()->user()->id,
                        'reply_type' => 'No Reply',
                        'comment' => 'Offer Cancel'
                    ]));
                    $offered_ansar->delete();
                } else {
                    $ansar->offerLog()->save(new OfferSmsLog([
                        'offered_date' => $received_ansar->sms_send_datetime,
                        'offered_district' => $received_ansar->offered_district,
                        'action_user_id' => auth()->user()->id,
                        'action_date' => Carbon::now(),
                        'reply_type' => 'Yes',
                        'comment' => 'Offer Cancel'
                    ]));
                    $received_ansar->delete();
                }
                DB::commit();
                auth()->user()->actionLog()->save(new ActionUserLog([
                    'ansar_id' => $ansar_id,
                    'action_type' => 'CANCEL OFFER',
                    'from_state' => 'OFFER',
                    'to_state' => 'PANEL'
                ]));
                $result['success']++;
            } catch (\Exception $e) {
                DB::rollback();
                return false;
            }
        
        if (count($ansar_id)) {
            $this->dispatch(new RearrangePanelPositionGlobal());
            $this->dispatch(new RearrangePanelPositionLocal());
        }
        return true;
    }

    
    public function processAnsarPanelData($ansar_id)
    {
        $modified_cancel_panel_date = Carbon::now()->format('Y-m-d');
        $cancel_panel_comment = 'Promotion Purpose';

        DB::beginTransaction();
        try {
            $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            if (!$ansar) throw new \Exception("This Ansar is not exists");
            if (in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->getStatus()) || in_array(AnsarStatusInfo::BLACK_STATUS, $ansar->getStatus()) || !in_array(AnsarStatusInfo::PANEL_STATUS, $ansar->getStatus())) throw new \Exception("This Ansar is not available in panel");
            $panel_info = $ansar->panel;
            if (!$panel_info) throw new \Exception("This Ansar is not in panel");
           
           $panel_info->saveLog("Panel", Carbon::now(), $cancel_panel_comment);
             
            $panel_info->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return true;
    }

    public function getPreviousStatus($ansar_id,$circular_id, $mi)
    {
        
        $statusLogData = AnsarPromotionStatusLog::where('ansar_id',$ansar_id)->where('circular_id',$circular_id)->first();
        $mobile_no = DB::table('tbl_ansar_parsonal_info')->where('ansar_id', $ansar_id)->select('tbl_ansar_parsonal_info.mobile_no_self')->first();
        DB::beginTransaction();
        try {
            
                    if($statusLogData->pannel_status == 1)
                    {
                        $panel_info = PanelModel::where('ansar_id', $ansar_id)->first();
                        $panel_log_save = new PanelInfoLogModel();
                        $panel_log_save->panel_id_old = $panel_info->id;
                        $panel_log_save->ansar_id = $ansar_id;
                        $panel_log_save->merit_list = $panel_info->ansar_merit_list;
                        $panel_log_save->panel_date = $panel_info->panel_date;
                        $panel_log_save->movement_date = Carbon::today();
                        $panel_log_save->come_from = $panel_info->come_from;
                        $panel_log_save->move_to = "Panel";
                        $panel_log_save->comment = "Promotion Purpose";
                        $panel_log_save->action_user_id = Auth::user()->id;
                        $panel_log_save->save();

                        $panel_info->delete();

                    }
                    
                    if($statusLogData->offer_sms_status == 1)
                    {
                        $sms_offer_info = OfferSMS::where('ansar_id', $ansar_id)->first();
                        $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();

                        if (!is_null($sms_offer_info)) {

                            $sms_log_save = new OfferSmsLog();
                            $sms_log_save->ansar_id = $ansar_id;
                            $sms_log_save->sms_offer_id = $sms_offer_info->id;
                            $sms_log_save->mobile_no = $mobile_no->mobile_no_self;

                            //$sms_log_save->offer_status=;
                            $sms_log_save->action_date = Carbon::now();
                            $sms_log_save->reply_type = "No Reply";
                            $sms_log_save->offered_district = $sms_offer_info->district_id;
                            $sms_log_save->offered_date = $sms_offer_info->sms_send_datetime;
                            $sms_log_save->action_user_id = Auth::user()->id;
                            $sms_log_save->save();

                            $sms_offer_info->delete();

                        } elseif (!is_null($sms_receive_info)) {
                            $sms_log_save = new OfferSmsLog();
                            $sms_log_save->ansar_id = $ansar_id;
                            $sms_log_save->sms_offer_id = $sms_receive_info->id;
                            $sms_log_save->mobile_no = $mobile_no->mobile_no_self;
                            //$sms_log_save->offer_status=;
                            $sms_log_save->reply_type = "Yes";
                            $sms_log_save->action_date = $sms_receive_info->sms_received_datetime;
                            $sms_log_save->offered_district = $sms_receive_info->offered_district;
                            $sms_log_save->offered_date = $sms_receive_info->sms_received_datetime;
                            $sms_log_save->action_user_id = Auth::user()->id;
                            $sms_log_save->save();

                            $sms_receive_info->delete();
                        }

                    }

                        if($statusLogData->embodied_status == 1)
                        {
                            
                            $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                            $embodiment_log_save = new EmbodimentLogModel();
                            $embodiment_log_save->old_embodiment_id = $embodiment_info->id;
                            $embodiment_log_save->old_memorandum_id = $embodiment_info->memorandum_id;
                            $embodiment_log_save->ansar_id = $ansar_id;
                            $embodiment_log_save->kpi_id = $embodiment_info->kpi_id;
                            $embodiment_log_save->reporting_date = $embodiment_info->reporting_date;
                            $embodiment_log_save->joining_date = $embodiment_info->joining_date;
                            $embodiment_log_save->release_date = Carbon::now();
                            $embodiment_log_save->move_to = "Panel";
                            $embodiment_log_save->service_extension_status = $embodiment_info->service_extension_status;
                            //$embodiment_log_save->comment = "Promotion Purpose";
                            $embodiment_log_save->comment = $mi;
                            $embodiment_log_save->disembodiment_reason_id = 8;
                            $embodiment_log_save->action_user_id = Auth::user()->id;
                            $embodiment_log_save->save();
        
                            $embodiment_info->delete();
                        }
                        
                        if($statusLogData->rest_status == 1)
                        {
                            $rest_info = RestInfoModel::where('ansar_id', $ansar_id)->first();
                            $rest_log_save = new RestInfoLogModel();
                            $rest_log_save->old_rest_id = $rest_info->id;
                            $rest_log_save->old_embodiment_id = $rest_info->old_embodiment_id;
                            $rest_log_save->old_memorandum_id = $rest_info->memorandum_id;
                            $rest_log_save->ansar_id = $ansar_id;
                            $rest_log_save->rest_date = $rest_info->rest_date;
                            $rest_log_save->total_service_days = $rest_info->total_service_days;
                            $rest_log_save->rest_type = $rest_info->rest_form;
                            $rest_log_save->disembodiment_reason_id = $rest_info->disembodiment_reason_id;
                            $rest_log_save->comment = "Promotion Purpose";
                            $rest_log_save->move_to = "Panel";
                            $rest_log_save->move_date = Carbon::now();
                            $rest_log_save->action_user_id = Auth::user()->id;
                            $rest_log_save->save();
        
                            $rest_info->delete();
                        }
                        
                        if($statusLogData->freezing_status == 1)
                        {
                            $freeze_info = FreezingInfoModel::where('ansar_id', $ansar_id)->first();
                            $freeze_log_save = new FreezingInfoLog();
                            $freeze_log_save->old_freez_id = $freeze_info->id;
                            $freeze_log_save->ansar_id = $ansar_id;
                            $freeze_log_save->ansar_embodiment_id = $freeze_info->ansar_embodiment_id;
                            $freeze_log_save->freez_reason = $freeze_info->freez_reason;
                            $freeze_log_save->freez_date = $freeze_info->freez_date;
                            $freeze_log_save->comment_on_freez = "Promotion Purpose";
                            $freeze_log_save->move_frm_freez_date = Carbon::now();
                            $freeze_log_save->move_to = "Panel";
                            $freeze_log_save->comment_on_move = "Promotion Purpose";
                            $freeze_log_save->action_user_id = Auth::user()->id;
                            $freeze_log_save->save();
        
                            $freeze_info->delete();
                        }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return true;
    }

    // public function getPreviousStatus($ansar_id,$circular_id)
    // {
    //     $statusLogData = AnsarPromotionStatusLog::where('ansar_id',$ansar_id)->where('circular_id',$circular_id)->first();
        
    //     if($statusLogData->pannel_status == 1){
    //         return 'PANEL';
    //     }elseif($statusLogData->offer_sms_status == 1){
    //         return 'OFFER SMS';
    //     }elseif($statusLogData->offered_status == 1){
    //         return 'OFFERED';
    //     }elseif($statusLogData->embodied_status == 1){
    //         return 'EMBODIED';
    //     }elseif($statusLogData->offer_block_status == 1){
    //         return 'OFFER BLOCK';
    //     }elseif($statusLogData->freezing_status == 1){
    //         return 'FREEZE';
    //     }elseif($statusLogData->early_retierment_status == 1){
    //         return 'EARLY RETIERMENT';
    //     }elseif($statusLogData->rest_status == 1){
    //         return 'REST';
    //     }elseif($statusLogData->retierment_status == 1){
    //         return 'RETIERMENT';
    //     }

    //     return $statusLogData;
    // }
    

    public function promotionEntry($applicant,$promoted_rank)
    {
        $ansar_id = $applicant->ansar_id;
        $circular_id = $applicant->job_circular_id;
        //$comment = 
        //print_r($comment);exit;
        
                $inserted_data = [
                    "ansar_id"=> $ansar_id,
                    "circular_id" => $circular_id,
                    "status" => "On Process",
                    "promoted_rank" => $promoted_rank
                    
                ];
                //print_r($inserted_data);exit;
                //DB::enableQueryLog();
                AnsarPromotion::insert($inserted_data);
            
             
    }

    public function promotionInfoEntry($applicant,$promoted_rank)
    {
        //echo "<pre>"; print_r($applicant); exit;
        $results = [];
        $ansar_id = $applicant->ansar_id;
        $circular_id = $applicant->job_circular_id;
        
                $inserted_data = [
                    "ansar_id"=> $ansar_id,
                    "circular_id" => $circular_id,
                    "promoted_rank" => $promoted_rank,
                    // "promoted_date" => Carbon::now(),
                    "action_user_id" => Auth::user()->id
                ];
                //print_r($inserted_data);exit;
                //DB::enableQueryLog();
                AnsarPromotionInfo::insert($inserted_data);
            
    }

    public function promotionEntryLog($applicant,$comment)
    {
        //print_r($comment);exit;
        $results = [];
        $ansar_id = $applicant->ansar_id;
        $circular_id = $applicant->job_circular_id;
        $ansarStatusData = AnsarStatusInfo::where('ansar_id',$ansar_id)->first();
        $free_status = $ansarStatusData->free_status;
        $pannel_status = $ansarStatusData->pannel_status;
        $offer_sms_status = $ansarStatusData->offer_sms_status;
        $offered_status = $ansarStatusData->offered_status;
        $embodied_status = $ansarStatusData->embodied_status;
        $offer_block_status = $ansarStatusData->offer_block_status;
        $freezing_status = $ansarStatusData->freezing_status;
        $early_retierment_status = $ansarStatusData->early_retierment_status;
        $block_list_status = $ansarStatusData->block_list_status;
        $black_list_status = $ansarStatusData->black_list_status;
        $rest_status = $ansarStatusData->rest_status;
        $retierment_status = $ansarStatusData->retierment_status;
        
                $inserted_data = [
                    "ansar_id"=> $ansar_id,
                    "circular_id" => $circular_id,
                    "action_user_id"=> Auth::user()->id,
                    "free_status" => $free_status,
                    "pannel_status"=> $pannel_status,
                    "offer_sms_status" => $offer_sms_status,
                    "offered_status" => $offered_status,
                    "embodied_status" => $embodied_status,
                    "offer_block_status"=> $offer_block_status,
                    "freezing_status" => $freezing_status,
                    "early_retierment_status"=> $early_retierment_status,
                    "block_list_status" => $block_list_status,
                    "black_list_status" => $black_list_status,
                    "rest_status"=> $rest_status,
                    "retierment_status" => $retierment_status,
                    "comment" => $comment
                ];
                //print_r($inserted_data);exit;
                //DB::enableQueryLog();
                AnsarPromotionStatusLog::insert($inserted_data);
            
             
    }

    // public function promotionEntryLog($applicant)
    // {
        
    //     $results = [];
    //     $ansar_id = $applicant->ansar_id;
    //     $circular_id = $applicant->job_circular_id;
    //     $ansarStatusData = AnsarStatusInfo::where('ansar_id',$ansar_id)->first();
    //     $free_status = $ansarStatusData->free_status;
    //     $pannel_status = $ansarStatusData->pannel_status;
    //     $offer_sms_status = $ansarStatusData->offer_sms_status;
    //     $offered_status = $ansarStatusData->offered_status;
    //     $embodied_status = $ansarStatusData->embodied_status;
    //     $offer_block_status = $ansarStatusData->offer_block_status;
    //     $freezing_status = $ansarStatusData->freezing_status;
    //     $early_retierment_status = $ansarStatusData->early_retierment_status;
    //     $block_list_status = $ansarStatusData->block_list_status;
    //     $black_list_status = $ansarStatusData->black_list_status;
    //     $rest_status = $ansarStatusData->rest_status;
    //     $retierment_status = $ansarStatusData->retierment_status;
        
    //     $data = CustomQuery::checkPromotionLogAnsarExistStatus($ansar_id);
            
    //         if(count($data)>0 ){
    //             return false;
    //         }else{
    //             $inserted_data = [
    //                 "ansar_id"=> $ansar_id,
    //                 "circular_id" => $circular_id,
    //                 "action_user_id"=> Auth::user()->id,
    //                 "free_status" => $free_status,
    //                 "pannel_status"=> $pannel_status,
    //                 "offer_sms_status" => $offer_sms_status,
    //                 "offered_status" => $offered_status,
    //                 "embodied_status" => $embodied_status,
    //                 "offer_block_status"=> $offer_block_status,
    //                 "freezing_status" => $freezing_status,
    //                 "early_retierment_status"=> $early_retierment_status,
    //                 "block_list_status" => $block_list_status,
    //                 "black_list_status" => $black_list_status,
    //                 "rest_status"=> $rest_status,
    //                 "retierment_status" => $retierment_status
    //             ];
    //             //print_r($inserted_data);exit;
    //             //DB::enableQueryLog();
    //             AnsarPromotionStatusLog::insert($inserted_data);
                
    //         }
                
    // }

    public function statusMakesNotVerified($applicant)
    {
                $selected_ansars = [];
                $selected_ansars[]= $applicant->ansar_id;
                
                if (!is_null($selected_ansars)) {
                    for ($i = 0; $i < count($selected_ansars); $i++) {
                        $makesNotVerified = AnsarStatusInfo::where('ansar_id',$selected_ansars[$i])->firstOrFail();
                            $makesNotVerified->free_status = 0;
                            $makesNotVerified->pannel_status = 0;
                            $makesNotVerified->offer_sms_status = 0;
                            $makesNotVerified->offered_status = 0;
                            $makesNotVerified->embodied_status = 0;
                            $makesNotVerified->offer_block_status = 0;
                            $makesNotVerified->freezing_status = 0;
                            $makesNotVerified->early_retierment_status = 0;
                            $makesNotVerified->block_list_status = 0;
                            $makesNotVerified->black_list_status = 0;
                            $makesNotVerified->rest_status = 0;
                            $makesNotVerified->retierment_status = 0;
                            $makesNotVerified->save();
                    }
                }
                
    }

    public function personalInfoMakesNotVerified($applicant)
    {
        $selected_ansars = [];
        $selected_ansars[]= $applicant->ansar_id;
        
        if (!is_null($selected_ansars)) {
            for ($i = 0; $i < count($selected_ansars); $i++) {
                $makesNotVerified = PersonalInfo::where('ansar_id',$selected_ansars[$i])->firstOrFail();
                $makesNotVerified->verified = 0;
                $makesNotVerified->save();
            }
        }
            
    }

    
    public function getPromotionList(Request $request)
    {
        //DB::enableQueryLog();
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $thana = Input::get('thana');
        $sex = Input::get('gender');
        $q = Input::get('q');
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9,]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/'],
           // 'from_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],
            //'to_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],

        ];
        //print_r($limit);exit;
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        //echo "anik";exit;
        $data = CustomQuery::getPromotionListWithRankGender($offset, $limit, $unit, $division, $thana, $sex, $rank, $q);
        //dd(DB::getQueryLog());exit;
       // print_r($data);exit;
        if ($request->exists('export')) {
            $data = collect($data['allPromotionAnsar'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_view_excel', $type);
        }
        return Response::json($data);
    }

    public function getPromotionAnsarList(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $sex = Input::get('gender');
        $q = Input::get('q');
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9,]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/'],
           // 'from_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],
            //'to_date' => ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],

        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        
        $data = [];
        $user = Auth::user();
        
            
        if($unit != 'all'){
            
            $data = CustomQuery::getPromotionAnsarInfo($offset, $limit, $unit, $division, $sex, $rank, $q);
           // echo "<pre>"; print_r($data);exit;
        }else{
            $data = [];
        }
        //$data = CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $request->filter_mobile_no, $request->filter_age, $q);
        
                
        if ($request->exists('export')) {
            $data = collect($data['allPromotionAnsar'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_view_excel', $type);
        }
        return Response::json($data);
    }
    
    function verifyRankPromotion(Request $request)
    {  
        //echo ("Anik");exit;
        if($request->rankUpdate==0)
        {
            //echo("Just Varified");exit;
            
            $row_id = $request->request_id;
            $comment = $request->comment;
            $results = [];  
                
            $requestAnsar = AnsarPromotion::findOrFail($row_id);
            $requestAnsar->not_verified_status = 1;
            $requestAnsar->promoted_status = 1;
            $requestAnsar->save();
            $requestAnsar = PersonalInfo::findOrFail($row_id);
            $requestAnsar->verified = 2;
            $requestAnsar->save();
            
            $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') has been verified!'];
            return Response::json($results);
        }
        elseif($request->rankUpdate==1)
        {
            if($request->ranks=='apc')
            {
                //echo("APC");exit;
                $this->promotedToAPC($request);
            }
            elseif($request->ranks=='pc')
            {
                //echo("PC");exit;
                $this->promotedToPC($request);
            }
            //$this->promotedToAPC($request);exit;
        }
    //     $row_id = $request->request_id;
    //     $comment = $request->comment;
    //     $results = [];  
            
    //     $requestAnsar = AnsarPromotion::findOrFail($row_id);
    //     $requestAnsar->not_verified_status = 1;
    //     $requestAnsar->promoted_status = 1;
    //     $requestAnsar->save();
    //     $requestAnsar = PersonalInfo::findOrFail($row_id);
    //     $requestAnsar->verified = 2;
    //     $requestAnsar->save();
        
    //    $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') has been verified!'];
    //     return Response::json($results);
       // $this->ab($request);
    }

    function rankPromotion(Request $request)
    {  
        echo "AnikZz";exit;
        $row_id = $request->request_id;
        $rank = $request->rank;
        $results = [];   

        if($request->rank=='Ansar')
        {
            $requestAnsar = AnsarPromotion::findOrFail($row_id);
            $requestAnsar->not_verified_status = 1;
            $requestAnsar->promoted_status = 1;
            $requestAnsar->save();
            $requestAnsar = PersonalInfo::findOrFail($row_id);
            $requestAnsar->verified = 2;
            $requestAnsar->save();
        }
        if($request->rank=='APC')
        {
            $requestAnsar = AnsarPromotion::findOrFail($row_id);
            $requestAnsar->not_verified_status = 1;
            $requestAnsar->promoted_status = 1;
            $requestAnsar->save();
            $requestAnsar = PersonalInfo::findOrFail($row_id);
            $requestAnsar->verified = 2;
            $requestAnsar->verifyRankPromotionation_id = 2;
            $requestAnsar->save();
        }
        if($request->rank=='PC')
        {
            $requestAnsar = AnsarPromotion::findOrFail($row_id);
            $requestAnsar->not_verified_status = 1;
            $requestAnsar->promoted_status = 1;
            $requestAnsar->save();
            $requestAnsar = PersonalInfo::findOrFail($row_id);
            $requestAnsar->verified = 2;
            $requestAnsar->designation_id = 3;
            $requestAnsar->save();
        }
        
       $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') has been verified!'];
        return Response::json($results);
    }

    function makeVerified(Request $request)
    {  
        // if($request->makeVerified==false)
        // {
        //     echo "false";exit;
        //     $row_id = $request->request_id;
        //     $results = [];  
                
        //     $requestAnsar = AnsarPromotion::findOrFail($row_id);
        //     $requestAnsar->not_verified_status = 1;
        //     $requestAnsar->save();
            
        //     $results = ['status' => true, 'message' => 'Ansar Verified Status has been updated!'];
        //     return Response::json($results);
        // }
        // elseif($request->makeVerified==true)
        // {
            //echo "true";exit;
            $row_id = $request->request_id;
            $results = [];  
                //echo "anik";exit;
            $requestAnsar = AnsarPromotion::findOrFail($row_id);
            $ansar_id = $requestAnsar->ansar_id;
            $requestAnsar->not_verified_status = 1;
            $requestAnsar->save();
            $requestAnsarPersonalInfo = PersonalInfo::where('ansar_id',$ansar_id)->first();
            $requestAnsarPersonalInfo->verified = 2;
            $requestAnsarPersonalInfo->save();
			
			$requestAnsarStatusInfo = AnsarStatusInfo::where('ansar_id',$ansar_id)->first();
            $requestAnsarStatusInfo->free_status = 1;
            $requestAnsarStatusInfo->save();

            $results = ['status' => true, 'message' => 'Ansar verified status has been updated!'];
            return Response::json($results);
        //}

    }

    function makeVerifiedByFile(Request $request)
    {  
        
        DB::enableQueryLog();
        $file = $request->file("applicant_id_list");
        $date = Carbon::yesterday()->format('d-M-Y H:i:s');
        $applicant_ids = "";
        $selected_ansars = [];
        
        Excel::load($file,function ($reader) use(&$applicant_ids){
            $applicant_ids = array_flatten($reader->limitColumns(1)->first());
        });
        
        $applicants = AnsarPromotion::where('circular_id',$request->circular)->where('not_verified_status',0)->whereIn('ansar_id',$applicant_ids)->get();
        $selected_ansars = $request->input('ansar_id');
        foreach($applicants as $applicant)
        {
            $selected_ansars[]= $applicant->ansar_id;
        }
        //print_r($selected_ansars);exit;
        DB::beginTransaction();
        $user = [];
        try{
           
            if (!is_null($selected_ansars)) {
                for ($i = 0; $i < count($selected_ansars); $i++) {
                      
                    // if($request->makeVerifiedCheckBox==false)
                    // {
                    //     //echo("Just Varified");exit;
                    //     $results = [];  
                    //     $requestAnsar = AnsarPromotion::where('ansar_id',$selected_ansars[$i])->firstOrFail();
                    //     //print_r($requestAnsar);exit;

                    //     $requestAnsar->not_verified_status = 1;
                    //     $requestAnsar->save();
                        
                    //     //$results = ['status' => true, 'message' => 'Ansar Varified Status has been updated!'];
                    //     //return Response::json($results);
                    // }
                    // elseif($request->makeVerifiedCheckBox==true)
                    // {
                        //echo ("Varified with personal info");exit;
                         
                        $requestAnsar = AnsarPromotion::where('ansar_id',$selected_ansars[$i])->firstOrFail();
                        
                        $requestAnsar->not_verified_status = 1;
                        $requestAnsar->save();
                        $requestAnsarPersonalInfo = PersonalInfo::where('ansar_id',$selected_ansars[$i])->firstOrFail();
                        $requestAnsarPersonalInfo->verified = 2;
                        $requestAnsarPersonalInfo->save();
						
						$requestAnsarStatusInfo = AnsarStatusInfo::where('ansar_id',$selected_ansars[$i])->first();
                        $requestAnsarStatusInfo->free_status = 1;
                        $requestAnsarStatusInfo->save();

                       // $results = ['status' => true, 'message' => 'Ansar varified status has been updated with Personal Info!'];
                        //return Response::json($results);
                    //}
                }
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        return redirect()->back()->with("success_message","Applicants successfully Make Verified...");

    }

    function rankUpdateByFile(Request $request)
    {  
        DB::enableQueryLog();
        $file = $request->file("applicant_id_list");
        $date = Carbon::yesterday()->format('d-M-Y H:i:s');
        $applicant_ids = "";
        $selected_ansars = [];
        
        Excel::load($file,function ($reader) use(&$applicant_ids){
            $applicant_ids = array_flatten($reader->limitColumns(1)->first());
        });
        
        $applicants = AnsarPromotion::where('circular_id',$request->circular)->where('not_verified_status',1)->where('promoted_status',0)->whereIn('ansar_id',$applicant_ids)->get();
        $selected_ansars = $request->input('ansar_id');
        foreach($applicants as $applicant)
        {
            $selected_ansars[]= $applicant->ansar_id;
        }
        if(count($selected_ansars) == 0){
            
            $results = ['status' => false, 'message' => 'No Ansar is Eligible!'];
            return Response::json($results);
        }
        DB::beginTransaction();
        $user = [];
        try{
           
            if (!is_null($selected_ansars)) {
                for ($i = 0; $i < count($selected_ansars); $i++) {
                    $requestAnsar = AnsarPromotion::where('ansar_id',$selected_ansars[$i])->firstOrFail();

                    if($requestAnsar->promoted_rank == 2)
                    {
                        $row_id = $request->request_id;
                        $requestAnsarPromotionData = AnsarPromotion::where('ansar_id',$selected_ansars[$i])->firstOrFail();
                        $requestedAnsar = $requestAnsarPromotionData->ansar_id;
                        $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
                        
                        AnsarPromotionInfo::create([
                            'ansar_id' => $requestAnsar->ansar_id,
                            'circular_id' => $requestAnsar->circular_id,
                            'promoted_rank' => $requestAnsar->promoted_rank,
                            'promoted_date' => Carbon::now()->format('Y-m-d'),
                            'action_user_id' => auth()->user()->id
                        ]);  
                        // $requestAnsar = AnsarPromotion::where('ansar_id',$selected_ansars[$i])->firstOrFail();
                        $requestAnsarPromotionData->status = "Completed";
                        $requestAnsarPromotionData->promoted_status = 1;
                        $requestAnsarPromotionData->save();
                        $ansarUpdatedData->designation_id = 2;
                        $ansarUpdatedData->save();
                    }
                    elseif($requestAnsar->promoted_rank == 3)
                    {
                        $row_id = $request->request_id;
                        $requestAnsarPromotionData = AnsarPromotion::where('ansar_id',$selected_ansars[$i])->firstOrFail();
                        $requestedAnsar = $requestAnsarPromotionData->ansar_id;
                        $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
                        
                        AnsarPromotionInfo::create([
                            'ansar_id' => $requestAnsar->ansar_id,
                            'circular_id' => $requestAnsar->circular_id,
                            'promoted_rank' => $requestAnsar->promoted_rank,
                            'promoted_date' => Carbon::now()->format('Y-m-d'),
                            'action_user_id' => auth()->user()->id
                        ]);  
                        // $requestAnsar = AnsarPromotion::where('ansar_id',$selected_ansars[$i])->firstOrFail();
                        $requestAnsarPromotionData->promoted_status = 1;
                        $requestAnsarPromotionData->save();
                        $ansarUpdatedData->designation_id = 3;
                        $ansarUpdatedData->save();
                    }
                    
                        
                }
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        return redirect()->back()->with("success_message","Applicants Rank Status Successfully Updated...");

    }

    // function rankUpdate(Request $request)
    // {  
    //     $row_id = $request->request_id;
    //     $requestAnsar = AnsarPromotion::findOrFail($row_id);
        
    //     echo $requestAnsar->promoted_rank;exit;
    //     $requestAnsar->promoted_rank = 1;
    //     $requestAnsar->save();
       
    //     //echo "AnikZz";exit;
    //     if($request->rankUpdate==0)
    //     {
    //         //echo("Just rank status updated");exit;
            
    //         $row_id = $request->request_id;
    //         $results = [];  
                
    //         $requestAnsar = AnsarPromotion::findOrFail($row_id);
    //         $requestAnsar->promoted_status = 1;
    //         $requestAnsar->save();
            
    //         $results = ['status' => true, 'message' => 'Ansar Promotion Status has been updated!'];
    //         return Response::json($results);
    //     }
    //     elseif($request->rankUpdate==1)
    //     {
    //         if($request->ranks=='apc')
    //         {
                
    //             $row_id = $request->request_id;
    //             $results = [];   
    //             $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
    //             $requestedAnsar = $requestAnsarPromotionData->ansar_id;
    //             $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
            
    //             if($ansarUpdatedData->designation_id==2)
    //             {
    //                 $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted APC to APC!'];
    //                 return Response::json($results);
    //             }
    //             else{
    //                 $requestAnsarPromotionData->promoted_status = 1;
    //                 $requestAnsarPromotionData->save();
    //                 $ansarUpdatedData->designation_id = 2;
    //                 $ansarUpdatedData->save();  
                    
    //                 $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') has been successfully promoted to APC!'];
    //                 return Response::json($results);
    //             }
    //         }
    //         elseif($request->ranks=='pc')
    //         {
                
    //             $row_id = $request->request_id;
    //             $results = [];   

    //             $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
    //             $requestedAnsar = $requestAnsarPromotionData->ansar_id;
    //             $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
                
    //             if($ansarUpdatedData->designation_id==3)
    //             {
    //                 $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted PC to PC!'];
    //                 return Response::json($results);
    //             }
    //             elseif($ansarUpdatedData->designation_id==1)
    //             {
    //                 $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted Ansar to PC!'];
    //                 return Response::json($results);
    //             }
    //             else{
    //                 $requestAnsarPromotionData->promoted_status = 1;
    //                 $requestAnsarPromotionData->save();
    //                 $ansarUpdatedData->designation_id = 3;
    //                 $ansarUpdatedData->save();  
                    
    //                 $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') has been successfully promoted to PC!'];
    //                 return Response::json($results);
    //             }
    //         }
    //     }

    // }

    function rankUpdate(Request $request)
    {  
         $row_id = $request->request_id;
         $requestAnsar = AnsarPromotion::findOrFail($row_id);
         
            if($requestAnsar->promoted_rank == 2)
            {
                
                $row_id = $request->request_id;
                $results = [];   
                $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
                $requestedAnsar = $requestAnsarPromotionData->ansar_id;
                $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
            
                if($ansarUpdatedData->designation_id==2)
                {
                    $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted APC to APC!'];
                    return Response::json($results);
                }
                else{
                    AnsarPromotionInfo::create([
                        'ansar_id' => $requestAnsar->ansar_id,
                        'circular_id' => $requestAnsar->circular_id,
                        'promoted_rank' => $requestAnsar->promoted_rank,
                        'promoted_date' => Carbon::now()->format('Y-m-d'),
                        'action_user_id' => auth()->user()->id
                    ]);  
                    $requestedAnsar = $requestAnsarPromotionData->ansar_id;
                    $requestAnsarPromotionData->promoted_status = 1;
                    $requestAnsarPromotionData->status = "Completed";
                    $requestAnsarPromotionData->save();
                    $ansarUpdatedData->designation_id = 2;
                    $ansarUpdatedData->save();  
                    
                    $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') has been successfully promoted to APC!'];
                    return Response::json($results);
                }
            }
            elseif($requestAnsar->promoted_rank == 3)
            {
                
                $row_id = $request->request_id;
                $results = [];   

                $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
                $requestedAnsar = $requestAnsarPromotionData->ansar_id;
                $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
                
                if($ansarUpdatedData->designation_id==3)
                {
                    $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted PC to PC!'];
                    return Response::json($results);
                }
                elseif($ansarUpdatedData->designation_id==1)
                {
                    $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted Ansar to PC!'];
                    return Response::json($results);
                }
                else{
                    AnsarPromotionInfo::create([
                        'ansar_id' => $requestAnsar->ansar_id,
                        'circular_id' => $requestAnsar->circular_id,
                        'promoted_rank' => $requestAnsar->promoted_rank,
                        'promoted_date' => Carbon::now()->format('Y-m-d'),
                        'action_user_id' => auth()->user()->id
                    ]); 
                    $requestAnsarPromotionData->promoted_status = 1;
                    $requestAnsarPromotionData->save();
                    $ansarUpdatedData->designation_id = 3;
                    $ansarUpdatedData->save();  
                    
                    $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') has been successfully promoted to PC!'];
                    return Response::json($results);
                }
            }
        

    }

    function promotedToAPC(Request $request)
    {  
        
        $row_id = $request->request_id;
        $results = [];   
        $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
        $requestAnsarPromotionData->promoted_status = 1;
        $requestAnsarPromotionData->save();
        $requestedAnsar = $requestAnsarPromotionData->ansar_id;
        $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
       
        if($ansarUpdatedData->designation_id==2)
        {
            $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted APC to APC!'];
            return Response::json($results);
        }
        else{
            $ansarUpdatedData->designation_id = 2;
            $ansarUpdatedData->save();  
            
            $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') has been successfully promoted to APC!'];
            return Response::json($results);
        }
        
    }

    function promotedToPC(Request $request)
    {  
        //echo "AnikZz";exit;
        $row_id = $request->request_id;
        $results = [];   

        $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
        $requestAnsarPromotionData->promoted_status = 1;
        $requestAnsarPromotionData->save();
        $requestedAnsar = $requestAnsarPromotionData->ansar_id;
        $ansarUpdatedData = PersonalInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
        
        if($ansarUpdatedData->designation_id==3)
        {
            $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted PC to PC!'];
            return Response::json($results);
        }
        elseif($ansarUpdatedData->designation_id==1)
        {
            $results = ['status' => false, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') can not be promoted Ansar to PC!'];
            return Response::json($results);
        }
        else{
            $ansarUpdatedData->designation_id = 3;
            $ansarUpdatedData->save();  
            
            $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsarPromotionData->ansar_id . ') has been successfully promoted to PC!'];
            return Response::json($results);
        }
        
    }

    function backtoPrevious(Request $request)
    {  
        $row_id = $request->request_id;
        $results = [];   

        $requestAnsarPromotionData = AnsarPromotion::findOrFail($row_id);
        $requestedAnsar = $requestAnsarPromotionData->ansar_id;
        $requestedAnsar_circular_id = $requestAnsarPromotionData->circular_id;
        //print_r($requestedAnsar_circular_id);exit;
        $ansarStatusLogData = AnsarPromotionStatusLog::where('ansar_id', $requestedAnsar)->where('circular_id', $requestedAnsar_circular_id)->firstOrFail();
        //print_r($ansarStatusLogData);exit;
        $ansarStatusData = AnsarStatusInfo::where('ansar_id', $requestedAnsar)->firstOrFail();
        $ansarStatusData->free_status = $ansarStatusLogData->free_status;
        $ansarStatusData->pannel_status = $ansarStatusLogData->pannel_status;
        $ansarStatusData->offer_sms_status = $ansarStatusLogData->offer_sms_status;
        $ansarStatusData->offered_status = $ansarStatusLogData->offered_status;
        $ansarStatusData->embodied_status = $ansarStatusLogData->embodied_status;
        $ansarStatusData->offer_block_status = $ansarStatusLogData->offer_block_status;
        $ansarStatusData->freezing_status = $ansarStatusLogData->freezing_status;
        $ansarStatusData->early_retierment_status = $ansarStatusLogData->early_retierment_status;
        $ansarStatusData->block_list_status = $ansarStatusLogData->block_list_status;
        $ansarStatusData->black_list_status = $ansarStatusLogData->black_list_status;
        $ansarStatusData->rest_status = $ansarStatusLogData->rest_status;
        $ansarStatusData->retierment_status = $ansarStatusLogData->retierment_status;
        $ansarStatusData->save();  
        $requestAnsarPromotionData->delete(); 
        $ansarStatusLogData->delete();
        
       $results = ['status' => true, 'message' => 'Ansar ('.$ansarStatusLogData->ansar_id . ') has been successfully back to previous!'];
        return Response::json($results);
    }

}
