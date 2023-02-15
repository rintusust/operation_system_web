<?php

namespace App\modules\HRM\Controllers;

use App\Helper\ExportDataToExcel;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\modules\HRM\Models\ActionUserLog;
use App\modules\HRM\Models\AnsarFutureState;
use App\modules\HRM\Models\AnsarIdCard;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentDailyLog;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\OfferBlockedAnsar;
use App\modules\HRM\Models\OfferSmsLog;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\PersonalnfoLogModel;
use App\modules\HRM\Models\RestInfoLogModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\TransferAnsar;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use App\modules\HRM\Models\NidRequestLog;
use Barryvdh\Snappy\Facades\SnappyImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use ExportDataToExcel;

    //
    function reportGuardSearchView()
    {
        return View::make('HRM::Report.report_guard_search');
    }

    function reportAllGuard(Request $request)
    {
        $kpi = Input::get('kpi_id');

        $rules = [
            'kpi_id' => 'required|regex:/^[0-9]+$/',
            'unit' => 'required|regex:/^[0-9]+$/',
            'thana' => 'required|regex:/^[0-9]+$/',
            'division' => 'required|regex:/^[0-9]+$/',
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return $valid->messages();
            return response("Invalid Request(400)", 400);
        } else {
            //DB::enableQueryLog();
            $edu = DB::table('tbl_ansar_education_info')->select(DB::raw('MAX(education_id) edu_id'), 'ansar_id')
                ->groupBy('ansar_id')->toSql();
            
            
             $ansarQuery =   DB::table('tbl_kpi_info')
                ->join('tbl_embodiment', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_ansar_education_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_education_info.ansar_id')
                ->join('tbl_education_info', 'tbl_education_info.id', '=', 'tbl_ansar_education_info.education_id')
                ->join(DB::raw("($edu) edu"), function ($q) {
                    $q->on('edu.edu_id', '=', 'tbl_ansar_education_info.education_id');
                    $q->on('edu.ansar_id', '=', 'tbl_ansar_education_info.ansar_id');
                })
                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
                ->where('tbl_kpi_info.id', '=', $kpi)
                ->where('tbl_kpi_info.unit_id', '=', $request->unit)
                ->where('tbl_kpi_info.thana_id', '=', $request->thana)
                ->where('tbl_kpi_info.division_id', '=', $request->division)
                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
                ->where('tbl_ansar_status_info.embodied_status', '=', 1);
                
                
//            $ansar = DB::table('tbl_kpi_info')
//                ->join('tbl_embodiment', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
//                ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
//                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
//                ->join('tbl_ansar_education_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_education_info.ansar_id')
//                ->join('tbl_education_info', 'tbl_education_info.id', '=', 'tbl_ansar_education_info.education_id')
//                ->join(DB::raw("($edu) edu"), function ($q) {
//                    $q->on('edu.edu_id', '=', 'tbl_ansar_education_info.education_id');
//                    $q->on('edu.ansar_id', '=', 'tbl_ansar_education_info.ansar_id');
//                })
//                ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
//                ->join('tbl_thana', 'tbl_ansar_parsonal_info.thana_id', '=', 'tbl_thana.id')
//                ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
//                ->where('tbl_kpi_info.id', '=', $kpi)
//                ->where('tbl_kpi_info.unit_id', '=', $request->unit)
//                ->where('tbl_kpi_info.thana_id', '=', $request->thana)
//                ->where('tbl_kpi_info.division_id', '=', $request->division)
//                ->where('tbl_embodiment.emboded_status', '=', 'Emboded')
//                ->where('tbl_ansar_status_info.block_list_status', '=', 0)
//                ->where('tbl_ansar_status_info.embodied_status', '=', 1)                
//                ->groupBy('tbl_ansar_parsonal_info.ansar_id')
//                ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.data_of_birth as dob', DB::raw('CONCAT(hight_feet," feet ",hight_inch," inch") as height'), 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_designations.name_bng',
//                    'tbl_units.unit_name_bng', 'tbl_embodiment.transfered_date', 'tbl_embodiment.joining_date', 'tbl_ansar_parsonal_info.avub_share_id', 'tbl_ansar_parsonal_info.mobile_no_self'
//                    , DB::raw("IF(tbl_education_info.id!=0,tbl_education_info.education_deg_bng,tbl_ansar_education_info.name_of_degree) AS education"))->orderBy('tbl_embodiment.joining_date', 'desc')->get();
//            //return DB::getQueryLog();
          
         $total_given_query = clone $ansarQuery;
         if ($request->rank != 'all') {
            $ansarQuery->where('tbl_designations.id', $request->rank);
          }  
          
          if ($request->q) {
          
            $ansarQuery->where('tbl_ansar_parsonal_info.ansar_id', '=',$request->q);
        
          } 
                
            $guards = DB::table('tbl_kpi_info')
                ->join('tbl_kpi_detail_info', 'tbl_kpi_detail_info.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_embodiment', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')
                ->join('tbl_units', 'tbl_kpi_info.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'tbl_kpi_info.thana_id', '=', 'tbl_thana.id')
                ->where('tbl_kpi_info.id', '=', $kpi)
                ->where('tbl_kpi_info.unit_id', '=', $request->unit)
                ->where('tbl_kpi_info.thana_id', '=', $request->thana)
                ->where('tbl_kpi_info.division_id', '=', $request->division)
                ->select('tbl_kpi_info.kpi_name', 'tbl_kpi_info.kpi_address', 'tbl_kpi_detail_info.total_ansar_given', 'tbl_units.unit_name_bng', 'tbl_thana.thana_name_bng')->first();
            
            
            $total = clone $ansarQuery;
            $total->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');
            
            $ansar = $ansarQuery->distinct()->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.data_of_birth as dob', DB::raw('CONCAT(hight_feet," feet ",hight_inch," inch") as height'), 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_designations.name_bng',
                    'tbl_units.unit_name_bng', 'tbl_embodiment.transfered_date', 'tbl_embodiment.joining_date', 'tbl_ansar_parsonal_info.avub_share_id', 'tbl_ansar_parsonal_info.mobile_no_self'
                    , DB::raw("IF(tbl_education_info.id!=0,tbl_education_info.education_deg_bng,tbl_ansar_education_info.name_of_degree) AS education"))->orderBy('tbl_embodiment.joining_date', 'desc')->get();
            //return DB::getQueryLog();
             $total_given = $total_given_query->distinct()->select('tbl_ansar_parsonal_info.ansar_id')->get();   
            
            
            $data = ['total' => collect($total->get())->pluck('t', 'code'),'total_given' =>$total_given,'ansars' => $ansar, 'guard' => $guards];
            if (Input::exists('export')) {
                return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.ansar_in_guard');
            }
            return Response::json($data);
        }
    }

    function localizeReport()
    {
        $s = file_get_contents(public_path("report_" . Input::get('type') . ".json"));
        return json_encode(json_decode($s, true)[Input::get('name')]);
    }

    function ansarServiceReportView()
    {
        return View::make('HRM::Report.report_ansar_in_service');
    }

    function ansarServiceReport()
    {
        $ansar_id = Input::get('ansar_id');
        
        $ansar_id = CustomQuery::getFullSmartCardNumber($ansar_id);
        Input::replace(['ansar_id' => $ansar_id]);
        
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/',
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        }
        $ansar = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_blood_group', 'tbl_ansar_parsonal_info.blood_group_id', '=', 'tbl_blood_group.id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
            ->select('tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.profile_pic', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng', 'tbl_blood_group.blood_group_name_bng')->first();
        $ansarCurrentServiceRecord = DB::table('tbl_embodiment')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->where('tbl_embodiment.ansar_id', '=', $ansar_id)
            ->select('tbl_embodiment.joining_date', 'tbl_embodiment.reporting_date', 'tbl_embodiment.memorandum_id', 'tbl_embodiment.service_ended_date',
                'tbl_units.unit_name_bng', 'tbl_kpi_info.kpi_name')->first();
        $ansarPastServiceRecord = DB::table('tbl_embodiment_log')
            ->join('tbl_disembodiment_reason', 'tbl_disembodiment_reason.id', '=', 'tbl_embodiment_log.disembodiment_reason_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment_log.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_kpi_info.unit_id')
            ->where('tbl_embodiment_log.ansar_id', '=', $ansar_id)->orderBy('tbl_embodiment_log.id', 'desc')
            ->select('tbl_embodiment_log.joining_date', 'tbl_embodiment_log.reporting_date', 'tbl_embodiment_log.old_memorandum_id',
                'tbl_units.unit_name_bng', 'tbl_kpi_info.kpi_name', 'tbl_embodiment_log.release_date',
                'tbl_disembodiment_reason.reason_in_bng', 'tbl_embodiment_log.joining_date')->get();
        return Response::json(['ansar' => $ansar, 'current' => $ansarCurrentServiceRecord, 'past' => $ansarPastServiceRecord, 'pi' => file_exists($ansar->profile_pic)]);
    }

    function ansarPrintIdCardView()
    {
        return View::make('HRM::Report.ansar_id_card_view');
    }
	
	
	 function testAnsarPrintIdCardView()
    {
        return View::make('HRM::Report.test_ansar_id_card_view');
    }

    function printIdCard()
    {
//        return Input::all();
        $id = Input::get('ansar_id');
        $issue_date = Input::get('issue_date');
        $expire_date = Input::get('expire_date');
        $type = Input::get('type');
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/',
            'issue_date' => 'required|date_format:d-M-Y',
            'expire_date' => 'required|date_format:d-M-Y',
        ];
        $message = [
            'required' => 'This field is required',
            'regex' => 'Enter a valid ansar id',
            'numeric' => 'Ansar id must be numeric',
            'date_format' => 'Invalid date format',
        ];
        $bng_data = ["title" => "বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী",
            "id_no" => "আইডি নং",
            "name" => "নাম",
            "rank" => "পদবী",
            "bg" => "রক্তের গ্রুপ",
            "unit" => "জেলা",
            "id" => "প্রদানের তারিখ",
            "ed" => "শেষের তারিখ",
            "bs" => "বাহকের স্বাক্ষর",
            "is" => "কর্তৃকারীর স্বাক্ষর",
            "footer_title" => "প্রদানকারী কর্তৃপক্ষ : বাংলাদেশ আনসার ও ভিডিপি"];
			
        $eng_data = [
            "title" => "Bangladesh Ansar and Village Defence Party",
            "name" => "Name",
            "id_no" => "ID NO",
            "rank" => "Rank",
            "bg" => "Blood Group",
            "unit" => "District",
            "id" => "Issue Date",
            "ed" => "Expire Date",
            "bs" => "Bearer`s Sign",
            "is" => "Issuer`s Sign",
            "footer_title" => "Issuing Authority: Bangladesh Ansar & VDP"
        ];
        $validation = Validator::make(Input::all(), $rules, $message);
        if ($validation->fails()) {
            return Response::json(['validation' => true, 'messages' => $validation->messages()]);
        }
        $report_data = ${$type . "_data"};
        $ansar = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_ansar_parsonal_info.division_id')
            ->join('tbl_blood_group', 'tbl_blood_group.id', '=', 'tbl_ansar_parsonal_info.blood_group_id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $id)
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_' . $type . ' as name', 'tbl_designations.name_' . $type . ' as rank', 'tbl_blood_group.blood_group_name_' . $type . ' as blood_group', 'tbl_units.unit_name_' . $type . ' as unit_name', 'tbl_units.unit_code', 'tbl_division.division_code', 'tbl_ansar_parsonal_info.profile_pic', 'tbl_ansar_parsonal_info.data_of_birth','tbl_ansar_parsonal_info.sign_pic')->first();
        if ($ansar) {
            $ansarIdHistory = AnsarIdCard::where('ansar_id', $id)->get();
            $id_card = new AnsarIdCard;
            $id_card->ansar_id = $id;
            $id_card->issue_date = Carbon::parse($issue_date)->format("Y-m-d");
            $id_card->expire_date = Carbon::parse($expire_date)->format("Y-m-d");
            $id_card->type = strtoupper($type);
            $id_card->status = 1;
            if (!$id_card->saveOrFail()) {
                return View::make('HRM::Report.no_ansar_found')->with('id', $id);
            }
            return View::make('HRM::Report.ansar_id_card_font', ['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::parse($issue_date)->format("d/m/Y"), 'ed' => Carbon::parse($expire_date)->format("d/m/Y"), 'type' => $type]);
            $path = public_path("{$id}.jpg");
            SnappyImage::loadView('HRM::Report.ansar_id_card_font', ['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::parse($issue_date)->format("d/m/Y"), 'ed' => Carbon::parse($expire_date)->format("d/m/Y"), 'type' => $type])->setOption('quality', 100)
                ->setOption('crop-x', 0)->setOption('crop-y', 0)->setOption('crop-h', 292)->setOption('crop-w', 340)->setOption('encoding', 'utf-8')->save($path);
            $image = Image::make($path)->encode('data-url');
            File::delete($path);
//            return View::make('HRM::Report.ansar_id_card_font',['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::parse( $issue_date)->format("d/m/Y"), 'ed' => Carbon::parse( $expire_date)->format("d/m/Y"), 'type' => $type]);
            return View::make('HRM::Report.id_card_print')->with(['image' => $image->encode('data-url'), 'history' => $ansarIdHistory]);
        }
        return View::make('HRM::Report.no_ansar_found')->with('id', $id);
    }
	
	
	
	    function testPrintIdCard()
    {
//        return Input::all();
        $id = Input::get('ansar_id');
        $issue_date = Input::get('issue_date');
        $expire_date = Input::get('expire_date');
        $type = Input::get('type');
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/',
            'issue_date' => 'required|date_format:d-M-Y',
            'expire_date' => 'required|date_format:d-M-Y',
        ];
        $message = [
            'required' => 'This field is required',
            'regex' => 'Enter a valid ansar id',
            'numeric' => 'Ansar id must be numeric',
            'date_format' => 'Invalid date format',
        ];
        $bng_data = ["title" => "বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী",
            "id_no" => "আইডি নং",
            "name" => "নাম",
            "rank" => "পদবী",
            "bg" => "রক্তের গ্রুপ",
            "unit" => "জেলা",
            "id" => "প্রদানের তারিখ",
            "ed" => "শেষের তারিখ",
            "bs" => "বাহকের স্বাক্ষর",
            "is" => "কর্তৃকারীর স্বাক্ষর",
            "footer_title" => "প্রদানকারী কর্তৃপক্ষ : বাংলাদেশ আনসার ও ভিডিপি"];
			
        $eng_data = [
            "title" => "Bangladesh Ansar and Village Defence Party",
            "name" => "Name",
            "id_no" => "ID NO",
            "rank" => "Rank",
            "bg" => "Blood Group",
            "unit" => "District",
            "id" => "Issue Date",
            "ed" => "Expire Date",
            "bs" => "Bearer`s Sign",
            "is" => "Issuer`s Sign",
            "footer_title" => "Issuing Authority: Bangladesh Ansar & VDP"
        ];
        $validation = Validator::make(Input::all(), $rules, $message);
        if ($validation->fails()) {
            return Response::json(['validation' => true, 'messages' => $validation->messages()]);
        }
        $report_data = ${$type . "_data"};
        $ansar = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_division', 'tbl_division.id', '=', 'tbl_ansar_parsonal_info.division_id')
            ->join('tbl_blood_group', 'tbl_blood_group.id', '=', 'tbl_ansar_parsonal_info.blood_group_id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $id)
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_' . $type . ' as name', 'tbl_designations.name_' . $type . ' as rank', 'tbl_blood_group.blood_group_name_' . $type . ' as blood_group', 'tbl_units.unit_name_' . $type . ' as unit_name', 'tbl_units.unit_code', 'tbl_division.division_code', 'tbl_ansar_parsonal_info.profile_pic', 'tbl_ansar_parsonal_info.sign_pic')->first();
        if ($ansar) {
            $ansarIdHistory = AnsarIdCard::where('ansar_id', $id)->get();
            $id_card = new AnsarIdCard;
            $id_card->ansar_id = $id;
            $id_card->issue_date = Carbon::parse($issue_date)->format("Y-m-d");
            $id_card->expire_date = Carbon::parse($expire_date)->format("Y-m-d");
            $id_card->type = strtoupper($type);
            $id_card->status = 1;
            if (!$id_card->saveOrFail()) {
                return View::make('HRM::Report.no_ansar_found')->with('id', $id);
            }
            return View::make('HRM::Report.test_ansar_id_card_font', ['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::parse($issue_date)->format("d/m/Y"), 'ed' => Carbon::parse($expire_date)->format("d/m/Y"), 'type' => $type]);
            $path = public_path("{$id}.jpg");
            SnappyImage::loadView('HRM::Report.test_ansar_id_card_font', ['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::parse($issue_date)->format("d/m/Y"), 'ed' => Carbon::parse($expire_date)->format("d/m/Y"), 'type' => $type])->setOption('quality', 100)
                ->setOption('crop-x', 0)->setOption('crop-y', 0)->setOption('crop-h', 292)->setOption('crop-w', 340)->setOption('encoding', 'utf-8')->save($path);
            $image = Image::make($path)->encode('data-url');
            File::delete($path);
//            return View::make('HRM::Report.test_ansar_id_card_font',['rd' => $report_data, 'ad' => $ansar, 'id' => Carbon::parse( $issue_date)->format("d/m/Y"), 'ed' => Carbon::parse( $expire_date)->format("d/m/Y"), 'type' => $type]);
            return View::make('HRM::Report.id_card_print')->with(['image' => $image->encode('data-url'), 'history' => $ansarIdHistory]);
        }
        return View::make('HRM::Report.no_ansar_found')->with('id', $id);
    }

    function getAnsarIDHistory(Request $request)
    {

        $ansarIdHistory = AnsarIdCard::where('ansar_id', $request->ansar_id)->get();
        return $ansarIdHistory;

    }

    function getReportData($type, $name)
    {
        $s = file_get_contents(asset("report_" . $type . ".json"));
        return json_decode($s, true)[$name];
    }

    public function ansarDisembodimentReportView()
    {
        return view('HRM::Report.ansar_disembodiment_report_view');
    }

    public function disembodedAnsarInfo(Request $request)
    {
        $from = Input::get('from_date');
        $to = Input::get('to_date');
        $unit = $request->input('unit_id');
        $division = $request->input('division_id');
        $thana = $request->input('thana_id');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $rank = Input::get('rank');
        $gender = Input::get('gender');
        $q = Input::get('q');
        $rules = [
            'limit' => 'numeric',
            'offset' => 'numeric',
            'from_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'to_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'unit_id' => ['required', 'regex:/^(all)$|^[0-9]+$/'],
            'thana_id' => ['required', 'regex:/^(all)$|^[0-9]+$/'],
            'division_id' => ['required', 'regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        if (!is_null($from) && !is_null($to) && !is_null($unit) && !is_null($thana)) {
            $from_date = Carbon::parse($from)->format('Y-m-d');
            $to_date = Carbon::parse($to)->format('Y-m-d');
            $data = CustomQuery::disembodedAnsarListforReportWithRankGender($offset, $limit, $from_date, $to_date, $division, $unit, $thana, $rank, $gender, $q);
            if (Input::exists('export')) {
                return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.disembodied_report');
            }
            return response()->json($data);
        }
    }

    public function blockListView()
    {
        return view('HRM::Report.blocklist_view');
    }

    public function blockListedAnsarInfoDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $thana = Input::get('thana');
        $unit = Input::get('unit');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $gender = Input::get('gender');
        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        $data = CustomQuery::getBlocklistedAnsarWithRankGender($offset, $limit, $division, $unit, $thana, $rank, $gender, $request->q);
        if (Input::exists('export')) {
            return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.blocklist_report');
        }
        return response()->json($data);
    }

    public function blackListView()
    {
        return view('HRM::Report.blacklist_view');
    }

    public function blackListedAnsarInfoDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $thana = Input::get('thana');
        $unit = Input::get('unit');
        $division = Input::get('division');
        $rank = Input::get('rank');
        $gender = Input::get('gender');
        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        $data = CustomQuery::getBlacklistedAnsarWithRankGender($offset, $limit, $division, $unit, $thana, $rank, $gender, $request->q);
        if (Input::exists('export')) {
            return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.blacklist_report');
        }
        return response()->json($data);
    }

    public function getAnserTransferHistory(Request $request)
    {
        //DB::enableQueryLog();
        $ansar_id = Input::get('ansar_id');
        
        $ansar_id = CustomQuery::getFullSmartCardNumber($ansar_id);
        #Input::replace(['ansar_id' => $ansar_id]);
        
        $rules = [
            'ansar_id' => 'required|numeric|regex:/^[0-9]+$/',
        ];
        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return Redirect::back()->withInput(Input::all())->withErrors($validation);
        } else {
            
             $ansar = PersonalInfo::where('ansar_id', $ansar_id)->first();
             
            $result["cEmbodiment"] = $ansar->embodiment()->with("kpi.unit", "kpi.division", "kpi.thana")->first();
            $result["lEmbodiment"] = $ansar->embodiment_log()->with("restData","restLogData","disembodimentReason", "kpi.unit", "kpi.division", "kpi.thana")->orderBy("joining_date", "desc")->get();

            //freeze information
            $result["cFreeze"] = $ansar->freezing_info()->with("kpi.unit", "kpi.division", "kpi.thana")->first();
            $result["lFreeze"] = $ansar->freezingInfoLog()->orderBy("freez_date", "desc")->get();

            //Transfer information
            $result["transfer"] = TransferAnsar::where('ansar_id', $ansar_id)->with("presentKpi.unit", "presentKpi.division", "presentKpi.thana", "transferKpi.unit", "transferKpi.division", "transferKpi.thana")->orderBy("id", "desc")->get();

            //echo '<pre>';
           // print_r($result["lEmbodiment"]); exit;

           
            
            $date_time_array = [];
            
            $tracker = 0;
            
            if ($result["lEmbodiment"]->count() > 0) {
                
                $loop = 0;
            
                foreach($result["lEmbodiment"] as $index=>$lEmbodiment){
					if($lEmbodiment->old_embodiment_id == 0){
						$date_time_array[$loop]['kpi'] = $this->getFirstKPIAlternative($lEmbodiment->old_embodiment_id, $ansar_id, $lEmbodiment->joining_date, $lEmbodiment->release_date);
					}else{
						$date_time_array[$loop]['kpi'] = $this->getFirstKPI($lEmbodiment->old_embodiment_id, $ansar_id);
					}
                    $date_time_array[$loop]['time'] = $lEmbodiment->joining_date;
                    $date_time_array[$loop]['type'] = 'embodiment';
                    $date_time_array[$loop]['data'] = $lEmbodiment;
                    $date_time_array[$loop]['Preference'] = '1';
                    
                    $tracker++; $loop++;
                    $date_time_array[$loop]['time'] = $lEmbodiment->release_date;
                    $date_time_array[$loop]['type'] = 'disembodiment';
                    $date_time_array[$loop]['data'] = $lEmbodiment;
                    $date_time_array[$loop]['Preference'] = '5';

                    $tracker++;$loop++;
                    
                    
                }
            }
            
            if (!empty($result["cEmbodiment"])){
               
                $index = $tracker;
				
                $date_time_array[$index + 1]['time'] = $result["cEmbodiment"]->joining_date;
                $date_time_array[$index + 1]['type'] = 'embodiment';
                $date_time_array[$index + 1]['data'] = $result["cEmbodiment"];
                $date_time_array[$index + 1]['Preference'] = '1';
				$date_time_array[$index + 1]['kpi'] = $this->getFirstKPI($result["cEmbodiment"]->id, $ansar_id);
                $tracker++;
            
            }
            
            
            
            if ($result["lFreeze"]->count() > 0) {  
                
                $loop = 0;
                $index = $tracker;            
                foreach($result["lFreeze"] as $index2=>$lFreeze){
                    $date_time_array[$index + $loop + 1]['time'] = $lFreeze->freez_date;
                    $date_time_array[$index + $loop  + 1]['type'] = 'freez';  
                    $date_time_array[$index + $loop  + 1]['data'] = $lFreeze;
                    $date_time_array[$index + $loop  + 1]['Preference'] = '2';
                    $tracker++; $loop++;
                    
                    $date_time_array[$index + $loop + 1]['time'] = $lFreeze->move_frm_freez_date;;
                    $date_time_array[$index + $loop  + 1]['type'] = 'unfreez';  
                    $date_time_array[$index + $loop  + 1]['data'] = $lFreeze;
                    $date_time_array[$index + $loop  + 1]['Preference'] = '3';
                    $tracker++; $loop++;
                }
            }
            
            
            if (!empty($result["cFreeze"])) {

                $index = $tracker;
                $date_time_array[$index + 1]['time'] = $result["cFreeze"]->freez_date;
                $date_time_array[$index + 1]['type'] = 'freez';
                $date_time_array[$index + 1]['Preference'] = '2';
                $date_time_array[$index + 1]['data']['freez_date'] = $result["cFreeze"]->freez_date;


                $tracker++;
            }

            if ($result["transfer"]->count() > 0) {
                $index = $tracker;            
                foreach($result["transfer"] as $index4=>$transfer){
                    $date_time_array[$index + $index4 + 1]['time'] = $transfer->transfered_kpi_join_date;
                    $date_time_array[$index + $index4 + 1]['type'] = 'transfer';
                    $date_time_array[$index + $index4 + 1]['data'] = $transfer;
                    $date_time_array[$index + $index4 + 1]['Preference'] = '4';


                     $tracker++;
                }
            }
            
            //usort($date_time_array, '$this->date_compare');
            //echo '<pre>'; 
           // print_r($date_time_array); exit;
            
            $array = collect($date_time_array)->sortByDesc('Preference')->sortByDesc('time')->reverse()->toArray();
            
//            foreach($array as $loop_index){
//                if($loop_index['type'] == 'transfer'){
//                   echo "Date: ".$loop_index['time'].'     Type:'.$loop_index['data']->presentKpi->kpi_name.'<br>';
//                }else{
//                   echo "Date: ".$loop_index['time'].'     Type:'.$loop_index['data'].'<br>';
//
//                }
//            }
//
//            //print_r($array); 
//            exit;
            
            $transfer_data = [];

            $final_loop = 0;

            foreach ($array as $loop_index) {

                $transfer_data[$final_loop] = $loop_index;
                
                if($final_loop == 0){
                    $transfer_data[$final_loop]['time_difference'] = ''; 
                } else {

                    if ($loop_index['type'] != 'embodiment') {
                        $previous_date = $this->formate_date($previous_data['time']);
                        $current_date = $this->formate_date($loop_index['time']);

                        $date_a = Carbon::parse($previous_date);
                        $date_b = Carbon::parse($current_date);

                        $interval = $date_b->diffInDays($date_a);

                        $transfer_data[$final_loop-1]['time_difference'] = $interval;
                    }
                }

                $previous_data = $loop_index;
                $final_loop++;
            }

            $ansar = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->join('tbl_blood_group', 'tbl_ansar_parsonal_info.blood_group_id', '=', 'tbl_blood_group.id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
            ->select('tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_ansar_parsonal_info.profile_pic', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng', 'tbl_blood_group.blood_group_name_bng', 'tbl_ansar_parsonal_info.mobile_no_self')->first();
 
            $transfer_history = DB::table('tbl_transfer_ansar')
                ->join(DB::raw('tbl_kpi_info as pk'), 'tbl_transfer_ansar.present_kpi_id', '=', 'pk.id')
                ->join(DB::raw('tbl_kpi_info as tk'), 'tbl_transfer_ansar.transfered_kpi_id', '=', 'tk.id')
                ->join('tbl_units', 'pk.unit_id', '=', 'tbl_units.id')
                ->join('tbl_thana', 'pk.thana_id', '=', 'tbl_thana.id')
                 ->join('tbl_units as tk_unit', 'tk.unit_id', '=', 'tk_unit.id')
                ->join('tbl_thana as tk_thana', 'tk.thana_id', '=', 'tk_thana.id')
                ->where('tbl_transfer_ansar.ansar_id', $ansar_id)
                ->select('tbl_transfer_ansar.present_kpi_join_date as joiningDate','tbl_transfer_ansar.transfer_memorandum_id as memorandum_id','tbl_transfer_ansar.transfer_memorandum_id as memorandum_id', 'tbl_transfer_ansar.action_by as action_user_id', 'tbl_transfer_ansar.transfered_kpi_join_date as transferDate',
                    'pk.kpi_name as FromkpiName', 'tk.kpi_name as TokpiName', 'tbl_units.unit_name_eng as unit','tbl_units.unit_name_bng as unit_bng', 'tbl_thana.thana_name_eng as thana', 'tbl_thana.thana_name_bng as thana_bng','tk_unit.unit_name_eng as tk_unit', 'tk_unit.unit_name_bng as tk_unit_bng', 'tk_thana.thana_name_eng as tk_thana','tk_thana.thana_name_bng as tk_thana_bng', DB::raw('DATEDIFF(tbl_transfer_ansar.transfered_kpi_join_date,tbl_transfer_ansar.present_kpi_join_date) as service_time'));
            if ($request->unit) {
                $transfer_history->where(function ($query) use ($request) {
                    $query->where('pk.unit_id', $request->unit)->orWhere('tk.unit_id', $request->unit);
                });
            }
            if ($request->range) {
                $transfer_history->where(function ($query) use ($request) {
                    $query->where('pk.division_id', $request->range)->orWhere('tk.division_id', $request->range);
                });
            }
            $b = $transfer_history->get();            
//            return DB::getQueryLog();
            //return $b;
            
             return Response::json(['ansar' => $ansar, 'transfer_data' => $transfer_data, 'pi' => file_exists($ansar->profile_pic)]);

            
        }
    }
    
    
    public function getFirstKPI($embodiment_id, $ansar_id) {
		//echo  $embodiment_id; 
       // DB::enableQueryLog();
		$KPI = [];
		
		if($embodiment_id == 0){
			
			return $KPI['status'] = '';
		}
        $result = TransferAnsar::whereRaw('id = (select `id` from `tbl_transfer_ansar` WHERE ansar_id ='.$ansar_id.' AND embodiment_id = '.$embodiment_id.' order by transfered_kpi_join_date ASC limit 1)')->get();
		 
		 //echo  'embodiment: '.$embodiment_id.'<br>'; echo 'result: '.$result->count().'<br>';
		if ($result->count()) { 			
				
			$result_1 = KpiGeneralModel::with('thana','unit','division')->where('id', $result[0]->present_kpi_id)->first();
            // echo '<pre>'; print_r($KPI); exit;
			$KPI['status'] = $result_1->kpi_name;
			$KPI['kpidetails'] = $result_1;
		}else {
            $KPI['status'] = '';
        }
        
        return $KPI;
        
    }
    
	public function getFirstKPIAlternative($embodiment_id, $ansar_id, $start_date, $end_date) {
        //echo $embodiment_id; exit;
		//DB::enableQueryLog();
		
		$KPI = [];
		
		$result = TransferAnsar::whereRaw('id = (select `id` from `tbl_transfer_ansar` WHERE ansar_id ='.$ansar_id.' AND transfered_kpi_join_date between "'.$start_date.'" and "'.$end_date.'" order by transfered_kpi_join_date ASC limit 1)')->get();

		//dd(DB::getQueryLog()); exit;
		
		if ($result->count()) { 				
			$result_1 = KpiGeneralModel::with('thana','unit','division')->where('id', $result[0]->present_kpi_id)->first();
            $KPI['status'] = $result_1->kpi_name;
			$KPI['kpidetails'] = $result_1;
		}else {
            $KPI['status'] = '';
        }
        
        //echo '<pre>'; print_r($KPI); exit;
        return $KPI;
        
    }
    
	
	
   public function formate_date($date){
       
       $time = strtotime($date);
       $newformat = date('Y-m-d',$time);
       
       return $newformat;
       
   }
    
   public function date_compare($a, $b) {
        $t1 = strtotime($a['datetime']);
        $t2 = strtotime($b['datetime']);
        return $t1 - $t2;
    }

    public function ansarEmbodimentReportView()
    {
        return view('HRM::Report.ansar_embodiment_report_view');
    }

    public function embodedAnsarInfo()
    {
        $division = Input::get('division_id');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $from = Input::get('from_date');
        $to = Input::get('to_date');
        $unit = Input::get('unit_id');
        $thana = Input::get('thana_id');
        $rank = Input::get('rank');
        $gender = Input::get('gender');
        $q = Input::get('q');
        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'from_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'to_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'unit_id' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana_id' => ['regex:/^(all)$|^[0-9]+$/'],
            'division_id' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        } else {
            if (!is_null($from) && !is_null($to) && !is_null($unit) && !is_null($thana)) {
                $from_date = Carbon::parse($from)->format('Y-m-d');
                $to_date = Carbon::parse($to)->format('Y-m-d');
                $data = CustomQuery::embodedAnsarListforReportWithRankGender($offset, $limit, $from_date, $to_date, $division, $unit, $thana, $rank, $gender, $q);
                if (Input::exists('export')) {
                    return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.embodiment_report');
                }
                return response()->json($data);
            }
        }
    }

    public function threeYearsOverListView()
    {
        return view('HRM::Report.three_years_over_report');
    }

    public function threeYearsOverAnsarInfo()
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $unit = Input::get('unit');
        $division = Input::get('division');
        $thana = Input::get('thana');
        $ansar_rank = Input::get('ansar_rank');
        $ansar_sex = Input::get('ansar_sex');
        $view = Input::get('view');
        $rules = [
            'limit' => 'numeric',
            'offset' => 'numeric',
            'unit' => 'required',
            'ansar_rank' => 'required',
            'ansar_sex' => 'regex:/^[A-Za-z]+$/|required',
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        $data = CustomQuery::threeYearsOverAnsarList($offset, $limit, $division, $unit, $ansar_rank, $ansar_sex);
        if (Input::exists('export')) {
            return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.three_years_over_list_view');
        }
        return response()->json($data);
    }

    public function ansarOverAgedInfo(Request $request)
    {
        if ($request->ajax()) {
            $limit = Input::get('limit');
            $offset = Input::get('offset');
            $unit = Input::get('unit');
            $division = Input::get('range');
            $thana = Input::get('thana');
            $rank = Input::get('rank');
            $gender = Input::get('gender');
            $view = Input::get('view');
            $rules = [
                'limit' => 'numeric',
                'offset' => 'numeric',
                'unit' => 'required',
            ];
            $valid = Validator::make(Input::all(), $rules);
            if ($valid->fails()) {
                return response("Invalid Request(400)", 400);
            }
            $data = CustomQuery::ansarListOverAgedWithRankGender($offset, $limit, $unit, $thana, $division, $rank, $gender);
            if (Input::exists('export')) {
                return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.ansar_over_age_report');
            }
            return response()->json($data);
        }
        return view('HRM::Report.ansar_over_age_report');
    }

    public function anserTransferHistory()
    {
        return View::make('HRM::Report.ansar_transfer_history');

    }

    public function viewAnsarServiceRecord()
    {
        return View::make('HRM::Report.ansar_service_record');
    }

    public function serviceRecordUnitWise()
    {
        return view('HRM::Report.service_record_unitwise');
    }

    public function ansarInfoForServiceRecordUnitWise()
    {
        DB::enableQueryLog();
//        return Input::all();
        $unit = Input::get('unit');
        $thana = Input::get('thana');
        $division = Input::get('division');
        $rules = [
            'unit' => ['regex:/^[0-9]+$/'],
            'thana' => ['regex:/^[0-9]+$/'],
            'division' => ['regex:/^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);

        if ($valid->fails()) {
            //return print_r($valid->messages());
            return response("Invalid Request(400)", 400);
        }
        $ansar_details = DB::table('tbl_embodiment')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_embodiment.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->join('tbl_kpi_info', 'tbl_kpi_info.id', '=', 'tbl_embodiment.kpi_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->where('tbl_embodiment.emboded_status', '=', 'Emboded');
        if ($unit) {
            $ansar_details->where('tbl_kpi_info.unit_id', '=', $unit);
        }
        if ($thana) {
            $ansar_details->where('tbl_kpi_info.thana_id', '=', $thana);
        }
        if ($division) {
            $ansar_details->where('tbl_kpi_info.division_id', '=', $division);
        }
        $b = $ansar_details->orderBy('tbl_embodiment.ansar_id', 'asc')
            ->select('tbl_embodiment.ansar_id as id', 'tbl_embodiment.reporting_date as r_date', 'tbl_embodiment.joining_date as j_date', 'tbl_embodiment.service_ended_date as se_date', 'tbl_ansar_parsonal_info.ansar_name_bng as name', 'tbl_designations.name_bng as rank',
                'tbl_units.unit_name_bng as unit', 'tbl_kpi_info.kpi_name as kpi')->get();
        return $b;
    }

    public function getPrintIdList()
    {
        $rules = [
            'f_date' => 'required',
            't_date' => 'required',
        ];
        $message = [
            'f_date.required' => 'From date field is required',
            't_date.required' => 'To date field is required'            
        ];
        $valid = Validator::make(Input::all(), $rules, $message);
        if ($valid->fails()) {
            return response($valid->messages()->toJson(), 400, ['Content-Type', 'application/json']);
        }
        $f_date = Carbon::parse(Input::get('f_date'))->format("Y-m-d");
        $t_date = Carbon::parse(Input::get('t_date'))->format("Y-m-d");
//        return Response::json(["f"=>$f_date,"t"=>$t_date]);
        $ansars = AnsarIdCard::whereBetween('created_at', [$f_date, $t_date])->get();
        return Response::json(['ansars' => $ansars]);
    }

    public function ansarCardStatusChange()
    {
        $rules = [
            'action' => 'required|regex:/^[a-z]+$/',
            'ansar_id' => 'required|regex:/^[0-9]+$/',
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid request(400)", 400);
        }
        switch (Input::get('action')) {
            case 'block':
                $ansar = AnsarIdCard::where('ansar_id', Input::get('ansar_id'))->first();
                $ansar->status = 0;
                if ($ansar->save()) {
                    return Response::json(['status' => 1]);
                } else {
                    return Response::json(['status' => 0]);
                }
                break;
            case 'active':
                $ansar = AnsarIdCard::where('ansar_id', Input::get('ansar_id'))->first();
                $ansar->status = 1;
                if ($ansar->save()) {
                    return Response::json(['status' => 1]);
                } else {
                    return Response::json(['status' => 0]);
                }
                break;
            default:
                return response("Invalid request(400)", 400);
        }
    }

    public function printIdList()
    {
        return View::make('HRM::Report.ansar_print_id_list');
    }

    public function checkFile()
    {
        return Response::json(['status' => file_exists(public_path() . '/' . Input::get('path'))]);
    }

    public function offerReportView()
    {
        return View::make('HRM::Report.offer_report');
    }

    public function getOfferedAnsar()
    {
        DB::enableQueryLog();
//        return Input::all();
        $unit = Input::get('unit');
        $division = Input::get('division');
        $past = Input::get('report_past');
        $type = Input::get('type');
        $gender = Input::get('gender');
        $rank = Input::get('rank');
        $tab = Input::get('tab');
        $rules = [
            'unit' => 'numeric',
            'division' => 'numeric',
            'report_past' => 'numeric',
            'type' => 'numeric',
            'gender' => 'in:Male,Female,Other',
            'rank' => 'numeric'
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid request(400)", 400);
        }
        $c_date = Carbon::now();
        switch ($type) {
            case 0:
            case 1:
                $c_date = $c_date->subDays($past);
                break;
            case 2:
                $c_date = $c_date->subMonths($past);
                break;
            case 3:
                $c_date = $c_date->subYears($past);
                break;
        }
        // DB::enableQueryLog();


        $offer_not_respond = DB::table('tbl_sms_offer_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_offer_info.district_id')
            ->join('tbl_units as u', 'u.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->whereDate('tbl_sms_offer_info.sms_send_datetime', '>=', $c_date)
            ->where('tbl_units.id', $unit)
            ->where('tbl_units.division_id', $division)
            //->where('tbl_ansar_parsonal_info.designation_id', $rank)
            ->where('tbl_ansar_parsonal_info.sex', $gender)
            ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'u.unit_name_bng as home_district', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_offer_info.sms_send_datetime', 'tbl_sms_offer_info.memo_id')
         ->unionAll(DB::table('tbl_sms_send_log')
             ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_send_log.ansar_id')
             ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
             ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_send_log.offered_district')
             ->join('tbl_units as u', 'u.id', '=', 'tbl_ansar_parsonal_info.unit_id')
             ->whereDate('tbl_sms_send_log.offered_date', '>=', $c_date)
             ->where('tbl_units.id', $unit)
             ->where('tbl_units.division_id', $division)
             ->where('tbl_ansar_parsonal_info.sex', $gender)
             ->where('tbl_sms_send_log.reply_type', 'No Reply')
            ->select('tbl_ansar_parsonal_info.ansar_name_eng', 'u.unit_name_bng as home_district','tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_send_log.offered_date as sms_send_datetime', 'tbl_sms_send_log.memo_id'))->get();
        //  dd(DB::getQueryLog());

        $offer_received_query = DB::table('tbl_sms_receive_info')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_receive_info.ansar_id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_receive_info.offered_district')
            ->join('tbl_units as u', 'u.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->whereDate('tbl_sms_receive_info.sms_send_datetime', '>=', $c_date)
            ->where('tbl_units.id', $unit)
            ->where('tbl_units.division_id', $division)
            ->where('tbl_ansar_parsonal_info.sex', $gender)
        //->where('tbl_ansar_parsonal_info.designation_id', 3)
        ->select('tbl_sms_receive_info.sms_send_datetime as offered_date', 'u.unit_name_bng as home_district', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_receive_info.sms_received_datetime', 'tbl_sms_receive_info.memo_id')
         ->unionAll(DB::table('tbl_sms_send_log')
             ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_send_log.ansar_id')
             ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_send_log.offered_district')
             ->join('tbl_units as u', 'u.id', '=', 'tbl_ansar_parsonal_info.unit_id')
             ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
             ->whereDate('tbl_sms_send_log.offered_date', '>=', $c_date)
             ->where('tbl_units.id', $unit)
             ->where('tbl_units.division_id', $division)
             ->where('tbl_ansar_parsonal_info.sex', $gender)
             ->where('tbl_sms_send_log.reply_type', 'Yes')
             ->select('tbl_sms_send_log.offered_date',  'u.unit_name_bng as home_district', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_send_log.action_date as sms_received_datetime', 'tbl_sms_send_log.memo_id'));



        //Offer reject
        $offer_reject = DB::table('tbl_sms_send_log')
            ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_sms_send_log.ansar_id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_units.id', '=', 'tbl_sms_send_log.offered_district')
            ->join('tbl_units as u', 'u.id', '=', 'tbl_ansar_parsonal_info.unit_id')
            ->whereDate('tbl_sms_send_log.offered_date', '>=', $c_date)
            ->where('tbl_units.id', $unit)
            ->where('tbl_units.division_id', $division)
            ->where('tbl_ansar_parsonal_info.sex', $gender)
            ->where('tbl_sms_send_log.reply_type', 'No');
        //->select('tbl_sms_send_log.offered_date', 'tbl_ansar_parsonal_info.mobile_no_self', 'u.unit_name_bng as home_district',  'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_send_log.action_date as reject_date', 'tbl_sms_send_log.memo_id')->get();

        if (!empty($rank) && ($tab==1)) {
            //Offer not respond
            $offer_not_respond = $offer_not_respond->where('tbl_ansar_parsonal_info.designation_id', $rank);
                 }
        if (!empty($rank) && ($tab==2)){
            //Offer received query
            $offer_received_query = $offer_received_query->where('tbl_ansar_parsonal_info.designation_id', $rank);

        }
        if (!empty($rank) && ($tab==3)){
            //Offer reject
            $offer_reject = $offer_reject->where('tbl_ansar_parsonal_info.designation_id', $rank);

        }
        $offer_received_query = $offer_received_query->select('tbl_sms_receive_info.sms_send_datetime as offered_date', 'u.unit_name_bng as home_district', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_receive_info.sms_received_datetime', 'tbl_sms_receive_info.memo_id');
        $offer_received = DB::table(DB::raw("(" . $offer_received_query->toSql() . ") t"))->mergeBindings($offer_received_query)->groupBy('ansar_id')->get();
        $offer_received_query = $offer_received_query->get();

        $offer_reject = $offer_reject->select('tbl_sms_send_log.offered_date', 'tbl_ansar_parsonal_info.mobile_no_self', 'u.unit_name_bng as home_district',  'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_units.unit_name_bng', 'tbl_ansar_parsonal_info.ansar_id', 'tbl_designations.code', 'tbl_sms_send_log.action_date as reject_date', 'tbl_sms_send_log.memo_id')->get();
        //print_r($offer_reject);exit;
        if (Input::exists('export') && Input::get('export') == 'true') {
            $e = Excel::create('offer_report', function ($excel) use ($offer_not_respond, $offer_received, $offer_reject) {
                $excel->sheet('offer_not_respond', function ($sheet) use ($offer_not_respond) {
                    $sheet->loadView('HRM::export.offer_not_respond', ['index' => 1, 'ansars' => $offer_not_respond]);
                });
                $excel->sheet('offer_received', function ($sheet) use ($offer_received) {
                    $sheet->loadView('HRM::export.offer_accepted', ['index' => 1, 'ansars' => $offer_received]);
                });
                $excel->sheet('offer_rejected', function ($sheet) use ($offer_reject) {
                    $sheet->loadView('HRM::export.offer_rejected', ['index' => 1, 'ansars' => $offer_reject]);
                });
            })->store('xls', storage_path());
            return response()->json(['status' => true, 'url' => url()->route('download_file_by_name', ['file' => base64_encode(storage_path('offer_report.xls'))])]);
        }
        $offer_not_respond_count = collect($offer_not_respond)->groupBy('code')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $offer_received_count = collect($offer_received)->groupBy('code')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $offer_reject_count = collect($offer_reject)->groupBy('code')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $r = Response::json([
            'onr' => [
                'data' => $offer_not_respond,
                'count' => $offer_not_respond_count
            ],
            'or' => [
                'data' => $offer_received,
                'count' => $offer_received_count
            ],
            'orj' => [
                'data' => $offer_reject,
                'count' => $offer_reject_count
            ]
        ]);
        //dd(DB::getQueryLog());
//        return DB::getQueryLog();
        return $r;
    }

    public function rejectedOfferListView()
    {
        return View::make('HRM::Offer.rejected_offer_list');
    }

    public function getRejectedAnsarList()
    {
        $rules = [
            'from_date' => 'required|date_format:d-M-Y',
            'to_date' => 'required|date_format:d-M-Y',
            'rejection_no' => 'required|numeric|regex:/^[0-9]+$/',
        ];
        $message = [
            'from_date.required' => 'From date field is required',
            'to_date.required' => 'To date field is required',
            'from_date.date_format' => 'From date field is invalid',
            'to_date.date_format' => 'To date field is invalid',
            'rejection_no.required' => 'Rejection no required',
            'rejection_no.numeric' => 'Rejection no must be integer.eg 1,2...',
            'rejection_no.regex' => 'Rejection no must be integer.eg 1,2...',
        ];
        $valid = Validator::make(Input::all(), $rules, $message);
        if ($valid->fails()) {
            return response($valid->messages()->toJson(), 400, ['Content-Type' => 'application/json']);
        }
        $fd = Carbon::parse(Input::get('from_date'))->format("Y-m-d");
        $td = Carbon::parse(Input::get('to_date'))->format("Y-m-d");
        $rejection_no = Input::get('rejection_no');
        $ansars = [];
        $rejected_ansar = OfferSmsLog::whereBetween('action_date', [$fd, $td])->whereIn('reply_type', ['No Reply', 'No'])->groupBy('ansar_id')->having(DB::raw('count(ansar_id)'), '>=', $rejection_no)->select('ansar_id', DB::raw('count(ansar_id)'))->get();
        foreach ($rejected_ansar as $ra) {
            $is_embodied = EmbodimentModel::where('ansar_id', $ra->ansar_id)->whereBetween('joining_date', [$fd, $td])->exists() || EmbodimentLogModel::where('ansar_id', $ra->ansar_id)->whereBetween('joining_date', [$fd, $td])->exists();
            $is_rest = RestInfoModel::where('ansar_id', $ra->ansar_id)->whereBetween('rest_date', [$fd, $td])->exists() || RestInfoLogModel::where('ansar_id', $ra->ansar_id)->whereBetween('rest_date', [$fd, $td])->exists();
            if (!$is_embodied && !$is_rest) {
                $a = DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')->where('tbl_ansar_parsonal_info.ansar_id', $ra->ansar_id)
                    ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_bng', 'tbl_designations.name_bng', 'tbl_units.unit_name_bng', 'tbl_ansar_status_info.*')->first();
                array_push($ansars, $a);
            }
            //return Response::json(['isEmbodied'=>$is_embodied,'isRest'=>$is_rest]);
        }
        return $ansars;
    }

    function ansarHistoryView()
    {
        if (Auth::user()->id == 1) {
            return View::make('HRM::Report.ansar_history');
        } else abort(401);
    }

    function getAnsarHistory(Request $request)
    {
        if (Auth::user()->id == 1) {
            $ansar_id = $request->ansar_id;
            if ($ansar_id) {
                $detail = ActionUserLog::with('user')->where('ansar_id', $ansar_id)->get();
                $ansarInfo = PersonalInfo::where('ansar_id', $ansar_id)->first();
                return Response::json(['logs' => $detail, 'ansarInfo' => $ansarInfo]);
            } else {
                return Response::json([]);
            }
        } else abort(401);
    }

    public function viewAnsarHistory()
    {
        return View::make('HRM::Report.view_ansar_history');
    }

    public function viewAnsarHistoryReport(Request $request)
    {
        //DB::enableQueryLog();
        $result = array();
        if (!isset($request->ansar_id) || empty($request->ansar_id) || $request->ansar_id == 0 || !is_numeric($request->ansar_id)) {
            return response("{'error':'Invalid Request'}", 400, ['Content-Type' => 'text/html']);
        }
        $ansar_id = $request->ansar_id;
        
        //process for full smart card
        $ansar_id = CustomQuery::getFullSmartCardNumber($ansar_id);
        
        
        
        $ansar = PersonalInfo::where('ansar_id', $ansar_id)->first();

        //personal information
        $result["ansar"] = $ansar;
        $result["status"] = $ansar->status->getStatus();
        $result["designation"] = $ansar->designation;
        $result["future"] = $ansar->future;

        //offer information
        $result["cOffer"] = $ansar->offer_sms_info()->with("district.division")->first();
        $result['rcvsms'] = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();
        $result["lOffer"] = $ansar->offerLog()->with("district.division", "user_details")->orderBy("offered_date", "desc")->get();
        $result["bOffer"] = OfferBlockedAnsar::where('ansar_id', $ansar_id)->withTrashed()->orderBy("id", "desc")->get();

        //panel information
        $result["cPanel"] = $ansar->panel()->first();
        $result["lPanel"] = $ansar->panelLog()->orderBy("panel_date", "desc")->get();
        if (!empty($result["cPanel"]) && !empty($result["lOffer"])) {
            if ($result["cPanel"]->go_panel_position == null && $result["cPanel"]->re_panel_position == null) {
                foreach ($result["lOffer"] as $key => $value) {
                    $result["lOffer"][$key]->offerBlocked = false;
                }
            } else if ($result["cPanel"]->go_panel_position == null) {
                $found = false;
                foreach ($result["lOffer"] as $key => $value) {
                    if ($value->offerType == "Global" && !$found) {
                        $found = true;
                        $result["lOffer"][$key]->offerBlocked = true;
                    } else {
                        $result["lOffer"][$key]->offerBlocked = false;
                    }
                }
            } else if ($result["cPanel"]->re_panel_position == null) {
                $found = false;
                foreach ($result["lOffer"] as $key => $value) {
                    if ($value->offerType == "Regional" && !$found) {
                        $found = true;
                        $result["lOffer"][$key]->offerBlocked = true;
                    } else {
                        $result["lOffer"][$key]->offerBlocked = false;
                    }
                }
            }
        }

        //rest information
        $result["cRest"] = $ansar->rest()->with("reason")->first();
        $result["lRest"] = $ansar->restLog()->with("reason", "user_details")->orderBy("rest_date", "desc")->get();

        //embodiment information
        $result["cEmbodiment"] = $ansar->embodiment()->with("kpi.unit", "kpi.division", "kpi.thana")->first();
        $result["lEmbodiment"] = $ansar->embodiment_log()->with("restData","restLogData","disembodimentReason", "kpi.unit", "kpi.division", "kpi.thana")->orderBy("joining_date", "desc")->get();

        //freeze information
        $result["cFreeze"] = $ansar->freezing_info()->with("kpi.unit", "kpi.division", "kpi.thana")->first();
        $result["lFreeze"] = $ansar->freezingInfoLog()->orderBy("freez_date", "desc")->get();

        //Transfer information
        $result["transfer"] = TransferAnsar::where('ansar_id', $ansar_id)->with("presentKpi.unit", "presentKpi.division", "presentKpi.thana", "transferKpi.unit", "transferKpi.division", "transferKpi.thana")->orderBy("id", "desc")->get();

        //block information
        $result["block"] = $ansar->block()->orderBy("date_for_block", "desc")->get();

        //black information
        $result["cBlack"] = $ansar->black()->first();
        $result["lBlack"] = $ansar->blackLog()->orderBy("black_listed_date", "desc")->get();        
        //Change Log information
        $result["cInfoHistoryLog"] = $ansar->personalInfoLog()->with("user")->orderBy("log_id", "desc")->get();

        $start = 0;
        $info_log = [];
        foreach($result["cInfoHistoryLog"] as $log){

              //$response = $this->processArryaLog($log);

              /************** Loop start **********/

                $ddd = $log->toArray();

                foreach ($ddd as $key => $value) {

                    if($value =='' || $value == null || $key == 'log_id' || $key == 'action' || $key == 'action_time' || $key == 'ansar_id' || $key == 'user_id' || $key == 'updated_at' || $key == 'created_at' || $key == 'user'){

                    }else {
                        if($key == 'designation_id'){
                            if($value == 1){
                                $value = 'Ansar';
                            }elseif ($value == 2){
                                $value = 'APC';
                            }elseif ($value == 3){
                                $value = 'PC';
                            }else{
                                
                            }
                        }
                        $key = str_replace('_', ' ', $key);
                        $info_log[$start]['key'] = ucwords($key);
                        $info_log[$start]['value'] = $value;
                        $info_log[$start]['action_time'] = $ddd['action_time'];
                        $info_log[$start]['log_id'] = $ddd['log_id'];
                        $info_log[$start]['user_id'] = $ddd['user'];
                        $info_log[$start]['user_name'] = $ddd['user']['user_name'];
                        $info_log[$start]['updated_at'] = $ddd['updated_at'];
                        $start++;
                    }

                  if ($key =='verified' && $value == 0 && $value !== null){
                        $key = str_replace('_', ' ', $key);
                        $info_log[$start]['key'] = ucwords($key);
                        $info_log[$start]['value'] = $value;
                        $info_log[$start]['action_time'] = $ddd['action_time'];
                        $info_log[$start]['log_id'] = $ddd['log_id'];
                        $info_log[$start]['user_id'] = $ddd['user_id'];
                        $info_log[$start]['user_name'] = $ddd['user']['user_name'];
                        $info_log[$start]['updated_at'] = $ddd['updated_at'];
                        $start++;

                    }

                }



        }

        $result["cInfoHistoryLog"] = $info_log;
        
        
        //rintu add new transfer history prt//
        $date_time_array = [];
            
            $tracker = 0;
            
            if ($result["lEmbodiment"]->count() > 0) {
                
                $loop = 0;
            
                foreach($result["lEmbodiment"] as $index=>$lEmbodiment){
					if($lEmbodiment->old_embodiment_id == 0){
						$date_time_array[$loop]['kpi'] = $this->getFirstKPIAlternative($lEmbodiment->old_embodiment_id, $ansar_id, $lEmbodiment->joining_date, $lEmbodiment->release_date);
					}else{
						$date_time_array[$loop]['kpi'] = $this->getFirstKPI($lEmbodiment->old_embodiment_id, $ansar_id);
					}
					
                    $date_time_array[$loop]['time'] = $lEmbodiment->joining_date;
                    $date_time_array[$loop]['type'] = 'embodiment';
                    $date_time_array[$loop]['data'] = $lEmbodiment;
                    $date_time_array[$loop]['Preference'] = '1';
                    
                    $tracker++; $loop++;
                    $date_time_array[$loop]['time'] = $lEmbodiment->release_date;
                    $date_time_array[$loop]['type'] = 'disembodiment';
                    $date_time_array[$loop]['data'] = $lEmbodiment;
                    $date_time_array[$loop]['Preference'] = '5';

                    $tracker++;$loop++;
                    
                    
                }
            }
            
            if (!empty($result["cEmbodiment"])){
               
                $index = $tracker;     
                $date_time_array[$index + 1]['kpi'] = $this->getFirstKPI($result["cEmbodiment"]->id, $ansar_id);				
                $date_time_array[$index + 1]['time'] = $result["cEmbodiment"]->joining_date;
                $date_time_array[$index + 1]['type'] = 'embodiment';
                $date_time_array[$index + 1]['data'] = $result["cEmbodiment"];
                $date_time_array[$index + 1]['Preference'] = '1';
                //$data_time_array[$index + 1]['kpi'] = $this->getFirstKPI($result["cEmbodiment"]->id, $ansar_id);
                
                $tracker++;
            
            }
            
            
            
            if ($result["lFreeze"]->count() > 0) {  
                
                $loop = 0;
                $index = $tracker;            
                foreach($result["lFreeze"] as $index2=>$lFreeze){
                    $date_time_array[$index + $loop + 1]['time'] = $lFreeze->freez_date;
                    $date_time_array[$index + $loop  + 1]['type'] = 'freez';  
                    $date_time_array[$index + $loop  + 1]['data'] = $lFreeze;
                    $date_time_array[$index + $loop  + 1]['Preference'] = '2';
                    $tracker++; $loop++;
                    
                    $date_time_array[$index + $loop + 1]['time'] = $lFreeze->move_frm_freez_date;
                    $date_time_array[$index + $loop  + 1]['type'] = 'unfreez';  
                    $date_time_array[$index + $loop  + 1]['data'] = $lFreeze;
                    $date_time_array[$index + $loop  + 1]['Preference'] = '3';
                    $tracker++; $loop++;
                }
            }
            
            
            if (!empty($result["cFreeze"])) {

                $index = $tracker;
                $date_time_array[$index + 1]['time'] = $result["cFreeze"]->freez_date;
                $date_time_array[$index + 1]['type'] = 'freez';
                $date_time_array[$index + 1]['Preference'] = '2';
                $date_time_array[$index + 1]['data']['freez_date'] = $result["cFreeze"]->freez_date;

                $tracker++;
            }

            if ($result["transfer"]->count() > 0) {
                $index = $tracker;            
                foreach($result["transfer"] as $index4=>$transfer){
                    $date_time_array[$index + $index4 + 1]['time'] = $transfer->transfered_kpi_join_date;
                    $date_time_array[$index + $index4 + 1]['type'] = 'transfer';
                    $date_time_array[$index + $index4 + 1]['data'] = $transfer;
                    $date_time_array[$index + $index4 + 1]['Preference'] = '4';


                     $tracker++;
                }
            }
            
            //usort($date_time_array, '$this->date_compare');
            //echo '<pre>'; 
           // print_r($date_time_array); exit;
            
            $array = collect($date_time_array)->sortByDesc('Preference')->sortByDesc('time')->reverse()->toArray();
            
//            foreach($array as $loop_index){
//                if($loop_index['type'] == 'transfer'){
//                   echo "Date: ".$loop_index['time'].'     Type:'.$loop_index['data']->presentKpi->kpi_name.'<br>';
//                }else{
//                   echo "Date: ".$loop_index['time'].'     Type:'.$loop_index['data'].'<br>';
//
//                }
//            }
//
//            //print_r($array); 
//            exit;
            
            $transfer_data = [];

            $final_loop = 0;

            foreach ($array as $loop_index) {

                $transfer_data[$final_loop] = $loop_index;
                
                if($final_loop == 0){
                    $transfer_data[$final_loop]['time_difference'] = ''; 
                } else {

                    if ($loop_index['type'] != 'embodiment') {
                        $previous_date = $this->formate_date($previous_data['time']);
                        $current_date = $this->formate_date($loop_index['time']);

                        $date_a = Carbon::parse($previous_date);
                        $date_b = Carbon::parse($current_date);

                        $interval = $date_b->diffInDays($date_a);

                        $transfer_data[$final_loop-1]['time_difference'] = $interval;
                    }
                }

                $previous_data = $loop_index;
                $final_loop++;
            }
            
            $result['transfer_data'] = $transfer_data;

//        dd(DB::getQueryLog());
        return Response::json($result);
    }

    public function viewAnsarNID(){
        return View::make('HRM::Report.view_ansar_nid');

    }

    public function viewAnsarNIDReport(Request $request)
    {
        //DB::enableQueryLog();
        $result = array();
        if (!isset($request->ansar_id) || empty($request->ansar_id) || $request->ansar_id == 0 || !is_numeric($request->ansar_id)) {
            return response("{'error':'Invalid Request'}", 400, ['Content-Type' => 'text/html']);
        }
        $nid = $request->ansar_id;
        $date_of_birth = $request->dob;
        if(strlen($nid) === 10){
            $post_data['nid10Digit'] = $nid;
        }elseif (strlen($nid) === 17){
            $post_data['nid17Digit'] = $nid;
        }else{
            return 'invalid NID';
        }

        // Save Request
        $client = array(
            'nid' => $nid,
            'dob' => $date_of_birth,
            'action_by' => Auth::user()->id
        );

        $dataClient = new NidRequestLog;
        $dataClient->fill($client);
        $dataClient->save();


        $post_data['dateOfBirth'] = $date_of_birth;
        $token_data = $this->generateToken();

        //print_r($post_data); exit;

        if($token_data['status'] == 'OK'){
            $token = $token_data['success']['data']['access_token'];

            $result = $this->getNIDReponse($token, $post_data);

            if($result['status'] == 'OK'){
                $image = $result['success']['data']['photo'];

                $content = file_get_contents($image);


                $path = storage_path('data/NID');
                File::makeDirectory($path,0777,true);
                //file_put_contents(DIRECTORY . '/image.jpg', $content);


                if(File::exists($path.'/'.$nid.'.jpg')){
                    //File::delete($path.'/'.$file->getClientOriginalName());

                }
                try {
                    Image::make($content)->save($path . '/' . $nid.'.jpg');

                }catch (\Exception $e){

                }

                $result['success']['data']['photo'] =  'data/NID'.'/'.$nid.'.jpg';



            }else{
                return 'problem, try again later';
            }
        }else{
            return 'token problem';
        }

        //$result = $this->getNIDReponse($token, $txID);
        return Response::json($result);
    }

    public function generateToken(){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://prportal.nidw.gov.bd/partner-service/rest/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER=> true,
            CURLOPT_POSTFIELDS =>'{
                "username":"ansarandvdp",
                "password":"aNsAr&VdP%2022#P@rTnEr"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $errno = curl_errno($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($errno) {
            return "cURL Error #:" . $err;
        } else {
            //return $response;
            $er_array = json_decode($response, true);
            return $er_array;
        }
    }

    public function getNIDReponse($token, $post_data)
    {
        $curl = curl_init();
        $data = json_encode($post_data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://prportal.nidw.gov.bd/partner-service/rest/voter/details',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER=> true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $errno = curl_errno($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($errno) {
            return "cURL Error #:" . $err;
        } else {
            //return $response;
            $er_array = json_decode($response, true);
            return $er_array;
        }
    }

    function GetImageFromUrl($link)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch,CURLOPT_URL,$link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result=curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function viewAnsarScheduleJobs(Request $request)
    {
        return View::make('HRM::Report.view_schedule_jobs');
    }

    public function viewAnsarScheduleJobsReport(Request $request)
    {
        $input = $request->all();
        $ansarList = DB::table('tbl_ansar_future_state')
            ->join("tbl_ansar_parsonal_info", "tbl_ansar_future_state.ansar_id", "=", "tbl_ansar_parsonal_info.ansar_id")
            ->join("tbl_designations", "tbl_designations.id", "=", "tbl_ansar_parsonal_info.designation_id");

        if (isset($input["gender"]) && ($input["gender"] == 'Male' || $input["gender"] == 'Female' || $input["gender"] == 'Other')) {
            $ansarList = $ansarList->where('tbl_ansar_parsonal_info.sex', '=', $input["gender"]);
        }
        if (isset($input["rank"]) && !empty($input["rank"]) && is_numeric($input["rank"])) {
            $ansarList = $ansarList->where('tbl_ansar_parsonal_info.designation_id', '=', $input["rank"]);
        }
        if (isset($input["q"]) && !empty($input["q"]) && is_numeric($input["q"])) {
            $ansarList = $ansarList->where('tbl_ansar_future_state.ansar_id', '=', $input["q"]);
        }
        $dataForCount = clone $ansarList;

        $rankCount = $dataForCount->groupBy('tbl_designations.id')->select(DB::raw("count('tbl_ansar_parsonal_info.ansar_id') as t"), 'tbl_designations.code');

        $rankCount = collect($rankCount->get())->pluck('t', 'code');

        return Response::json(['list' => $ansarList->get(), 'tCount' => $rankCount]);
    }
    
    
    // Ansar Embodiment Count Log 
    
     public function embodimentCountView()
    {
        return view('HRM::Report.embodiment_count_view');
    }

    public function embodimentCountDetails(Request $request)
    {
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric'
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        }
        $data = CustomQuery::getEbodimentAnsarLogData($offset, $limit, $request->q);
        if (Input::exists('export')) {
            return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.blocklist_report');
        }
        return response()->json($data);
    }


    public function anserTransferReport()
    {
        return View::make('HRM::Report.ansar_transfer_report');

    }

    public function transferAnsarInfo()
    {
        $division = Input::get('division_id');
        $limit = Input::get('limit');
        $offset = Input::get('offset');
        $from = Input::get('from_date');
        $to = Input::get('to_date');
        $unit = Input::get('unit_id');
        $thana = Input::get('thana_id');
        $rank = Input::get('rank');
        $gender = Input::get('gender');
        $q = Input::get('q');
        $rules = [
            'view' => 'regex:/^[a-z]+/',
            'limit' => 'numeric',
            'offset' => 'numeric',
            'from_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'to_date' => ['regex:/^[0-9]{1,2}\-((Jan)|(Feb)|(Mar)|(Apr)|(May)|(Jun)|(Jul)|(Aug)|(Sep)|(Oct)|(Nov)|(Dec))\-[0-9]{4}$/'],
            'unit_id' => ['regex:/^(all)$|^[0-9]+$/'],
            'thana_id' => ['regex:/^(all)$|^[0-9]+$/'],
            'division_id' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid Request(400)", 400);
        } else {
            if (!is_null($from) && !is_null($to) && !is_null($unit) && !is_null($thana)) {
                $from_date = Carbon::parse($from)->format('Y-m-d');
                $to_date = Carbon::parse($to)->format('Y-m-d');
                $data = CustomQuery::transferAnsarListforReportWithRankGender($offset, $limit, $from_date, $to_date, $division, $unit, $thana, $rank, $gender, $q);
                if (Input::exists('export')) {
                    return $this->exportData(collect($data['ansars'])->chunk(2000)->toArray(), 'HRM::export.ansar_transfer_report');
                }
                return response()->json($data);
            }
        }
    }
}
