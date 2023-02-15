<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Facades\GlobalParameterFacades;
use App\Helper\SystemSettingHelper;
use App\Http\Controllers\Controller;
use App\Jobs\DisembodiedSMS;
use App\Jobs\SendSms;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\MemorandumModel;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\ServiceExtensionModel;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\TransferAnsar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Psy\Exception\Exception;

class EmbodimentController extends Controller
{
    public function kpiName(Request $request)
    {
        DB::enableQueryLog();
        $query = [];
        if (Input::exists('division') && Input::get('division') != 'all') {
            array_push($query, ['division_id', '=', $request->division]);
        }
        if (Input::exists('unit') && Input::get('unit') != 'all') {
            array_push($query, ['unit_id', '=', $request->unit]);
        }
        if (Input::exists('id') && Input::get('id') != 'all') {
            array_push($query, ['thana_id', '=', $request->id]);
        }
        if ($request->type != 'all') {
            array_push($query, ['status_of_kpi', '=', 1]);
            array_push($query, ['withdraw_status', '=', 0]);
        }
        $kpi = KpiGeneralModel::where($query)->get();
//        return DB::getQueryLog();
        return Response::json($kpi);
    }

    public function newEmbodimentView()
    {
        $user_type = Auth::user()->type;
        $user_unit = Auth::user()->district_id;
        $user_thanas = Thana::where('unit_id', $user_unit)->get();
        $kpi_names = KpiGeneralModel::all();
        return view('HRM::Embodiment.new_embodiment_entry')->with(['user_type' => $user_type, 'user_thanas' => $user_thanas, 'kpi_names' => $kpi_names, 'user_unit' => $user_unit]);
    }

    public function embodimentDateCorrectionView()
    {
        return view('HRM::Embodiment.embodiment_date_correction_view');
    }

