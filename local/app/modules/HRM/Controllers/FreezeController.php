<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Facades\UserPermissionFacades;
use App\Http\Controllers\Controller;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\BlackListModel;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\FreezedAnsarEmbodimentDetail;
use App\modules\HRM\Models\FreezingInfoLog;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\Login;
use App\modules\HRM\Models\MemorandumModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\TransferAnsar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;

class FreezeController extends Controller
{
    //  Do the freeze by id for law breaking 
    //view
    public function freezeView()
    {
        return View::make('HRM::Freeze.freeze_view');
    }

    //submit
    public function loadAnsarDetailforFreeze(Request $request)
    {
        $ansar_id = Input::get('ansar_id');
        $ansar_details = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_embodiment.ansar_id', '=', $ansar_id)
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->where('tbl_ansar_status_info.black_list_status', '=', 0)
			->where('tbl_ansar_status_info.embodied_status', '=', 1)
            ->select('tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_ansar_parsonal_info.ansar_name_eng as name',
                'tbl_ansar_parsonal_info.data_of_birth as dob', 'tbl_ansar_parsonal_info.sex', 'tbl_kpi_info.id', 'tbl_kpi_info.kpi_name as kpi', 'tbl_designations.name_eng as rank',
                'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana', 'tbl_kpi_detail_info.kpi_withdraw_date as withdraw_date', 'tbl_kpi_info.withdraw_status');
        if ($request->exists('unit')) {
            $ansar_details->where('tbl_kpi_info.unit_id', $request->unit);
        }
        if ($request->exists('range')) {
            $ansar_details->where('tbl_kpi_info.division_id', $request->range);
        }
        return Response::json($ansar_details->first());
    }

    public function freezeEntry(Request $request)
    {

        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|unique:tbl_freezing_info,ansar_id',
            'freeze_date' => 'required',
            'memorandum_id' => 'required',
            'freeze_reason' => 'required',
            'freeze_comment' => 'required',
        ];
        //$this->validate($request, $rules);


        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
           return Response::json(['status' => false, 'message' => $valid->messages()->toJson()]);
        }
        $ansar_id = $request->input('ansar_id');
        $freeze_date = $request->input('freeze_date');
        $freeze_comment = $request->input('freeze_comment');
        $memorandum_id = $request->input('memorandum_id');
        $modifed_freeze_date = Carbon::parse($freeze_date);

        DB::beginTransaction();
        try {
            $memorandum_info = new MemorandumModel();
            $memorandum_info->memorandum_id = $memorandum_id;
            $memorandum_info->save();

            $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
			$ansar_status_info = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
			
			if($ansar_status_info->embodied_status != 1) throw new \Exception("Ansar is not eligible for freeze, It is not in embodied status!");         
            if ($request->exists('unit') && $embodiment_info->kpi->unit_id != $request->unit) throw new \Exception("Invalid Ansar for freeze");
            if ($request->exists('range') && $embodiment_info->kpi->division_id != $request->range) throw new \Exception("Invalid Ansar for freeze");
            if (!$embodiment_info && $embodiment_info->emboded_status == "freeze") throw new \Exception("Invalid Ansar for freeze");
            if (Carbon::parse($embodiment_info->joining_date)->gt($modifed_freeze_date)) throw new \Exception("Freeze date must be greater then embodiment date");

            // Added By Rintu - Instruction From Ansar ICT - Only Admit User Can User Back Date
            if(Auth::user()->type != 11){

                if (!$modifed_freeze_date->isToday()) throw new \Exception("Freeze date will be current date!");
            }

            $kpi_data = KpiGeneralModel::with(['unit'])->find($embodiment_info->kpi_id);

            $unit_id = $kpi_data->unit_id;
            $freeze_info = new FreezingInfoModel();
            $freeze_info->ansar_id = $ansar_id;
            $freeze_info->freez_reason = $request->freeze_reason;
            $freeze_info->freez_date = $modifed_freeze_date;
            $freeze_info->comment_on_freez = $freeze_comment;
            $freeze_info->memorandum_id = $memorandum_id;
            $freeze_info->kpi_id = $embodiment_info->kpi_id;
            $freeze_info->ansar_embodiment_id = $embodiment_info->id;
           // $freeze_info->embodiment_date = $embodiment_info->joining_date;
            $freeze_info->action_user_id = Auth::user()->id;
            $freeze_info->save();
            FreezedAnsarEmbodimentDetail::create([
                'freezed_id' => $freeze_info->id,
                'freezed_kpi_id' => $embodiment_info->kpi_id,
                'freezed_joining_kpi_id' => $embodiment_info->joining_kpi_id,
                'embodiment_id' => $embodiment_info->id,
                'em_mem_id' => $embodiment_info->memorandum_id,
                'ansar_id' => $ansar_id,
                'embodied_date' => $embodiment_info->joining_date,
                'transfer_date' => $embodiment_info->transfered_date,
                'reporting_date' => $embodiment_info->reporting_date,
                'service_ended_date' => $embodiment_info->service_ended_date
            ]);
            $embodiment_info->delete();
            AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 1]);
            CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'FREEZE', 'from_state' => 'EMBODIED', 'to_state' => 'FREEZE', 'action_by' => auth()->user()->id]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            //return Redirect::back()->with('error_message', $e->getMessage());
            return Response::json(['status' => false, 'message' => $e->getMessage()]);

        }
        //return "aise";
       // return Redirect::back()->with('success_message', 'Ansar Freezed for Disciplinary Action Successfully!');
        
        $letter = [];
        $letter['option'] = 'memorandumNo';
        $letter['id'] = $memorandum_id;
        $letter['type'] = 'FREEZ';
        $letter['unit'] = $unit_id;
        $letter['status'] = true;
        
        return Response::json(['status' => true, 'message' => 'Ansar is Freezed Successfully!', 'printData' => $letter]);


    }

