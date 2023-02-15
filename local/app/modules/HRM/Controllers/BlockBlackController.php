<?php

namespace App\modules\HRM\Controllers;

use App\Helper\Facades\GlobalParameterFacades;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\BlockStatusSms;
use App\modules\HRM\Models\AnsarFutureState;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\HRM\Models\BlackListInfoModel;
use App\modules\HRM\Models\BlackListModel;
use App\modules\HRM\Models\BlockListModel;
use App\modules\HRM\Models\CustomQuery;
use App\modules\HRM\Models\EmbodimentLogModel;
use App\modules\HRM\Models\EmbodimentModel;
use App\modules\HRM\Models\FreezedAnsarEmbodimentDetail;
use App\modules\HRM\Models\FreezingInfoModel;
use App\modules\HRM\Models\OfferBlockedAnsar;
use App\modules\HRM\Models\OfferSMS;
use App\modules\HRM\Models\OfferZone;
use App\modules\HRM\Models\PanelInfoLogModel;
use App\modules\HRM\Models\PanelModel;
use App\modules\HRM\Models\RestInfoModel;
use App\modules\HRM\Models\RestInfoLogModel;
use App\modules\HRM\Models\SmsReceiveInfoModel;
use App\modules\HRM\Models\AnsarRetireHistory;
use App\modules\HRM\Models\EmbodimentUnitDailyLog;
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

class BlockBlackController extends Controller {

    function sendSMSFromPHPArray(){
        $data = array(
           /* 0 => array('8801763322498', 'Please Join in CHITTAGONGSOUTH by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC CHITTAGONGSOUTH'),
            1 => array('8801738701145', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            2 => array('8801762878398', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            3 => array('8801305891783', 'Please Join in SYLHET by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            4 => array('8801601335551', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            5 => array('8801772687282', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            6 => array('8801758493202', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            7 => array('8801756419260', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            8 => array('8801778162796', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            9 => array('8801788822958', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            10 => array('8801797989543', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            11 => array('8801316469443', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            12 => array('8801764221323', 'Please Join in HABIGANJ by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC HABIGANJ'),
            13 => array('8801734248075', 'Please Join in SYLHET by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            14 => array('8801791762247', 'Please Join in SYLHET by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            15 => array('8801770810115', 'Please Join in DHAKASOUTH by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            16 => array('8801765037000', 'Please Join in SYLHET by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            17 => array('8801988104417', 'Please Join in SYLHET by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            18 => array('8801719596474', 'Please Join in SYLHET by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            19 => array('8801741868024', 'Please Join in SYLHET by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            20 => array('8801914598514', 'Please Join in SYLHET by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            21 => array('8801812127454', 'Please Join in COXS BAZAR by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC COXS BAZAR'),
            22 => array('8801717214982', 'Please Join in MADARIPUR by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MADARIPUR'),
            23 => array('8801853654070', 'Please Join in DHAKASOUTH by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            24 => array('8801840158005', 'Please Join in CHANDPUR by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC CHANDPUR'),
            25 => array('8801988829009', 'Please Join in COXS BAZAR by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC COXS BAZAR'),
            26 => array('8801765407581', 'Please Join in CHANDPUR by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC CHANDPUR'),
            28 => array('8801825633408', 'Please Join in DHAKASOUTH by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            29 => array('8801625578587', 'Please Join in MAULVIBAZAR by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            30 => array('8801725559148', 'Please Join in DHAKASOUTH by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            31 => array('8801729971773', 'Please Join in DHAKASOUTH by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            32 => array('8801731652916', 'Please Join in MAULVIBAZAR by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            33 => array('8801823035623', 'Please Join in DHAKASOUTH by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            34 => array('8801911182998', 'Please Join in SYLHET by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            35 => array('8801535196819', 'Please Join in CHANDPUR by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC CHANDPUR'),
            36 => array('8801937908948', 'Please Join in NILPHAMARI by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC NILPHAMARI'),
            37 => array('8801853708384', 'Please Join in DHAKASOUTH by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            38 => array('8801857927610', 'Please Join in COXS BAZAR by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC COXS BAZAR'),
            39 => array('8801859002480', 'Please Join in NILPHAMARI by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC NILPHAMARI'),
            40 => array('8801745827159', 'Please Join in DHAKAEAST by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKAEAST'),
            41 => array('8801843675605', 'Please Join in DHAKASOUTH by 26-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            42 => array('8801933110998', 'Please Join in DHAKASOUTH by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            43 => array('8801755739985', 'Please Join in DHAKAEAST by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKAEAST'),
            44 => array('8801765895859', 'Please Join in DHAKASOUTH by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            45 => array('8801726019757', 'Please Join in SYLHET by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            46 => array('8801326550797', 'Please Join in CHITTAGONGNORTH by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC CHITTAGONGNORTH'),
            47 => array('8801757455645', 'Please Join in CHITTAGONGNORTH by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC CHITTAGONGNORTH'),
            48 => array('8801718466616', 'Please Join in CHITTAGONGNORTH by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC CHITTAGONGNORTH'),
            49 => array('8801719380412', 'Please Join in NARAYANGANJ by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC NARAYANGANJ'),
            50 => array('8801739725957', 'Please Join in DHAKASOUTH by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            51 => array('8801764758108', 'Please Join in CHITTAGONGNORTH by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC CHITTAGONGNORTH'),
            52 => array('8801761314952', 'Please Join in NARAYANGANJ by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC NARAYANGANJ'),
            53 => array('8801732860738', 'Please Join in MAULVIBAZAR by 25-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            54 => array('8801798929228', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            55 => array('8801767667266', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            56 => array('8801920854687', 'Please Join in DHAKASOUTH by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            57 => array('8801732730166', 'Please Join in BARISHAL by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC BARISHAL'),
            58 => array('8801317354152', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            59 => array('8801912941079', 'Please Join in DHAKASOUTH by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            60 => array('8801725226114', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            62 => array('8801785783100', 'Please Join in DHAKASOUTH by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            63 => array('8801788772527', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            64 => array('8801771285418', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            65 => array('8801792598467', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            66 => array('8801748500835', 'Please Join in DHAKASOUTH by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKASOUTH'),
            67 => array('8801781224714', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            68 => array('8801764493022', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            69 => array('8801721809697', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            70 => array('8801313739803', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            71 => array('8801799675973', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            72 => array('8801995980302', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            73 => array('8801785579656', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            74 => array('8801714631140', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            75 => array('8801948640823', 'Please Join in CHITTAGONGSOUTH by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC CHITTAGONGSOUTH'),
            76 => array('8801712748886', 'Please Join in MAULVIBAZAR by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC MAULVIBAZAR'),
            77 => array('8801741665603', 'Please Join in DHAKAEAST by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC DHAKAEAST'),
            78 => array('8801921557212', 'Please Join in NARAYANGANJ by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC NARAYANGANJ'),
            79 => array('8801918068875', 'Please Join in NILPHAMARI by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC NILPHAMARI'),
            80 => array('8801925654419', 'Please Join in CHITTAGONGNORTH by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC CHITTAGONGNORTH'),
            81 => array('8801721521029', 'Please Join in BARISHAL by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC BARISHAL'),
            82 => array('8801722340239', 'Please Join in SYLHET by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC SYLHET'),
            83 => array('8801746398996', 'Please Join in NILPHAMARI by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC NILPHAMARI'),
            84 => array('8801752578639', 'Please Join in BARISHAL by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC BARISHAL'),
            85 => array('8801969104282', 'Please Join in NARAYANGANJ by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC NARAYANGANJ'),
            86 => array('8801734626948', 'Please Join in NARAYANGANJ by 24-11-2022 with Smart Card. Otherwise your offer will be cancelled -DC NARAYANGANJ'),
        */);

        foreach($data as $row){
            $body1 = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $row[1])));

            $user = 'ansarapi';
            $pass = 'h83?7U79';
            $sid = 'ANSARVDPBANGLA';
            $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";

            $param = "user=$user&pass=$pass&sms[0][0]=$row[0]&sms[0][1]=" . urlencode($body1) . "&sid=$sid";
            $crl = curl_init();
            curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($crl, CURLOPT_URL, $url);
            curl_setopt($crl, CURLOPT_HEADER, 0);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($crl, CURLOPT_POST, 1);
            curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
            $response = curl_exec($crl);
            curl_close($crl);
            Log::info($response);
        }
    }

    public function blockListEntryView() {
        return view('HRM::Blackblock_view.blocklist_entry');
    }

    public function loadAnsarDetailforBlock(Request $request) {
        $rule = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:hrm.tbl_ansar_parsonal_info,ansar_id'
        ];
        $vaild = Validator::make($request->all(), $rule);
        if ($vaild->fails()) {
            return Response::json([]);
        }
        $ansar_id = Input::get('ansar_id');

        $status = AnsarStatusInfo::where('ansar_id', $ansar_id)->first()->getStatus();
        $ansar_details = DB::table('tbl_ansar_parsonal_info')
                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                ->first();
