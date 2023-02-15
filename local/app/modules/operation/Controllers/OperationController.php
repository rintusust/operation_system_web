<?php

namespace App\modules\operation\Controllers;

use App\Helper\Facades\GlobalParameterFacades;
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

class OperationController  extends Controller
{
    use ExportDataToExcel;

    function opDashboard()
    {
//        $type = auth()->user()->type;
//        if ($type == 22 || $type == 66) {
//            return View::make('HRM::Dashboard.hrm-rc-dc');
//        } else {
//            return View::make('HRM::Dashboard.hrm');
//        }

            return View::make('operation::Dashboard.operation');
    }

    function progressInfo()
    {
        DB::enableQueryLog();
        $tseity = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->where('tbl_embodiment.emboded_status', 'Emboded')
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_status_info.black_list_status', 0)
            ->whereRaw('service_ended_date between NOW() and DATE_ADD(NOW(),INTERVAL 2 MONTH)');
        $ansarQuery = DB::table("tbl_ansar_parsonal_info")
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->where('tbl_ansar_status_info.retierment_status', 0)
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_status_info.black_list_status', 0)
            ->where('tbl_ansar_status_info.free_status', 0)
            ->select('tbl_ansar_parsonal_info.ansar_id');
        $ageLimitForAnsar = +GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
        $ageLimitForApcPc = +GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;

        $pcQuery = clone $ansarQuery;
        $apcQuery = clone $ansarQuery;

        $ansarQuery->where('designation_id', 1)->where(DB::raw("TIMESTAMPDIFF(YEAR,data_of_birth,DATE_ADD(NOW(),INTERVAL 3 MONTH))"), ">=", $ageLimitForAnsar);
        $pcQuery->where('designation_id', 3)->where(DB::raw("TIMESTAMPDIFF(YEAR,data_of_birth,DATE_ADD(NOW(),INTERVAL 3 MONTH))"), ">=", $ageLimitForApcPc);
        $apcQuery->where('designation_id', 2)->where(DB::raw("TIMESTAMPDIFF(YEAR,data_of_birth,DATE_ADD(NOW(),INTERVAL 3 MONTH))"), ">=", $ageLimitForApcPc);


        $arfyoa = $ansarQuery->unionAll($pcQuery)->unionAll($apcQuery);
        $tnimutt = DB::table('tbl_sms_offer_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->havingRaw('count(tbl_sms_offer_info.ansar_id)>10')->groupBy('tbl_sms_offer_info.ansar_id');
        if (Input::exists('division_id')) {
            $tseity->where('tbl_kpi_info.division_id', Input::get('division_id'));
            $arfyoa->where('division_id', Input::get('division_id'));
            $tnimutt->where('tbl_ansar_parsonal_info.division_id', Input::get('division_id'));
        }
        if (Input::exists('district_id')) {
            $tseity->where('tbl_kpi_info.unit_id', Input::get('district_id'));
            $arfyoa->where('unit_id', Input::get('district_id'));
            $tnimutt->where('tbl_ansar_parsonal_info.unit_id', Input::get('district_id'));

        }
        $tseity = $tseity->count('tbl_embodiment.ansar_id');
        $arfyoa = DB::table(DB::raw("(" . $arfyoa->toSql() . ") t"))->mergeBindings($arfyoa)->count('ansar_id');
        $tnimutt = $tnimutt->get();
        //return $tnimutt;
        $i = 0;
        $uiui = array();
        foreach ($tnimutt as $tttt) {

            $uiui[$i] = $tttt->ansar_id;
            $i++;
        }

//return $tnimutt;
        //return (DB::getQueryLog());
        // $t = DB::select(DB::raw("(SELECT count(ansar_id) as t FROM tbl_embodiment WHERE emboded_status = 'Emboded' AND service_ended_date BETWEEN NOW() AND DATE_ADD(NOW(),INTERVAL 2 MONTH)) UNION ALL (SELECT count(ansar_id) as t FROM tbl_ansar_parsonal_info WHERE TIMESTAMPDIFF(YEAR,DATE_ADD(data_of_birth,INTERVAL 3 MONTH),NOW())>=50) UNION ALL (SELECT IFNULL((SELECT count(ansar_id) as t FROM tbl_sms_offer_info  GROUP BY ansar_id HAVING count(ansar_id)>10),0))"));
        $progressInfo = array(
            'totalServiceEndedInThreeYears' => $tseity,
            'totalAnsarReachedFiftyYearsOfAge' => $arfyoa,
            'totalNotInterestedMembersUptoTenTimes' => $i
        );
        return Response::json($progressInfo);
    }

    public function graphEmbodiment(Request $request)
    {
        DB::enableQueryLog();
        $ea1 = DB::table('tbl_embodiment_log')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->whereRaw('joining_date BETWEEN DATE_FORMAT(DATE_ADD(DATE_SUB(NOW(),INTERVAL 1 YEAR),INTERVAL 1 MONTH),"%Y-%m-01") AND NOW()')
            ->groupBy(DB::raw('MONTH(joining_date)'))->orderBy(DB::raw('YEAR(joining_date)'))->orderBy(DB::raw('MONTH(joining_date)'))
            ->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as total,joining_date as month'));
        $ea2 = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->whereRaw('joining_date BETWEEN DATE_FORMAT(DATE_ADD(DATE_SUB(NOW(),INTERVAL 1 YEAR),INTERVAL 1 MONTH),"%Y-%m-01") AND NOW()')
            ->groupBy(DB::raw('MONTH(joining_date)'))->orderBy(DB::raw('YEAR(joining_date)'))->orderBy(DB::raw('MONTH(joining_date)'))
            ->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as total,joining_date as month'));
        $da = DB::table('tbl_embodiment_log')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment_log.ansar_id')
            ->whereRaw('release_date BETWEEN DATE_FORMAT(DATE_ADD(DATE_SUB(NOW(),INTERVAL 1 YEAR),INTERVAL 1 MONTH),"%Y-%m-01") AND NOW()')
            ->groupBy(DB::raw('MONTH(release_date)'))->orderBy(DB::raw('YEAR(release_date)'))->orderBy(DB::raw('MONTH(release_date)'))
            ->select(DB::raw('count(tbl_ansar_parsonal_info.ansar_id) as total,DATE_FORMAT(release_date,"%b,%y") as month'));
        if ($request->division_id) {
            $ea1->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $ea2->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $da->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
        }
        if ($request->district_id) {
            $ea1->where('tbl_ansar_parsonal_info.unit_id', $request->district_id);
            $ea2->where('tbl_ansar_parsonal_info.unit_id', $request->district_id);
            $da->where('tbl_ansar_parsonal_info.unit_id', $request->district_id);
        }
        $sql = $ea1->unionAll($ea2);
        $ea = DB::table(DB::raw("({$sql->toSql()}) x"))->mergeBindings($sql)->select(DB::raw('SUM(total) as total,DATE_FORMAT(month,"%b,%y") as month'))->orderBy(DB::raw('YEAR(month)'))->orderBy(DB::raw('MONTH(month)'))->groupBy(DB::raw('MONTH(month)'));
        $b = Response::json(["ea" => $ea->get(), 'da' => $da->get()]);
        //return DB::getQueryLog();
        return $b;
    }

    public function graphDisembodiment()
    {
        $ansars = DB::select(DB::raw('(select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =1 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =2 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =3 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =4 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =5 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =6 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =7 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =8 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =9 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =10 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =11 ) UNION ALL  (select count(rest_date) as em from tbl_rest_info WHERE EXTRACT(MONTH from rest_date) =12 )'));
        $graph_disembodiment = array(
            'jan_ansar' => $ansars[0]->em,
            'feb_ansar' => $ansars[1]->em,
            'march_ansar' => $ansars[2]->em,
            'april_ansar' => $ansars[3]->em,
            'may_ansar' => $ansars[4]->em,
            'june_ansar' => $ansars[5]->em,
            'july_ansar' => $ansars[6]->em,
            'aug_ansar' => $ansars[7]->em,
            'sep_ansar' => $ansars[8]->em,
            'oct_ansar' => $ansars[9]->em,
            'nov_ansar' => $ansars[10]->em,
            'dec_ansar' => $ansars[11]->em
        );
        return Response::json($graph_disembodiment);
    }

    public function getRecentAnsar(Request $request)
    {
        $recentTime = Carbon::now();
        $backTime = Carbon::now()->subDays(7);
        $allStatus = array(
            'recentAnsar' => DB::table('tbl_ansar_parsonal_info')->whereBetween('tbl_ansar_parsonal_info.created_at', array($backTime, $recentTime)),
            'recentNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->whereBetween('tbl_ansar_parsonal_info.updated_at', array($backTime, $recentTime)),
            'recentFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->whereBetween('tbl_panel_info.panel_date', array($backTime, $recentTime)),
            'recentOffered' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->join('tbl_units', 'tbl_sms_offer_info.district_id', '=', 'tbl_units.id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentOfferedReceived' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_sms_receive_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')->join('tbl_units', 'tbl_sms_receive_info.offered_district', '=', 'tbl_units.id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('embodied_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentFreezeOther' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_freezing_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')->join('tbl_kpi_info', 'tbl_freezing_info.kpi_id', '=', 'tbl_kpi_info.id')->where('freezing_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_blacklist_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')->where('block_list_status', 1)->whereBetween('tbl_blacklist_info.black_listed_date', array($backTime, $recentTime)),
            'recentBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentRetire' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('retierment_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentOfferBlock' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('offer_block_status', 1)->where('block_list_status', 0)->whereBetween('tbl_ansar_status_info.updated_at', array($backTime, $recentTime)),
            'recentDeath' => DB::table('tbl_embodiment_log')->join('tbl_ansar_parsonal_info', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('tbl_embodiment_log.disembodiment_reason_id', 9)->whereBetween('tbl_embodiment_log.updated_at', array($backTime, $recentTime)),
        );
        if ($request->division_id) {
            $allStatus['recentAnsar']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentNotVerified']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentFree']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentPanel']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentOffered']->where('tbl_units.division_id', $request->division_id);
            $allStatus['recentOfferedReceived']->where('tbl_units.division_id', $request->division_id);
            $allStatus['recentEmbodied']->where('tbl_kpi_info.division_id', $request->division_id);
            $allStatus['recentEmbodiedOwn']->where('tbl_kpi_info.division_id', $request->division_id);
            $allStatus['recentEmbodiedDiff']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentFreeze']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentFreezeOther']->where('tbl_kpi_info.division_id', $request->division_id);
            $allStatus['recentBlockList']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentBlackList']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentRest']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentRetire']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['recentOfferBlock']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
        }
        if ($request->unit_id) {
            $allStatus['recentAnsar']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentNotVerified']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentFree']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentPanel']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentOffered']->where('tbl_units.id', $request->unit_id);
            $allStatus['recentOfferedReceived']->where('tbl_units.id', $request->unit_id);
            $allStatus['recentEmbodied']->where('tbl_kpi_info.unit_id', $request->unit_id);
            $allStatus['recentEmbodiedOwn']->where('tbl_kpi_info.unit_id', $request->unit_id);
            $allStatus['recentEmbodiedDiff']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentFreeze']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentFreezeOther']->where('tbl_kpi_info.unit_id', $request->unit_id);
            $allStatus['recentBlockList']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentBlackList']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentRest']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentRetire']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['recentOfferBlock']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
        }
        $results = [];
        foreach ($allStatus as $key => $q) {
            $results[$key] = $q->distinct()->count('tbl_ansar_parsonal_info.ansar_id');
        }
        return Response::json($results);
    }

    public function showAnsarList($type)
    {   
        $pageTitle = '';
        $custom_filter = '<filter-template
                                show-item="[\'range\',\'unit\',\'thana\',\'gender\']"
                                type="all"
                                range-change="loadPage()"
                                unit-change="loadPage()"
                                thana-change="loadPage()"
                                gender-change="loadPage()"
                                on-load="loadPage()"
                                data="param"
                                start-load="range"
                                field-width="{range:\'col-sm-3\',unit:\'col-sm-3\',thana:\'col-sm-3\',gender:\'col-sm-3\'}"
                        ></filter-template>';
        $view = '';
        if (strcasecmp($type, 'all_ansar') == 0) {
            $pageTitle = "Total Ansars";
        } elseif (strcasecmp($type, 'not_verified_ansar') == 0) {
            $pageTitle = "Total Unverified Ansars";
        } elseif (strcasecmp($type, 'offerred_ansar') == 0) {
            $pageTitle = "Total Offered Ansars";
            
            $view = '<div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Offer Date Range
                                </label></br>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                    <input type="text" name="from_date" id="from_date" date-picker=""
                                           class="form-control" placeholder="From Date" ng-model="from_date">
                                </div>
                                <div class="col-md-1 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                    <div class="" style="text-align: center; padding:5px">to</div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                    <input type="text" name="to_date" date-picker="" id="to_date" class="form-control"
                                           placeholder="To Date" ng-model="to_date">
                                </div>
                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <a class="btn btn-primary" ng-click="loadPage()">Load Result</a>
                                </div>
                            </div>
                        </div>
                </div>';
        } elseif (strcasecmp($type, 'freezed_ansar') == 0) {
            $pageTitle = "Total Frozen Ansars";
            
            $view = '<div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Freeze Date Range
                                </label></br>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                    <input type="text" name="from_date" id="from_date" date-picker=""
                                           class="form-control" placeholder="From Date" ng-model="from_date">
                                </div>
                                <div class="col-md-1 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                    <div class="" style="text-align: center; padding:5px">to</div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                    <input type="text" name="to_date" date-picker="" id="to_date" class="form-control"
                                           placeholder="To Date" ng-model="to_date">
                                </div>
                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <a class="btn btn-primary" ng-click="loadPage()">Load Result</a>
                                </div>
                            </div>
                        </div>
                </div>';
        } elseif (strcasecmp($type, 'free_ansar') == 0) {
            $pageTitle = "Total Free Ansars";
        } elseif (strcasecmp($type, 'paneled_ansar') == 0) {
            $pageTitle = "Total Paneled Ansars";
            $custom_filter = '<filter-template
                                show-item="[\'range\',\'unit\',\'thana\',\'gender\']"
                                type="all"
                                range-change="loadPage()"
                                unit-change="loadPage()"
                                thana-change="loadPage()"
                                gender-change="loadPage()"
                                enable-offer-zone="0"
                                on-load="loadPage()"
                                data="param"
                                start-load="range"
                                field-width="{range:\'col-sm-3\',unit:\'col-sm-3\',thana:\'col-sm-3\',gender:\'col-sm-3\'}"
                        ></filter-template>';
            $view = '<div class="row">
                    <div class="col-sm-3">
                        <label style="display:block">&nbsp</label>
                        <label class="control-label">
                            <div class="styled-checkbox">
                                <input id="global_available_ansar"  type="checkbox" ng-model="param.filter_mobile_no" ng-true-value="1" ng-false-value="0" ng-change="loadPage()">
                                <label for="global_available_ansar"></label>
                            </div>
                            &nbsp;Available global ansar
                        </label>
                    </div>
                    <div class="col-sm-3">
                        <label style="display:block">&nbsp</label>
                        <label class="control-label">
                            <div class="styled-checkbox">
                                <input id="regional_available_ansar"  type="checkbox" ng-model="param.filter_age" ng-true-value="1" ng-false-value="0" ng-change="loadPage()">
                                <label for="regional_available_ansar"></label>
                            </div>
                            &nbsp;Available regional ansar
                        </label>
                    </div>
                </div>';
        } elseif (strcasecmp($type, 'rest_ansar') == 0) {
            $pageTitle = "Total Resting Ansars";
            
            $view = '<div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Joining Date Range
                                </label></br>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                    <input type="text" name="from_date" id="from_date" date-picker=""
                                           class="form-control" placeholder="From Date" ng-model="from_date">
                                </div>
                                <div class="col-md-1 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                    <div class="" style="text-align: center; padding:5px">to</div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                    <input type="text" name="to_date" date-picker="" id="to_date" class="form-control"
                                           placeholder="To Date" ng-model="to_date">
                                </div>
                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <a class="btn btn-primary" ng-click="loadPage()">Load Result</a>
                                </div>
                            </div>
                        </div>
                </div>';
        } elseif (strcasecmp($type, 'blocked_ansar') == 0) {
            $pageTitle = "Total Blocklisted Ansars";
            
            $view = '<div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Block Date Range
                                </label></br>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                    <input type="text" name="from_date" id="from_date" date-picker=""
                                           class="form-control" placeholder="From Date" ng-model="from_date">
                                </div>
                                <div class="col-md-1 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                    <div class="" style="text-align: center; padding:5px">to</div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                    <input type="text" name="to_date" date-picker="" id="to_date" class="form-control"
                                           placeholder="To Date" ng-model="to_date">
                                </div>
                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <a class="btn btn-primary" ng-click="loadPage()">Load Result</a>
                                </div>
                            </div>
                        </div>
                </div>';
        } elseif (strcasecmp($type, 'blacked_ansar') == 0) {
            $pageTitle = "Total Blacklisted Ansars";
            
             $view = '<div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Black Listed Date Range
                                </label></br>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                    <input type="text" name="from_date" id="from_date" date-picker=""
                                           class="form-control" placeholder="From Date" ng-model="from_date">
                                </div>
                                <div class="col-md-1 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                    <div class="" style="text-align: center; padding:5px">to</div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                    <input type="text" name="to_date" date-picker="" id="to_date" class="form-control"
                                           placeholder="To Date" ng-model="to_date">
                                </div>
                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <a class="btn btn-primary" ng-click="loadPage()">Load Result</a>
                                </div>
                            </div>
                        </div>
                </div>';            
            
        } elseif (strcasecmp($type, 'embodied_ansar') == 0) {
            $pageTitle = "Total Embodied Ansars";
            
            $view = '<div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Embodiment Date Range
                                </label></br>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                    <input type="text" name="from_date" id="from_date" date-picker=""
                                           class="form-control" placeholder="From Date" ng-model="from_date">
                                </div>
                                <div class="col-md-1 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                    <div class="" style="text-align: center; padding:5px">to</div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                    <input type="text" name="to_date" date-picker="" id="to_date" class="form-control"
                                           placeholder="To Date" ng-model="to_date">
                                </div>
                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <a class="btn btn-primary" ng-click="loadPage()">Load Result</a>
                                </div>
                            </div>
                        </div>
                </div>';
        }elseif (strcasecmp($type, 'same_kpi_six_month_ansar') == 0) {
            
            $pageTitle = "6 Months Over In Guard";
			$custom_filter = '<filter-template
                                show-item="[\'range\',\'unit\',\'thana\',\'kpi\']"
                                type="all"
                                range-change="loadPage()"
                                unit-change="loadPage()"
                                thana-change="loadPage()"
	                        kpi-change="loadPage()"                                
                                enable-offer-zone="0"
                                on-load="loadPage()"
                                data="param"
                                custom-field="true"
                                custom-model="param.selectedDate"
                                custom-label="Select an Option"
                                custom-data="customData"
                                custom-change="loadPage()"
                                start-load="range" 
                                field-width="{range:\'col-sm-3\',unit:\'col-sm-2\',thana:\'col-sm-2\',kpi:\'col-sm-3\',custom:\'col-sm-2\'}"
                        ></filter-template>';
            
            $view = '<div class="row">
                    <div class="col-sm-4 col-sm-offset-8">
                            <div class="form-group row" ng-if="param.selectedDate==-1">
                                <div class="col-xs-5">
                                    <input type="text" class="form-control" ng-model="param.selected.custom"
                                           placeholder="No of day,month or year">
                                </div>
                                <div class="col-xs-5" style="padding-left: 0;" ng-init="param.selected.type="';
            $view = $view."'1'";
            $view = $view.' ">
                                    <select class="form-control" ng-model="param.selected.type">
                                        <option value="1">Days</option>
                                        <option value="2">Week</option>
                                        <option value="3">Months</option>
                                        <option value="4">Years</option>
                                    </select>
                                </div>
                                <div class="col-xs-2" style="padding-left: 0;">
                                    <button class="btn btn-primary pull-right" ng-click="loadPage()">
                                        <i class="fa fa-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                </div>';
            
            
            
        } elseif (strcasecmp($type, 'own_embodied_ansar') == 0) {
            $pageTitle = "Own Embodied Ansars";
        } elseif (strcasecmp($type, 'embodied_ansar_in_different_district') == 0) {
            $pageTitle = "Embodied Ansar in Different District";
        } elseif (strcasecmp($type, 'offer_block') == 0) {
            $pageTitle = "Total Offer Blocked";
            
            $view = '<div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Offer Blocked Date Range
                                </label></br>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                    <input type="text" name="from_date" id="from_date" date-picker=""
                                           class="form-control" placeholder="From Date" ng-model="from_date">
                                </div>
                                <div class="col-md-1 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                    <div class="" style="text-align: center; padding:5px">to</div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                    <input type="text" name="to_date" date-picker="" id="to_date" class="form-control"
                                           placeholder="To Date" ng-model="to_date">
                                </div>
                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <a class="btn btn-primary" ng-click="loadPage()">Load Result</a>
                                </div>
                            </div>
                        </div>
                </div>';
        } elseif (strcasecmp($type, 'retire_ansar') == 0) {
            $pageTitle = "Total Blocked For Aged Ansar";
            
            $view = '<div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Retirement Date Range
                                </label></br>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                    <input type="text" name="from_date" id="from_date" date-picker=""
                                           class="form-control" placeholder="From Date" ng-model="from_date">
                                </div>
                                <div class="col-md-1 col-sm-12 col-xs-12"
                                     style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                    <div class="" style="text-align: center; padding:5px">to</div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12"
                                     style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                    <input type="text" name="to_date" date-picker="" id="to_date" class="form-control"
                                           placeholder="To Date" ng-model="to_date">
                                </div>
                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <a class="btn btn-primary" ng-click="loadPage()">Load Result</a>
                                </div>
                            </div>
                        </div>
                </div>';
        } elseif (strcasecmp($type, 'freezed_ansar_other') == 0) {
            $pageTitle = "Total Frozen Ansars in Different District";
        }elseif (strcasecmp($type, 'death_ansar') == 0) {
            $pageTitle = "Total Death Ansars";
        }
        return View::make('HRM::Dashboard.view_ansar_list')->with(['type' => $type, 'pageTitle' => $pageTitle, 'custom_view' => $view, 'custom_filter' => $custom_filter]);
    }
    
    
    public function showAvailableAnsarList()
    {
        $pageTitle = '';
        
        $custom_filter = '<filter-template
                                show-item="[\'range\',\'unit\',\'gender\']"
                                type="all"
                                range-change="loadPage()"
                                unit-change="loadPage()"
                                gender-change="loadPage()"
                                on-load="loadPage()"
                                data="param"
                                start-load="range"
                                field-width="{range:\'col-sm-3\',unit:\'col-sm-3\',thana:\'col-sm-3\',gender:\'col-sm-3\'}"
                        ></filter-template>';
        $view = '';
       
        $pageTitle = "Available Ansar In Individual Unit Offer";
      
        return View::make('HRM::Dashboard.view_available_ansar_list')->with(['pageTitle' => $pageTitle, 'custom_view' => $view, 'custom_filter' => $custom_filter]);
    }
    

    public function showRecentAnsarList($type)
    {
        $pageTitle = '';
        if (strcasecmp($type, 'all_ansar') == 0) {
            $pageTitle = "Total Ansars (Recent)";
        } elseif (strcasecmp($type, 'not_verified_ansar') == 0) {
            $pageTitle = "Total Unverified Ansars (Recent)";
        } elseif (strcasecmp($type, 'offerred_ansar') == 0) {
            $pageTitle = "Total Offered Ansars (Recent)";
        } elseif (strcasecmp($type, 'freezed_ansar') == 0) {
            $pageTitle = "Total Frozen Ansars (Recent)";
        } elseif (strcasecmp($type, 'free_ansar') == 0) {
            $pageTitle = "Total Free Ansars (Recent)";
        } elseif (strcasecmp($type, 'paneled_ansar') == 0) {
            $pageTitle = "Total Paneled Ansars (Recent)";
        } elseif (strcasecmp($type, 'rest_ansar') == 0) {
            $pageTitle = "Total Resting Ansars (Recent)";
        } elseif (strcasecmp($type, 'blocked_ansar') == 0) {
            $pageTitle = "Total Block-listed Ansars (Recent)";
        } elseif (strcasecmp($type, 'blacked_ansar') == 0) {
            $pageTitle = "Total Blacklisted Ansars (Recent)";
        } elseif (strcasecmp($type, 'embodied_ansar') == 0) {
            $pageTitle = "Total Embodied Ansars (Recent)";
        } elseif (strcasecmp($type, 'embodied_ansar_in_different_district') == 0) {
            $pageTitle = "Total Embodied Ansars in Diffrenet District (Recent)";
        } elseif (strcasecmp($type, 'own_embodied_ansar') == 0) {
            $pageTitle = "Total Embodied Ansars in Own District (Recent)";
        } elseif (strcasecmp($type, 'offer_block') == 0) {
            $pageTitle = "Total Offer Blocked(Recent)";
        } elseif (strcasecmp($type, 'retire') == 0) {
            $pageTitle = "Total Total Blocked For Aged Ansar (Recent)";
        } elseif (strcasecmp($type, 'freezed_ansar_other') == 0) {
            $pageTitle = "Total Frozen Ansars in Different District (Recent)";
        }elseif (strcasecmp($type, 'death_ansar') == 0) {
            $pageTitle = "Total Death Ansars (Recent)";
        }
        return View::make('HRM::Dashboard.view_recent_ansar_list')->with(['type' => $type, 'pageTitle' => $pageTitle]);
    }

    public function offerAcceptLastFiveDays()
    {
        return view('HRM::Dashboard.offer_accept_last_5_days');
    }

    public function getAnsarList(Request $request)
    {
        $type = Input::get('type');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $sex = Input::get('gender');
        $q = Input::get('q');
        $available_global = Input::get('filter_mobile_no');
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');    
        $kpi = Input::get('kpi_id');  
       
        

        $rules = [
            'type' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
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
        switch ($type) {
            case 'all_ansar':
                $data = CustomQuery::getAllAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'not_verified_ansar':
                $data = CustomQuery::getTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'free_ansar':
                $data = CustomQuery::getTotalFreeAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'paneled_ansar':
                $data = CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $request->filter_mobile_no, $request->filter_age, $q);
                break;
            case 'embodied_ansar':
                $data = CustomQuery::getTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q, $sex, $from_date, $to_date);
                break;
            case 'same_kpi_six_month_ansar':
                $data = CustomQuery::getTotalSixMonthFlaggedAnsarList($offset, $limit, $unit, $thana, $division, CustomQuery::ALL_TIME, $rank, $q, $sex, $from_date, $to_date, $kpi, $request->selected_date, $request->custom_date);
                break;
            case 'rest_ansar':
                $data = CustomQuery::getTotalRestAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q, $from_date, $to_date );
                break;
            case 'retire_ansar':
                $data = CustomQuery::getTotalRetireAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q, $from_date, $to_date );
                break;
            case 'freezed_ansar':
                $data = CustomQuery::getTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q, $from_date, $to_date);
                break;
            case 'blocked_ansar':
                $data = CustomQuery::getTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q, $from_date, $to_date);
                break;
            case 'blacked_ansar':
                $data = CustomQuery::getTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q, $from_date, $to_date);
                break;
            case 'offerred_ansar':
                $data = CustomQuery::getTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q,  $from_date, $to_date );
                break;
            case 'offer_block':
                $data = CustomQuery::getTotalOfferBlockAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q, $from_date, $to_date);
                break;
            case 'own_embodied_ansar':
                $data = CustomQuery::getTotalOwnEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'embodied_ansar_in_different_district':
                $data = CustomQuery::getTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'freezed_ansar_other':
                $data = CustomQuery::getTotalOtherFreezedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q);
                break;
            case 'death_ansar':
                $data = CustomQuery::getTotalDeathAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $q);
                break;
        }
        if ($request->exists('export')) {
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_view_excel', $type);
        }
        return Response::json($data);
    }
    
    
     public function getAvailableAnsarList(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $sex = Input::get('gender');
        $q = Input::get('q');
        $available_global = Input::get('filter_mobile_no');
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');
        $type = 'paneled_ansar';
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
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
        
        
        # Offer Process Start
        
        $user = Auth::user();
        
//        if ($user->type == 22) {
//             $district_id = $user->district_id;
//              if (in_array($district_id, Config::get('app.offer'))) {
//             //if (in_array($district_id, $global_offer)) {    
//                 $offer_type = 'GB';               
//             } else {
//                 $offer_type = 'RE';
//             }
//        } else {
//             $district_id = $request->get('district_id');
//             if (in_array($district_id, Config::get('app.offer'))) {
//             //if (in_array($district_id, $global_offer)) {    
//                 $offer_type = 'GB';
//             } else {
//                 $offer_type = 'RE';
//             }
//        }        
        #
        
         $district_id = $unit;
              if (in_array($district_id, Config::get('app.offer'))) {
             //if (in_array($district_id, $global_offer)) {    
                 $offer_type = 'GB';               
             } else {
                 $offer_type = 'RE';
             }



         //$com_ctg_range = array(2,7,8,9,11,16,26,31,48,55,72);

         /** Decision by Ansar ICT to lift restriction  12-10-2022 (Rintu) */
         $com_ctg_range = array();

        if(in_array($unit, $com_ctg_range)){
            $offerZone= array();
        }else{
            $offerZone = OfferZone::where('unit_id', $district_id)->pluck('offer_zone_unit_id')->toArray();  
        }
        
        
            
        if($unit != 'all'){
            
            $data = CustomQuery::getAvailableAnsarInfo(
                ['male' => $request->get('pc_male'), 'female' => $request->get('pc_female')],
                ['male' => $request->get('apc_male'), 'female' => $request->get('apc_female')],
                ['male' => $request->get('ansar_male'), 'female' => $request->get('ansar_female')],
                $request->get('district'),
                $district_id, Auth::user(), $offerZone, $offer_type, $district_id, $offset, $limit, $sex, $rank, $q);
            
        }else{
            $data = [];
        }
        //$data = CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $request->filter_mobile_no, $request->filter_age, $q);
        
                
        if ($request->exists('export')) {
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_view_excel', $type);
        }
        return Response::json($data);
    }

    public function getRecentAnsarList(Request $request)
    {
        $type = Input::get('type');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $view = Input::get('view');
        $rank = Input::get('rank');
        $division = Input::get('division');
        $q = Input::get('q');
        $sex = Input::get('gender');
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'view' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        switch ($type) {
            case 'all_ansar':
                $data = CustomQuery::getAllAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'not_verified_ansar':
                $data = CustomQuery::getTotalNotVerifiedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'free_ansar':
                $data = CustomQuery::getTotalFreeAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'paneled_ansar':
                $data = CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $request->filter_mobile_no, $request->filter_age, $q);
                break;
            case 'embodied_ansar':
                $data = CustomQuery::getTotalEmbodiedAnsarList($offset, $limit, $unit, $thana, $division,  CustomQuery::RECENT, $rank, $q, $sex);
                break;
            case 'embodied_ansar_in_different_district':
                $data = CustomQuery::getTotalDiffEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'own_embodied_ansar':
                $data = CustomQuery::getTotalOwnEmbodiedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'retire_ansar':
                $data = CustomQuery::getTotalRetireAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'rest_ansar':
                $data = CustomQuery::getTotalRestAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'freezed_ansar':
                $data = CustomQuery::getTotalFreezedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'blocked_ansar':
                $data = CustomQuery::getTotalBlockedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'blacked_ansar':
                $data = CustomQuery::getTotalBlackedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'offerred_ansar':
                $data = CustomQuery::getTotalOfferedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'offer_block':
                $data = CustomQuery::getTotalOfferBlockAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'freezed_ansar_other':
                $data = CustomQuery::getTotalOtherFreezedAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;
            case 'death_ansar':
                $data = CustomQuery::getTotalDeathAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::RECENT, $rank, $q);
                break;

        }
        if ($request->exists('export')) {
            return $this->exportData($data['ansars'], $type);
        }
        return Response::json($data);
    }

    public function showAnsarForServiceEnded($count)
    {
        $pages = ceil($count / 10);
        return View::make('HRM::Dashboard.ansar_service_ended_list')->with(['total' => $count, 'pages' => $pages, 'item_per_page' => 10]);
    }

    public function serviceEndedInfoDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $q = Input::get('q');
        $division = Input::get('division');
        $interval = Input::get('interval');
        $rank = Input::get('rank');
        $gender = Input::get('gender');
        $rules = [
            'view' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'interval' => 'numeric|regex:/^[0-9]+$/',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        $data = CustomQuery::ansarListForServiceEndedWithRankGender($offset, $limit, $unit, $thana, $division, $interval, $rank, $gender, $q);
        if ($request->exists('export')) {
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.selected_service_ended_ansar_list');
        }
        return Response::json($data);
    }

    public function showAnsarForReachedFifty($count)
    {
        $pages = ceil(intval($count) / 10);
        return View::make('HRM::Dashboard.ansar_fifty_age_list')->with(['total' => $count, 'pages' => $pages, 'item_per_page' => 10]);
    }

    public function ansarReachedFiftyDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $q = Input::get('q');
        $rank = Input::get('rank');
        $gender = Input::get('gender');
        $division = Input::get('division');
        $status = Input::get('status_data');
        $rules = [
            'limit' => 'numeric',
            'offset' => 'numeric',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        $data = CustomQuery::ansarListWithFiftyYearsWithRankGender($offset, $limit, $unit, $thana, $division, $q, $request->selected_date, $request->custom_date, $request->rank, $request->gender, $status);
        if ($request->exists('export')) {
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_fifty_age_report');
        }
        return Response::json($data);
    }

    public function showAnsarForNotInterested($count)
    {
        $pages = ceil($count / 10);
        return View::make('HRM::Dashboard.ansar_not_interested')->with(['total' => $count, 'pages' => $pages, 'item_per_page' => 10]);
    }

    public function notInterestedInfoDetails()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $q = Input::get('q');
        $division = Input::get('division');
        $rules = [
            'view' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        return CustomQuery::ansarListForNotInterested($offset, $limit, $unit, $thana, $division, $q);
    }

    public function getTotalAnsar(Request $request)
    {
        $allStatus = array(
            'totalAnsar' => DB::table('tbl_ansar_parsonal_info'),
            'totalNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('black_list_status', 0),
            'totalFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0),
            'totalPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0),
            'totalOffered' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->join('tbl_units', 'tbl_sms_offer_info.district_id', '=', 'tbl_units.id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0),
            'totalOfferedReceived' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_sms_receive_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')->join('tbl_units', 'tbl_sms_receive_info.offered_district', '=', 'tbl_units.id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0),
            'totalEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('embodied_status', 1)->where('block_list_status', 0),
            'totalEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1),
            'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1),
            'totalFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0),
            'totalFreezeOther' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_freezing_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_freezing_info.ansar_id')->join('tbl_kpi_info', 'tbl_freezing_info.kpi_id', '=', 'tbl_kpi_info.id')->where('freezing_status', 1)->where('block_list_status', 0),
            'totalBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1),
            'totalBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1),
            'totalRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0),
            'totalRetire' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('retierment_status', 1)->where('block_list_status', 0),
            'totalOfferBlock' => DB::table('tbl_ansar_status_info')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_offer_blocked_ansar', 'tbl_offer_blocked_ansar.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_units', 'tbl_offer_blocked_ansar.last_offer_unit', '=', 'tbl_units.id')
                ->where('offer_block_status', 1)->where('block_list_status', 0)->whereNull('tbl_offer_blocked_ansar.deleted_at'),
            'totalOfferBlockOwnDistrict' => DB::table('tbl_ansar_status_info')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('offer_block_status', 1)->where('block_list_status', 0),
            'totalDeath' => DB::table('tbl_embodiment_log')->join('tbl_ansar_parsonal_info', 'tbl_embodiment_log.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('tbl_embodiment_log.disembodiment_reason_id', 9),

        );
        if ($request->division_id) {
            $allStatus['totalAnsar']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalNotVerified']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalFree']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalPanel']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalOffered']->where('tbl_units.division_id', $request->division_id);
            $allStatus['totalOfferedReceived']->where('tbl_units.division_id', $request->division_id);
            $allStatus['totalEmbodied']->where('tbl_kpi_info.division_id', $request->division_id);
            $allStatus['totalEmbodiedOwn']->where('tbl_kpi_info.division_id', $request->division_id);
            $allStatus['totalEmbodiedDiff']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalFreeze']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalFreezeOther']->where('tbl_kpi_info.division_id', $request->division_id);
            $allStatus['totalBlockList']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalBlackList']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalRest']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalRetire']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
            $allStatus['totalOfferBlock']->where('tbl_units.division_id', $request->division_id);
            $allStatus['totalOfferBlockOwnDistrict']->where('tbl_ansar_parsonal_info.division_id', $request->division_id);
        }
        if ($request->unit_id) {
            $allStatus['totalAnsar']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalNotVerified']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalFree']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalPanel']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalOffered']->where('tbl_units.id', $request->unit_id);
            $allStatus['totalOfferedReceived']->where('tbl_units.id', $request->unit_id);
            $allStatus['totalEmbodied']->where('tbl_kpi_info.unit_id', $request->unit_id);
            $allStatus['totalEmbodiedOwn']->where('tbl_kpi_info.unit_id', $request->unit_id);
            $allStatus['totalEmbodiedDiff']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalFreeze']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalFreezeOther']->where('tbl_kpi_info.unit_id', $request->unit_id);
            $allStatus['totalBlockList']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalBlackList']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalRest']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalRetire']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
            $allStatus['totalOfferBlock']->where('tbl_units.id', $request->unit_id);
            $allStatus['totalOfferBlockOwnDistrict']->where('tbl_ansar_parsonal_info.unit_id', $request->unit_id);
        }
        $results = [];
        foreach ($allStatus as $key => $q) {
            $results[$key] = $q->distinct()->count('tbl_ansar_parsonal_info.ansar_id');
        }
        return Response::json($results);
    }

    function updateGlobalParameter()
    {
        $rules = [
            'id' => 'required|numeric',
            'pv' => 'required|numeric|regex:/^[0-9]+$/',
            'pu' => 'regex:/^[a-zA-Z]+$/',
            'pd' => 'regex:/^[a-zA-Z\s]+$/',
            'pp' => 'numeric|regex:/^[0-9]+$/'
        ];
        $messages = [
            'pv.required' => 'Parameter value is required',
            'pv.numeric' => 'Parameter value must be numeric.eg 1,2..',
            'pv.regex' => 'Parameter value must be numeric.eg 1,2..',
            'pp.numeric' => 'Parameter unit must be numeric.eg 1,2..',
            'pp.regex' => 'Parameter unit must be numeric.eg 1,2..',
            'pu.regex' => 'Parameter unit is invalid',
            'pd.regex' => 'Parameter description only contain a-z,A-Z and space',
        ];
        $valid = Validator::make(Input::all(), $rules, $messages);
        if ($valid->fails()) {
            return response($valid->messages()->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        $id = Input::get('id');
        $pv = Input::get('pv');
        $pd = Input::get('pd');
        $pp = Input::get('pp');
        $pu = Input::get('pu');
        DB::beginTransaction();
        try {
            $gp = GlobalParameter::find($id);
            $gp->param_value = $pv;
            $gp->param_description = $pd;
            $gp->param_piority = $pp;
            $gp->param_unit = $pu;
            $gp->save();
            DB::commit();
        } catch (Exception $e) {
            return Response::json(['status' => false, 'data' => 'Unable to update. try again later']);
        }

        return Response::json(['status' => true, 'data' => 'Update complete successfully']);
    }

    function globalParameterView()
    {
        return View::make('HRM::global_perameter')->with('gp', GlobalParameter::all());
    }

    function getTemplate($key)
    {
        return View::make('HRM::Partial_view.' . $key . '_list');
    }

    function ansarAcceptOfferLastFiveDays(Request $request)
    {
//        return $request->all();
        $rules = [
            'division' => ['required', 'regex:/^(all)||[0-9]+$/'],
            'unit' => ['required', 'regex:/^(all)||[0-9]+$/'],
            'thana' => ['required', 'regex:/^(all)||[0-9]+$/'],
            'rank' => ['required', 'regex:/^(all)||[0-9]+$/'],
            'sex' => ['required', 'regex:/^(all)||[0-9]+$/'],
            'offset' => 'numeric',
            'limit' => 'numeric',
        ];
        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            return response($valid->messages()->toJson(), 422, ['Content-Type' => 'application/json']);
        }
        $result = CustomQuery::ansarAcceptOfferLastFiveDays($request->division, $request->unit, $request->thana, $request->rank, $request->sex, $request->offset, $request->limit, $request->q, $request->type);
        if ($result === false) {
            return response("Invalid Request", 400, ['Content-Type' => 'text/html']);
        }
        return $result;
    }

    function getAnsarInfoinExcel()
    {
        return View::make('HRM::Entryform.ansar_info_excel');
    }

    function generateAnsarInfoExcel(Request $request)
    {
        $ansar_ids = explode(",", $request->ansar_ids);
        $data = DB::table(DB::raw('(SELECT @i:=0) as i,tbl_ansar_parsonal_info'))
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_thana', 'tbl_thana.id', '=', 'tbl_ansar_parsonal_info.thana_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->whereIn('tbl_ansar_parsonal_info.ansar_id', $ansar_ids)
            ->select(DB::raw('@i:=@i+1 sl_no'), 'tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng',
                'tbl_ansar_parsonal_info.father_name_bng', 'tbl_designations.name_bng', 'tbl_ansar_parsonal_info.data_of_birth',
                'tbl_ansar_parsonal_info.village_name_bng', 'tbl_ansar_parsonal_info.union_name_bng',
                'tbl_ansar_parsonal_info.post_office_name_bng', 'tbl_thana.thana_name_bng',
                'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.mobile_no_self')->get();
//        $data = collect($data)->toArray();

        $d = array();
        foreach ($data as $dd) {
            array_push($d, collect($dd)->toArray());
        }
//                var_dump($d);die;
        Excel::create('test', function ($excel) use ($d) {
            $excel->sheet('sheet1', function ($sheet) use ($d) {
                $sheet->fromArray($d);
            });
        })->download('xls');
//        return $data;
    }
    
    function ansarLogDetails(Request $request)
    {  
        $log_id = $request->log_id[0][0];
        
        $query = PersonalnfoLogModel::where('log_id', '=', $log_id)->with('designation', 'alldisease', 'division', 'district','thana' );

        $returnHTML = view('HRM::Report.ansar_log_details')->with('data', $query->first());

        return response()->json(['view' => $returnHTML->render(), 'id' => $log_id]);


    }

    function systemSettingIndex()
    {

        $system_setting = SystemSetting::all();
        return view("HRM::system_setting", ['data' => $system_setting]);

    }

    function systemSettingUpdate(Request $request, $id)
    {
        $data = [
            'setting_value' => $request->exists('values') ? implode(',', $request->values) : null,
            'active' => $request->exists('active') ? $request->active : 0,
            'description' => $request->description
        ];
        DB::beginTransaction();
        try {
            SystemSetting::find($id)->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e->getTraceAsString());
            return redirect()->route('system_setting')->with('error', $e->getMessage());
        }
        return redirect()->route('system_setting')->with('success', "Setting updated successfully");

    }

    function systemSettingEdit(Request $request, $id)
    {

        $system_setting = SystemSetting::find($id);
        $units = District::where('id', '<>', 0)->get();
        return view("HRM::system_setting_edit", ['data' => $system_setting, 'units' => $units]);

    }

    public function showAvailableUnitCompanyAnsarList()
    {
        $pageTitle = '';

        $custom_filter = '<filter-template
                                show-item="[\'range\',\'unit\']"
                                type="all"
                                range-change="loadPage()"
                                unit-change="loadPage()"
                                gender-change="loadPage()"
                                on-load="loadPage()"
                                data="param"
                                start-load="range"
                                field-width="{range:\'col-sm-3\',unit:\'col-sm-3\',gender:\'col-sm-3\'}"
                        ></filter-template>';
        $view = '';

        $pageTitle = "Available Ansar In Unit Company";

        return View::make('HRM::Dashboard.view_available_unit_company_ansar_list')->with(['pageTitle' => $pageTitle, 'custom_view' => $view, 'custom_filter' => $custom_filter]);
    }

    public function getAvailableUnitCompanyAnsarList(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $sex = Input::get('gender');
        $q = Input::get('q');
        $type = Input::get('type');
        $rules = [
            'type' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9,]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/']
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }

        $data = [];
        $user = Auth::user();

        if($unit != 'all'){
            $data = CustomQuery::getAvailableUnitCompanyAnsarInfo($offset, $limit, $unit, $division, $sex, $rank, $q);
        }else{
            $data = [];
        }

        if ($request->exists('export')) {
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_view_excel', $type);
        }
        return Response::json($data);
    }


    public function checkUnitAnsarEligibility(Request $request)
    {
        $results = [];
        $ansar_id = $request->ansar_id;
        $unit_id = $request->unit;
        $comment = $request->request_comment;


        $rules = [
            'type' => 'regex:/[a-z]+/',
            'view' => 'regex:/[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'rank' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails())
        {
            return response("Invalid Request(400)", 400);
        }

        $data = CustomQuery::checkUnitAnsarEligibilityStatus($ansar_id, $unit_id);

        if(count($data)==0 ){
            $message = "This Ansar is not in this Unit";
            $results = ['status' => false, 'message' => $message];
            return Response::json($results);
        }else
        {
            if($data->block_list_status == 1 || $data->black_list_status == 1 || $data->retierment_status == 1 )
            {
                $message = "Ansar cann't be Block, Black or Retired";
                $results = ['status' => false, 'message' => $message];
                return Response::json($results);
            }
            else{
                $inserted_data = [
                    "ansar_id"=> $ansar_id,
                    "unit_id" => $unit_id,
                    "action_id" => Auth::user()->id,
                    "requested_type" => "add",
                    "status" => 0,
                    "request_comment" => $comment
                ];
                UnitCompany::insert($inserted_data);
                $message = "Ansar ".$ansar_id." Successfully added in the pending list. Please wait for approval";

                $results = ['status' => true, 'message' => $message];
                return Response::json($results);
            }
        }
    }



    function deleteAnsarRequest(Request $request)
    {
        $row_id = $request->request_id;
        $comment = $request->comment;
        $results = [];

        $requestAnsar = UnitCompany::findOrFail($row_id);
        $requestAnsar->request_comment = $comment;
        $requestAnsar->requested_type = "remove";
        $requestAnsar->status = 0;
        $requestAnsar->action_id = Auth::user()->id;
        $requestAnsar->save();
        //$message = "Ansar ".$ansar_id." Delete in the pending list. Please wait for approval";

        $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') Delete in the pending list. Please wait for approval'];
        return Response::json($results);
    }

    public function showPendingUnitCompanyAnsarList()
    {
        $pageTitle = '';

        $custom_filter = '<filter-template
                                show-item="[\'range\',\'unit\']"
                                type="all"
                                range-change="loadPage()"
                                unit-change="loadPage()"
                                gender-change="loadPage()"
                                on-load="loadPage()"
                                data="param"
                                start-load="range"
                                field-width="{range:\'col-sm-3\',unit:\'col-sm-3\',gender:\'col-sm-3\'}"
                        ></filter-template>';
        $view = '';

        $pageTitle = "Pending Ansars In Unit Company";

        return View::make('HRM::Dashboard.view_pending_unit_company_ansar_list')->with(['pageTitle' => $pageTitle, 'custom_view' => $view, 'custom_filter' => $custom_filter]);
    }



    public function getPendingUnitCompanyAnsarList(Request $request)
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
            $data = CustomQuery::getPendingUnitCompanyAnsarInfo($offset, $limit, $unit, $division, $sex, $rank, $q);
            //echo "<pre>"; print_r($data);exit;
        }else{
            $data = [];
        }
        //$data = CustomQuery::getTotalPaneledAnsarList($offset, $limit, $unit, $thana, $division, $sex, CustomQuery::ALL_TIME, $rank, $request->filter_mobile_no, $request->filter_age, $q);

        if ($request->exists('export')) {
            $data = collect($data['ansars'])->chunk(2000)->toArray();
            return $this->exportData($data, 'HRM::export.ansar_view_excel', $type);
        }
        return Response::json($data);
    }


    function processUnitRequest(Request $request)
    {
        $row_id = $request->request_id;
        $comment = $request->comment;
        $results = [];
        $update_data = [
            "comment" => $comment,
            "requested_type" => "solved",
            "rejected_by" => Auth::user()->id
        ];

        $requestAnsar = UnitCompany::findOrFail($row_id);

        if($requestAnsar->requested_type == "add")
        {
            $requestAnsar->comment = $comment;
            $requestAnsar->status = 0;
            $requestAnsar->requested_type = "solved";
            $requestAnsar->rejected_by =  Auth::user()->id;
            $requestAnsar->save();

            $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') Rejected successfully'];
            return Response::json($results);
        }
        if($requestAnsar->requested_type == "remove")
        {
            $requestAnsar->comment = $comment;
            $requestAnsar->status = 1;
            $requestAnsar->requested_type = "solved";
            $requestAnsar->rejected_by =  Auth::user()->id;
            $requestAnsar->save();

            $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') Rejected successfully'];
            return Response::json($results);
        }
    }


    function acceptUnitRequest(Request $request)
    {
        //echo "Anik";
        $max_allowed_unit_member = 115;

        $row_id = $request->request_id;
        $results = [];
        $update_data = [

            "requested_type" => "add",
            "approved_by" => Auth::user()->id
        ];

        $requestAnsar = UnitCompany::findOrFail($row_id);

        if($requestAnsar->requested_type == "add"){

            $unit_total_members =  UnitCompany::where("unit_id",$requestAnsar->unit_id)->where("requested_type","solved")->where("status",1)->count();

            if($max_allowed_unit_member > $unit_total_members){
                $requestAnsar->requested_type = "solved";
                $requestAnsar->status = 1;
                $requestAnsar->approved_by =  Auth::user()->id;
                $requestAnsar->save();
                $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') Added successfully'];
                return Response::json($results);
            }else{
                $results = ['status' => false, 'message' => 'Unit capacity full.'];
                return Response::json($results);
            }
        }

        if($requestAnsar->requested_type == "remove"){
            $unit_company_log = new UnitCompanyLog();
            $unit_company_log->unit_id =  $requestAnsar->unit_id;
            $unit_company_log->old_company_id =  $requestAnsar->id;
            $unit_company_log->ansar_id=  $requestAnsar->ansar_id;
            $unit_company_log->remove_date=  date("Y-m-d");
            $unit_company_log->remove_reason=  $requestAnsar->request_comment;
            $unit_company_log->save();

            $requestAnsar->delete();
            $results = ['status' => true, 'message' => 'Ansar ('.$requestAnsar->ansar_id . ') Removed successfully'];
            return Response::json($results);
        }
    }

    public function addUnitCompanyByFile(Request $request){
        $file = $request->file("applicant_id_list");
        $selected_ansars = [];
        $comment = $request->comment;
        $notProcessedAnsars = "";
        $applicant_ids = [];
        Excel::load($file,function ($reader) use(&$applicant_ids){
            $applicant_ids = array_flatten($reader->limitColumns(1)->first());
        });
        $applicants = PersonalInfo::join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->leftjoin('tbl_unit_company_ansar_list', 'tbl_unit_company_ansar_list.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->where('tbl_ansar_parsonal_info.unit_id',$request->unit)
            ->where('tbl_ansar_status_info.block_list_status',0)
            ->where('tbl_ansar_status_info.black_list_status',0)
            ->where('tbl_ansar_status_info.retierment_status',0)
            ->whereNull('tbl_unit_company_ansar_list.ansar_id')
            ->whereIn('tbl_ansar_parsonal_info.ansar_id',$applicant_ids)
            ->select ('tbl_ansar_parsonal_info.unit_id','tbl_ansar_parsonal_info.ansar_id')
            ->get();
        //echo "<pre>";print_r($applicants);exit;
        foreach($applicants as $applicant)
        {
            $selected_ansars[]= $applicant->ansar_id;
        }
        $diff_result = array_diff($applicant_ids, $selected_ansars);

        if(count($diff_result) == count($applicant_ids)){
            return Response::json(['status' => false, 'message' => "No Ansar is eligible!"]);
        }
        $applicant_list = implode(",",$diff_result);
        DB::beginTransaction();
        try{

            foreach ($applicants as $applicant){

                $inserted_data = [
                    "ansar_id"=> $applicant->ansar_id,
                    "unit_id" => $applicant->unit_id,
                    "action_id" => Auth::user()->id,
                    "requested_type" => "add",
                    "request_comment" => $comment,
                    "status" => 0
                ];
                UnitCompany::insert($inserted_data);
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return Response::json(['status' => false, 'message' => "Ansar/s can not be added"]);
        }
        if(count($diff_result) == 0){
            $notProcessedAnsars = implode(",",$diff_result);
            $message = 'Ansar uploaded in the queue. Please wait for approval. But follwing ansars not processed '.$notProcessedAnsars;
        }
        $results = ['status' => true, 'message' => 'Ansar uploaded in the queue. Please wait for approval...'];
        return Response::json($results);
    }




}