    public function loadAnsarForEmbodiment(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'unit' => 'required_without:ansar_id'
        ]);
        if ($valid->fails()) {
            return [];
        }
        $ansarPersonalDetail = DB::table('tbl_ansar_parsonal_info')
            ->leftJoin('tbl_ansar_bank_account_info', 'tbl_ansar_bank_account_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units as pu', 'pu.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_units as ou', 'ou.id', '=', 'tbl_sms_receive_info.offered_district')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->where('tbl_ansar_status_info.black_list_status', '=', 0)
            ->select('tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_bank_account_info.prefer_choice',
                'pu.unit_name_bng as home_district', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_designations.name_bng',
                'tbl_sms_receive_info.ansar_id', 'tbl_sms_receive_info.sms_send_datetime as offerDate', 'ou.unit_name_bng as offered_district', 'ou.id as ouid');
        if ($request->unit) {
            $ansarPersonalDetail->where('ou.id', $request->unit);
        }
        if (isset($request->gender) && !empty($request->gender) && $request->gender != 'all') {
            $ansarPersonalDetail->where('tbl_ansar_parsonal_info.sex', '=', $request->gender);
        }
        if (isset($request->rank) && is_numeric($request->rank)) {
            $ansarPersonalDetail->where('tbl_designations.id', '=', $request->rank);
        }
        if ($request->ansar_id) {
            $ansarPersonalDetail->where('tbl_ansar_parsonal_info.ansar_id', $request->ansar_id);
        }
        $detail = $ansarPersonalDetail->get();
        $ansar_ids = collect($detail)->pluck('ansar_id');
        $q = DB::table('tbl_panel_info_log')->whereIn('ansar_id', $ansar_ids)->orderBy('id', 'desc')
            ->select('panel_date', 'old_memorandum_id as memorandum_id', 'ansar_id', 're_panel_date');
        $ansarPanelInfo = collect(DB::table(DB::raw("(" . $q->toSql() . ") as t"))->mergeBindings($q)
            ->groupBy('t.ansar_id')
            ->select('t.panel_date', 't.memorandum_id', 't.ansar_id')->get());
        $apd = [];
        $temp_ansar_id = [];
        foreach ($detail as $d) {
            if(in_array($d->ansar_id, $temp_ansar_id))continue;
            array_push($temp_ansar_id, $d->ansar_id);
            $data = PanelModel::where('ansar_id', $d->ansar_id)->first();
            if (!$data) {
                $data = $ansarPanelInfo->where('ansar_id', $d->ansar_id)->first();
            }
            if ($data) {
                $pi = (array)$d;
                $pi['panel_date'] = in_array($request->unit, Config::get('app.offer')) ? $data->panel_date : $data->re_panel_date;
                $pi['memorandum_id'] = $data->memorandum_id;
                array_push($apd, $pi);
            }
        }
        return Response::json(['apd' => $apd]);
    }

    public function newEmbodimentEntry(Request $request)
    {
//        return $request->all();
        $ansar_id = $request->input('ansar_id');
        $kpi_id = $request->input('kpi_id');
        $rules = [
            'kpi_id' => 'required|numeric|regex:/^[0-9]+$/',
            'ansar_ids' => 'required|is_array|array_type:int',
            'reporting_date' => ['required', 'regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'joining_date' => ['required', 'regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/', 'joining_date_validate:reporting_date'],
            'division_name_eng' => 'required|numeric|regex:/^[0-9]+$/',
            'thana_name_eng' => 'required|numeric|regex:/^[0-9]+$/',
        ];
        if (auth()->user()->type == 11 || auth()->user()->type == 77) {
            $rules['memorandum_id'] = 'required';
        } else {
            $rules['memorandum_id'] = 'required|unique:hrm.tbl_memorandum_id,memorandum_id|unique:hrm.tbl_embodiment,memorandum_id|unique:hrm.tbl_rest_info,memorandum_id||unique:hrm.tbl_transfer_ansar,transfer_memorandum_id';
        }
        $message = [
            'ansar_ids.required' => 'Ansar ID is required',
            'ansar_id.is_eligible' => 'This Ansar Cannot be Embodied. Because the total number of Ansars in this KPI already exceed. First Transfer or Disembodied Ansar from this selected KPI.',
            'memorandum_id.required' => 'Memorandum ID is required',
            'reporting_date.required' => 'Reporting Date is required',
            'joining_date.required' => 'Embodiment Date is required',
            'division_name_eng.required' => 'Division  is required',
            'thana_name_eng.required' => 'Thana is required',
            'kpi_id.required' => 'KPI is required',
            'ansar_id.numeric' => 'Ansar ID must be numeric',
            'ansar_id.regex' => 'Ansar ID must be numeric',
            'memorandum_id.unique' => 'Memorandum ID has already been taken',
            'reporting_date.regex' => 'Reporting Date format is invalid',
            'joining_date.regex' => 'Embodiment Date format is invalid',
            'division_name_eng.numeric' => 'Division format is invalid',
            'division_name_eng.regex' => 'Division format is invalid',
            'thana_name_eng.numeric' => 'Thana format is invalid',
            'thana_name_eng.regex' => 'Thana format is invalid',
            'kpi_id.numeric' => 'KPI format is invalid',
            'kpi_id.regex' => 'KPI format is invalid',
        ];
        $valid = Validator::make($request->all(), $rules, $message);
        if ($valid->fails()) {
            return $valid->messages()->toJson();
        }
        $kpi_info = KpiGeneralModel::find($request->kpi_id);
        $embodimentInfo = $kpi_info->embodiment->count();
        $kpi_details = $kpi_info->details;
        if ($embodimentInfo + count($request->ansar_ids) > $kpi_details->total_ansar_given) {
            return Response::json(['status' => false, 'message' => "This Ansar Cannot be Embodied. Because the total number of Ansars in this KPI already exceed"]);
        };
        $memorandum_id = $request->input('memorandum_id');
        $global_value = GlobalParameterFacades::getValue("embodiment_period");
        $global_unit = GlobalParameterFacades::getUnit("embodiment_period");
        foreach ($request->ansar_ids as $ansar_id) {
            DB::beginTransaction();
            try {
                $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();

//            return $sms_receive_info->offered_district!=$request->division_name_eng?"same":"differ";
                if (!$sms_receive_info) {
                    throw new \Exception('Invalid request for Ansar ID: ' . $ansar_id);
                }
                if (Carbon::parse($sms_receive_info->sms_send_datetime)->gt(Carbon::parse($request->joining_date))) {
                    throw new \Exception('Offer date greater then 
                    embodiment date for Ansar ID: ' . $ansar_id . " .Offer date " . $sms_receive_info->sms_send_datetime);
                }
                if ($sms_receive_info->offered_district != $request->division_name_eng) {
                    throw new \Exception('Ansar ID: ' . $ansar_id . ' not offered for this district');
                }
                $kpi = KpiGeneralModel::where('unit_id', $request->division_name_eng)->where('thana_id', $request->thana_name_eng)->where('id', $kpi_id)->first();
                if (!$kpi) {
                    throw new \Exception('Invalid request for Ansar ID: ' . $ansar_id);
                }
                $embodimentInfo = $kpi_info->embodiment->count();
                $kpi_details = $kpi_info->details;
                if ($embodimentInfo + count($request->ansar_ids) > $kpi_details->total_ansar_given) {
                    throw new \Exception("This Ansar Cannot be Embodied. Because the total number of Ansars in this KPI already exceed");
                };
                if (strcasecmp($global_unit, "Year") == 0) {
                    $service_ending_period = $global_value;
                    $service_ended_date = Carbon::parse($request->input('joining_date'))->addYear($service_ending_period)->subDay(1);
                } elseif (strcasecmp($global_unit, "Month") == 0) {
                    $service_ending_period = $global_value;
                    $service_ended_date = Carbon::parse($request->input('joining_date'))->addMonth($service_ending_period)->subDay(1);
                } elseif (strcasecmp($global_unit, "Day") == 0) {
                    $service_ending_period = $global_value;
                    $service_ended_date = Carbon::parse($request->input('joining_date'))->addDay($service_ending_period)->subDay(1);
                }
                $panel = $sms_receive_info->panel;
                if ($panel) {
                    $panel->panelLog()->save(new PanelInfoLogModel([
                        'ansar_id' => $panel->ansar_id,
                        'merit_list' => $panel->ansar_merit_list,
                        'panel_date' => $panel->panel_date,
                        're_panel_date' => $panel->re_panel_date,
                        'old_memorandum_id' => !$panel->memorandum_id ? "N\A" : $panel->memorandum_id,
                        'movement_date' => Carbon::today(),
                        'come_from' => $panel->come_from,
                        'move_to' => 'Offer',
                        'go_panel_position' => $panel->go_panel_position,
                        're_panel_position' => $panel->re_panel_position
                    ]));
                    $panel->delete();
                }
                $kpi->embodiment()->save(new EmbodimentModel([
                    'ansar_id' => $ansar_id,
                    'received_sms_id' => $sms_receive_info->id,
                    'emboded_status' => 'Emboded',
                    'action_user_id' => Auth::user()->id,
                    'service_ended_date' => $service_ended_date,
                    'memorandum_id' => $memorandum_id,
                    'reporting_date' => Carbon::parse($request->input('reporting_date'))->format('Y-m-d'),
                    'transfered_date' => Carbon::parse($request->input('joining_date'))->format('Y-m-d'),
                    'joining_date' => Carbon::parse($request->input('joining_date'))->format('Y-m-d'),
                ]));
                $memorandum_entry = new MemorandumModel();
                $memorandum_entry->memorandum_id = $memorandum_id;
                $memorandum_entry->mem_date = Carbon::parse($request->mem_date);
                $memorandum_entry->save();

                $mobile_no = PersonalInfo::where('ansar_id', $ansar_id)->select('tbl_ansar_parsonal_info.mobile_no_self')->first();
                $sms_log_save = new OfferSmsLog();
                $sms_log_save->ansar_id = $ansar_id;
                $sms_log_save->sms_offer_id = $sms_receive_info->id;
                $sms_log_save->mobile_no = $mobile_no->mobile_no_self;
                //$sms_log_save->offer_status=;
                $sms_log_save->reply_type = "Yes";
                $sms_log_save->action_date = $sms_receive_info->sms_received_datetime;
                $sms_log_save->offered_district = $sms_receive_info->offered_district;
                $sms_log_save->offered_date = $sms_receive_info->sms_send_datetime;
                $sms_log_save->action_user_id = Auth::user()->id;
                $sms_log_save->save();
                $sms_receive_info->deleteCount();
                $sms_receive_info->deleteOfferStatus();
                $sms_receive_info->delete();

                AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_block_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 1, 'pannel_status' => 0, 'freezing_status' => 0]);

                CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'EMBODIED', 'from_state' => 'OFFER', 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();
                return Response::json(['status' => false, 'message' => $e->getMessage()]);
            }
        }
        $this->dispatch(new SendSms($request->ansar_ids));
        $letter = [];
        $letter['option'] = 'memorandumNo';
        $letter['id'] = $memorandum_id;
        $letter['type'] = 'EMBODIMENT';
        $letter['unit'] = $request->unit_id;
        $letter['status'] = true;
        return Response::json(['status' => true, 'message' => 'Ansar is Embodied Successfully!', 'printData' => $letter]);
    }

    public function newMultipleEmbodimentEntry(Request $request)
    {
//        return $request->all();
        $rules = [];
        if (auth()->user()->type == 11 || auth()->user()->type == 77) {
            $rules['memorandum_id'] = 'required';
        } else {
            $rules['memorandum_id'] = 'required|unique:hrm.tbl_memorandum_id,memorandum_id|unique:hrm.tbl_embodiment,memorandum_id|unique:hrm.tbl_rest_info,memorandum_id||unique:hrm.tbl_transfer_ansar,transfer_memorandum_id';
        }
        $message = [
            'memorandum_id.required' => 'Memorandum ID is required'
        ];
        $valid = Validator::make($request->all(), $rules, $message);
        if ($valid->fails()) {
            return Response::json(['status' => false, 'message' => 'Invalid memorandum no.']);
        }
        $memorandum_id = $request->input('memorandum_id');
        $result = ['success' => 0, 'fail' => 0];
        $letter = [];
        foreach ($request->data as $ansar) {
            DB::beginTransaction();
            try {
                $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar['ansar_id'])->first();
//            return $sms_receive_info->offered_district!=$request->division_name_eng?"same":"differ";
                if (!$sms_receive_info) {
                    throw new \Exception('Invalid request for Ansar ID: ' . $ansar['ansar_id']);
                }
                $kpi = KpiGeneralModel::find($ansar['kpi_id']);
                if (!$kpi) {
                    throw new \Exception('Invalid request for Ansar ID: ' . $ansar['ansar_id']);
                }
                if ($sms_receive_info->offered_district != $kpi->unit_id) {
                    throw new \Exception('Ansar ID: ' . $ansar['ansar_id'] . ' not offered for this district');
                }
                $panel = $sms_receive_info->panel;
                if ($panel) {
                    $panel->panelLog()->save(new PanelInfoLogModel([
                        'ansar_id' => $panel->ansar_id,
                        'merit_list' => $panel->ansar_merit_list,
                        'panel_date' => $panel->panel_date,
                        're_panel_date' => $panel->re_panel_date,
                        'old_memorandum_id' => !$panel->memorandum_id ? "N\A" : $panel->memorandum_id,
                        'movement_date' => Carbon::today(),
                        'come_from' => $panel->come_from,
                        'move_to' => 'Offer',
                        'go_panel_position' => $panel->go_panel_position,
                        're_panel_position' => $panel->re_panel_position
                    ]));
                    $panel->delete();
                }
                $kpi->embodiment()->save(new EmbodimentModel([
                    'ansar_id' => $ansar['ansar_id'],
                    'received_sms_id' => $sms_receive_info->id,
                    'emboded_status' => 'Emboded',
                    'action_user_id' => Auth::user()->id,
                    'service_ended_date' => GlobalParameterFacades::getServiceEndedDate(Carbon::parse($ansar['joining_date'])),
                    'memorandum_id' => $memorandum_id,
                    'reporting_date' => Carbon::parse($ansar['reporting_date'])->format('Y-m-d'),
                    'transfered_date' => Carbon::parse($ansar['joining_date'])->format('Y-m-d'),
                    'joining_date' => Carbon::parse($ansar['joining_date'])->format('Y-m-d'),
                ]));
                $memorandum_entry = new MemorandumModel();
                $memorandum_entry->memorandum_id = $memorandum_id;
                $memorandum_entry->mem_date = Carbon::parse($request->mem_date);
                $memorandum_entry->save();
                $mobile_no = PersonalInfo::where('ansar_id', $ansar['ansar_id'])->select('tbl_ansar_parsonal_info.mobile_no_self')->first();
                $sms_log_save = new OfferSmsLog();
                $sms_log_save->ansar_id = $ansar['ansar_id'];
                $sms_log_save->sms_offer_id = $sms_receive_info->id;
                $sms_log_save->mobile_no = $mobile_no->mobile_no_self;
                //$sms_log_save->offer_status=;
                $sms_log_save->reply_type = "Yes";
                $sms_log_save->action_date = $sms_receive_info->sms_received_datetime;
                $sms_log_save->offered_district = $sms_receive_info->offered_district;
                $sms_log_save->offered_date = $sms_receive_info->sms_send_datetime;
                $sms_log_save->action_user_id = Auth::user()->id;
                $sms_log_save->save();
                $sms_receive_info->delete();
                AnsarStatusInfo::where('ansar_id', $ansar['ansar_id'])->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 1, 'pannel_status' => 0, 'freezing_status' => 0]);
                CustomQuery::addActionlog(['ansar_id' => $ansar['ansar_id'], 'action_type' => 'EMBODIED', 'from_state' => 'OFFER', 'to_state' => 'EMBODIED', 'action_by' => auth()->user()->id]);
                DB::commit();
                $result['success']++;
            } catch (\Exception $e) {
                DB::rollback();
                Log::info($e->getMessage());
                $result['fail']++;
            }
        }
        if ($result['success'] > 0) {
            $letter['option'] = 'memorandumNo';
            $letter['id'] = $memorandum_id;
            $letter['type'] = 'EMBODIMENT';
            $letter['unit'] = $request->unit_id;
            $letter['status'] = true;
        }
        return Response::json(['status' => true, 'message' => "Success {$result['success']}, Failed {$result['fail']}", 'letterData' => $letter]);
    }

    public function transferProcessView()
    {
        return View::make('HRM::Transfer.transfer_ansar');
    }

    function completeTransferProcess()
    {
//        return Input::get('transferred_ansar');
        $rules = [
            'transfer_date' => ['required', 'regex:/^[0-9]{2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'kpi_id' => 'required|is_array|array_length_same:2|array_type:int',
        ];
        if (auth()->user()->type == 11 || auth()->user()->type == 77) {
            $rules['memorandum_id'] = 'required';
        } else {
            $rules['memorandum_id'] = 'required|unique:hrm.tbl_memorandum_id,memorandum_id|unique:hrm.tbl_embodiment,memorandum_id|unique:hrm.tbl_rest_info,memorandum_id||unique:hrm.tbl_transfer_ansar,transfer_memorandum_id';
        }
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response($valid->messages()->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        $m_id = Input::get('memorandum_id');
        $t_date = Input::get('transfer_date');
        $kpi_id = Input::get('kpi_id');
        $transferred_ansar = Input::get('transferred_ansar');
        $status = array('success' => array('count' => 0, 'data' => array()), 'error' => array('count' => 0, 'data' => array()));
        DB::beginTransaction();
        try {
            $kpi = KpiGeneralModel::find($kpi_id[1]);
            $total_em = $kpi->embodiment->count();
            $total_given = intval($kpi->details->total_ansar_given);
            $total_ta = count($transferred_ansar);
            if ($total_given - $total_em < $total_ta) throw new \Exception("Number of transfer ansar exceed total number of given ansar");
            $tp = SystemSettingHelper::getValue(SystemSettingHelper::$TRANSFER_POLICY);
            Log::info($tp);
            $memorandum = new MemorandumModel;
            $memorandum->memorandum_id = $m_id;
            $memorandum->mem_date = Carbon::parse(Input::get('mem_date'));
            $memorandum->save();
            foreach ($transferred_ansar as $ansar) {
                DB::beginTransaction();
                try {
                    $e_id = EmbodimentModel::where('ansar_id', $ansar['ansar_id'])->where('kpi_id', $kpi_id[0])->first();
                    if ($e_id) {
                        if ($kpi_id[0] == $kpi_id[1]) throw new \Exception("Ansar(" . $e_id->ansar_id . ") can`t transferred in same kpi");
                        if (in_array($e_id->kpi->unit_id, $tp['data']) && $tp['status'] == 1) {
                            $t_history = $e_id->transfer()->pluck('present_kpi_id');
                            if (in_array($kpi_id[1], collect($t_history)->toArray())) throw new \Exception("Ansar(" . $e_id->ansar_id . ") previously transferred in this kpi");
                        }
                        $e_id->kpi_id = $kpi_id[1];
                        $e_id->transfered_date = Carbon::parse($t_date)->format("Y-m-d");
                        $e_id->save();
                        $transfer = new TransferAnsar;
                        $transfer->ansar_id = $ansar['ansar_id'];
                        $transfer->embodiment_id = $e_id->id;
                        $transfer->transfer_memorandum_id = $m_id;
                        $transfer->present_kpi_id = $kpi_id[0];
                        $transfer->transfered_kpi_id = $kpi_id[1];
                        $transfer->present_kpi_join_date = Carbon::parse($ansar['joining_date']);
                        $transfer->transfered_kpi_join_date = Carbon::parse($t_date)->format("Y-m-d");
                        $transfer->action_by = Auth::user()->id;
                        $transfer->save();
                        $status['success']['count']++;
                        array_push($status['success']['data'], $ansar['ansar_id']);
                        CustomQuery::addActionlog(['ansar_id' => $ansar['ansar_id'], 'action_type' => 'TRANSFER', 'from_state' => $kpi_id[0], 'to_state' => $kpi_id[1], 'action_by' => auth()->user()->id]);
                        DB::commit();
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    $status['error']['count']++;
                    array_push($status['error']['data'], $e->getMessage());
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $status['error']['count'] = count($transferred_ansar);
            //return Response::json(['status'=>false,'message'=>'Can`t transfer ansar. There is an error.Please try again later']);
        }
        return Response::json(['status' => true, 'data' => $status]);
    }

    public function getEmbodiedAnsarOfKpi()
    {
        $kpi_id = Input::get('kpi_id');
        return Response::json(CustomQuery::getEmbodiedAnsar($kpi_id));
    }

    public function getEmbodiedAnsarOfKpiV()
    {
        $u_id = Input::get('thana_id');
        $t_id = Input::get('unit_id');
        return Response::json(CustomQuery::getEmbodiedAnsarV($t_id, $u_id));
    }

    public function newDisembodimentView()
    {
        return view('HRM::Embodiment.new_disembodiment_rough');
    }

    public function loadAnsarForDisembodiment(Request $request)
    {
        //return $request->all();
//        DB::enableQueryLog();
        $rules = [
            'range' => 'required_without:q|regex:/^[0-9]+$/',
            'unit' => 'required_without:q|regex:/^[0-9]+$/',
            'thana' => 'required_without:q|regex:/^[0-9]+$/',
            'kpi' => 'required_without:q|regex:/^[0-9]+$/',
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return Response::json(['status' => false, 'message' => 'Invalid request']);
        }
        $reasons = DB::table('tbl_disembodiment_reason')->select('tbl_disembodiment_reason.id', 'tbl_disembodiment_reason.reason_in_bng')->get();
        $status = "Emboded";
        $ansar_infos = DB::table('tbl_kpi_info')
            ->join('tbl_embodiment', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->where('tbl_embodiment.emboded_status', '=', $status)
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->where('tbl_ansar_status_info.black_list_status', '=', 0);
        if ($request->q) {
            $ansar_infos->where('tbl_ansar_parsonal_info.ansar_id', $request->q);
        }
        if ($request->unit) {
            $ansar_infos->where('tbl_kpi_info.unit_id', '=', $request->unit);
        }
        if ($request->range) {
            $ansar_infos->where('tbl_kpi_info.division_id', '=', $request->range);
        }
        if ($request->thana) {
            $ansar_infos->where('tbl_kpi_info.thana_id', '=', $request->thana);
        }
        if ($request->kpi) {
            $ansar_infos->where('tbl_embodiment.kpi_id', '=', $request->kpi);
        }
        $ansar_infos = $ansar_infos->distinct()
            ->select('tbl_kpi_info.kpi_name', 'tbl_embodiment.joining_date', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_units.unit_name_bng', 'tbl_thana.thana_name_bng', 'tbl_designations.name_bng')
            ->get();
//        return DB::getQueryLog();
        if (count($ansar_infos) <= 0) return Response::json(array('result' => true));
        return Response::json(['ansar_infos' => $ansar_infos, 'type' => 1, 'reasons' => $reasons]);
//        }
    }

    public function disembodimentEntry(Request $request)
    {
        return CustomQuery::disembodimentEntry($request);
    }

    public function serviceExtensionView()
    {
        return view('HRM::Embodiment.service_extension_view');
    }

    public function loadAnsarDetail()
    {
        $ansar_id = Input::get('ansar_id');
        $ansar_check = DB::table('tbl_ansar_status_info')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_status_info.ansar_id')
            ->where('tbl_embodiment.ansar_id', '=', $ansar_id)
            ->where('tbl_ansar_status_info.rest_status', '=', 0)
            ->where('tbl_ansar_status_info.block_list_status', '=', 0)
            ->where('tbl_ansar_status_info.black_list_status', '=', 0)
            ->select('tbl_ansar_status_info.ansar_id')
            ->first();
        if (!is_null($ansar_check)) {
            $ansar_details = DB::table('tbl_embodiment')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_embodiment.ansar_id', '=', $ansar_id)
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->where('tbl_embodiment.service_extension_status', '=', 0)
                ->select('tbl_embodiment.*', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_kpi_info.kpi_name', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng')
                ->first();
            return Response::json($ansar_details);
        }
    }

    public function serviceExtensionEntry(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/',
            'extended_period' => 'required|numeric|min:1|max:12',
            'service_extension_comment' => 'required|regex:/^[a-zA-Z0-9 ]+$/',
            'ansarExist' => 'numeric|min:0|max:1'
        ];
        $message = [
            'ansar_id.required' => 'Ansar ID is required',
            'ansar_id.numeric' => 'Ansar ID must be numeric',
            'ansar_id.regex' => 'Ansar ID must be numeric',
            'extended_period.required' => 'Extended Period is required',
            'extended_period.numeric' => 'Extended Period must be numeric',
            'extended_period.min' => 'Extended Period Cannot be less than 1 Months',
            'extended_period.max' => 'Extended Period Cannot be more than 12 Months',
            'service_extension_comment.required' => 'Comment is required',
            'service_extension_comment.regex' => 'Comment must contain Alphabets, Numbers and Space Characters',
        ];
        $valid = Validator::make(Input::all(), $rules, $message);
        if ($valid->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($valid);
        }
        $ansar_id = $request->input('ansar_id');
        $extended_period = $request->input('extended_period');
        $service_extension_comment = $request->input('service_extension_comment');
        $ansarExist = $request->input('ansarExist');
        if ($ansarExist == 1) {
            DB::beginTransaction();
            try {
                $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                $serviceExtenstionEntry = new ServiceExtensionModel();
                $serviceExtenstionEntry->embodiment_id = $embodiment_info->id;
                $serviceExtenstionEntry->ansar_id = $ansar_id;
                $serviceExtenstionEntry->pre_service_ended_date = $embodiment_info->service_ended_date;
                $serviceExtenstionEntry->new_extended_date = Carbon::parse($embodiment_info->service_ended_date)->addMonth($extended_period);
                $serviceExtenstionEntry->service_extension_comment = $service_extension_comment;
                $serviceExtenstionEntry->action_user_id = Auth::user()->id;
                $serviceExtenstionEntry->save();
                $embodiment_info->service_ended_date = Carbon::parse($embodiment_info->service_ended_date)->addMonth($extended_period);
                $embodiment_info->service_extension_status = 1;
                $embodiment_info->save();
                DB::commit();
                return Redirect::route('service_extension_view')->with('success_message', 'Service Date for Ansar Extended Successfully!');
            } catch (\Exception $e) {
                return Redirect::route('service_extension_view')->with('error_message', 'Service Date for Ansar has not been Extended!');
            }
        }
    }

    public function disembodimentDateCorrectionView()
    {
        return view('HRM::Embodiment.disembodiment_date_correction_view');
    }

    public function loadAnsarForDisembodimentDateCorrection()
    {
        $ansar_id = Input::get('ansar_id');
        $ansar_details = DB::table('tbl_embodiment_log')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_embodiment_log.ansar_id', '=', $ansar_id)
            ->select('tbl_embodiment_log.ansar_id as id', 'tbl_embodiment_log.release_date as r_date', 'tbl_ansar_parsonal_info.ansar_name_eng as name', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.data_of_birth as dob', 'tbl_designations.name_eng as rank', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')
            ->orderBy('r_date', 'desc')
            ->first();
        return Response::json($ansar_details);
    }

    public function loadAnsarEmbodimentDateCorrection(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/',
            'range' => 'regex:/^[0-9]+$/',
            'unit' => 'regex:/^[0-9]+$/',
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) return Response::json([]);
        $ansar_id = Input::get('ansar_id');
        $ansar_details = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_embodiment.ansar_id', '=', $request->ansar_id);
        if ($request->range) {
            $ansar_details->where('tbl_kpi_info.division_id', $request->range);
        }
        if ($request->unit) {
            $ansar_details->where('tbl_kpi_info.unit_id', $request->unit);
        }
        $ansar_details = $ansar_details
            ->select('tbl_embodiment.ansar_id as id', 'tbl_kpi_info.kpi_name', 'tbl_ansar_parsonal_info.ansar_name_eng as name', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.data_of_birth as dob', 'tbl_embodiment.joining_date', 'tbl_embodiment.service_ended_date', 'tbl_designations.name_eng as rank', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')
            ->first();
        return Response::json($ansar_details);
    }

    public function newDisembodimentDateEntry(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/|exists:tbl_embodiment_log,ansar_id',
            'new_disembodiment_date' => ['required', 'regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
        ];
        $message = [
            'ansar_id.required' => 'Ansar ID is required',
            'new_disembodiment_date.required' => 'New Disembodiment Date is required',
            'ansar_id.numeric' => 'Ansar ID must be numeric',
            'ansar_id.regex' => 'Ansar ID must be numeric',
            'new_disembodiment_date.regex' => 'New Disembodiment Date format is invalid',
        ];
        $valid = Validator::make(Input::all(), $rules, $message);
        if ($valid->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($valid);
        }
        $ansar_id = $request->input('ansar_id');
        $new_disembodiment_date = $request->input('new_disembodiment_date');
        $modified_new_disembodiment_date = Carbon::parse($new_disembodiment_date)->format('Y-m-d');
        DB::beginTransaction();
        try {
            $status = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            $embodiment_log_update = EmbodimentLogModel::where('ansar_id', $ansar_id)->orderBy('release_date', 'desc')->first();
            $embodiment_log_update->release_date = $modified_new_disembodiment_date;
            $embodiment_log_update->save();
            if ($status->getStatus()[0] == AnsarStatusInfo::PANEL_STATUS) {
                if ($embodiment_log_update && Carbon::now()->lt(Carbon::parse($embodiment_log_update->release_date)->addMonths(6))) {
                    Log::info("ddd:" . Carbon::parse($embodiment_log_update->release_date)->addMonths(6)->format("d-m-Y"));
                    $p = PanelModel::where('ansar_id', $ansar_id)->first();
                    $p->saveLog("Rest");
                    $p->delete();
                    RestInfoModel::create([
                        'ansar_id' => $ansar_id,
                        'old_embodiment_id' => $embodiment_log_update->old_embodiment_id,
                        'memorandum_id' => 'n\a',
                        'rest_date' => $embodiment_log_update->release_date,
                        'active_date' => GlobalParameterFacades::getActiveDate($embodiment_log_update->release_date),
                        'total_service_days' => Carbon::parse($embodiment_log_update->release_date)->diffInDays(Carbon::parse($embodiment_log_update->joining_date)),
                        'disembodiment_reason_id' => $embodiment_log_update->disembodiment_reason_id,
                        'rest_form' => 'Regular',
                        'action_user_id' => Auth::user()->id,
                        'comment' => $embodiment_log_update->comment,
                    ]);
                    $status->rest_status = 1;
                    $status->pannel_status = 0;
                    $status->save();
                }
            } else {
                $rest_info_update = RestInfoModel::where('ansar_id', $ansar_id)->first();
                if ($rest_info_update) {
                    $rest_info_update->rest_date = $modified_new_disembodiment_date;
                    $rest_info_update->active_date = GlobalParameterFacades::getActiveDate($modified_new_disembodiment_date);
                    $rest_info_update->save();
                }
            }
            CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'DISEMBODIMENT DATE CORRECTION', 'from_state' => 0, 'to_state' => 0, 'action_by' => auth()->user()->id]);
            DB::commit();
        } catch (\Exception $e) {
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        return Redirect::route('disembodiment_date_correction_view')->with('success_message', 'Dis-Embodiment Date is corrected Successfully!');
    }

    public function newEmbodimentDateEntry(Request $request)
    {
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/',
            'new_embodiment_date' => ['required'],
        ];
        $message = [
            'ansar_id.required' => 'Ansar ID is required',
            'new_Embodiment_date.required' => 'New Disembodiment Date is required',
            'ansar_id.numeric' => 'Ansar ID must be numeric',
            'ansar_id.regex' => 'Ansar ID must be numeric',
            'new_disembodiment_date.regex' => 'New Disembodiment Date format is invalid',
        ];
        $valid = Validator::make(Input::all(), $rules, $message);
        if ($valid->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($valid);
        }
        $ansar_id = $request->input('ansar_id');
        $new_embodiment_date = Carbon::parse($request->input('new_embodiment_date'));
        DB::beginTransaction();
        try {
            $embodied_ansar = EmbodimentModel::where('ansar_id', $ansar_id)->first();
            if ($embodied_ansar) {
                $embodied_ansar->update([
                    'joining_date' => $new_embodiment_date,
                    'service_ended_date' => GlobalParameterFacades::getServiceEndedDate($request->input('new_embodiment_date'))
                ]);
            } else {
                throw new \Exception('This Ansar does not embodied anywhere');
            }
            DB::commit();
        } catch (\Exception $e) {
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        return Redirect::route('embodiment_date_correction_view')->with('success_message', 'Embodiment Date is corrected Successfully!');
    }

    public function embodimentMemorandumIdCorrectionView()
    {
        return view('HRM::Embodiment.embodiment_memorandum_id_correction');
    }

    public function loadAnsarForEmbodimentMemorandumIdCorrection()
    {
        $ansar_id = Input::get('ansar_id');
        $ansar_details = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('tbl_embodiment.ansar_id', '=', $ansar_id)
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
            ->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.memorandum_id as m_id', 'tbl_kpi_info.kpi_name as kpi', 'tbl_ansar_parsonal_info.ansar_name_eng as name', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng as rank', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')
            ->first();
        return Response::json($ansar_details);
    }

    public function newMemorandumIdCorrectionEntry(Request $request)
    {
        DB::beginTransaction();
        try {
            $ansar_id = $request->input('ansar_id');
            $new_memorandum_id = $request->input('memorandum_id');
            $memorandum_entry = new MemorandumModel();
            $memorandum_entry->memorandum_id = $new_memorandum_id;
            $memorandum_entry->save();
            $mem_id_updated = EmbodimentModel::where('ansar_id', $ansar_id)->first();
            $mem_id_updated->memorandum_id = $new_memorandum_id;
            $mem_id_updated->save();

            DB::commit();
        } catch
        (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        return Redirect::route('embodiment_memorandum_id_correction_view')->with('success_message', 'Memorandum ID Corrected Successfully');
    }

    public function getKpiDetail()
    {
        $id = Input::get('id');
        $detail = KpiDetailsModel::where('kpi_id', $id)->select('total_ansar_given', 'no_of_ansar', 'no_of_apc', 'no_of_pc')->first();
        if (Input::exists('ansar_id')) {
            $a_id = Input::get('ansar_id');
            $embodiment_detail = DB::table('tbl_embodiment')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_embodiment.kpi_id', $id)->where('tbl_designations.id', $a_id)
                ->select(DB::raw('count(tbl_embodiment.ansar_id) as total'))->first();
        } else {
            $ansar = DB::table('tbl_embodiment')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_embodiment.kpi_id', $id)->where('tbl_designations.id', 1)
                ->select(DB::raw('count(tbl_embodiment.ansar_id) as total'));
            $apc = DB::table('tbl_embodiment')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_embodiment.kpi_id', $id)->where('tbl_designations.id', 2)
                ->select(DB::raw('count(tbl_embodiment.ansar_id) as total'));
            $pc = DB::table('tbl_embodiment')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_embodiment.kpi_id', $id)->where('tbl_designations.id', 3)
                ->select(DB::raw('count(tbl_embodiment.ansar_id) as total'));
            $embodiment_detail = $ansar->unionAll($apc)->unionAll($pc)->get();
        }
        return Response::json(['detail' => $detail, 'ansar_count' => $embodiment_detail]);
    }

    public function multipleKpiTransferView()
    {
        return View::make("HRM::Transfer.multiple_kpi_transfer");
    }

    public function getEmbodiedAnsarInfo(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'ansar_id' => 'required|numeric',
            'unit' => 'required|numeric'
        ], [
            'ansar_id.required' => 'Ansar id required',
            'ansar_id.numeric' => 'Invalid ansar id',
        ]);
        if ($valid->fails()) {
            return Response::json(['status' => 0, 'messages' => $valid->messages()->all()]);
        }
        $query = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
            ->where('tbl_embodiment.ansar_id', $request->get('ansar_id'))
            ->where('tbl_units.id', $request->get('unit'));
        if ($query->exists()) {
            $query = $query->select('tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_kpi_info.kpi_name', 'tbl_kpi_info.id as kpi_id', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_embodiment.joining_date', 'tbl_embodiment.transfered_date', 'tbl_units.id');
            return Response::json(['status' => 1, 'data' => $query->first()]);
        } else {
            return Response::json(['status' => 0, 'messages' => ['Ansar id not available']]);
        }
    }

    public function confirmTransfer(Request $request)
    {
        $rules = [];
        if (auth()->user()->type == 11 || auth()->user()->type == 77) {
            $rules['memId'] = 'required';
        } else {
            $rules['memId'] = 'required|unique:hrm.tbl_memorandum_id,memorandum_id|unique:hrm.tbl_embodiment,memorandum_id|unique:hrm.tbl_rest_info,memorandum_id||unique:hrm.tbl_transfer_ansar,transfer_memorandum_id';
        }
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            Log::info($valid->messages());
            Log::info(Auth::user()->user_name);
            return response($valid->messages()->toJson(), 400, ['Content-type' => 'application/json']);
        }
        $data = $request->ansars;
        $status = array('success' => array('count' => 0, 'data' => array()), 'error' => array('count' => 0, 'data' => array()));
        DB::beginTransaction();
        try {
            $m_id = $request->memId;
            $memorandum = new MemorandumModel;
            $memorandum->memorandum_id = $m_id;
            $memorandum->mem_date = Carbon::parse(Input::get('mem_date'));
            $memorandum->save();
            $tp = SystemSettingHelper::getValue(SystemSettingHelper::$TRANSFER_POLICY);
            Log::info($tp);
            foreach ($data as $ansar) {
                $ansar = (object)$ansar;
                DB::beginTransaction();
                try {
                    $e_ansar = EmbodimentModel::where('ansar_id', $ansar->ansarId)->where('kpi_id', $ansar->currentKpi)->first();
                    //print_r($ansar->ansarId); die;
                    if ($e_ansar) {
                        if (in_array($e_ansar->kpi->unit_id, $tp['data']) && $tp['status'] == 1) {
                            $t_history = $e_ansar->transfer()->pluck('present_kpi_id');
                            if (in_array($ansar->transferKpi, collect($t_history)->toArray())) {
                                throw new \Exception("Ansar(" . $e_ansar->ansar_id . ") previously transferred in this kpi");
                            }
                        }
                        $transfer = new TransferAnsar;
                        //print_r($ansar->id);die;
                        $transfer->ansar_id = $ansar->ansarId;
                        $transfer->embodiment_id = $e_ansar->id;
                        $transfer->transfer_memorandum_id = $m_id;
                        $transfer->present_kpi_id = $ansar->currentKpi;
                        $transfer->transfered_kpi_id = $ansar->transferKpi;
                        $transfer->transfered_kpi_join_date = Carbon::parse($ansar->tKpiJoinDate)->format("Y-m-d");
                        $transfer->present_kpi_join_date = Carbon::parse($e_ansar->transfered_date)->format("Y-m-d");
                        $transfer->action_by = Auth::user()->id;
                        $transfer->save();
                        $e_ansar->kpi_id = $ansar->transferKpi;
                        $e_ansar->transfered_date = Carbon::parse($ansar->tKpiJoinDate)->format("Y-m-d");
                        $e_ansar->save();
                        $status['success']['count']++;
                        array_push($status['success']['data'], $ansar->ansarId);
                        CustomQuery::addActionlog(['ansar_id' => $ansar->ansarId, 'action_type' => 'TRANSFER', 'from_state' => $ansar->currentKpi, 'to_state' => $ansar->transferKpi, 'action_by' => auth()->user()->id]);
                        DB::commit();
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    $status['error']['count']++;
                    array_push($status['error']['data'], $e->getMessage());
//                    return response(collect(['message' => "An error occur while transfer. Please try again later"])->toJson(), 400, ['Content-Type' => 'application/json']);
                }
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response(collect(['status' => false, 'message' => "An error occur while transfer. Please try again later"])->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        return Response::json(['status' => true, 'data' => $status, 'memId' => $m_id]);
    }

    public function getSingleEmbodiedAnsarInfo($id)
    {
        $ansar = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
            ->where('tbl_embodiment.ansar_id', $id)
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->select('tbl_kpi_info.id as kpi_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_eng', 'tbl_kpi_info.kpi_name', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_embodiment.joining_date as join_date');
        return Response::json($ansar->first());
    }

    public function disembodiedThreeYearOverList()
    {
        $data = EmbodimentModel::whereYear('joining_date', '<=', 2013);
        return $data;
    }

    //disembodied ansar over 3 years
    public function disembodiedAnsarOver3Year()
    {
        $data = DB::table('tbl_embodiment')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->where('tbl_units.id', 13)
            ->where('block_list_status', 0)
            ->where('embodied_status', 1)
            ->whereYear('joining_date', '<=', 2013)
            ->select('tbl_embodiment.*', 'tbl_ansar_status_info.block_list_status')
            ->take(100)
            ->get();
        foreach ($data as $ansar) {
            EmbodimentModel::find($ansar->id)->delete();
            $b = AnsarStatusInfo::where('ansar_id', $ansar->ansar_id)->first();
            $b->update(['rest_status' => 1, 'embodied_status' => 0]);
            $rest_entry = new RestInfoModel();
            $rest_entry->ansar_id = $ansar->ansar_id;
            $rest_entry->old_embodiment_id = $ansar->id;
            $rest_entry->memorandum_id = 'auto';
            $rest_entry->rest_date = Carbon::now();
            $rest_entry->active_date = Carbon::now();
            $rest_entry->total_service_days = Carbon::now()->addDays(1)->diffInDays(Carbon::parse($ansar->joining_date));
            $rest_entry->disembodiment_reason_id = 1;
            $rest_entry->rest_form = "Regular";
            $rest_entry->action_user_id = Auth::user()->id;
            $rest_entry->comment = "NO COMMENT";
            $rest_entry->save();
            $embodiment_log_update = new EmbodimentLogModel();
            $embodiment_log_update->old_embodiment_id = $ansar->id;
            $embodiment_log_update->old_memorandum_id = $ansar->memorandum_id;
            $embodiment_log_update->ansar_id = $ansar->ansar_id;
            $embodiment_log_update->kpi_id = $ansar->kpi_id;
            $embodiment_log_update->reporting_date = $ansar->reporting_date;
            $embodiment_log_update->joining_date = $ansar->joining_date;
            $embodiment_log_update->transfered_date = $ansar->transfered_date;
            $embodiment_log_update->release_date = Carbon::now();
            $embodiment_log_update->disembodiment_reason_id = 1;
            $embodiment_log_update->move_to = "Rest";
            $embodiment_log_update->service_extension_status = $ansar->service_extension_status;
            $embodiment_log_update->comment = "NO COMMENT";
            $embodiment_log_update->action_user_id = 0;
            $embodiment_log_update->save();
        }
    }

    public function loadDisembodiedAnsar(Request $request)
    {
        if ($request->ajax()) {
            if (strcasecmp($request->method(), 'post') == 0) {
                DB::enablequeryLog();
                $query = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_division', 'tbl_division.id', '=', 'tbl_ansar_parsonal_info.division_id')
                    ->join('tbl_rest_info', 'tbl_rest_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_embodiment_log', function ($join) {
                        $join->on('tbl_rest_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id');
                        $join->on('tbl_rest_info.rest_date', '=', 'tbl_embodiment_log.release_date');
                    })
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                    ->join('tbl_disembodiment_reason', 'tbl_rest_info.disembodiment_reason_id', '=', 'tbl_disembodiment_reason.id')
                    ->where('tbl_embodiment_log.old_embodiment_id', '!=', 0)
                    ->where('tbl_rest_info.total_service_days', '<', 365 * 3);
                if ($request->range) {
                    $query->where('tbl_kpi_info.division_id', $request->range);
                }
                if ($request->unit) {
                    $query->where('tbl_kpi_info.unit_id', $request->unit);
                }
                if ($request->thana) {
                    $query->where('tbl_kpi_info.thana_id', $request->thana);
                }
                if ($request->kpi) {
                    $query->where('tbl_kpi_info.id', $request->kpi);
                }
                if ($request->reason) {
                    $query->where('tbl_disembodiment_reason.id', $request->reason);
                }
                $data = $query->select('tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.ansar_id',
                    'tbl_designations.name_bng', 'tbl_division.division_name_bng', 'tbl_units.unit_name_bng',
                    'tbl_kpi_info.kpi_name', 'tbl_rest_info.total_service_days', 'tbl_rest_info.rest_date',
                    'tbl_disembodiment_reason.reason_in_bng')->get();
                return $data;
            } else {
                abort(403);
            }
        } else if (strcasecmp($request->method(), 'get') == 0) {
            return view('HRM::Embodiment.disembodied_ansar_correction');
        } else {
            abort(403);
        }
    }

    public function addNewBankAccount(Request $request)
    {
        $rules = [
            'ansar_id' => 'required',
            'bank_name' => 'required_if:prefer_choice,general',
            'account_no' => 'required_if:prefer_choice,general',
            'mobile_bank_type' => 'required_if:prefer_choice,mobile',
            'mobile_bank_account_no' => 'required_if:prefer_choice,mobile',
            'prefer_choice' => 'required',
        ];
        $this->validate($request, $rules);
        DB::connection("hrm")->beginTransaction();
        try {
            $data = $request->all();
            $ansar = PersonalInfo::where("ansar_id", $data['ansar_id'])
                ->whereHas("status", function ($q) {
                    $q->where('offer_sms_status', 1)->where('block_list_status', 0)->where('black_list_status', 0);
                })->first();
            if ($ansar) {
                unset($data['ansar_id'], $data['action_user_id']);
                $account = $ansar->account;
                if ($account) {
                    $account->update($data);
                } else $ansar->account()->create($data);
                DB::connection("hrm")->commit();
                return response()->json(['status' => 'success', 'message' => "Bank account info added successfully"]);
            }
            throw new \Exception("Invalid ansar");
        } catch (\Exception $e) {
            DB::connection("hrm")->rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

    }
}