//    show freeze list
    public function freezeList()
    {
        return View::make('HRM::Freeze.freezelist');
    }

    public function getfreezelist(Request $request)
    {
        $rules = [
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'range' => ['regex:/^(all)$|^[0-9]+$/'],
            'kpi' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return response($valid->messages()->toJson(), 422, ['Content-Type' => 'application/json']);
        }
        $data = CustomQuery::getFreezeListWithRankGender($request->range, $request->unit, $request->thana, $request->kpi, ($request->limit ? $request->limit : 50), $request->q, $request->export, $request->rank, $request->gender, $request->freez_reason);
        if ($request->exists('export') && $request->export == 1) {
            return Excel::create('freeze_report', function ($excel) use ($data) {
                $excel->sheet('sheet1', function ($sheet) use ($data) {
                    $sheet->loadView('HRM::export.freezelist', ['allFreezeAnsar' => $data]);
                });
            })->export('xlsx');
        }
        return $data;
    }

    public function freezeRembodied(Request $request)
    {
//        return $request->all();
        $ansarids = $request->ansarId;
        $results = [];
        if (is_array($ansarids)) {

            foreach ($ansarids as $ansarid) {
                DB::beginTransaction();
                try {
                    $frezeInfo = FreezingInfoModel::where('ansar_id', $ansarid)->first();
                    if (!$frezeInfo) throw new \Exception("{$ansarid} is invalid");
                    $kpi = $frezeInfo->kpi;
                    if (!$kpi) throw new \Exception("invalid kpi");
                    if ($kpi && $kpi->withdraw_status == 1 && $kpi->status_of_kpi == 0 && !$kpi->details->kpi_withdraw_date) throw new \Exception("{$kpi->kpi_name} already withdrawn");
                    $updateEmbodiment = $frezeInfo->embodiment;
                    $freezed_ansar_embodiment_detail = $frezeInfo->freezedAnsarEmbodiment;
//                    return $updateEmbodiment;
                    if (!($updateEmbodiment || $freezed_ansar_embodiment_detail)) throw new \Exception("{$ansarid} is invalid");
                    $kpi = $updateEmbodiment ? $updateEmbodiment->kpi : $freezed_ansar_embodiment_detail->kpi;

                    if (!$kpi) throw new \Exception("This ansar kpi not found");
                    if (!$kpi || $kpi->status_of_kpi == 0 || $kpi->withdraw_status == 1) throw new \Exception("This ansar id:{$ansarid} kpi already withdraw");
                    $date = !$request->unfreeze_date ? Carbon::now() : Carbon::parse($request->unfreeze_date);
//                    return Response::json(['status' => true, 'message' => $date->gt(Carbon::now())]);
                    if ($date->lt(Carbon::parse($frezeInfo->freez_date)) || $date->gt(Carbon::now())) throw new \Exception('Unfreeze date can`t be smaller then freeze date or greater then current date');
					
                    $date_differ = $date->diffInDays(Carbon::parse($frezeInfo->freez_date), true);
					
                    /*FreezingInfoLog::create([
                        'old_freez_id' => $frezeInfo->id,
                        'ansar_embodiment_id' => $frezeInfo->ansar_embodiment_id,
                        'freez_reason' => $frezeInfo->freez_reason,
                        'freez_date' => $frezeInfo->freez_date,
                        'comment_on_freez' => $frezeInfo->comment_on_freez,
                        'comment_on_move' => 'Continue Service',
                        'move_to' => 'Emodiment',
                        'move_frm_freez_date' => $date,
                        'ansar_id' => $ansarid,
                    ]);*/
					
					
					FreezingInfoLog::create([
                            'old_freez_id' => $frezeInfo->id,
                            'ansar_id' => $ansarid,
                            'freez_reason' => $frezeInfo->freez_reason,
                            'freez_date' => $frezeInfo->freez_date,
                            'ansar_embodiment_id' => $frezeInfo->ansar_embodiment_id,
                            'comment_on_freez' => $frezeInfo->comment_on_freez,
                            'move_frm_freez_date' => $date,
                            'move_to' => 'Emodiment',
                            'comment_on_move' => 'Continue Service',
                            'action_user_id' => Auth::user()->id,
                        ]);

					
					
                    $frezeInfo->delete();
                    if ($updateEmbodiment) {
                        if ($request->include_freeze_date === 1) $updateEmbodiment->service_ended_date = Carbon::parse($updateEmbodiment->service_ended_date)->addDays($date_differ);
                        $updateEmbodiment->emboded_status = 'Emboded';
                        $updateEmbodiment->save();
                    } else if ($freezed_ansar_embodiment_detail) {
                        if ($request->include_freeze_date === 1) $service_ended_date = Carbon::parse($freezed_ansar_embodiment_detail->service_ended_date)->addDays($date_differ);
                        else  $service_ended_date = $freezed_ansar_embodiment_detail->service_ended_date;
                        EmbodimentModel::create([
                            'ansar_id' => $freezed_ansar_embodiment_detail->ansar_id,
                            'kpi_id' => $freezed_ansar_embodiment_detail->freezed_kpi_id,
                            'joining_kpi_id' => $freezed_ansar_embodiment_detail->freezed_joining_kpi_id,
                            'received_sms_id' => 0,
                            'emboded_status' => 'Emboded',
                            'action_user_id' => Auth::user()->id,
                            'service_ended_date' => $service_ended_date,
                            'memorandum_id' => $freezed_ansar_embodiment_detail->em_mem_id,
                            'reporting_date' => $freezed_ansar_embodiment_detail->reporting_date,
                            'transfered_date' => $freezed_ansar_embodiment_detail->transfer_date,
                            'joining_date' => $freezed_ansar_embodiment_detail->embodied_date,
                        ]);
                        $freezed_ansar_embodiment_detail->delete();
                    }
                    AnsarStatusInfo::where('ansar_id', $ansarid)->update(['freezing_status' => 0, 'embodied_status' => 1]);
                    CustomQuery::addActionlog(['ansar_id' => $ansarid, 'action_type' => 'EMBODIED', 'from_state' => 'FREEZE', 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
                    DB::commit();
                    array_push($results, ['status' => true, 'message' => $ansarid . ' Re-embodied successfully']);
                } catch (\Exception $rollback) {
                    DB::rollback();
                    array_push($results, ['status' => false, 'message' => $rollback->getMessage()]);
                }
            }
            return Response::json($results);
        } else {
            return Response::json(['status' => false, 'message' => 'Invalid Request']);
        }
    }

    public function freezeDisEmbodied(Request $request)
    {
//        return $request->all();
        /*if (is_array($request->ansarId)) {
            foreach ($request->ansarId as $ansarId) {
                $frezeInfo = FreezingInfoModel::where('ansar_id', $ansarId)->first();
                $embodiment = $frezeInfo->embodiment;
                $freezed_ansar_embodiment_detail = $frezeInfo->freezedAnsarEmbodiment;


                DB::beginTransaction();
                try {
                    if (!$frezeInfo || !($embodiment || $freezed_ansar_embodiment_detail)) throw new \Exception("Invalid Request");
                    $m = new MemorandumModel;
                    $m->memorandum_id = $request->memorandum;
                    $m->save();
                    FreezingInfoLog::create([
                        'old_freez_id' => $frezeInfo->id,
                        'ansar_id' => $ansarId,
                        'freez_reason' => $frezeInfo->freez_reason,
                        'comment_on_freez' => $frezeInfo->comment_on_freez,
                        'move_frm_freez_date' => Carbon::parse($request->rest_date)->format('Y-m-d'),
                        'move_to' => 'rest',
                        'comment_on_move' => $request->comment ? $request->comment : 'No Comment',
                    ]);
                    EmbodimentLogModel::create([
                        'old_embodiment_id' => $embodiment ? $embodiment->id : $freezed_ansar_embodiment_detail->embodiment_id,
                        'old_memorandum_id' => $embodiment ? $embodiment->memorandum_id : $freezed_ansar_embodiment_detail->em_mem_id,
                        'ansar_id' => $ansarId,
                        'reporting_date' => $embodiment ? $embodiment->reporting_date : $freezed_ansar_embodiment_detail->reporting_date,
                        'joining_date' => $embodiment ? $embodiment->joining_date : $freezed_ansar_embodiment_detail->embodied_date,
                        'kpi_id' => $embodiment ? $embodiment->kpi_id : $freezed_ansar_embodiment_detail->freezed_kpi_id,
                        'move_to' => 'rest',
                        'disembodiment_reason_id' => $request->disembodiment_reason_id,
                        'release_date' => Carbon::parse($request->rest_date)->format('Y-m-d'),
                        'action_user_id' => Auth::id(),
                    ]);
                    RestInfoModel::create([
                        'ansar_id' => $ansarId,
                        'old_embodiment_id' => $embodiment ? $embodiment->id : $freezed_ansar_embodiment_detail->embodiment_id,
                        'memorandum_id' => $request->memorandum,
                        'rest_date' => Carbon::parse($request->rest_date)->format('Y-m-d'),
                        'active_date' => Carbon::parse($request->rest_date)->addMonths(6)->format('Y-m-d'),
                        'disembodiment_reason_id' => $request->disembodiment_reason_id,
                        'total_service_days' => Carbon::parse($embodiment ? $embodiment->joining_date : $freezed_ansar_embodiment_detail->embodied_date)->diffInDays(Carbon::parse(Input::get('rest_date')), true),
                        'rest_form' => 'Freeze',
                        'comment' => $request->comment ? $request->comment : 'No Comment',
                        'action_user_id' => Auth::id(),
                    ]);
                    $frezeInfo->delete();
                    if ($embodiment) $embodiment->delete();
                    if ($freezed_ansar_embodiment_detail) $freezed_ansar_embodiment_detail->delete();
                    AnsarStatusInfo::where('ansar_id', $ansarId)->update([
                        'rest_status' => 1,
                        'freezing_status' => 0
                    ]);
                    CustomQuery::addActionlog(['ansar_id' => $ansarId, 'action_type' => 'DISEMBODIMENT', 'from_state' => 'FREEZE', 'to_state' => 'REST', 'action_by' => auth()->user()->id]);
                    DB::commit();


//            throw new Exception();
                } catch (\Exception $rollback) {
                    DB::rollback();
                    return Response::json(['status' => false, 'message' => $rollback->getMessage()]);
                }
            }
            return Response::json(['status' => true, 'message' => 'dis-embodied successfully']);
        } else {
            return Response::json(['status' => false, 'message' => "Invalid Request"]);
        }*/
        return CustomQuery::freezeDisEmbodied($request);
    }

    public function transferFreezedAnsar(Request $request)
    {
        $ansarids = $request->ansarIds;
        if (is_array($ansarids)) {
            DB::beginTransaction();
            foreach ($ansarids as $ansarid) {
                try {

                    if(Carbon::parse($request->joining_date)->lt(Carbon::now())){
                        throw new \Exception("Joining Date cannot be back date");

                    }
                    $m = new MemorandumModel;
                    $m->memorandum_id = $request->memorandum_transfer;
                    $m->save();

                    $frezeInfo = FreezingInfoModel::where('ansar_id', $ansarid)->first();

                    $prev_embodied_id = $frezeInfo->ansar_embodiment_id;

                    $t_history = TransferAnsar::where('ansar_id', $ansarid)->where('embodiment_id', $prev_embodied_id)->pluck('present_kpi_id');

                    if(count($t_history)){
                        if (in_array($request->selectedKpi, collect($t_history)->toArray())) {
                            throw new \Exception("Ansar(" . $ansarid . ") previously transferred or embodied in this kpi");
                        }
                    }

                    $updateEmbodiment = $frezeInfo->embodiment;
                    if($updateEmbodiment){
                        $prev_kpi_id = $updateEmbodiment->kpi_id;
                    }
                    $freezed_ansar_embodiment_detail = $frezeInfo->freezedAnsarEmbodiment;
                    $date = Carbon::now();
                    $date_differ = $date->diffInDays(Carbon::parse($frezeInfo->freez_date), true);
					
                    //if (!($frezeInfo || $updateEmbodiment))
                        FreezingInfoLog::create([
                            'old_freez_id' => $frezeInfo->id,
                            'ansar_id' => $ansarid,
                            'freez_reason' => $frezeInfo->freez_reason,
                            'freez_date' => $frezeInfo->freez_date,
                            'ansar_embodiment_id' => $frezeInfo->ansar_embodiment_id,
                            'comment_on_freez' => $frezeInfo->comment_on_freez,
                            'move_frm_freez_date' => $date,
                            'move_to' => 'Emodiment',
                            'comment_on_move' => 'Transfer Freeze Ansar',
                            'action_user_id' => Auth::user()->id,
                        ]);

                    $frezeInfo->delete();
                    if ($freezed_ansar_embodiment_detail) {
                        $prev_kpi_id = $freezed_ansar_embodiment_detail->freezed_kpi_id;
                        if (!UserPermissionFacades::userPermissionExists('include_freeze_days') || ($request->exists('include_freeze_date') && intval($request->include_freeze_date) === 1)) $service_ended_date = Carbon::parse($freezed_ansar_embodiment_detail->service_ended_date)->addDays($date_differ);
                        else  $service_ended_date = $freezed_ansar_embodiment_detail->service_ended_date;
                        $updateEmbodiment = EmbodimentModel::create([
                            'id'=> $freezed_ansar_embodiment_detail->embodiment_id,
                            'ansar_id' => $freezed_ansar_embodiment_detail->ansar_id,
                            'kpi_id' => $request->selectedKpi,
                            'joining_kpi_id' => $freezed_ansar_embodiment_detail->freezed_joining_kpi_id,
                            'received_sms_id' => 0,
                            'emboded_status' => 'Emboded',
                            'action_user_id' => Auth::user()->id,
                            'service_ended_date' => $service_ended_date,
                            'memorandum_id' => $freezed_ansar_embodiment_detail->em_mem_id,
                            'reporting_date' => $freezed_ansar_embodiment_detail->reporting_date,
                            'transfered_date' => $freezed_ansar_embodiment_detail->transfer_date,
                            'joining_date' => $freezed_ansar_embodiment_detail->embodied_date,
                        ]);
                        $freezed_ansar_embodiment_detail->delete();
                    }
                    $transfer = new TransferAnsar;
                    $transfer->ansar_id = $ansarid;
                    $transfer->embodiment_id = $updateEmbodiment->id;
                    $transfer->transfer_memorandum_id = $request->memorandum_transfer;
                    //$transfer->present_kpi_id = $updateEmbodiment->kpi_id;
                    $transfer->present_kpi_id = $prev_kpi_id;
                    $transfer->transfered_kpi_id = $request->selectedKpi;
                    $transfer->present_kpi_join_date = $updateEmbodiment->transfered_date;
                    $transfer->transfered_kpi_join_date = Carbon::parse($request->joining_date)->format("Y-m-d");
                    $transfer->action_by = Auth::user()->id;
                    $transfer->save();
                    if (!UserPermissionFacades::userPermissionExists('include_freeze_days') || ($request->exists('include_freeze_date') && intval($request->include_freeze_date) === 1)) $updateEmbodiment->service_ended_date = Carbon::parse($updateEmbodiment->service_ended_date)->addDays($date_differ);
                    $updateEmbodiment->emboded_status = 'Emboded';
                    $updateEmbodiment->transfered_date = Carbon::parse($request->joining_date)->format("Y-m-d");
                    $updateEmbodiment->kpi_id = $request->selectedKpi;
                    $updateEmbodiment->save();


                    AnsarStatusInfo::where('ansar_id', $ansarid)->update(['freezing_status' => 0, 'embodied_status' => 1]);
                    CustomQuery::addActionlog(['ansar_id' => $ansarid, 'action_type' => 'EMBODIED', 'from_state' => 'FREEZE', 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
                    DB::commit();

                } catch (\Exception $rollback) {
                    DB::rollback();
                    return Response::json(['status' => false, 'message' => $rollback->getMessage()]);
                }
            }
            return Response::json(['status' => true, 'message' => "Re embodied complete successfully"]);
        } else {
            return Response::json(['status' => false, 'message' => "Invalid request"]);
        }
    }

    public function freezeBlack(Request $request)
    {
        $ansar_ids = $request->ansarid;
        $black_date = $request->input('black_date');
        $black_comment = $request->input('black_comment');
        if (is_array($ansar_ids)) {
            foreach ($ansar_ids as $ansar_id) {
                DB::beginTransaction();
                try {
                    $frezeInfo = FreezingInfoModel::where('ansar_id', $ansar_id)->first();
                    $embodiment = $frezeInfo->embodiment;
                    if (!$frezeInfo || !$embodiment) throw new \Exception('Invalid request sasfssdfdsfsf');
                    FreezingInfoLog::create([
                        'old_freez_id' => $frezeInfo->id,
                        'ansar_id' => $ansar_id,
                        'freez_reason' => $frezeInfo->freez_reason,
                        'comment_on_freez' => $frezeInfo->comment_on_freez,
                        'move_frm_freez_date' => Carbon::parse($request->rest_date)->format('Y-m-d'),
                        'move_to' => 'rest',
                        'comment_on_move' => $request->comment ? $request->comment : 'No Comment',
                        'action_user_id' => Auth::user()->id
                    ]);
                    EmbodimentLogModel::create([
                        'old_embodiment_id' => $embodiment->id,
                        'old_memorandum_id' => $embodiment->memorandum_id,
                        'ansar_id' => $ansar_id,
                        'reporting_date' => $embodiment->reporting_date,
                        'joining_date' => $embodiment->joining_date,
                        'kpi_id' => $embodiment->kpi_id,
                        'move_to' => 'Blacklist',
                        'comment' => $black_comment,
                        'disembodiment_reason_id' => 0,
                        'release_date' => Carbon::parse($request->rest_date)->format('Y-m-d'),
                        'action_user_id' => Auth::id(),
                    ]);
                    BlackListModel::create([
                        'ansar_id' => $ansar_id,
                        'black_list_from' => 'Freeze',
                        'from_id' => $embodiment->id,
                        'black_listed_date' => Carbon::parse($black_date)->format('Y-m-d'),
                        'black_list_comment' => $black_comment,
                        'action_user_id' => Auth::user()->id,
                    ]);
                    $frezeInfo->delete();
                    $embodiment->delete();
                    AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['freezing_status' => 0, 'black_list_status' => 1]);
                    CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLACKED', 'from_state' => 'FREEZE', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    DB::commit();

                } catch (Exception $rollback) {
                    DB::rollback();
                    return Response::json(['status' => false, 'message' => $rollback->getMessage()]);
                }
            }
            return Response::json(['status' => true, 'message' => 'Blacklisted successfully complete!']);
        } else {
            return Response::json(['status' => false, 'message' => 'Invalid request']);
        }

    }
}