//        if ($ansar_check->verified == 0 || $ansar_check->verified == 1) {
//            $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                    'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                ->first();
//
//            $status = "Entry";
//        }
//        else {
//            if ($ansar_check->free_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
//                $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                    ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                    ->first();
//
//                $status = "Free";
//
//            }
//            elseif ($ansar_check->pannel_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
//                $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_panel_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
//                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                    ->select('tbl_panel_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                    ->first();
//
//                $status = "Paneled";
//
//            } elseif ($ansar_check->offer_sms_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
//                $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
//                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                    ->select('tbl_sms_offer_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                    ->first();
//
//                $status = "Offer";
//
//            } elseif ($ansar_check->embodied_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
//                $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
//                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                    ->select('tbl_embodiment.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                    ->first();
//
//                $status = "Embodied";
//
//            } elseif ($ansar_check->rest_status == 1 && $ansar_check->block_list_status == 0 && $ansar_check->black_list_status == 0) {
//                $ansar_details = DB::table('tbl_ansar_parsonal_info')
//                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
//                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
//                    ->join('tbl_rest_info', 'tbl_rest_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
//                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
//                    ->select('tbl_rest_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex',
//                        'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
//                    ->first();
//
//                $status = "Rest";
//            }
//        }

        return Response::json(array('ansar_details' => $ansar_details, 'status' => $status[0]));
    }

    public function blockListEntry(Request $request) {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/',
            'block_date' => 'required',
            'block_comment' => 'required',
        ];

        if ($request->has('is_periodical')) {
            $rules['unblock_date'] = 'required|date|after:block_date';
            $rules['move_status'] = 'required';
        }


        $this->validate($request, $rules);
        $ansar_status = $request->input('ansar_status');
        $ansar_id = $request->input('ansar_id');
        $block_date = $request->input('block_date');
        $modified_block_date = Carbon::parse($block_date)->format('Y-m-d');
        $unblock_date = $request->input('unblock_date');
        $modified_unblock_date = Carbon::parse($unblock_date)->format('Y-m-d');
        $block_comment = $request->input('block_comment');
        $from_id = $request->input('from_id');
