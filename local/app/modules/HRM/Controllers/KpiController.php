<?php

namespace App\modules\HRM\Controllers;

use App\Helper\ExportDataToExcel;
use App\Http\Controllers\Controller;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\KpiDetailsModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\KpiInfoLogModel;
use App\modules\HRM\Models\MemorandumModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class KpiController extends Controller
{
    use ExportDataToExcel;

    public function kpiIndex()
    {
        return view('HRM::Kpi.kpi_entry');
    }

    public function kpiView()
    {
        return view('HRM::Kpi.kpi_view');
    }

    public function kpiViewDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $division = Input::get('division');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $organization = Input::get('organization');
        $q = Input::get('q');
        $rules = [
            'limit' => 'numeric',
            'offset' => 'numeric',
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return response('Invalid Request (400)', 400);
        }
        $data = CustomQuery::kpiInfo($offset, $limit, $division, $unit, $thana, $organization, $q);
        if ($request->exists('export')) {
            $data = collect($data['kpis'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.kpi_export_view');

        }
        return Response::json($data);
    }
    
    
    public function vacancyKPI()
    {
        return view('HRM::Kpi.vacancy_kpi_list');
    }
    
    
    public function vacancyKpiViewDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $division = Input::get('division');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $organization = Input::get('organization');
        $q = Input::get('q');
        
        
       // print_r($request->all()); exit;
        $rules = [
            'limit' => 'numeric',
            'offset' => 'numeric',
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return response('Invalid Request (400)', 400);
        }
        $data = CustomQuery::vacancyKpiInfo($offset, $limit, $request->division, $request->unit, $request->thana,  $organization, $q, $request->type);
        if ($request->exists('export')) {
            $data = collect($data['kpis'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.vacancy_kpi_export_view');

        }
        
        return Response::json($data);
    }

    public function inactiveKpiList()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $division = Input::get('division');
        $q = Input::get('q');
        $rules = [
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['required', 'regex:/^(all)$|^[0-9]+$/'],
            'thana' => ['required', 'regex:/^(all)$|^[0-9]+$/'],
            'division' => ['required', 'regex:/^(all)$|^[0-9]+$/'],
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return response('Invalid Request(400)', 400);
        } else {
            return CustomQuery::inactiveKpiInfo($offset, $limit, $unit, $thana, $division, $q);
        }
    }

    public function inactiveKpiView()
    {
        return view('HRM::Kpi.inactive_kpi_view');
    }

    public function saveKpiInfo(Request $request)
    {
        $kpi_name = $request->input('kpi_name');        
        $org_id = $request->input('organization_name_eng');        
        $division_id = $request->input('division_name_eng');
        $unit_id = $request->input('unit_name_eng');
        $thana_id = $request->input('thana_name_eng');
        $kpi_address = $request->input('kpi_address');
        $kpi_contact_no = $request->input('kpi_contact_no');
        $total_ansar_request = $request->input('total_ansar_request');
        $total_ansar_given = $request->input('total_ansar_given');
        $with_weapon = $request->input('with_weapon');
        $weapon_count = $request->input('weapon_count');
        $bullet_no = $request->input('bullet_no');
        $weapon_description = $request->input('weapon_description');
        $no_of_ansar = $request->input('no_of_ansar');
        $no_of_apc = $request->input('no_of_apc');
        $no_of_pc = $request->input('no_of_pc');
        $activation_date = $request->input('activation_date');
        $withdraw_date = $request->input('withdraw_date');
        if (Auth::user()->type == 22) {
            $division_id = District::find($unit_id)->division_id;
        }
        $rules = [
            'kpi_name' => 'required',
            'organization_name_eng' => 'required',
            'division_name_eng' => 'required',
            'unit_name_eng' => 'required',
            'thana_name_eng' => 'required',
            'kpi_address' => 'required',
            'kpi_contact_no' => 'required',
            'total_ansar_request' => 'required',
            'total_ansar_given' => 'required',
            'with_weapon' => 'required',
            'weapon_count' => 'required',
            'bullet_no' => 'required',
            'weapon_description' => 'required',
            'no_of_ansar' => 'required',
            'no_of_apc' => 'required',
            'no_of_pc' => 'required',
        ];
        $modified_activation_date = Carbon::parse($activation_date)->format('Y-m-d');
        if (strcasecmp($withdraw_date, '') != 0) {
            $modified_withdraw_date = Carbon::parse($withdraw_date)->format('Y-m-d');
        } else {
            $modified_withdraw_date = NULL;
        }

//        return $modified_withdraw_date;
        DB::beginTransaction();
        try {
            $kpi_general = new KpiGeneralModel();
            $kpi_general->kpi_name = $kpi_name;
            $kpi_general->org_id = $org_id;
            $kpi_general->division_id = $division_id;
            $kpi_general->unit_id = $unit_id;
            $kpi_general->thana_id = $thana_id;
            $kpi_general->kpi_address = $kpi_address;
            $kpi_general->kpi_contact_no = $kpi_contact_no;
            $kpi_general->status_of_kpi = 1;
            $kpi_general->save();

            $kpi_details = new KpiDetailsModel();
            $kpi_details->kpi_id = $kpi_general->id;
            $kpi_details->total_ansar_request = $total_ansar_request;
            $kpi_details->total_ansar_given = $total_ansar_given;
            $kpi_details->with_weapon = $with_weapon;
            $kpi_details->weapon_count = $weapon_count;
            $kpi_details->bullet_no = $bullet_no;
            $kpi_details->weapon_description = $weapon_description;
            $kpi_details->activation_date = $modified_activation_date;
            $kpi_details->withdraw_date = $modified_withdraw_date;
            $kpi_details->no_of_ansar = $no_of_ansar;
            $kpi_details->no_of_apc = $no_of_apc;
            $kpi_details->no_of_pc = $no_of_pc;
            $kpi_details->save();
            CustomQuery::addActionlog(['ansar_id' => $kpi_general->id, 'action_type' => 'ADD KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]);
            DB::commit();
        } catch
        (\Exception $e) {
            DB::rollback();
            return Redirect::route('kpi_view')->with('success_message', $e->getMessage());
        }
        return Redirect::route('kpi_view')->with('success_message', 'New KPI is Entered Successfully!');
    }

    public function edit($id)
    {
        $kpi_info = KpiGeneralModel::find($id);
        return view('HRM::Kpi.kpi_edit', ['id' => $id])->with(['kpi_info' => $kpi_info]);
    }

    public function updateKpi(Request $request)
    {
        $kpi_name = $request->input('kpi_name');
        $org_id = $request->input('organization_name_eng'); 
        $division_id = $request->input('division_name_eng');
        $unit_id = $request->input('unit_name_eng');
        $thana_id = $request->input('thana_name_eng');
        $kpi_address = $request->input('kpi_address');
        $kpi_contact_no = $request->input('kpi_contact_no');
        $total_ansar_request = $request->input('total_ansar_request');
        $total_ansar_given = $request->input('total_ansar_given');
        $with_weapon = $request->input('with_weapon');
        $weapon_count = $request->input('weapon_count');
        $bullet_no = $request->input('bullet_no');
        $weapon_description = $request->input('weapon_description');
        $no_of_ansar = $request->input('no_of_ansar');
        $no_of_apc = $request->input('no_of_apc');
        $no_of_pc = $request->input('no_of_pc');
        $is_special_kpi = $request->input('is_special_kpi');
        $special_amount = $request->input('special_amount');
        if (Auth::user()->type == 22) {
            $division_id = District::find($unit_id)->division_id;
        }
        $id = $request->input('id');
        $activation_date = $request->input('activation_date');
        $withdraw_date = $request->input('withdraw_date');
        $modified_activation_date = Carbon::parse($activation_date)->format('Y-m-d');
        if (strcasecmp($withdraw_date, '') != 0) {
            $modified_withdraw_date = Carbon::parse($request->input('withdraw_date'))->format('Y-m-d');
        } else {
            $modified_withdraw_date = NULL;
        }

        DB::beginTransaction();
        try {

            $kpi_general = KpiGeneralModel::find($id);
            $kpi_general->kpi_name = $kpi_name;
            $kpi_general->org_id = $org_id;
            $kpi_general->division_id = $division_id;
            $kpi_general->unit_id = $unit_id;
            $kpi_general->thana_id = $thana_id;
            $kpi_general->kpi_address = $kpi_address;
            $kpi_general->kpi_contact_no = $kpi_contact_no;
            $kpi_general->save();


            $kpi_details = KpiDetailsModel::where('kpi_id', $id)->first();
            $kpi_details->total_ansar_request = $total_ansar_request;
            $kpi_details->total_ansar_given = $total_ansar_given;
            $kpi_details->with_weapon = $with_weapon;
            $kpi_details->weapon_count = $weapon_count;
            $kpi_details->bullet_no = $bullet_no;
            $kpi_details->weapon_description = $weapon_description;
            $kpi_details->activation_date = $modified_activation_date;
            $kpi_details->withdraw_date = $modified_withdraw_date;
            $kpi_details->no_of_ansar = $no_of_ansar;
            $kpi_details->no_of_apc = $no_of_apc;
            $kpi_details->no_of_pc = $no_of_pc;
            $kpi_details->is_special_kpi = $is_special_kpi;
            $kpi_details->special_amount = $special_amount;
            $kpi_details->save();

            DB::commit();
            CustomQuery::addActionlog(['ansar_id' => $kpi_general->id, 'action_type' => 'EDIT KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]);
        } catch
        (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        return Redirect::route('kpi_view')->with('success_message', 'New KPI is Updated Successfully!');
    }

    public function delete($id)
    {
        KpiGeneralModel::find($id)->delete();
        return redirect('/kpi_view');
    }

    public function kpiVerify()
    {
        $kpi_id = Input::get('verified_id');
        $kpi_status_update = KpiGeneralModel::where('id', $kpi_id)->update(['status_of_kpi' => 1]);
        if ($kpi_status_update) {
            return 1;
        } else {
            return 0;
        }
    }

    public function ansarWithdrawView()
    {
        $kpi_names = KpiGeneralModel::where('status_of_kpi', 1)->get();
        return view('HRM::Kpi.ansar_withdraw_view')->with('kpi_names', $kpi_names);
    }

    public function ansarListForWithdraw(Request $request)
    {
        $rules = [
            'range' => 'regex:/^[0-9]+$/',
            'unit' => 'regex:/^[0-9]+$/',
            'thana' => 'regex:/^[0-9]+$/',
            'kpi' => 'required|regex:/^[0-9]+$/'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return Response::json(array('valid' => true));
        } else {
            $kpi_infos = DB::table('tbl_embodiment')
                ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_units as pu', 'tbl_ansar_parsonal_info.unit_id', '=', 'pu.id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_embodiment.kpi_id', '=', $request->kpi)
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
				->where('tbl_ansar_status_info.embodied_status', '=', 1)
                ->where('tbl_embodiment.emboded_status', '=', "Emboded");
            if ($request->range) $kpi_infos->where('tbl_kpi_info.division_id', $request->range);
            if ($request->unit) $kpi_infos->where('tbl_kpi_info.unit_id', $request->unit);
            if ($request->thana) $kpi_infos->where('tbl_kpi_info.thana_id', $request->thana);

            $data = $kpi_infos->distinct()
                ->select('tbl_embodiment.reporting_date', 'tbl_embodiment.joining_date', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_designations.name_bng', 'pu.unit_name_bng')
                ->get();
//            if (count($kpi_infos) <= 0) return Response::json(array('result' => true));
            return Response::json($data);
        }
    }

    public function ansarWithdrawUpdate(Request $request)
    {
        $rules = [
            'kpi_id_withdraw' => 'required|numeric|regex:/^[0-9]+$/',
            'memorandum_id' => 'required',
            'kpi_withdraw_date' => 'required|date_format:d-M-Y|date_validity',
            'ansarIds' => 'required|is_array|array_type:int'
        ];

        // Added By Rintu - Instruction From Ansar ICT - Only Admit User Can User Back Date
        if(Auth::user()->type != 11){
            $rules['kpi_withdraw_date'] = 'required|date_format:d-M-Y|date_validity|after:yesterday|before:tomorrow';
        }

        $customMessages = [
            'after' => 'Freeze date will be current date!',
            'before' => 'Freeze date will be current date!',
        ];


        $valid = Validator::make($request->all(), $rules,$customMessages);
        if ($valid->fails()) {
           return Response::json(['status' => false, 'message' => $valid->messages()->toJson()]);
        }

        DB::beginTransaction();
        try {
            $kpi_id = $request->input('kpi_id_withdraw');
            $ansar_ids = EmbodimentModel::where('kpi_id', $kpi_id)->get();
            $withdraw_date = $request->input('kpi_withdraw_date');
            $modified_withdraw_date = Carbon::parse($withdraw_date)->format('Y-m-d');
            $memorandum_id = $request->input('memorandum_id');
            $memorandum_id_save = new MemorandumModel();
            $memorandum_id_save->memorandum_id = $memorandum_id;
            $memorandum_id_save->save();
            $user = [];


            $kpi_data = KpiGeneralModel::with(['unit'])->find($kpi_id);
            $unit_id = $kpi_data->unit_id;
            
            if ($kpi_id && $ansar_ids && $withdraw_date && $memorandum_id) {
                foreach ($ansar_ids as $ansar_id) {
                    if (!in_array($ansar_id->ansar_id, $request->get('ansarIds'))) continue;
                    $withdraw_ansar_id = $ansar_id->ansar_id;
                    $freeze_change = new FreezingInfoModel();
                    $freeze_change->ansar_id = $withdraw_ansar_id;
                    $freeze_change->freez_reason = "Guard Withdraw";
                    $freeze_change->freez_date = $modified_withdraw_date;
                    $freeze_change->comment_on_freez = $request->input('kpi_withdraw_reason');
                    $freeze_change->memorandum_id = $memorandum_id;
                    $freeze_change->kpi_id = $ansar_id->kpi_id;
                    $freeze_change->ansar_embodiment_id = $ansar_id->id;
                    $freeze_change->action_user_id = Auth::user()->id;
                    $freeze_change->save();

                    $embodied_status_freezed = EmbodimentModel::where('ansar_id', $withdraw_ansar_id)->first();
                    $embodied_status_freezed->emboded_status = "Freeze";
                    $embodied_status_freezed->action_user_id = Auth::user()->id;
                    $embodied_status_freezed->save();

                    AnsarStatusInfo::where('ansar_id', $withdraw_ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 1]);

                    $kpi_log_change = new KpiInfoLogModel();
                    $kpi_log_change->kpi_id = $kpi_id;
                    $kpi_log_change->ansar_id = $withdraw_ansar_id;
                    $kpi_log_change->reason_for_freeze = "Withdraw";
                    $kpi_log_change->comment_on_freeze = $request->input('kpi_withdraw_reason');
                    $kpi_log_change->date_of_freeze = $modified_withdraw_date;
                    $kpi_log_change->reporting_date = $ansar_id->reporting_date;
                    $kpi_log_change->joining_date = $ansar_id->joining_date;
                    $kpi_log_change->action_user_id = Auth::user()->id;
                    $kpi_log_change->save();
                    array_push($user, ['ansar_id' => $ansar_id->ansar_id, 'action_type' => 'FREEZE', 'from_state' => 'EMBODIED', 'to_state' => 'FREEZE', 'action_by' => auth()->user()->id]);

                }
                DB::commit();
                CustomQuery::addActionlog($user, true);
                CustomQuery::addActionlog(['ansar_id' => $kpi_id, 'action_type' => 'WITHDRAW KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]);
            } else throw new \Exception("Invalid request");
            //return Redirect::route('ansar-withdraw-view')->with('success_message', 'Ansar/s Withdrawn Successfully!');
        } catch
        (\Exception $e) {
            DB::rollback();
            Log::info("error code" . $e->getCode());
             return Response::json(['status' => false, 'message' => $e->getMessage()]);
        }
        
        
        $letter = [];
        $letter['option'] = 'memorandumNo';
        $letter['id'] = $memorandum_id;
        $letter['type'] = 'FREEZ';
        $letter['unit'] = $unit_id;
        $letter['status'] = true;
        return Response::json(['status' => true, 'message' => 'Ansar is Freezed Successfully!', 'printData' => $letter]);
    }

    public function reduceGuardStrength()
    {
        $kpi_names = KpiGeneralModel::where('status_of_kpi', 1)->get();
        return view('HRM::Kpi.reduce_guard_strength')->with('kpi_names', $kpi_names);
    }

    public function kpiWithdrawActionView()
    {
        return view('HRM::Kpi.withdraw_actionView');
    }

    public function singleKpiInfo($id)
    {
        $info = KpiGeneralModel::with(['unit', 'division'])->find($id);
        return $info;
    }

    public function ansarListForReduce()
    {
        $unit_id = Input::get('unit');
        $thana = Input::get('thana');
        $selctedReducedAnsars = Input::get('kpi');
        $rules = [
            'unit' => 'regex:/^[0-9]+$/',
            'thana' => 'regex:/^[0-9]+$/',
            'kpi' => 'regex:/^[0-9]+$/'
        ];
        $params = Input::all();
        $validation = Validator::make($params, $rules);
        if ($validation->fails()) {
            return response('Invalid request', 400, ['Content-Type' => 'text/html']);
        } else {
            $kpi_infos = DB::table('tbl_embodiment')
                ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_kpi_info.division_id', '=', 'tbl_division.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_embodiment.kpi_id', '=', $selctedReducedAnsars)
                ->where('tbl_kpi_info.unit_id', '=', $unit_id)
                ->where('tbl_kpi_info.thana_id', '=', $thana)
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.black_list_status', '=', 0)
                ->where('tbl_embodiment.emboded_status', '=', "Emboded");

            if (isset($params["gender"]) && ($params["gender"] === 'Male' || $params["gender"] === 'Female' || $params["gender"] === 'Other')) {
                $kpi_infos = $kpi_infos->where('tbl_ansar_parsonal_info.sex', '=', $params["gender"]);
            }
            if (isset($params["rank"]) && !empty($params["rank"]) && is_numeric($params["rank"])) {
                $kpi_infos = $kpi_infos->where('tbl_designations.id', '=', $params["rank"]);
            }

            $dataForCount = clone $kpi_infos;
            $rankCount = $dataForCount->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
            $rankCount = collect($rankCount->get())->pluck('t', 'code');

            $kpi_infos = $kpi_infos->distinct()
                ->select('tbl_embodiment.*', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_kpi_info.kpi_name', 'tbl_division.division_name_eng', 'tbl_units.unit_name_eng', 'tbl_thana.thana_name_eng', 'tbl_designations.name_eng')
                ->get();

            return Response::json(['list' => $kpi_infos, 'tCount' => $rankCount]);
        }
    }

    public function ansarReduceUpdate(Request $request)
    {
        DB::beginTransaction();
        $kpi_id = '';
        try {
            if ($request->ajax()) {
                $selected_ansars = $request->ansarId;
                $mi = $request->memorandumId;
                $rd = $request->reduce_guard_strength_date;
                if (!isset($request->memorandumId) || !isset($request->reduce_guard_strength_date)
                    || empty($request->memorandumId) || empty($request->reduce_guard_strength_date)) {
                    return Response::json(['status' => false, 'message' => "Memorandum id & Withdrawal required"]);
                }
                $modified_reduce_date = Carbon::parse($rd)->format('Y-m-d');
                $reduce_reason = Input::get('reduce_reason');
                $memorandum_entry = new MemorandumModel();
                $memorandum_entry->memorandum_id = $mi;
                $memorandum_entry->save();
                $user = [];
                if (is_array($selected_ansars)) {
                    $kpi = KpiGeneralModel::find($request->kpiId);
                    foreach ($selected_ansars as $reduced_ansar) {

//                        $reduced_ansar = $selected_ansars[$i];
                        $ansar_ids = $kpi->embodiment()->where('ansar_id', $reduced_ansar)->where('emboded_status', 'Emboded')->get();
                        foreach ($ansar_ids as $ansar_id) {
                            $reduced_ansar_id = $ansar_id->ansar_id;
                            $kpi_id = $ansar_id->kpi_id;
                            $freeze_change = new FreezingInfoModel();
                            $freeze_change->ansar_id = $reduced_ansar_id;
                            $freeze_change->freez_reason = "Guard Reduce";
                            $freeze_change->freez_date = $modified_reduce_date;
                            $freeze_change->comment_on_freez = $reduce_reason;
                            $freeze_change->memorandum_id = $mi;
                            $freeze_change->kpi_id = $ansar_id->kpi_id;
                            $freeze_change->ansar_embodiment_id = $ansar_id->id;
                            $freeze_change->action_user_id = Auth::user()->id;
                            $freeze_change->save();

                            $embodied_status_freezed = EmbodimentModel::where('ansar_id', $reduced_ansar_id)->first();
                            $embodied_status_freezed->emboded_status = "Freeze";
                            $embodied_status_freezed->action_user_id = Auth::user()->id;
                            $embodied_status_freezed->save();

                            AnsarStatusInfo::where('ansar_id', $reduced_ansar_id)->update(['embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 1]);

                            $kpi_log_change = new KpiInfoLogModel();
                            $kpi_log_change->kpi_id = $ansar_id->kpi_id;
                            $kpi_log_change->ansar_id = $reduced_ansar_id;
                            $kpi_log_change->reason_for_freeze = "Reduce";
                            $kpi_log_change->comment_on_freeze = $reduce_reason;
                            $kpi_log_change->date_of_freeze = $modified_reduce_date;
                            $kpi_log_change->reporting_date = $ansar_id->reporting_date;
                            $kpi_log_change->joining_date = $ansar_id->joining_date;
                            $kpi_log_change->action_user_id = Auth::user()->id;
                            $kpi_log_change->save();
                            array_push($user, ['ansar_id' => $ansar_id->ansar_id, 'action_type' => 'FREEZE', 'from_state' => 'EMBODIED', 'to_state' => 'FREEZE', 'action_by' => auth()->user()->id]);
                        }
                    }
                    CustomQuery::addActionlog($user, true);
                    CustomQuery::addActionlog(['ansar_id' => $kpi_id, 'action_type' => 'REDUCE KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]);
                    DB::commit();
                } else {
                    return Response::json(['status' => false, 'message' => "Invalid Request"]);
                }

            } else {
                return Response::json(['status' => false, 'message' => "Invalid Request"]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'message' => $e->getMessage()]);
        }
        return Response::json(['status' => true, 'message' => "Ansar/s Reduced Successfully!"]);
    }

    public function guardBeforeWithdrawView()
    {
        return view('HRM::Kpi.ansar_before_withdraw_list');
    }

    public function loadAnsarsForBeforeWithdraw(Request $request)
    {
        $kpi_id = Input::get('kpi_id');
        $rules = [
            'kpi_id' => ['regex:/^([0-9]+)|(all)$/', 'required'],
            'division_id' => ['regex:/^([0-9]+)|(all)$/', 'required'],
            'unit_id' => ['regex:/^([0-9]+)|(all)$/', 'required'],
            'thana_id' => ['regex:/^([0-9]+)|(all)$/', 'required'],
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response('Invalid Request (400)', 400);
        } else {
            if (Auth::user()->type == 22) {
                $request->unit_id = Auth::user()->district_id;
            }
            if (Auth::user()->type == 66) {
                $request->division_id = Auth::user()->division_id;
            }
            $freeze_reason = "Guard Withdraw";

            $guards_before_withdraw = DB::table('tbl_freezing_info')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_freezing_info.kpi_id')
                ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                ->where('tbl_freezing_info.freez_reason', '=', $freeze_reason);
            if ($request->division_id != 'all') {
                $guards_before_withdraw->where('tbl_kpi_info.division_id', $request->division_id);
            }
            if ($request->kpi_id != 'all') {
                $guards_before_withdraw->where('tbl_freezing_info.kpi_id', '=', $kpi_id);
            }
            if ($request->thana_id != 'all') {
                $guards_before_withdraw->where('tbl_kpi_info.thana_id', '=', $request->thana_id);
            }
            if ($request->unit_id != 'all') {
                $guards_before_withdraw->where('tbl_kpi_info.unit_id', '=', $request->unit_id);
            }

            $dataForCount = clone $guards_before_withdraw;
            $rankCount = $dataForCount->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
            $rankCount = collect($rankCount->get())->pluck('t', 'code');

            if (isset($request->gender) && ($request->gender === 'Male' || $request->gender === 'Female' || $request->gender === 'Other')) {
                $guards_before_withdraw->where('tbl_ansar_parsonal_info.sex', '=', $request->gender);
            }
            if (isset($request->rank) && !empty($request->rank) && is_numeric($request->rank)) {
                $guards_before_withdraw->where('tbl_designations.id', '=', $request->rank);
            }
            if (isset($request->q) && !empty($request->q) && is_numeric($request->q)) {
                $guards_before_withdraw->where('tbl_freezing_info.ansar_id', '=', $request->q);
            }
            $data = $guards_before_withdraw->distinct()
                ->select('tbl_freezing_info.ansar_id as id', 'tbl_freezing_info.freez_date as date', 'tbl_freezing_info.comment_on_freez as reason', 'tbl_ansar_parsonal_info.ansar_name_eng as name', 'tbl_designations.name_eng as rank', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana', 'tbl_kpi_info.kpi_name',
                    'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.reporting_date as r_date', 'tbl_kpi_info.kpi_name')->get();
            return ['tCount' => $rankCount, 'list' => $data];
        }
    }

    public function guardBeforeReduceView()
    {
        return view('HRM::Kpi.ansar_before_reduce_list');
    }

    public function loadAnsarsForBeforeReduce(Request $request)
    {
        $unit_id = Input::get('unit_id');
        $thana = Input::get('thana_id');
        $kpi_id = Input::get('kpi_id');
        $rules = [
            'kpi_id' => ['regex:/^([0-9]+)|(all)$/', 'required'],
            'division_id' => ['regex:/^([0-9]+)|(all)$/', 'required'],
            'unit_id' => ['regex:/^([0-9]+)|(all)$/', 'required'],
            'thana_id' => ['regex:/^([0-9]+)|(all)$/', 'required'],
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return response('Invalid Request (400)', 400);
        } else {
            if (Auth::user()->type == 22) {
                $request->unit_id = Auth::user()->district_id;
            }
            if (Auth::user()->type == 66) {
                $request->division_id = Auth::user()->division_id;
            }
            $freeze_reason = "Guard Reduce";

            $guards_before_reduce = DB::table('tbl_freezing_info')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_freezing_info.kpi_id')
                ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_freezing_info.ansar_id')
                ->where('tbl_freezing_info.freez_reason', '=', $freeze_reason);
            //        return Response::json($guards_before_withdraw);
            if ($request->division_id != 'all') {
                $guards_before_reduce->where('tbl_kpi_info.division_id', $request->division_id);
            }
            if ($request->kpi_id != 'all') {
                $guards_before_reduce->where('tbl_freezing_info.kpi_id', '=', $kpi_id);
            }
            if ($request->thana_id != 'all') {
                $guards_before_reduce->where('tbl_kpi_info.thana_id', '=', $request->thana_id);
            }
            if ($request->unit_id != 'all') {
                $guards_before_reduce->where('tbl_kpi_info.unit_id', '=', $request->unit_id);
            }
            $data = $guards_before_reduce->distinct()
                ->select('tbl_freezing_info.ansar_id as id', 'tbl_freezing_info.freez_date as date', 'tbl_freezing_info.comment_on_freez as reason', 'tbl_ansar_parsonal_info.ansar_name_eng as name', 'tbl_designations.name_eng as rank', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana', 'tbl_kpi_info.kpi_name',
                    'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.reporting_date as r_date', 'tbl_kpi_info.kpi_name')->get();

            return $data;
        }
    }

    public function kpiWithdrawView()
    {
        return view('HRM::Kpi.kpi_withdraw_view');
    }

    public function loadKpiForWithdraw()
    {
        $kpi_id = Input::get('kpi_id');
        $rules = [
            'kpi_id' => 'required|numeric|regex:/^[0-9]+$/',
        ];
        $validation = Validator::make(Input::all(), $rules);
        if (!$validation->fails()) {
            $kpi_info = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
                ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
                ->where('tbl_kpi_info.id', '=', $kpi_id)
                ->whereNull('tbl_kpi_detail_info.kpi_withdraw_date')->distinct()
                ->select('tbl_kpi_info.kpi_name as kpi', 'tbl_kpi_detail_info.total_ansar_request as tar', 'tbl_kpi_detail_info.total_ansar_given as tag', 'tbl_kpi_detail_info.weapon_count as weapon', 'tbl_kpi_detail_info.bullet_no as bullet', 'tbl_kpi_detail_info.activation_date as a_date', 'tbl_division.division_name_eng as division', 'tbl_units.unit_name_eng as unit', 'tbl_thana.thana_name_eng as thana')
                ->first();
            return Response::json($kpi_info);
        }
    }

    public function kpiWithdrawUpdate(Request $request, $id)
    {
        $kpi_id = $request->get('id');
        $withdraw_date = $request->get('date');
        $a = $request->all();
        $a['validate_id'] = intval($id);
        if (isset($a['id'])) $a['id'] = intval($a['id']);
        else return Response::json(['status' => false, 'message' => "Invalid Request"]);
        $rules = [
            'id' => 'numeric|required|same:validate_id',
            //  'date' => ['required', 'regex:/^[0-9]{2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
        ];
        $message = [
            'id.required' => 'KPI is required',
            'date.required' => 'Withdraw Date is required',
            'id.numeric' => 'Select a valid KPI',
            //  'date.regex' => 'Select a valid Date',
        ];
        $validation = Validator::make($a, $rules, $message);
        if ($validation->fails()) {
            return response($validation->messages()->toJson(), 422, ['Content-Type' => 'application/json']);
        }
        $modified_withdraw_date = Carbon::parse($withdraw_date)->format('Y-m-d');
        DB::beginTransaction();
        try {
            $m = new MemorandumModel;
            $m->memorandum_id = $request->mem_id;
            $m->save();
            if (Carbon::parse($modified_withdraw_date)->lte(Carbon::now())) {
                $kpi = KpiGeneralModel::find($kpi_id);
                $kpi->update(['withdraw_status' => 1, 'status_of_kpi' => 0, 'withdrew_date' => Carbon::now()]);
                $kpi->details->update(['kpi_withdraw_date' => null, 'kpi_withdraw_mem_id' => $request->mem_id]);
                $embodiment_infos = $kpi->embodiment->where('emboded_status', 'Emboded');
                foreach ($embodiment_infos as $embodiment_info) {
                    $freeze_info_update = new FreezingInfoModel();
                    $freeze_info_update->ansar_id = $embodiment_info->ansar_id;
                    $freeze_info_update->freez_reason = "Guard Withdraw";
                    $freeze_info_update->freez_date = $modified_withdraw_date;
                    $freeze_info_update->kpi_id = $kpi->id;
                    $freeze_info_update->ansar_embodiment_id = $embodiment_info->id;
                    $freeze_info_update->save();
                    $embodiment_info->emboded_status = "Freeze";
                    $embodiment_info->save();
                    AnsarStatusInfo::where('ansar_id', $embodiment_info->ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 1]);
                    $a = ['ansar_id' => $embodiment_info->ansar_id, 'action_type' => 'WITHDRAW KPI', 'from_state' => 'EMBODIED', 'to_state' => 'FREEZE', 'action_by' => auth()->user()->id];
                    CustomQuery::addActionlog($a);
                }
            } else {
                $kpi_withdraw_date_entry = KpiDetailsModel::where('kpi_id', $kpi_id)->first();
                $kpi_withdraw_date_entry->kpi_withdraw_date = $modified_withdraw_date;
                $kpi_withdraw_date_entry->kpi_withdraw_mem_id = $request->mem_id;
                $kpi_withdraw_date_entry->save();
            }

            DB::commit();
        } catch
        (\Exception $e) {
            DB::rollback();
            return Response::json(['status' => false, 'message' => $e->getMessage()]);
        }
        return Response::json(['status' => true, 'message' => 'Kpi Withdraw Complete']);
    }

    public function withdrawnKpiView()
    {
        return view('HRM::Kpi.withdrawn_kpi_list_view');
    }

    public function withdrawnKpiList()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $division = Input::get('division');
        $view = Input::get('view');
        $rules = [
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
            'view' => 'regex:/[a-z]+/'
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return response('Invalid Request (400)', 400);
        } else {
            if (strcasecmp($view, 'view') == 0) {
                return CustomQuery::withdrawnKpiInfo($offset, $limit, $unit, $thana, $division);
            } else {
                return CustomQuery::withdrawnKpiInfoCount($unit, $thana, $division);
            }
        }
    }

    public function kpiWithdrawDateEdit($id)
    {
        $kpi_details = KpiDetailsModel::where('kpi_id', $id)->first();
        $kpi_info = KpiGeneralModel::find($id);
        return view('HRM::Kpi.kpi_withdraw_date_edit', ['id' => $id])->with(['kpi_info' => $kpi_info, 'kpi_details' => $kpi_details, 'id' => $id]);
    }

    public function kpiWithdrawDateUpdate(Request $request, $id)
    {
//        return $request->all();
//        $id = $request->input('id');
        $a = $request->all();
//        return $a;
        $a['id'] = (int)$id;
        $withdraw_date = $request->get('date');
        $rules = array(
            'id' => 'required|numeric|min:0|integer|same:kpi_id',
            'date' => ['required', 'regex:/^[0-9]{2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
        );
        $messages = array(
            'withdraw-date.required' => 'Withdraw Date is required.',
            'withdraw-date.regex' => 'Withdraw Date format is invalid',
        );
        $validation = Validator::make($a, $rules, $messages);

        if ($validation->fails()) {
            return response($validation->messages()->toJson(), 422, ['Content-Type' => 'application/json']);
        } else {
            DB::beginTransaction();
            try {
                $kpi_details = KpiDetailsModel::where('kpi_id', $id)->first();
//                return $kpi_details;
                $modified_activation_date = Carbon::parse($withdraw_date);
                if ($modified_activation_date->lte(Carbon::today())) {
                    $kpi_details->kpi_withdraw_date = null;
                    $kpi_details->kpi_withdraw_date_update_mem_id = $request->mem_id;
                    $kpi_details->kpiInfo->update([
                        'status_of_kpi' => 0,
                        'withdraw_status' => 1
                    ]);
                } else {
                    $kpi_details->kpi_withdraw_date = $modified_activation_date;
                    $kpi_details->kpi_withdraw_date_update_mem_id = $request->mem_id;
                }

                $kpi_details->save();
                DB::commit();
//            Event::fire(new ActionUserEvent(['ansar_id' => $kpi_general->id, 'action_type' => 'EDIT KPI', 'from_state' => '', 'to_state' => '', 'action_by' => auth()->user()->id]));
            } catch
            (\Exception $e) {
                DB::rollback();
                return Response::json(['status' => false, 'message' => $e->getMessage()]);
            }
            return Response::json(['status' => true, 'message' => 'KPI withdraw Date is Updated Successfully']);
        }
    }


    public function activeKpi($id, Request $request)
    {
        $a = $request->all();
        $a['id'] = intval($id);
        if (isset($a['verified_id'])) $a['verified_id'] = intval($a['verified_id']);
        else return Response::json(['status' => false, 'message' => 'Invalid Request']);
        //return $a;
        $valid = Validator::make($a, [
            'id' => 'same:verified_id|regex:/^[0-9]+$/'
        ]);
        if ($valid->fails()) {
            return $valid->messages()->toJson();
            return Response::json(['status' => false, 'message' => 'Invalid Request']);
        } else {
            DB::beginTransaction();
            try {
                KpiGeneralModel::find($id)->update(['withdraw_status' => 0, 'status_of_kpi' => 1]);
                DB::commit();
            } catch
            (\Exception $e) {
                DB::rollback();
                return Response::json(['status' => false, 'message' => $e->getMessage()]);
            }
            return Response::json(['status' => true, 'message' => 'Kpi Active Successfully']);
        }
    }

    public function withdrawnKpiName(Request $request)
    {
        if (Input::exists('id')) {
            $id = $request->input('id');
            $kpi = KpiGeneralModel::where('thana_id', '=', $id)->where('status_of_kpi', 1)->get();
        } else
            $kpi = KpiGeneralModel::where('status_of_kpi', 1)->get();

        return Response::json($kpi);
    }

    public function kpiWithdrawCancelView()
    {
        return view('HRM::Kpi.kpi_withdraw_cancel');
    }

    public function kpiListForWithdrawCancel()
    {
        $kpi_id = Input::get('kpi_id');
        $kpi_info = DB::table('tbl_kpi_info')
            ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_kpi_info.division_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->where('tbl_kpi_info.id', '=', $kpi_id)
            ->whereNotNull('tbl_kpi_detail_info.kpi_withdraw_date')
            ->select('tbl_kpi_info.kpi_name as kpi', 'tbl_kpi_detail_info.total_ansar_request as tar',
                'tbl_kpi_detail_info.total_ansar_given as tag', 'tbl_kpi_detail_info.weapon_count as weapon',
                'tbl_kpi_detail_info.bullet_no as bullet', 'tbl_kpi_detail_info.activation_date as a_date',
                'tbl_division.division_name_eng as division', 'tbl_units.unit_name_eng as unit',
                'tbl_thana.thana_name_eng as thana', 'tbl_kpi_detail_info.kpi_withdraw_date as w_date')
            ->first();
        return Response::json($kpi_info);
    }

    public function kpiWithdrawCancelUpdate(Request $request, $id)
    {
        $a = $request->all();
        $a['id'] = (int)$id;
        $rules = [
            'kpi_id' => 'required|same:id|numeric|regex:/^[0-9]+$/',
            'mem_id' => 'unique:tbl_memorandum_id,memorandum_id',
        ];
        $message = [
            'mem_id.unique' => 'Memorandum no. must be unique'
        ];
        $validation = Validator::make($a, $rules, $message);
        if ($validation->fails()) {
            return response($validation->messages()->toJson(), 422, ['Content-Type' => 'application/json']);
        } else {
            $kpi = KpiGeneralModel::find($id);
            if (!$kpi->details->kpi_withdraw_date) {
                return Response::json(['status' => false, 'message' => "Invalid kpi"]);
            }
            DB::beginTransaction();
            try {
                $kpi->details()->update([
                    'kpi_withdraw_date' => null,
                    'kpi_withdraw_cancel_mem_id' => $request->mem_id,
                ]);
                $kpi->update([
                    'withdraw_status' => 0,
                    'status_of_kpi' => 1,
                ]);

                DB::commit();
                return Response::json(['status' => true, 'message' => "Kpi withdraw cancel successfully"]);
            } catch (\Exception $e) {
                DB::rollback();
                return Response::json(['status' => false, 'message' => "An occur while cancel withdraw. Try again later"]);
            }
        }
    }
	
	public function cancelKpiInfoDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $division = $request->division;
        $unit = $request->unit;
        $thana = $request->thana;

        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana' => ['regex:/^(all)$|^[0-9]+$/']
        ];
        
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        $data = CustomQuery::getCancelKpiList($offset, $limit, $division, $unit, $thana, $request->q);

        return $this->exportDataCustom(collect($data['kpis'])->chunk(50000)->toArray(), 'HRM::export.cancel_kpi_report', '', 'cancel_kpi_withdraw_report');

    }
}

