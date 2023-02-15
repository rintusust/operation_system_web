<?php

namespace App\modules\HRM\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\MemorandumModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class LetterController extends Controller
{
    //
    function transferLetterView()
    {
        return View::make('HRM::Letter.transfer_letter');
    }

    function getMemorandumIds(Request $requests)
    {
//        return $requests->all();
        //DB::enableQueryLog();
        $t = DB::table('tbl_memorandum_id')
            ->join('tbl_transfer_ansar', 'tbl_transfer_ansar.transfer_memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_transfer_ansar.transfered_kpi_id', '=', 'tbl_kpi_info.id')
            ->select('tbl_memorandum_id.*')->groupBy('tbl_memorandum_id.memorandum_id');
        $e = DB::table('tbl_memorandum_id')
            ->join('tbl_embodiment', 'tbl_embodiment.memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
            ->select('tbl_memorandum_id.*')->groupBy('tbl_memorandum_id.memorandum_id');
        $d = DB::table('tbl_memorandum_id')
            ->join('tbl_rest_info', 'tbl_rest_info.memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            ->join('tbl_embodiment_log', 'tbl_rest_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->join('tbl_kpi_info', 'tbl_embodiment_log.kpi_id', '=', 'tbl_kpi_info.id')
            ->whereRaw("`tbl_rest_info`.`rest_date` = `tbl_embodiment_log`.`release_date`")
            ->select('tbl_memorandum_id.*')->groupBy('tbl_memorandum_id.memorandum_id');
			
		$f = DB::table('tbl_freezing_info')
            #->join('tbl_freezing_info', 'tbl_freezing_info.memorandum_id', '=', 'tbl_memorandum_id.memorandum_id')
            #->join('tbl_kpi_info', 'tbl_freezing_info.kpi_id', '=', 'tbl_kpi_info.id')
            ->select('tbl_freezing_info.*')->groupBy('tbl_freezing_info.memorandum_id');
			
         if($requests->unit){
            $e->where('tbl_kpi_info.unit_id',$requests->unit);
			#$f->where('tbl_kpi_info.unit_id',$requests->unit);
            $t->where('tbl_kpi_info.unit_id',$requests->unit);
            $d->where('tbl_kpi_info.unit_id',$requests->unit)->orderBy('tbl_embodiment_log.id','desc');
        }
        if($requests->q){
            $e->where('tbl_memorandum_id.memorandum_id','LIKE','%'.$requests->q.'%');
            $f->where('tbl_freezing_info.memorandum_id','LIKE','%'.$requests->q.'%');
            $t->where('tbl_memorandum_id.memorandum_id','LIKE','%'.$requests->q.'%');
            $d->where('tbl_memorandum_id.memorandum_id','LIKE','%'.$requests->q.'%');
        }
        #$d->distinct('tbl_rest_info.memorandum_id')->paginate(20);
        #return DB::getQueryLog();

        switch ($requests->type) {
            case 'TRANSFER':
                //return $t->distinct()->paginate(20);
                return view('HRM::Letter.partial_letter_view',['data'=>$t->distinct()->paginate(20),'units'=>District::all(),'type'=>'TRANSFER']);
            case 'EMBODIMENT':
                return view('HRM::Letter.partial_letter_view',['data'=>$e->distinct()->paginate(20),'units'=>District::all(),'type'=>'EMBODIMENT']);
            case 'DISEMBODIMENT':
                return view('HRM::Letter.partial_letter_view',['data'=>$d->distinct('tbl_rest_info.memorandum_id')->paginate(20),'units'=>District::all(),'type'=>'DISEMBODIMENT']);
            case 'REEMBODIMENT':
                return view('HRM::Letter.partial_letter_view',['data'=>$d->distinct('tbl_rest_info.memorandum_id')->paginate(20),'units'=>District::all(),'type'=>'REEMBODIMENT']);
            case 'FREEZE':
                #echo 'Feature under maintenance. Will come soon! '; exit;
                return view('HRM::Letter.partial_letter_view',['data'=>$f->distinct()->orderBy('freez_date', 'desc')->paginate(20),'units'=>District::all(),'type'=>'FREEZE']);
            default:
                return [];
        }

    }

    function printLetter(Request $request)
    {
//        return $request->all();
        $id = Input::get('id');
        $type = Input::get('type');
        $unit = Input::get('unit');
        $view = Input::get('view');
        $option = Input::get('option');
        $rules = [
            'type' => 'regex:/^[A-Z]+$/',
            'unit' => 'numeric|regex:/^[0-9]+$/',
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        switch ($type) {
            case 'TRANSFER':
                return $this->transferLetterPrint($id, $unit, $view,$option);
            case 'EMBODIMENT':
                return $this->embodimentLetterPrint($id, $unit, $view,$option);
            case 'DISEMBODIMENT':
                return $this->disembodimentLetterPrint($id, $unit, $view,$option);
            case 'FREEZE':
                return $this->freezLetterPrint($id, $unit, $view,$option);
            case 'REEMBODIMENT':
                return $this->reembodimentLetterPrint($id, $unit, $view,$option);
        }
    }

    function transferLetterPrint($id, $unit, $v,$option)
    {
        //DB::enableQueryLog();
        $mem = DB::table('tbl_memorandum_id')
            ->join('tbl_transfer_ansar','tbl_transfer_ansar.transfer_memorandum_id','=','tbl_memorandum_id.memorandum_id')
            ->distinct('tbl_memorandum_id.memorandum_id')->orderBy('tbl_memorandum_id.created_at','desc')->select('tbl_memorandum_id.memorandum_id as memorandum_id', 'mem_date as created_at');
        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
        $result = DB::table('tbl_transfer_ansar')
            ->join('tbl_kpi_info as pk', 'tbl_transfer_ansar.present_kpi_id', '=', 'pk.id')
            ->join('tbl_kpi_info as tk', 'tbl_transfer_ansar.transfered_kpi_id', '=', 'tk.id')
            ->join('tbl_thana as tk_thana', 'tk_thana.id', '=', 'tk.thana_id')
            ->join('tbl_thana as pk_thana', 'pk_thana.id', '=', 'pk.thana_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_transfer_ansar.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->where('pk.unit_id',$unit)
            ->orderBy('tbl_transfer_ansar.created_at','desc')			
            ->select('pk_thana.thana_name_bng as pk_thana','tk_thana.thana_name_bng as tk_thana','pk.unit_id','tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'pk.kpi_name as p_kpi_name', 'tk.kpi_name as t_kpi_name');
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
			$result->limit(1);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_transfer_ansar.ansar_id',$id);
        }
        else{
            $result->where('tbl_transfer_ansar.transfer_memorandum_id', $id);
            $mem->where('tbl_transfer_ansar.transfer_memorandum_id', $id);
        }
        $result = DB::table(DB::raw('('.$result->toSql().') x'))->mergeBindings($result)->get();
		//print_r(DB::getQueryLog());
		//echo '<pre>'; print_r($result); exit;
        $mem = $mem->first();
        if($user->unit_eng=="CHITTAGONGNORTH" || $user->unit_eng=="CHITTAGONGSOUTH" || $user->unit_eng=="CHITTAGONGADMIN")
            $user->unit_short="চট্টগ্রাম";
        elseif ($user->unit_eng=="DHAKAADMIN"||$user->unit_eng=="DHAKAEAST"||$user->unit_eng=="DHAKAWEST"||$user->unit_eng=="DHAKASOUTH"||$user->unit_eng=="DHAKANORTH")
            $user->unit_short = "ঢাকা";
        else $user->unit_short = $user->unit;
        if ($mem && $result) {
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_transfer_letter']);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with(['id' => $id]);
        }
    }

    function embodimentLetterPrint($id, $unit, $v,$option)
    {
		
        $mem = DB::table('tbl_embodiment')
            ->leftJoin('tbl_memorandum_id','tbl_memorandum_id.memorandum_id','=','tbl_embodiment.memorandum_id')
            ->select('tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
        $result = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.joining_kpi_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_thana as kt', 'kt.id', '=', 'tbl_kpi_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')		
            ->select('tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_kpi_info.unit_id','tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name as village_name', 'tbl_ansar_parsonal_info.post_office_name as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment.joining_date','kt.thana_name_bng as kpi_thana');
		
		if($unit != 0){
			$result->where('tbl_kpi_info.unit_id',$unit);
		}	
			
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_embodiment.ansar_id',$id);
        }
        else{
            $result->where('tbl_embodiment.memorandum_id', $id);
            $mem->where('tbl_embodiment.memorandum_id', $id);
        }
        $result = $result->get();

		
        $mem = $mem->first();
       
        if ($mem && $result) {
			$user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $result[0]->unit_id)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
			
			 if($user->unit_eng=="CHITTAGONGNORTH" || $user->unit_eng=="CHITTAGONGSOUTH" || $user->unit_eng=="CHITTAGONGADMIN")
            $user->unit_short="চট্টগ্রাম";
        elseif ($user->unit_eng=="DHAKAADMIN"||$user->unit_eng=="DHAKAEAST"||$user->unit_eng=="DHAKAWEST"||$user->unit_eng=="DHAKASOUTH"||$user->unit_eng=="DHAKANORTH")
            $user->unit_short = "ঢাকা";
        else $user->unit_short = $user->unit;
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_embodiment_letter']);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with('id', $id);
        }
    }

    function disembodimentLetterPrint($id, $unit, $v,$option)
    {
        DB::enableQueryLog();
        $mem = DB::table('tbl_rest_info')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_rest_info.memorandum_id')
            ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_rest_info.disembodiment_reason_id')
            ->select('tbl_disembodiment_reason.reason_in_bng as reason', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();
        $result = DB::table('tbl_embodiment_log')
            ->join('tbl_rest_info', 'tbl_rest_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_rest_info.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_thana as kpi_thana', 'kpi_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->whereRaw('tbl_embodiment_log.release_date=tbl_rest_info.rest_date')
            ->where('tbl_kpi_info.unit_id',$unit)
            ->select('kpi_thana.thana_name_bng as kpi_thana','tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name_bng as village_name', 'tbl_ansar_parsonal_info.post_office_name_bng as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment_log.joining_date', 'tbl_embodiment_log.release_date')->orderBy('tbl_embodiment_log.id','DESC');
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_rest_info.ansar_id',$id);
        }
        else{
            $result->where('tbl_memorandum_id.memorandum_id', $id);
            $mem->where('tbl_memorandum_id.memorandum_id', $id);
        }




        if (!$result->exists()) {


            $ansar_id_list = [10094,5505,7098,20595,10876,2484,67562,42184,30797,62845,41896,10751,82568,29822,64433,14483,64726,42160,38825,29432,41892,45379,64424,492,42051,42559,69174,42369,42087,73256,42509,60007,10217,42182,25235,21864,10849,64326,29425,20142,11081,21781,42135,33204,28919,42382,42317,21237,29810,29381,2726,62589,42168,73016,44316,11148,42398,29024,29365,41880,22958,69136,20947,10329,20271,42137,32771,42171,42295,41846,9542,49886,41841,37507,16481,2807,10202,42173,6937,29246,29261,29414,70448,42529,39225,27506,9660,11432,10542,30958,42102,63067,10819,18834,18011,41771,25531,57649,41891,30786,29126,70860,69090,33726,26104,42337,79388,42517,42213,64403,71075,30965,69118,41832,21724,62730,64431,59887,13850,69952,66254,3852,2993,49251,20536,41562,42112,50729,42359,56726,7899,76101,45366];
            if (in_array($id, $ansar_id_list)) {
                //echo 'rintu';

                $otherStarttime = '2022-08-01';
                $otherEndtime = '2022-08-30';

                $result = DB::table('tbl_embodiment_log')
                    #->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_embodiment_log.comment')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                    ->join('tbl_ansar_parsonal_info', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_thana as kpi_thana', 'kpi_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where(function($query) use ($otherStarttime,$otherEndtime){
                        $query->where('tbl_embodiment_log.release_date', '>=', $otherStarttime);
                        $query->orWhere('tbl_embodiment_log.release_date', '<=', $otherEndtime);
                    })
                    ->where('tbl_kpi_info.unit_id', $unit)
                    ->select('kpi_thana.thana_name_bng as kpi_thana', 'tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name as village_name', 'tbl_ansar_parsonal_info.post_office_name as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment_log.joining_date', 'tbl_embodiment_log.release_date')->orderBy('tbl_embodiment_log.id', 'DESC');

                if ($option == 'smartCardNo') {
                    $l = strlen($id . '');
                    if ($l > 6)
                        $id = substr($id . '', 6);
                    $result->where('tbl_ansar_parsonal_info.ansar_id', $id);
                }
            } else {
                $result = DB::table('tbl_embodiment_log')
                    ->join('tbl_rest_info_log as tbl_rest_info', 'tbl_rest_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
                    ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_rest_info.old_memorandum_id')
                    ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
                    ->join('tbl_ansar_parsonal_info', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
                    ->join('tbl_thana as kpi_thana', 'kpi_thana.id', '=', 'tbl_kpi_info.thana_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->whereRaw('tbl_embodiment_log.release_date=tbl_rest_info.rest_date')
                    ->where('tbl_kpi_info.unit_id', $unit)
                    ->select('kpi_thana.thana_name_bng as kpi_thana', 'tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name as village_name', 'tbl_ansar_parsonal_info.post_office_name as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment_log.joining_date', 'tbl_embodiment_log.release_date')->orderBy('tbl_embodiment_log.id', 'DESC');
                if ($option == 'smartCardNo') {
                    $l = strlen($id . '');
                    if ($l > 6)
                        $id = substr($id . '', 6);
                    $result->where('tbl_ansar_parsonal_info.ansar_id', $id);
                }
                else {
                    $result->where('tbl_memorandum_id.memorandum_id', $id);
                }
            }
        }
        if(!$mem->exists()){

            if (in_array($id, $ansar_id_list)) {


                $mem = DB::table('tbl_embodiment_log')
                    ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_embodiment_log.comment')
                    ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
                    ->select('tbl_disembodiment_reason.reason_in_bng as reason', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
                if ($option == 'smartCardNo') {
                    $l = strlen($id . '');
                    if ($l > 6)
                        $id = substr($id . '', 6);
                    $mem->where('tbl_embodiment_log.ansar_id', $id);
                }
                $mem->where('tbl_memorandum_id.memorandum_id', 'ICT Memo - 44.03.0000.048.50.011.21-378,Date- 11/08/2022');


            } else {
                $mem = DB::table('tbl_rest_info_log as tbl_rest_info')
                    ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_rest_info.old_memorandum_id')
                    ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_rest_info.disembodiment_reason_id')
                    ->select('tbl_disembodiment_reason.reason_in_bng as reason', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');

                if ($option == 'smartCardNo') {
                    $l = strlen($id . '');
                    if ($l > 6)
                        $id = substr($id . '', 6);
                    $mem->where('tbl_rest_info.ansar_id', $id);
                }
                else {
                    $mem->where('tbl_memorandum_id.memorandum_id', $id);
                }
            }
        }
        $mem = $mem->first();
        // dd(DB::getQueryLog());
        // print_r( $result); exit;
        $result = DB::table(DB::raw("({$result->toSql()}) x"))->mergeBindings($result)->groupBy('ansar_id')->get();
        // dd(DB::getQueryLog()); exit;
        if($user->unit_eng=="CHITTAGONGNORTH" || $user->unit_eng=="CHITTAGONGSOUTH" || $user->unit_eng=="CHITTAGONGADMIN")
            $user->unit_short="চট্টগ্রাম";
        elseif ($user->unit_eng=="DHAKAADMIN"||$user->unit_eng=="DHAKAEAST"||$user->unit_eng=="DHAKAWEST"||$user->unit_eng=="DHAKASOUTH"||$user->unit_eng=="DHAKANORTH")
            $user->unit_short = "ঢাকা";
        else $user->unit_short = $user->unit;

        if ($mem && $result) {
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_disembodiment_letter']);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with('id', $id);
        }
    }


    function freezLetterPrint($id, $unit, $v,$option)
    {
        //DB::enableQueryLog();


        $mem = DB::table('tbl_freezing_info')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_freezing_info.memorandum_id')
            ->select('tbl_freezing_info.freez_reason as reason', 'tbl_freezing_info.comment_on_freez as comment', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');
        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')
            ->first();

        $result = DB::table('tbl_freezing_info')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_freezing_info.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_freezing_info.kpi_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_freezing_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_thana as kpi_thana', 'kpi_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            //->where('tbl_kpi_info.unit_id',$unit)
            ->select('kpi_thana.thana_name_bng as kpi_thana','tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name_bng as village_name', 'tbl_ansar_parsonal_info.post_office_name_bng as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_freezing_info.freez_date', 'tbl_freezing_info.embodiment_date', 'tbl_freezing_info.comment_on_freez', 'tbl_kpi_info.unit_id');
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_freezing_info.ansar_id',$id);
        }
        else{
            $result->where('tbl_memorandum_id.memorandum_id', $id);
            $mem->where('tbl_memorandum_id.memorandum_id', $id);
        }



        $mem = $mem->first();


        $result = DB::table(DB::raw("({$result->toSql()}) x"))->mergeBindings($result)->groupBy('ansar_id')->get();
//		dd(DB::getQueryLog());


        if ($mem && $result) {
            $user = DB::table('tbl_user')
                ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
                ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
                ->where('tbl_user.district_id', $result[0]->unit_id)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();

            if($user->unit_eng=="CHITTAGONGNORTH" || $user->unit_eng=="CHITTAGONGSOUTH" || $user->unit_eng=="CHITTAGONGADMIN")
                $user->unit_short="চট্টগ্রাম";
            elseif ($user->unit_eng=="DHAKAADMIN"||$user->unit_eng=="DHAKAEAST"||$user->unit_eng=="DHAKAWEST"||$user->unit_eng=="DHAKASOUTH"||$user->unit_eng=="DHAKANORTH")
                $user->unit_short = "ঢাকা";
            else $user->unit_short = $user->unit;

            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_freez_letter']);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with('id', $id);
        }
    }

    function reembodimentLetterPrint($id, $unit, $v,$option){

        DB::enableQueryLog();

        $mem = DB::table('tbl_freezing_info_log')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_freezing_info_log.memorandum_id')
            ->select('tbl_freezing_info_log.freez_reason as reason', 'tbl_freezing_info_log.comment_on_move as comment', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');

        $user = DB::table('tbl_user')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_user.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_user.district_id')
            ->join('tbl_division', 'tbl_units.division_id', '=', 'tbl_division.id')
            ->where('tbl_user.district_id', $unit)->select('tbl_units.unit_name_eng as unit_eng','tbl_user_details.first_name', 'tbl_user_details.last_name', 'tbl_user_details.mobile_no', 'tbl_user_details.email', 'tbl_units.unit_name_bng as unit','tbl_division.division_name_eng as division','tbl_division.division_name_bng as division_bng')->first();

        $result = DB::table('tbl_freezing_info_log')
            ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_freezing_info_log.memorandum_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
            ->join('tbl_ansar_parsonal_info', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_thana as kpi_thana', 'kpi_thana.id', '=', 'tbl_kpi_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->whereRaw('tbl_embodiment_log.release_date=tbl_rest_info.rest_date')
            ->where('tbl_kpi_info.unit_id',$unit)
            ->select('kpi_thana.thana_name_bng as kpi_thana','tbl_ansar_parsonal_info.ansar_id as ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_ansar_parsonal_info.father_name_bng as father_name', 'tbl_designations.name_bng as rank', 'tbl_kpi_info.kpi_name as kpi_name', 'tbl_ansar_parsonal_info.village_name_bng as village_name', 'tbl_ansar_parsonal_info.post_office_name_bng as pon', 'tbl_units.unit_name_bng as unit', 'tbl_thana.thana_name_bng as thana', 'tbl_embodiment_log.joining_date', 'tbl_embodiment_log.release_date')->orderBy('tbl_embodiment_log.id','DESC');
        if($option=='smartCardNo'){
            $l  = strlen($id.'');
            if($l>6) $id = substr($id.'',6);
            $result->where('tbl_ansar_parsonal_info.ansar_id',$id);
            $mem->where('tbl_rest_info.ansar_id',$id);
        }
        else{
            $result->where('tbl_memorandum_id.memorandum_id', $id);
            $mem->where('tbl_memorandum_id.memorandum_id', $id);
        }


        if(!$mem->exists()){

                $mem = DB::table('tbl_rest_info_log as tbl_rest_info')
                    ->join('tbl_memorandum_id', 'tbl_memorandum_id.memorandum_id', '=', 'tbl_rest_info.old_memorandum_id')
                    ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_rest_info.disembodiment_reason_id')
                    ->select('tbl_disembodiment_reason.reason_in_bng as reason', 'tbl_memorandum_id.memorandum_id', 'tbl_memorandum_id.mem_date as created_at');

                if ($option == 'smartCardNo') {
                    $l = strlen($id . '');
                    if ($l > 6)
                        $id = substr($id . '', 6);
                    $mem->where('tbl_rest_info.ansar_id', $id);
                }
                else {
                    $mem->where('tbl_memorandum_id.memorandum_id', $id);
                }

        }
        $mem = $mem->first();
        //dd(DB::getQueryLog());
        // print_r( $result); exit;
        $result = DB::table(DB::raw("({$result->toSql()}) x"))->mergeBindings($result)->groupBy('ansar_id')->get();
        //dd(DB::getQueryLog()); exit;
        if($user->unit_eng=="CHITTAGONGNORTH" || $user->unit_eng=="CHITTAGONGSOUTH" || $user->unit_eng=="CHITTAGONGADMIN")
            $user->unit_short="চট্টগ্রাম";
        elseif ($user->unit_eng=="DHAKAADMIN"||$user->unit_eng=="DHAKAEAST"||$user->unit_eng=="DHAKAWEST"||$user->unit_eng=="DHAKASOUTH"||$user->unit_eng=="DHAKANORTH")
            $user->unit_short = "ঢাকা";
        else $user->unit_short = $user->unit;

        if ($mem && $result) {
            return View::make('HRM::Letter.master')->with(['mem' => $mem, 'user' => $user, 'result' => $result, 'view' => 'print_disembodiment_letter']);
        } else {
            return View::make('HRM::Letter.no_mem_found')->with('id', $id);
        }

    }


    function embodimentLetterView()
    {
        return View::make('HRM::Letter.embodiment_letter');
    }

    function disembodimentLetterView()
    {
        return View::make('HRM::Letter.disembodiment_letter');
    }
    
    function freezeLetterView()
    {
        return View::make('HRM::Letter.freeze_letter');
    }

    function reembodimentLetterView()
    {
        return View::make('HRM::Letter.reembodiment_letter');
    }
}