//        return $request->all();
        DB::beginTransaction();
        try {
            $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            if (!$ansar)
                throw new\Exception('This Ansar doesn`t exists');
            $ansar_block_details = [
                'ansar_id' => $ansar_id,
                'block_list_from' => $ansar->getStatus()[0] == "Embodied" ? "Embodiment" : $ansar->getStatus()[0],
                'from_id' => $from_id,
                'date_for_block' => $modified_block_date,
                'comment_for_block' => $block_comment,
                'action_user_id' => Auth::user()->id,
            ];

            if ($request->has('is_periodical')) {
                $ansar_block_details['is_periodic'] = 1;
                $ansar_block_details['assigned_unblock_date'] = $modified_unblock_date;
                $ansar_block_details['assigned_unblock_stutus'] = $request->input('move_status');
            }

            if (Carbon::parse($block_date)->lte(Carbon::now())) {
                BlockListModel::create($ansar_block_details);
                switch ($ansar->getStatus()[0]) {
                    case AnsarStatusInfo::NOT_VERIFIED_STATUS:
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'ENTRY', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::FREE_STATUS:
                        $ansar->update(['block_list_status' => 1, 'free_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'FREE', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::PANEL_STATUS:
                        $ansar->ansar->panel->saveLog("Blocklist", $modified_block_date, $block_comment);
                        $ansar->ansar->panel->delete();
                        $ansar->update(['block_list_status' => 1, 'pannel_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'PANEL', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::OFFER_STATUS:
                        $offer = $ansar->ansar->offer_sms_info;
                        if (!$offer) {
                            $offer = $ansar->ansar->receiveSMS;
                            $offer->saveLog();
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        } else {
                            $offer->saveLog("No Reply");
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        }
                        $ansar->update(['block_list_status' => 1, 'offer_sms_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::EMBODIMENT_STATUS:
                        $ansar->ansar->embodiment->saveLog("Blocklist", $modified_block_date, 8);
                        $ansar->ansar->embodiment->delete();
                        $ansar->update(['block_list_status' => 1, 'embodied_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::REST_STATUS:
                        $ansar->ansar->rest->saveLog("Blocklist", $modified_block_date, $block_comment);
                        $ansar->ansar->rest->delete();
                        $ansar->update(['block_list_status' => 1, 'rest_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'REST', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::FREEZE_STATUS:
                        //echo '<pre>'; print_r($ansar->ansar->freezing_info); exit;
                        $ansar->ansar->freezing_info->saveLog("Blocklist", $modified_block_date, $block_comment);
                        $ansar->ansar->freezing_info->delete();
                        $ansar->update(['block_list_status' => 1, 'freezing_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'FREEZ', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    case AnsarStatusInfo::OFFER_BLOCK_STATUS:
                        $offer_blocked = OfferBlockedAnsar::where('ansar_id', $ansar->ansar_id)->first();
                        $offer_blocked->delete();
                        $ansar->update(['block_list_status' => 1, 'offer_block_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER BLOCK', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    default:
                        throw new \Exception('This Ansar can`t be blocked.Because he is BLACKED');
                        break;
                }
                $this->dispatch(new BlockStatusSms($ansar_id, $block_comment));
            } else {
                $ansar_future_state = [
                    'ansar_id' => $request->ansar_id,
                    'data' => serialize($ansar_block_details),
                    'action_date' => Carbon::now()->format("y-m-d H:i:s"),
                    'activation_date' => Carbon::parse($block_date)->format("Y-m-d"),
                    'action_by' => Auth::user()->id,
                    'from_status' => 'Embodiment',
                    'to_status' => 'Block',
                ];
                switch ($ansar->getStatus()[0]) {

                    case AnsarStatusInfo::NOT_VERIFIED_STATUS:
                        $ansar_future_state['from_status'] = 'Unverified';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'ENTRY', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::FREE_STATUS:
                        $ansar_future_state['from_status'] = 'Free';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'FREE', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::PANEL_STATUS:
                        $ansar_future_state['from_status'] = 'Panel';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'PANEL', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::OFFER_STATUS:
                        $offer = $ansar->ansar->offer_sms_info;
                        if (!$offer) {
                            $offer = $ansar->ansar->receiveSMS;
                            $offer->saveLog();
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        } else {
                            $offer->saveLog("No Reply");
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        }
                        $ansar->update(['block_list_status' => 1, 'offer_sms_status' => 0]);
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::EMBODIMENT_STATUS:
                        $ansar_future_state['from_status'] = 'Embodiment';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::REST_STATUS:
                        $ansar_future_state['from_status'] = 'Rest';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'REST', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::FREEZE_STATUS:
                        $ansar_future_state['from_status'] = 'Freeze';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'FREEZ', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case AnsarStatusInfo::OFFER_BLOCK_STATUS:
                        $ansar_future_state['from_status'] = 'Offer_blocked';
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'OFFER BLOCK', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    default:
                        throw new \Exception('This Ansar can`t be blocked.Because he is BLACKED');
                        break;
                }
                AnsarFutureState::create($ansar_future_state);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return Response::json(['status' => false, 'message' => $e->getMessage()]);
            }
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        if ($request->ajax()) {
            return Response::json(['status' => true, 'message' => 'Ansar Id: ' . $ansar_id . " successfully blocked"]);
        }
        return Redirect::route('blocklist_entry_view')->with('success_message', 'Ansar Id:' . $ansar_id . ' successfully blocked');
    }

    public function arrayBlockListEntry(Request $request) {
//        return $request->all();
        $ansar = $request->input('ansar');
        $block_date = $request->input('block_date');
        $modified_block_date = Carbon::parse($block_date)->format('Y-m-d');
        $block_comment = $request->input('block_comment');
        $from_id = $request->input('from_id');

        DB::beginTransaction();
        foreach ($ansar as $a) {
            //return $a;
            $ansar_id = $a['ansar_id'];
            $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            try {
                switch ($a['status']) {

                    case "Entry":
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Entry";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'ENTRY','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLOCKED', 'from_state' => 'ENTRY', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case "Free":
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Free";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
                        $ansar->update(['block_list_status' => 1, 'free_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'FREE','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'FREE', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case "Paneled":
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Panel";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
                        $ansar->ansar->panel->saveLog("Block", $modified_block_date, $block_comment);
                        $ansar->ansar->panel->delete();
                        $ansar->update(['block_list_status' => 1, 'pannel_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'PANEL','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'PANEL', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case "Offer":
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Offer";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
                        $offer = $ansar->ansar->offer_sms_info;
                        if (!$offer) {
                            $offer = $ansar->ansar->receiveSMS;
                            $offer->saveLog();
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        } else {
                            $offer->saveLog("No Reply");
                            $offer->deleteCount();
                            $offer->deleteOfferStatus();
                            $offer->delete();
                        }
                        $ansar->update(['block_list_status' => 1, 'offer_sms_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'OFFER','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'OFFER', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case "Embodied":
                        Log::info("OK BLOCK EMBODIED");
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Embodiment";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
                        $ansar->ansar->embodiment->saveLog("Blocklist", $modified_block_date, 8);
                        $ansar->ansar->embodiment->delete();
                        $ansar->update(['block_list_status' => 1, 'embodied_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'EMBODIED','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;

                    case "Rest":
                        $blocklist_entry = new BlockListModel();
                        $blocklist_entry->ansar_id = $ansar_id;
                        $blocklist_entry->block_list_from = "Rest";
                        $blocklist_entry->from_id = $from_id;
                        $blocklist_entry->date_for_block = $modified_block_date;
                        $blocklist_entry->comment_for_block = $block_comment;
                        $blocklist_entry->action_user_id = Auth::user()->id;
                        $blocklist_entry->save();
                        $ansar->ansar->rest->saveLog("Blocklist", $modified_block_date, $block_comment);
                        $ansar->ansar->rest->delete();
                        $ansar->update(['block_list_status' => 1, 'rest_status' => 0]);
//                    Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'BLOCKED','from_state'=>'REST','to_state'=>'BLOCKED','action_by'=>auth()->user()->id]));
                        CustomQuery::addActionlog(['ansar_id' => $ansar_id, 'action_type' => 'BLOCKED', 'from_state' => 'REST', 'to_state' => 'BLOCKED', 'action_by' => auth()->user()->id]);
                        break;
                    default:
                        if ($request->ajax()) {
                            return Response::json(['status' => false, 'message' => 'Invalid Request']);
                        }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return Response::json(['status' => false, 'message' => $e->getMessage()]);
            }
        }
        DB::commit();
        if ($request->ajax()) {
            return Response::json(['status' => true, 'message' => "Ansars successfully blocked"]);
        }
        return Response::json(['status' => false, 'message' => 'Invalid Request']);
    }

    public function unblockListEntryView() {
        return view('HRM::Blackblock_view.unblocklist_entry');
    }

    public function loadAnsarDetailforUnblock(Request $request) {
        $valid = Validator::make($request->all(), [
                    'ansar_id' => 'required|regex:/^[0-9]+$/'
        ]);
        if ($valid->fails()) {
            return [];
        }
        $ansar_id = Input::get('ansar_id');

        $ansar_details = DB::table('tbl_blocklist_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blocklist_info.ansar_id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                        ->where('tbl_blocklist_info.ansar_id', '=', $ansar_id)
                        ->where('tbl_blocklist_info.date_for_unblock', '=', null)
                        ->where('tbl_ansar_status_info.block_list_status', '=', 1)->orderBy('tbl_blocklist_info.id', 'desc')
                        ->select('tbl_blocklist_info.block_list_from', 'tbl_blocklist_info.date_for_block', 'tbl_blocklist_info.comment_for_block', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_units.unit_name_eng', 'tbl_designations.name_eng')->first();

        return Response::json($ansar_details);
    }

    public function unblockListEntry(Request $request) {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:hrm.tbl_blocklist_info,ansar_id',
            'unblock_date' => 'required',
            'move_status' => 'required'
        ];
        $this->validate($request, $rules);
        $ansar_id = $request->input('ansar_id');
        $unblock_date = $request->input('unblock_date');
        $moveStatus = $request->input('move_status');
        if (!empty($request->input('memo_id')))
            $memorandumId = $request->input('memo_id');
        else
            $memorandumId = 'N/A';
        $modified_unblock_date = Carbon::parse($unblock_date)->format('Y-m-d');
        $unblock_comment = $request->input('unblock_comment');
        DB::beginTransaction();
        try {
            $ansar = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            if (empty($ansar) || !in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->getStatus())) {
                throw new \Exception("This ansar is not in block list");
            }
            $ansar_unblock_details = [
                'date_for_unblock' => $modified_unblock_date,
                'comment_for_unblock' => $unblock_comment
            ];
            if (Carbon::parse($unblock_date)->lte(Carbon::now())) {
                $this->removeOtherStatusExceptBlock($ansar);
                $blocklist_entry = BlockListModel::where('ansar_id', $ansar_id)->orderBy('id', 'desc')->first();
                $blocklist_entry->update($ansar_unblock_details);
                $blocklist_entry->save();
                switch (strtolower($moveStatus)) {
                    case "free":
                        $ansar->updateToFreeState()->save();
                        break;
                    case "rest":
                        RestInfoModel::create([
                            'ansar_id' => $ansar_id,
                            'old_embodiment_id' => 0,
                            'memorandum_id' => $memorandumId,
                            'rest_date' => Carbon::now()->format("Y-m-d"),
                            'active_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                            'disembodiment_reason_id' => 8,
                            'total_service_days' => 0,
                            'rest_form' => 'Block',
                            'comment' => 'After unblock move to rest status',
                            'action_user_id' => auth()->user()->id
                        ]);
                        $ansar->updateToRestState()->save();
                        break;
                    case "panel":
                        $myansar = DB::table('tbl_ansar_parsonal_info')->where('ansar_id', $ansar_id)->first();
                        $gender = $myansar->sex;
                        $designation = $myansar->designation_id;

                        if ($designation == 1) {
                            $max_go_panel_position = DB::table('tbl_panel_info')
                                            ->join('tbl_ansar_parsonal_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                                            ->where('tbl_ansar_parsonal_info.designation_id', $designation)
                                            ->where('tbl_ansar_parsonal_info.sex', $gender)
                                            ->max('go_panel_position') + 1;

                            $max_re_panel_position = DB::table('tbl_panel_info')
                                            ->join('tbl_ansar_parsonal_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                                            ->where('tbl_ansar_parsonal_info.designation_id', $designation)
                                            ->where('tbl_ansar_parsonal_info.sex', $gender)
                                            ->max('re_panel_position') + 1;
                        } else {
                            $max_go_panel_position = DB::table('tbl_panel_info')
                                            ->join('tbl_ansar_parsonal_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                                            ->where('tbl_ansar_parsonal_info.designation_id', $designation)
                                            ->max('go_panel_position') + 1;

                            $max_re_panel_position = DB::table('tbl_panel_info')
                                            ->join('tbl_ansar_parsonal_info', 'tbl_panel_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                                            ->where('tbl_ansar_parsonal_info.designation_id', $designation)
                                            ->max('re_panel_position') + 1;
                        }

                        PanelModel::create([
                            'ansar_id' => $ansar_id,
                            'come_from' => 'Block',
                            'panel_date' => Carbon::now(),
                            're_panel_date' => Carbon::now(),
                            'memorandum_id' => $memorandumId,
                            'ansar_merit_list' => 'N\A',
                            'action_user_id' => auth()->user()->id,
                            'go_panel_position' => $max_go_panel_position,
                            're_panel_position' => $max_re_panel_position,
                        ]);
                        $ansar->updateToPanelState()->save();

                        break;
                    case "not_verified":
                        $ansar->ansar->update(['verified' => 0]);
                        break;
                }
            } else {
                $futureData = [
                    'ansar_id' => $ansar_id,
                    'data' => serialize($ansar_unblock_details),
                    'action_date' => Carbon::now()->format("y-m-d H:i:s"),
                    'activation_date' => $modified_unblock_date,
                    'action_by' => Auth::user()->id,
                    'from_status' => 'Block'
                ];
                switch (strtolower($moveStatus)) {
                    case "free":
                        $futureData["to_status"] = "Free";
                        break;
                    case "rest":
                        $futureData["to_status"] = "Rest";
                        break;
                    case "panel":
                        $futureData["to_status"] = "Panel";
                        break;
                    case "not_verified":
                        $futureData["to_status"] = "Unverified";
                        break;
                    default:
                        $futureData["to_status"] = "Unverified";
                        break;
                }
                AnsarFutureState::create($futureData);
            }
            CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLOCKED', 'from_state' => 'BLOCKED', 'to_state' => 'UNVERIFIED', 'action_by' => auth()->user()->id]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        return Redirect::route('unblocklist_entry_view')->with('success_message', 'Ansar Removed from Blocklist Successfully');
    }

    public function blackListEntryView() {
        return view('HRM::Blackblock_view.blacklist_entry');
    }

    public function loadAnsarDetailforBlack(Request $request) {
        $valid = Validator::make($request->all(), [
                    'ansar_id' => 'required|regex:/^[0-9]+$/'
        ]);
        if ($valid->fails()) {
            return [];
        }
        try {
            $ansar_id = Input::get('ansar_id');

            $ansar_check = AnsarStatusInfo::where('ansar_id', $ansar_id)->first();
            $ansar_details = DB::table('tbl_ansar_parsonal_info')
                    ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                    ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                    ->where('tbl_ansar_parsonal_info.ansar_id', '=', $ansar_id)
                    ->select('tbl_ansar_parsonal_info.id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_units.unit_name_eng', 'tbl_designations.name_eng')
                    ->first();
            $r = array('ansar_details' => $ansar_details, 'status' => $ansar_check->getStatus()[0]);
            return Response::json($r);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function blackListEntry(Request $request) {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|unique:tbl_blacklist_info,ansar_id',
            'black_date' => 'required',
            'black_comment' => 'required',
        ];
        $this->validate($request, $rules);
        $ansar_status = $request->input('ansar_status');
        $ansar_id = $request->input('ansar_id');
        $black_date = $request->input('black_date');
        $modified_black_date = Carbon::parse($black_date)->format('Y-m-d');
        $black_comment = $request->input('black_comment');
        $from_id = $request->input('from_id');
        $mobile_no = DB::table('tbl_ansar_parsonal_info')->where('ansar_id', $ansar_id)->select('tbl_ansar_parsonal_info.mobile_no_self')->first();

        DB::beginTransaction();
//        return $ansar_status->getStatus();
        try {
            $ansar_status = AnsarStatusInfo::where('ansar_id', $request->ansar_id)->first();
            if (!$ansar_status)
                throw new \Exception("This is Ansar doesn`t exists");
            BlackListModel::create([
                'ansar_id' => $request->ansar_id,
                'black_list_from' => $ansar_status->getStatus()[0],
                'from_id' => 0,
                'black_listed_date' => $modified_black_date,
                'black_list_comment' => $black_comment,
                'action_user_id' => Auth::user()->id,
            ]);
            switch ($ansar_status->getStatus()[0]) {

                case AnsarStatusInfo::NOT_VERIFIED_STATUS:
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'ENTRY', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    break;

                case AnsarStatusInfo::FREE_STATUS:
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'FREE', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    break;

                case AnsarStatusInfo::PANEL_STATUS:
                    $panel_info = PanelModel::where('ansar_id', $ansar_id)->first();
                    $panel_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                    $panel_info->delete();
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'PANEL', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;
				case AnsarStatusInfo::RETIRE_STATUS:
                    $retire_info = AnsarRetireHistory::where('ansar_id', $ansar_id)->first();
                    $retire_info->delete();
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'Retire', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case AnsarStatusInfo::OFFER_STATUS:
                    $sms_offer_info = OfferSMS::where('ansar_id', $ansar_id)->first();
                    $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();

                    if (!is_null($sms_offer_info)) {

                        $sms_offer_info->saveLog('No Reply');
                        $sms_offer_info->delete();
                        $sms_offer_info->deleteCount();
                        $sms_offer_info->deleteOfferStatus();
                    } elseif (!is_null($sms_receive_info)) {
                        $sms_receive_info->saveLog();
                        $sms_receive_info->delete();
                        $sms_receive_info->deleteCount();
                        $sms_receive_info->deleteOfferStatus();
                    }

                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'OFFER', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case AnsarStatusInfo::EMBODIMENT_STATUS:
                    $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                    $embodiment_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                    $embodiment_info->delete();
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'EMBODIED', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;
					

                case AnsarStatusInfo::REST_STATUS:
                    $rest_info = RestInfoModel::where('ansar_id', $ansar_id)->first();
                    $rest_info->saveLog('Blacklist', $modified_black_date);
                    $rest_info->delete();

                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'REST', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case AnsarStatusInfo::FREEZE_STATUS:
                    $freeze_info = FreezingInfoModel::where('ansar_id', $ansar_id)->first();
                    $freeze_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                    $freeze_info->delete();
                    $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                    if (!$embodiment_info)
                        $embodiment_info = FreezedAnsarEmbodimentDetail::where('ansar_id', $ansar_id)->first();
                    $embodiment_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                    $embodiment_info->delete();
                    $ansar_status->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'FREEZE', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);

                    break;

                case AnsarStatusInfo::BLOCK_STATUS:
                    $blocklist_entry = BlockListModel::where('ansar_id', $ansar_id)->first();
                    $blocklist_entry->update([
                        'date_for_unblock' => $modified_black_date,
                        'comment_for_unblock' => $black_comment
                    ]);
                    if (!isset($ansar_status->getStatus()[1])) {
                        AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 0, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
                    } else {

                        switch ($ansar_status->getStatus()[1]) {
                            case AnsarStatusInfo::FREE_STATUS:
                                $ansar_status->update(['free_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                            case AnsarStatusInfo::PANEL_STATUS:
                                $panel_info = PanelModel::where('ansar_id', $ansar_id)->first();
                                $panel_info->saveLog('Blacklist', $modified_black_date);
                                $panel_info->delete();
                                $ansar_status->update(['pannel_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                            case AnsarStatusInfo::EMBODIMENT_STATUS:
                                $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                                $embodiment_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                                $embodiment_info->delete();
                                $ansar_status->update(['embodied_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                            case AnsarStatusInfo::REST_STATUS:
                                $rest_info = RestInfoModel::where('ansar_id', $ansar_id)->first();
                                $rest_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                                $rest_info->delete();
                                $ansar_status->update(['rest_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                            case AnsarStatusInfo::FREEZE_STATUS:
                                $freeze_info = FreezingInfoModel::where('ansar_id', $ansar_id)->first();
                                $freeze_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                                $freeze_info->delete();
                                $embodiment_info = EmbodimentModel::where('ansar_id', $ansar_id)->first();
                                $embodiment_info->saveLog('Blacklist', $modified_black_date, $black_comment);
                                $embodiment_info->delete();
                                $ansar_status->update(['freezing_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                            case AnsarStatusInfo::OFFER_STATUS:
                                $sms_offer_info = OfferSMS::where('ansar_id', $ansar_id)->first();
                                $sms_receive_info = SmsReceiveInfoModel::where('ansar_id', $ansar_id)->first();

                                if (!is_null($sms_offer_info)) {

                                    $sms_offer_info->saveLog('No Reply');
                                    $sms_offer_info->delete();
                                } elseif (!is_null($sms_receive_info)) {
                                    $sms_receive_info->saveLog();
                                    $sms_receive_info->delete();
                                }
                                $ansar_status->update(['offer_sms_status' => 0, 'block_list_status' => 0, 'black_list_status' => 1]);
                                break;
                        }
                    }
                    CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'BLACKED', 'from_state' => 'BLOCKED', 'to_state' => 'BLACKED', 'action_by' => auth()->user()->id]);
                    break;
                default :
                    throw new \Exception("This Ansar already in black list");
                    break;
            }
            //return $ansar_status->getStatus()[0];
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        return Redirect::route('blacklist_entry_view')->with('success_message', 'Ansar Blacklisted Successfully');
    }

    public function unblackListEntryView() {

        return view('HRM::Blackblock_view.unblacklist_entry');
    }

    public function loadAnsarDetailforUnblack() {
        $ansar_id = Input::get('ansar_id');

        $ansar_details = DB::table('tbl_blacklist_info')
                        ->join('tbl_ansar_parsonal_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_blacklist_info.ansar_id')
                        ->join('tbl_units', 'tbl_units.id', '=', 'tbl_ansar_parsonal_info.unit_id')
                        ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                        ->where('tbl_blacklist_info.ansar_id', '=', $ansar_id)
                        ->select('tbl_blacklist_info.black_list_from', 'tbl_blacklist_info.black_listed_date', 'tbl_blacklist_info.black_list_comment', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_ansar_parsonal_info.sex', 'tbl_units.unit_name_eng', 'tbl_designations.name_eng')->first();

        return Response::json($ansar_details);
    }

    public function unblackListEntry(Request $request) {
        $rules = [
            'ansar_id' => 'required|regex:/^[0-9]+$/|exists:tbl_blacklist_info,ansar_id',
            'unblack_date' => 'required'
        ];
        $this->validate($request, $rules);
        $ansar_id = $request->input('ansar_id');
        $unblack_date = $request->input('unblack_date');
        $modified_unblack_date = Carbon::parse($unblack_date)->format('Y-m-d');
        $unblack_comment = $request->input('unblack_comment');

        DB::beginTransaction();
        try {
            $blacklist_info = BlackListModel::where('ansar_id', $ansar_id)->first();
            $ansar_unblack_detail = [
                'old_blacklist_id' => $blacklist_info->id,
                'ansar_id' => $ansar_id,
                'black_list_from' => $blacklist_info->black_list_from,
                'from_id' => $blacklist_info->from_id,
                'black_listed_date' => $blacklist_info->black_listed_date,
                'black_list_comment' => $blacklist_info->black_list_comment,
                'unblacklist_date' => $modified_unblack_date,
                'unblacklist_comment' => $unblack_comment,
                'move_to' => "Free",
                'move_date' => $modified_unblack_date,
                'action_user_id' => Auth::user()->id,
            ];
            if (Carbon::parse($unblack_date)->lte(Carbon::now())) {

                BlackListInfoModel::create($ansar_unblack_detail);

                $blacklist_info->delete();
                AnsarStatusInfo::where('ansar_id', $ansar_id)->update(['free_status' => 1, 'offer_sms_status' => 0, 'offered_status' => 0, 'block_list_status' => 0, 'black_list_status' => 0, 'rest_status' => 0, 'embodied_status' => 0, 'pannel_status' => 0, 'freezing_status' => 0]);
            } else {
                AnsarFutureState::create([
                    'ansar_id' => $ansar_id,
                    'data' => serialize($ansar_unblack_detail),
                    'action_date' => Carbon::now()->format("y-m-d H:i:s"),
                    'activation_date' => $modified_unblack_date,
                    'action_by' => Auth::user()->id,
                    'from_status' => 'Black',
                    'to_status' => 'Free'
                ]);
            }


//            Event::fire(new ActionUserEvent(['ansar_id'=>$ansar_id,'action_type'=>'FREE','from_state'=>'BLACKED','to_state'=>'FREE','action_by'=>auth()->user()->id]));
            CustomQuery::addActionlog(['ansar_id' => $request->input('ansar_id'), 'action_type' => 'UNBLACKED', 'from_state' => 'BLACKED', 'to_state' => 'FREE', 'action_by' => auth()->user()->id]);


            DB::commit();
        } catch (\Exception $e) {
            return Redirect::back()->with('error_message', $e->getMessage());
        }
        return Redirect::back()->with('success_message', 'Ansar removed from Blacklist Successfully');
    }

    /**
     * Remove Double Status
     * Type: AnsarStatusInfo
     * @param null $ansar
     */
    private function removeOtherStatusExceptBlock($ansar = null) {
        if (!empty($ansar) && in_array(AnsarStatusInfo::BLOCK_STATUS, $ansar->getStatus())) {
            if (in_array(AnsarStatusInfo::PANEL_STATUS, $ansar->getStatus())) {
                //$ansar->panel->saveLog("Blocklist", Carbon::now()->format('Y-m-d'), '44.03.0000.048.50.007.18-577 Date:Oct-27-2019');
                //$ansar->panel->delete();
            } elseif (in_array(AnsarStatusInfo::EMBODIMENT_STATUS, $ansar->getStatus())) {
                $ansar->embodiment->saveLog('Blocklist', Carbon::now()->format('Y-m-d'), '44.03.0000.048.50.007.18-577 Date:Oct-27-2019', 8);
                $ansar->embodiment->delete();
            } elseif (in_array(AnsarStatusInfo::REST_STATUS, $ansar->getStatus())) {
                $ansar->rest->saveLog('Blocklist', Carbon::now()->format('Y-m-d'), '44.03.0000.048.50.007.18-577 Date:Oct-27-2019');
                $ansar->rest->delete();
            }
        }
    }

    public function process_retirement() {

        $ansars = RestInfoModel::whereHas('ansarInfo.status', function ($q) {
                    $q->where('block_list_status', 0);
                    $q->where('rest_status', 1);
                    $q->where('black_list_status', 0);
                })->with(['ansarInfo' => function($q) {
                        $q->select('ansar_id', 'data_of_birth', 'designation_id');
                        $q->with(['designation', 'status']);
                    }])->take(15000)->get();

        DB::connection('hrm')->beginTransaction();
        try {
            $now = \Carbon\Carbon::now();
            foreach ($ansars as $ansar) {

                $info = $ansar->ansarInfo;
                $dob = $info->data_of_birth;

                //echo $info->data_of_birth; exit;

                $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;



                if ($info->designation->code == "ANSAR" && (($age->y >= $ansarRe && ($age->m > 0 || $age->d > 0)) || $age->y > $ansarRe)) {

                    $data = (array) $ansar;
                    $data['come_from'] = 'After Retier';

                    $info->status->update([
                        'rest_status' => 0,
                        'retierment_status' => 1
                    ]);

                    $info->retireHistory()->create([
                        'retire_from' => 'rest',
                        'retire_date' => $now->format('Y-m-d'),
                        'data' => json_encode($data)
                    ]);



                    $ansar->saveLog('Parmanenet Retierment', null, 'over aged');
                    $ansar->delete();
                } else if (($info->designation->code == "PC" || $info->designation->code == "APC") && (($age->y >= $pcApcRe && ($age->m > 0 || $age->d > 0)) || $age->y > $pcApcRe)) {

                    $data = (array) $ansar;
                    $data['come_from'] = 'After Retier';

                    $info->status->update([
                        'rest_status' => 0,
                        'retierment_status' => 1
                    ]);

                    $info->retireHistory()->create([
                        'retire_from' => 'rest',
                        'retire_date' => $now->format('Y-m-d'),
                        'data' => json_encode($data)
                    ]);

                    $ansar->saveLog('Parmanenet Retierment', null, 'over aged');
                    $ansar->delete();
                }
            }
            DB::connection('hrm')->commit();
        } catch (\Exception $e) {
            ////Log::info("ansar_block_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }

    public function process_freeze_data() {

        $ansars = FreezingInfoModel::whereHas('ansar.status', function ($q) {
                    $q->where('block_list_status', 0);
                    $q->where('freezing_status', 1);
                    $q->where('black_list_status', 0);
                })->with(['ansar' => function($q) {
                        $q->select('ansar_id', 'data_of_birth', 'designation_id');
                        $q->with(['designation', 'status']);
                    }])->take(15000)->get();
                    
                    
       //echo '<pre>';  print_r($ansars);  exit;

        DB::connection('hrm')->beginTransaction();
        try {
            $now = \Carbon\Carbon::now();
            foreach ($ansars as $ansar) {

                $info = $ansar->ansar;
                $dob = $info->data_of_birth;

                //echo $info->data_of_birth; exit;

                $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;



                if ($info->designation->code == "ANSAR" && (($age->y >= $ansarRe && ($age->m > 0 || $age->d > 0)) || $age->y > $ansarRe)) {
                    
                    //echo $info->data_of_birth; exit;
                    $data = (array) $ansar;
                    $data['come_from'] = 'After Retier';

                    $info->status->update([
                        'freezing_status' => 0,
                        'retierment_status' => 1
                    ]);

                    $info->retireHistory()->create([
                        'retire_from' => 'freeze',
                        'retire_date' => $now->format('Y-m-d'),
                        'data' => json_encode($data)
                    ]);



                    $ansar->saveLog('Retierment', null, 'over aged');
                    $ansar->delete();
                } else if (($info->designation->code == "PC" || $info->designation->code == "APC") && (($age->y >= $pcApcRe && ($age->m > 0 || $age->d > 0)) || $age->y > $pcApcRe)) {
                    
                    //echo $info->data_of_birth; exit;
                    $data = (array) $ansar;
                    $data['come_from'] = 'After Retier';

                    $info->status->update([
                        'freezing_status' => 0,
                        'retierment_status' => 1
                    ]);

                    $info->retireHistory()->create([
                        'retire_from' => 'freeze',
                        'retire_date' => $now->format('Y-m-d'),
                        'data' => json_encode($data)
                    ]);

                    $ansar->saveLog('Retierment', null, 'over aged');
                    $ansar->delete();
                }
            }
            DB::connection('hrm')->commit();
        } catch (\Exception $e) {
            ////Log::info("ansar_block_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }
    
    public function process_offer_block_data() {

        $ansars = OfferBlockedAnsar::whereHas('personalinfo.status', function ($q) {
                    $q->where('block_list_status', 0);
                    $q->where('offer_block_status', 1);
                    $q->where('black_list_status', 0);
                })->with(['personalinfo' => function($q) {
                        $q->select('ansar_id', 'data_of_birth', 'designation_id');
                        $q->with(['designation', 'status']);
                    }])->take(15000)->get();
                    
                    
        //echo '<pre>';  print_r($ansars);  exit;

        DB::connection('hrm')->beginTransaction();
        try {
            $now = \Carbon\Carbon::now();
            foreach ($ansars as $ansar) {

                $info = $ansar->personalinfo;
                $dob = $info->data_of_birth;

                //echo $info->data_of_birth; exit;

                $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;



                if ($info->designation->code == "ANSAR" && (($age->y >= $ansarRe && ($age->m > 0 || $age->d > 0)) || $age->y > $ansarRe)) {
                    
                    //echo $info->data_of_birth; exit;
                    $data = (array) $ansar;
                    $data['come_from'] = 'After Retier';

                    $info->status->update([
                        'offer_block_status' => 0,
                        'retierment_status' => 1
                    ]);

                    $info->retireHistory()->create([
                        'retire_from' => 'offer_block',
                        'retire_date' => $now->format('Y-m-d'),
                        'data' => json_encode($data)
                    ]);



                   // $ansar->saveLog('Retierment', null, 'over aged');
                    $ansar->delete();
                } else if (($info->designation->code == "PC" || $info->designation->code == "APC") && (($age->y >= $pcApcRe && ($age->m > 0 || $age->d > 0)) || $age->y > $pcApcRe)) {
                    
                   // echo $info->data_of_birth; exit;
                    $data = (array) $ansar;
                    $data['come_from'] = 'After Retier';

                    $info->status->update([
                        'offer_block_status' => 0,
                        'retierment_status' => 1
                    ]);

                    $info->retireHistory()->create([
                        'retire_from' => 'offer_block',
                        'retire_date' => $now->format('Y-m-d'),
                        'data' => json_encode($data)
                    ]);

                    // $ansar->saveLog('Retierment', null, 'over aged');
                    $ansar->delete();
                }
            }
            DB::connection('hrm')->commit();
        } catch (\Exception $e) {
            ////Log::info("ansar_block_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }
    
    
    public function process_free_data() {

        $ansars = AnsarStatusInfo::whereHas('ansar.status', function ($q) {
                    $q->where('block_list_status', 0);
                    $q->where('free_status', 1);
                    $q->where('black_list_status', 0);
                })->with(['ansar' => function($q) {
                        $q->select('ansar_id', 'data_of_birth', 'designation_id');
                        $q->with(['designation', 'status']);
                    }])->take(15000)->get();
                    
                    
        //echo '<pre>';  print_r($ansars);  exit;

        DB::connection('hrm')->beginTransaction();
        try {
            $now = \Carbon\Carbon::now();
            foreach ($ansars as $ansar) {

                $info = $ansar->ansar;
                $dob = $info->data_of_birth;

                //echo $info->data_of_birth; exit;

                $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;



                if ($info->designation->code == "ANSAR" && (($age->y >= $ansarRe && ($age->m > 0 || $age->d > 0)) || $age->y > $ansarRe)) {
                    
                    //echo $info->data_of_birth; exit;
                    $data = (array) $ansar;
                    $data['come_from'] = 'After Retier';

                    $info->status->update([
                        'free_status' => 0,
                        'retierment_status' => 1
                    ]);

                    $info->retireHistory()->create([
                        'retire_from' => 'free',
                        'retire_date' => $now->format('Y-m-d'),
                        'data' => json_encode($data)
                    ]);


                   // $ansar->saveLog('Retierment', null, 'over aged');
                   // $ansar->delete();
                } else if (($info->designation->code == "PC" || $info->designation->code == "APC") && (($age->y >= $pcApcRe && ($age->m > 0 || $age->d > 0)) || $age->y > $pcApcRe)) {
                    
                   // echo $info->data_of_birth; exit;
                    $data = (array) $ansar;
                    $data['come_from'] = 'After Retier';

                    $info->status->update([
                        'free_status' => 0,
                        'retierment_status' => 1
                    ]);

                    $info->retireHistory()->create([
                        'retire_from' => 'free',
                        'retire_date' => $now->format('Y-m-d'),
                        'data' => json_encode($data)
                    ]);

                    // $ansar->saveLog('Retierment', null, 'over aged');
                    // $ansar->delete();
                }
            }
            DB::connection('hrm')->commit();
        } catch (\Exception $e) {
            ////Log::info("ansar_block_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }
    
    public function process_not_verified_data() {

        $ansars = AnsarStatusInfo::whereHas('ansar.status', function ($q) {
                    $q->where('block_list_status', 0);
                    $q->where('offer_block_status', 0);
                    $q->where('black_list_status', 0);
                    $q->where('free_status', 0);
                    $q->where('rest_status', 0);
                    $q->where('pannel_status', 0);
                    $q->where('offer_sms_status', 0);
                    $q->where('offered_status', 0);
                    $q->where('retierment_status', 0);
                    $q->where('embodied_status', 0);
                    $q->where('freezing_status', 0);
                    $q->where('early_retierment_status', 0);
                    $q->where('expired_status', 0);
                })->with(['ansar' => function($q) {
                        $q->select('ansar_id', 'data_of_birth', 'designation_id');
                        $q->with(['designation', 'status']);
                    }])->take(15000)->get();
                    
                    
       // echo '<pre>';  print_r($ansars);  exit;

        DB::connection('hrm')->beginTransaction();
        try {
            $now = \Carbon\Carbon::now();
            foreach ($ansars as $ansar) {

                $info = $ansar->ansar;
                $dob = $info->data_of_birth;

                //echo $info->data_of_birth; exit;

                $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;



                if ($info->designation->code == "ANSAR" && (($age->y >= $ansarRe && ($age->m > 0 || $age->d > 0)) || $age->y > $ansarRe)) {
                    
                    //echo $info->data_of_birth; exit;
                    $data = (array) $ansar;
                    $data['come_from'] = 'After Retier';

                    $info->status->update([
                        'retierment_status' => 1
                    ]);

                    $info->retireHistory()->create([
                        'retire_from' => 'not_verified',
                        'retire_date' => $now->format('Y-m-d'),
                        'data' => json_encode($data)
                    ]);


                   // $ansar->saveLog('Retierment', null, 'over aged');
                   // $ansar->delete();
                } else if (($info->designation->code == "PC" || $info->designation->code == "APC") && (($age->y >= $pcApcRe && ($age->m > 0 || $age->d > 0)) || $age->y > $pcApcRe)) {
                    
                   // echo $info->data_of_birth; exit;
                    $data = (array) $ansar;
                    $data['come_from'] = 'After Retier';

                    $info->status->update([
                        'retierment_status' => 1
                    ]);

                    $info->retireHistory()->create([
                        'retire_from' => 'not_verified',
                        'retire_date' => $now->format('Y-m-d'),
                        'data' => json_encode($data)
                    ]);

                    // $ansar->saveLog('Retierment', null, 'over aged');
                    // $ansar->delete();
                }
            }
            DB::connection('hrm')->commit();
        } catch (\Exception $e) {
            ////Log::info("ansar_block_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }

    public function test_global_position_job()
    {
        if (!DB::connection('hrm')->getDatabaseName()) {
            //Log::info("SERVER RECONNECTING....");
            DB::reconnect('hrm');
        }
        //Log::info("CONNECTION DATABASE : " . DB::connection('hrm')->getDatabaseName());
        DB::connection('hrm')->beginTransaction();

        try {
            $go_offer_count = +GlobalParameterFacades::getValue('ge_offer_count');
            $data = DB::table('tbl_ansar_parsonal_info')
                ->leftJoin('tbl_offer_status', 'tbl_offer_status.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->leftJoin('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->leftJoin('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.block_list_status', 0)
                ->where('tbl_ansar_status_info.black_list_status', 0)
                ->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^(/+88)?01[0-9]{9}$"')
                ->select('tbl_panel_info.ansar_id', 'panel_date', 'tbl_panel_info.come_from', 'tbl_panel_info.id',
                    'locked', 'sex', 'division_id', 'tbl_designations.code', 'tbl_panel_info.go_panel_position',
                    'tbl_sms_offer_info.district_id', 'tbl_sms_receive_info.offered_district',
                    DB::raw('REPLACE(REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(offer_type,\',\',LENGTH(offer_type)
                    -LENGTH(REPLACE(offer_type,\',\',\'\'))+1),\',\',-1),"DG","GB"),"CG","GB") as last_offer_region'), 'offer_type')
                ->get();

            $values = collect($data)->groupBy(function ($item) {
                return $item->code . "-" . $item->sex;
            })->toArray();


            foreach ($values as $value) {
                $value = collect($value)->sort(function ($a, $b) {
                    $id1 = +isset($a->id) ? $a->id : 0;
                    $id2 = +isset($b->id) ? $b->id : 0;
                    $d1 = isset($a->panel_date) ? Carbon::parse($a->panel_date) : Carbon::now();
                    $d2 = isset($b->panel_date) ? Carbon::parse($b->panel_date) : Carbon::now();
                    if ($d1->gt($d2)) {
                        return 1;
                    } else if ($d1->eq($d2) && $id1 > $id2) {
                        return 1;
                    } else {
                        return -1;
                    }
                })->values()->toArray();
                $i = 1;
                $query = "UPDATE tbl_panel_info SET go_panel_position = (CASE ansar_id ";

                foreach ($value as $p) {
                  // echo '<pre>'; print_r($p) ; exit;

                    $p = (array)$p;
                    $locked_region = "";

                    if ($p['locked'] && $p['last_offer_region']) {
                        $locked_region = " (" . $p['last_offer_region'] . ") ";
                    }

                    if ($p['offer_type'] == null || $p['offer_type'] == "") {
//                        offer type is null when first panel entry or empty string when last offer is ongoing.

                        if ($p['locked'] == 0) {
                            $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                            ////Log::info('UPDATE_GLOBAL_ANSAR: FIRST_PANEL_ENTRY ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current global position:' . $p['go_panel_position'] . ' future g position:' . $i);
                            $i++;
                        } elseif (in_array($p['district_id'], Config::get('app.offer'))) {
                            $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                            ////Log::info('UPDATE_GLOBAL_ANSAR:LAST_GB_OFFER ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current global position:' . $p['go_panel_position'] . ' future g position:' . $i);
                            $i++;
                        } elseif (in_array($p['offered_district'], Config::get('app.offer'))) {
                            $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                            ////Log::info('UPDATE_GLOBAL_ANSAR:LAST_GB_OFFER (ACCEPTED) ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current global position:' . $p['go_panel_position'] . ' future g position:' . $i);
                            $i++;
                        } else {
                            $query .= "WHEN " . $p['ansar_id'] . " THEN NULL ";
                            ////Log::info('UPDATE_GLOBAL_ANSAR:LAST_RE_OFFER ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current global position:' . $p['go_panel_position'] . ' future g position:null');
                        }

                    } elseif ((substr_count($p['offer_type'], 'GB') + substr_count($p['offer_type'], 'DG') + substr_count($p['offer_type'], 'CG')) < $go_offer_count) {
//                       global offer quota is not filled up yet. so, locked unlocked doesn't matter to update global position
                        $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                        ////Log::info('UPDATE_GLOBAL_ANSAR ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . $locked_region . ', current global position:' . $p['go_panel_position'] . ' future g position:' . $i);
                        $i++;
                    } else {
                        if ($p['last_offer_region'] == 'GB' && $p['locked'] == 1) {
                            $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                            ////Log::info('UPDATE_GLOBAL_ANSAR ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . $locked_region . ', current global position:' . $p['go_panel_position'] . ' future g position:' . $i);
                            $i++;
                        } else {
//                            all global offer filled up. so, set position null
                            $query .= "WHEN " . $p['ansar_id'] . " THEN NULL ";
                            ////Log::info('UPDATE_GLOBAL_ANSAR ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current global position:' . $p['go_panel_position'] . ' future g position:null');
                        }
                    }
                    //echo $query; exit;

                }
                $query .= "ELSE go_panel_position END) WHERE ansar_id IN (" . implode(",", array_column($value, 'ansar_id')) . ")";
                DB::statement($query);

            }
            DB::connection('hrm')->commit();
        } catch (\Exception $e) {
            //Log::info("global panel rearr:" . $e->getMessage());
            DB::connection('hrm')->rollback();
        }
        //$this->delete();
    }

    public function test_regional_position_job()
    {
        if (!DB::connection('hrm')->getDatabaseName()) {
            //Log::info("SERVER RECONNECTING....");
            DB::reconnect('hrm');
        }
        //Log::info("CONNECTION DATABASE : " . DB::connection('hrm')->getDatabaseName());
        DB::connection('hrm')->beginTransaction();
        try {
            $re_offer_count = +GlobalParameterFacades::getValue('re_offer_count');
            $data = DB::table('tbl_ansar_parsonal_info')
                ->leftJoin('tbl_offer_status', 'tbl_offer_status.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->leftJoin('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->leftJoin('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.block_list_status', 0)
                ->where('tbl_ansar_status_info.black_list_status', 0)
                ->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^(/+88)?01[0-9]{9}$"')
                ->select('tbl_panel_info.ansar_id', 'tbl_panel_info.come_from', 're_panel_date', 'tbl_panel_info.id', 'tbl_panel_info.re_panel_position',
                    'locked', 'sex', 'division_id', 'tbl_designations.code', 'tbl_sms_offer_info.district_id', 'tbl_sms_receive_info.offered_district',
                    DB::raw('SUBSTRING_INDEX(SUBSTRING_INDEX(offer_type,\',\',LENGTH(offer_type)-LENGTH(REPLACE(offer_type,\',\',\'\'))+1),\',\',-1) as last_offer_region'), 'offer_type')
                ->get();

            $ansars = collect($data)->groupBy('division_id', true)->toArray();
            foreach ($ansars as $k => $ansar) {
                $of = OfferZone::where('range_id', $k)
                    ->select(DB::raw('GROUP_CONCAT(DISTINCT(offer_zone_range_id) SEPARATOR "-" ) as offer_zone_range'))
                    ->groupBy('range_id')->first();
                if ($of) {
                    $r = explode("-", $of->offer_zone_range);
                    $values = [];
                    foreach ($r as $rr) {
                        $values = array_merge($values, array_values($ansars[$rr]));
                    }
                    $values = collect(array_merge($values, array_values($ansar)))->groupBy(function ($item) {
                        return $item->code . "-" . $item->sex;
                    })->toArray();
                } else {
                    $values = collect(array_values($ansar))->groupBy(function ($item) {
                        return $item->code . "-" . $item->sex;
                    })->toArray();
                }

                foreach ($values as $value) {
                    $value = collect($value)->sort(function ($a, $b) {
                        $id1 = +isset($a->id) ? $a->id : 0;
                        $id2 = +isset($b->id) ? $b->id : 0;
                        $d1 = isset($a->re_panel_date) ? Carbon::parse($a->re_panel_date) : Carbon::now();
                        $d2 = isset($b->re_panel_date) ? Carbon::parse($b->re_panel_date) : Carbon::now();
                        if ($d1->gt($d2)) {
                            return 1;
                        } else if ($d1->eq($d2) && $id1 > $id2) {
                            return 1;
                        } else {
                            return -1;
                        }
                    })->values()->toArray();
                    $i = 1;
                    $query = "UPDATE tbl_panel_info SET re_panel_position = (CASE ansar_id ";
                    foreach ($value as $p) {
                        $p = (array)$p;
                        $locked_region = "";
                        if ($p['locked'] && $p['last_offer_region']) {
                            $locked_region = " (" . $p['last_offer_region'] . ") ";
                        }
                        if ($p['offer_type'] == null || $p['offer_type'] == "") {
//                        offer type is null when first panel entry or empty string when last offer is ongoing.
                            if ($p['locked'] == 0) {
                                $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                                //   Log::info('UPDATE_REGIONAL_ANSAR: FIRST_PANEL_ENTRY ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current regional position:' . $p['re_panel_position'] . ' future re position:' . $i);
                                $i++;
                            } elseif (in_array($p['district_id'], Config::get('app.offer')) || in_array($p['offered_district'], Config::get('app.offer'))) {
                                $query .= "WHEN " . $p['ansar_id'] . " THEN NULL ";
                                //   Log::info('UPDATE_REGIONAL_ANSAR:LAST_GB_OFFER ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current regional position:' . $p['re_panel_position'] . ' future re position:null');
                            } else {
                                $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                                //   Log::info('UPDATE_REGIONAL_ANSAR:LAST_RE_OFFER ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current regional position:' . $p['re_panel_position'] . ' future re position:' . $i);
                                $i++;
                            }

                        } elseif (substr_count($p['offer_type'], 'RE') < $re_offer_count) {
//                       global offer quota is not filled up yet. so, locked unlocked doesn't matter to update regional position
                            $dist=$p['district_id'];
                            $dist_r=$p['offered_district'];
                            if($p['last_offer_region'] == 'RE'){
                                if(empty($dist) && empty($dist_r))
                                {
                                    $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                                    $i++;
                                }

                                else{
                                    $query .= "WHEN " . $p['ansar_id'] . " THEN 0 ";
                                }
                            }else
                            {
                                $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                                $i++;
                            }

                            //Log::info('UPDATE_REGIONAL_ANSAR ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . $locked_region . ', current regional position:' . $p['re_panel_position'] . ' future re position:' . $i);

                        } else {
                            if ($p['last_offer_region'] == 'RE' && $p['locked'] == 1) {
                                $dist=$p['district_id'];
                                $dist_r=$p['offered_district'];
                                if(empty($dist) && empty($dist_r))
                                {
                                    $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                                    $i++;
                                }
                                else{
                                    $query .= "WHEN " . $p['ansar_id'] . " THEN 0 ";
                                }
                            } else {
//                            all regional offer filled up. so, set position null
                                if(substr_count($p['offer_type'], 'RE')< $re_offer_count  || $p['offer_type']=='' || $p['offer_type']==null)
                                {
                                    $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                                    $i++;
                                }
                                else
                                {
                                    $query .= "WHEN " . $p['ansar_id'] . " THEN NULL ";
                                }
                                //Log::info('UPDATE_REGIONAL_ANSAR ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current regional position:' . $p['re_panel_position'] . ' future re position:null');
                            }
                        }
                    }

                    $query .= "ELSE re_panel_position END) WHERE ansar_id IN (" . implode(",", array_column($value, 'ansar_id')) . ")";
                    DB::statement($query);

                }

            }
            DB::connection('hrm')->commit();
        } catch (\Exception $e) {
            //Log::info("ansar_block_for_age:" . $e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }

    public function test_block_for_age_matter()
    {
        /*
        $ansars = AnsarRetireHistory::where('retire_from', 'offer_block')->get();

        DB::connection('hrm')->beginTransaction();
        try {
            $now = \Carbon\Carbon::now();
            foreach ($ansars as $ansar) {

                $info = $ansar->ansar;
                $dob = $info->data_of_birth;

                $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;

                //echo("called : Ansar Block For Age-".$ansar->ansar_id."Age:".$age->y."year ".$age->m."month ".$age->d." days");
                if ($info->designation->code == "ANSAR" && $age->y < $ansarRe) {
                    //echo $ansar->ansar_id.'<br>';
                   if ($ansar->retire_from == 'offer_block') {

                        $info->status->update([
                            'pannel_status' => 1,
                            'retierment_status' => 0
                        ]);
                    }
                    $ansar->delete();
                } else if (($info->designation->code == "PC" || $info->designation->code == "APC") && $age->y < $pcApcRe) {


                    $info->status->update([
                        'offer_block_status' => 1,
                        'retierment_status' => 0
                    ]);
                    $ansar->delete();


                }
            }

            DB::connection('hrm')->commit();
        } catch (\Exception $e) {
            //Log::info("ansar_unblock_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }

        */

        $re_offer_count = +GlobalParameterFacades::getValue('re_offer_count');
        $data = DB::table('tbl_ansar_parsonal_info')
            ->leftJoin('tbl_offer_status', 'tbl_offer_status.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
            ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
            ->leftJoin('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->leftJoin('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
            ->where('tbl_ansar_status_info.block_list_status', 0)
            ->where('tbl_ansar_status_info.black_list_status', 0)
            ->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^(/+88)?01[0-9]{9}$"')
            ->select('tbl_panel_info.ansar_id', 'tbl_panel_info.come_from', 're_panel_date', 'tbl_panel_info.id', 'tbl_panel_info.re_panel_position',
                'locked', 'sex', 'division_id', 'tbl_designations.code', 'tbl_sms_offer_info.district_id', 'tbl_sms_receive_info.offered_district',
                DB::raw('SUBSTRING_INDEX(SUBSTRING_INDEX(offer_type,\',\',LENGTH(offer_type)-LENGTH(REPLACE(offer_type,\',\',\'\'))+1),\',\',-1) as last_offer_region'), 'offer_type')
            ->get();

        $ansars = collect($data)->groupBy('division_id', true)->toArray();
        //echo '<pre>'; print_r($ansars);
        foreach ($ansars as $k => $ansar) {
            // echo $k.'<br>';
            $of = OfferZone::where('range_id', $k)
                ->select(DB::raw('GROUP_CONCAT(DISTINCT(offer_zone_range_id) SEPARATOR "-" ) as offer_zone_range'))
                ->groupBy('range_id')->first();
            //echo '<pre>'; print_r($of);
            if($of){
                echo $k.'<br>';
                echo '<pre>';
                echo $of;
            }
        }

        exit;

    }

    public function test_block_for_age_rest()
    {
        $ansars = AnsarRetireHistory::where('retire_from', 'rest')->get();

        DB::connection('hrm')->beginTransaction();
        try {
            $now = \Carbon\Carbon::now();
            foreach ($ansars as $ansar) {

                $info = $ansar->ansar;
                if($info->status->retierment_status == 1 && $info->status->block_list_status == 0 && $info->status->black_list_status == 0 && $info->status->pannel_status == 0 && $info->status->embodied_status == 0 && $info->status->freezing_status == 0 && $info->status->rest_status == 0){
                    $dob = $info->data_of_birth;

                    $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                    $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                    $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;


                    if (($info->designation->code == "ANSAR" && $age->y < $ansarRe) || (($info->designation->code == "PC" || $info->designation->code == "APC") && $age->y < $pcApcRe)) {

                        if ($ansar->retire_from == 'rest') {

                            $rest_log = RestInfoLogModel::where('ansar_id', $info->ansar_id)->orderBy('rest_date', 'desc')->first();

                            if($rest_log){

                                $rest_diff = \Carbon\Carbon::parse($rest_log->rest_date)->diffInMonths($now, true);

                                if($rest_diff > 6){

                                    $info->panel()->create([                                       'ansar_merit_list' => 1,
                                        'panel_date' => $now->toDateString(),
                                        're_panel_date' => $now->toDateString(),
                                        'memorandum_id' => 'back from retirement',
                                        'come_from' => 'After Retier'
                                    ]);

                                    $info->status->update([
                                        'pannel_status' => 1,
                                        'retierment_status' => 0
                                    ]);
                                    $ansar->delete();

                                }else{

                                    $info->rest()->create([
                                        'old_embodiment_id' => $rest_log->old_embodiment_id,
                                        'memorandum_id' => $rest_log->old_memorandum_id,
                                        'rest_date' => $rest_log->rest_date,
                                        'active_date' => \Carbon\Carbon::parse($rest_log->rest_date)->addMonths(6)->format('Y-m-d'),
                                        'disembodiment_reason_id' => $rest_log->disembodiment_reason_id,
                                        'total_service_days' => $rest_log->total_service_days,
                                        'rest_form' => $rest_log->rest_type,
                                        'comment' => $rest_log->comment,
                                        'action_user_id' => 1,
                                    ]);
                                    $rest_log->delete();

                                    $info->status->update([
                                        'rest_status' => 1,
                                        'retierment_status' => 0
                                    ]);
                                    $ansar->delete();
                                }
                            }
                        }
                    }
                }
            }
        DB::connection('hrm')->commit();

        } catch (\Exception $e) {
            //Log::info("ansar_unblock_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }

    public function test_block_for_age_offer_block()
    {
        $ansars = AnsarRetireHistory::where('retire_from', 'offer_block')->get();

        DB::connection('hrm')->beginTransaction();
        try {
            $now = \Carbon\Carbon::now();
            foreach ($ansars as $ansar) {

                $info = $ansar->ansar;
                if($info->status->retierment_status == 1 && $info->status->block_list_status == 0 && $info->status->black_list_status == 0 && $info->status->pannel_status == 0 && $info->status->embodied_status == 0 && $info->status->freezing_status == 0 && $info->status->rest_status == 0){
                    $dob = $info->data_of_birth;

                    $age = \Carbon\Carbon::parse($dob)->diff($now, true);
                    $ansarRe = GlobalParameterFacades::getValue('retirement_age_ansar') - 3;
                    $pcApcRe = GlobalParameterFacades::getValue('retirement_age_pc_apc') - 3;


                    if (($info->designation->code == "ANSAR" && $age->y < $ansarRe) || (($info->designation->code == "PC" || $info->designation->code == "APC") && $age->y < $pcApcRe)) {

                        if ($ansar->retire_from == 'offer_block') {

                            $rest_log = RestInfoLogModel::where('ansar_id', $info->ansar_id)->orderBy('rest_date', 'desc')->first();

                            if($rest_log){

                                $rest_diff = \Carbon\Carbon::parse($rest_log->rest_date)->diffInMonths($now, true);

                                if($rest_diff > 6){

                                    $info->panel()->create([                                       'ansar_merit_list' => 1,
                                        'panel_date' => $now->toDateString(),
                                        're_panel_date' => $now->toDateString(),
                                        'memorandum_id' => 'back from retirement',
                                        'come_from' => 'After Retier'
                                    ]);

                                    $info->status->update([
                                        'pannel_status' => 1,
                                        'retierment_status' => 0
                                    ]);
                                    $ansar->delete();

                                }else{

                                    $info->rest()->create([
                                        'old_embodiment_id' => $rest_log->old_embodiment_id,
                                        'memorandum_id' => $rest_log->old_memorandum_id,
                                        'rest_date' => $rest_log->rest_date,
                                        'active_date' => \Carbon\Carbon::parse($rest_log->rest_date)->addMonths(6)->format('Y-m-d'),
                                        'disembodiment_reason_id' => $rest_log->disembodiment_reason_id,
                                        'total_service_days' => $rest_log->total_service_days,
                                        'rest_form' => $rest_log->rest_type,
                                        'comment' => $rest_log->comment,
                                        'action_user_id' => 1,
                                    ]);
                                    $rest_log->delete();

                                    $info->status->update([
                                        'rest_status' => 1,
                                        'retierment_status' => 0
                                    ]);
                                    $ansar->delete();
                                }
                            }
                        }
                    }
                }
            }
            DB::connection('hrm')->commit();

        } catch (\Exception $e) {
            //Log::info("ansar_unblock_for_age:".$e->getMessage());
            DB::connection('hrm')->rollback();
        }
    }


    public function unitDailyEmbodimentLog()
    {
        $today = Carbon::now()->format('Y-m-d');
        $exisDayData = EmbodimentUnitDailyLog::where('date', $today)->exists();

        if($exisDayData){
            return;
        }
      $results = DB::select(DB::raw("SELECT u.id, u.unit_name_eng, COUNT(*) AS total, SUM(p.`designation_id` =' 1') AS 'ansar', SUM(p.`designation_id` =' 2') AS 'apc', SUM(p.`designation_id` =' 3') AS 'pc', SUM(CASE WHEN p.designation_id = '1' AND p.sex = 'male' THEN 1 ELSE 0 END) ansarMale, SUM(CASE WHEN p.designation_id = '1' AND p.sex = 'female' THEN 1 ELSE 0 END) ansarFemale FROM `tbl_embodiment` e JOIN `tbl_ansar_status_info` s ON s.ansar_id = e.ansar_id JOIN `tbl_ansar_parsonal_info` p ON p.ansar_id = e.ansar_id JOIN `tbl_kpi_info` k ON k.id = e.kpi_id JOIN tbl_units u ON u.id = k.unit_id WHERE s.embodied_status = 1 AND e.emboded_status = 'emboded' GROUP BY k.unit_id"));
      $insert = [];

       foreach ($results as $row) {
           $draw = [
               'unit_id' => $row->id,
               'total' => $row->total,
               'ansar' => $row->ansar,
               'apc' => $row->apc,
               'pc' => $row->pc,
               'ansarMale' => $row->ansarMale,
               'ansarFemale' => $row->ansarFemale,
               'apcMale' => $row->apc,
               'apcFemale' => 0,
               'pcMale' => $row->pc,
               'pcFemale' => 0,
               'date' => Carbon::now()->format('Y-m-d')
           ];
           $insert[] = $draw;
       }

       EmbodimentUnitDailyLog::insert($insert);
    }

}
