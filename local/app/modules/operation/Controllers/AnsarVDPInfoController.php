<?php

namespace App\modules\operation\Controllers;

use App\Helper\EmailHelper;
use App\Helper\Facades\LanguageConverterFacades;
use App\Http\Controllers\Controller;
use App\modules\operation\Models\MemberIdCard;
use App\modules\operation\Models\VDPAnsarInfo;
use App\modules\operation\Models\VdpDesignation;
use App\modules\operation\Repositories\VDPInfo\OperationVDPInfoRepository;
use App\modules\operation\Requests\OperationVDPInfoRequest;
use App\modules\HRM\Models\AllEducationName;
use App\modules\HRM\Models\Blood;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Unions;

use Barryvdh\Snappy\Facades\SnappyImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class AnsarVDPInfoController extends Controller
{
    use EmailHelper;
    private $infoRepository;

    /**
     * AnsarVDPInfoController constructor.
     * @param OperationVDPInfoRepository $infoRepository
     */
    public function __construct(OperationVDPInfoRepository $infoRepository)
    {
        $this->infoRepository = $infoRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $limit = $request->limit ? $request->limit : 30;
            if (auth()->user()->usertype->type_name == "Dataentry") {
                $vdp_infos = $this->infoRepository->getInfos($request->only(['range', 'unit', 'thana']), $limit, $request->action_user_id);
            } else $vdp_infos = $this->infoRepository->getInfos($request->only(['range', 'unit', 'thana','vdpdesignation']), $limit);
            return view('operation::ansar_vdp_info.data', compact('vdp_infos'));
        }
        return view('operation::ansar_vdp_info.index');
    }

    public function export(Request $request)
    {

        $limit = $request->limit ? $request->limit : 30;
        if (auth()->user()->usertype->type_name == "Dataentry") {
            $vdp_infos = $this->infoRepository->getInfos($request->only(['range', 'unit', 'thana']), $limit, $request->action_user_id);
        } else $vdp_infos = $this->infoRepository->getInfos($request->only(['range', 'unit', 'thana']), $limit);
        return Excel::create("vdp_info", function ($excel) use ($vdp_infos) {
            $excel->sheet("sheet1", function ($sheet) use ($vdp_infos) {
                $sheet->setAutoSize(false);
                $sheet->setWidth('A', 5);
                $sheet->loadView('operation::ansar_vdp_info.export', compact('vdp_infos'));
            });
        })->download('xls');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('operation::ansar_vdp_info.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OperationVDPInfoRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(OperationVDPInfoRequest $request)
    {
//        return $request->file('profile_pic');
        $response = $this->infoRepository->create($request);
        if (!$response['status']) {
            return response()->json($response['data'], 500);
        }
        Session::flash('success_message', 'New entry added successfully');
        return response()->json($response['data']);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if (auth()->user()->usertype->type_name == "Dataentry") {
            $info = $this->infoRepository->getInfo($id, $request->action_user_id);
        } else $info = $this->infoRepository->getInfo($id);
        if ($request->ajax()) {
            return response()->json($info);
        }
        if (!$info) return abort(404);

        return view('operation::ansar_vdp_info.view', compact('info'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if (auth()->user()->usertype->type_name == "Dataentry") {
            $info = $this->infoRepository->getInfoForEdit($id, $request->action_user_id);
        } else $info = $this->infoRepository->getInfoForEdit($id);
        if ($request->ajax()) {
//            return $id;
            return response()->json($info);
        }
        if (!$info) return abort(404);
        return view('operation::ansar_vdp_info.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(OperationVDPInfoRequest $request, $id)
    {

        $response = $this->infoRepository->update($request, $id);
        if (!$response['status']) {
            return response()->json($response['data'], 500);
        }
        Session::flash('success_message', 'data updated successfully');
        return response()->json($response['data']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function verifyVDP($id)
    {
        $response = $this->infoRepository->verifyVDP($id);
        if (!$response['status']) {
            return redirect()->route('operation.info.index')->with('error_message', $response['data']['message']);
        }
        return redirect()->route('operation.info.index')->with('success_message', $response['data']['message']);
    }

    public function approveVDP($id)
    {
        $response = $this->infoRepository->approveVDP($id);
        if (!$response['status']) {
            return redirect()->route('operation.info.index')->with('error_message', $response['data']['message']);
        }
        return redirect()->route('operation.info.index')->with('success_message', $response['data']['message']);
    }

    public function verifyAndApproveVDP($id)
    {
        $response = $this->infoRepository->verifyAndApproveVDP($id);
        if (!$response['status']) {
            return redirect()->route('operation.info.index')->with('error_message', $response['data']['message']);
        }
        return redirect()->route('operation.info.index')->with('success_message', $response['data']['message']);
    }

    public function loadImage($id)
    {
        $info = VDPAnsarInfo::find($id);
        if ($info) {
            $image = storage_path('operation/profile_pic') . '/' . $info->geo_id.'.jpeg';
            if (!File::exists($image) || File::isDirectory($image)) $image = public_path('dist/img/nimage.png');
            //return $image;

        } else {
            $image = public_path('dist/img/nimage.png');
        }
        return Image::make($image)->response();
    }

    public function loadSignImage($id)
    {
        $info = VDPAnsarInfo::find($id);
        if ($info) {
            $image = storage_path('operation/sign_pic') . '/' . $info->geo_id.'.jpeg';
            if (!File::exists($image) || File::isDirectory($image)) $image = public_path('dist/img/nimage.png');
            //return $image;

        } else {
            $image = public_path('dist/img/nimage.png');
        }
        return Image::make($image)->response();
    }

    public function import()
    {
        return view('operation::ansar_vdp_info.import');
    }

    public function processImportedFile(Request $request)
    {
        $rules = [
              "import_file" => 'required',
        ];
		
        $this->validate($request, $rules);
        if ($request->hasFile('import_file')) {

            $fields = [
                "division", "unit", "thana", "union", "designation_text", "ansar_name_bng", "father_name_bng", "mobile_no_self", "blood_group","date_of_birth","own_district","joining_date", "expire_date", "div_geo","unit_geo","thana_geo","serial"];

            $keys = ["ansar_name_bng", "designation", "father_name_bng"];
			
            $sheets = Excel::load($request->file('import_file'), function () {

            })->get();

            $all_data = [];
            $error_headers = [];
            foreach ($sheets as $sheet) {
                $rows = collect($sheet)->toArray();
                $error_headers = [$rows[0]];
                unset($rows[0]);
                /*unset($rows[1]);
                unset($rows[2]);
                unset($rows[3]);
                unset($rows[4]); */

                foreach ($rows as $row) {
                   // if (count($row) > 32) $row = array_slice($row, 0, 32);
                    if (count($row) == count($fields)) array_push($all_data, array_combine($fields, array_slice($row, 0, count($fields))));
                    //else if (count($row) == count($fields_extended)) array_push($all_data, array_combine($fields_extended, array_slice($row, 0, count($fields_extended))));
                    // array_push($all_data, [count($row),count($fields_extended),count($fields)]);
                }
            }

            $insertData = [];
            foreach ($all_data as $data) {
                $r = [];
                
                foreach ($data as $key => $value) {
                    if (in_array($key, $keys)) {
                        $r[$key] = $value;
                    } else if ($key == 'blood_group') {
                        $m = null;
                        preg_match_all('/[^\(\)VE]/', $value, $m);
                        if (count($m) > 0 && is_array($m[0])) {
                            $bg = implode('', $m[0]);
                            $b = Blood::where('blood_group_name_eng', $bg)->orWhere('blood_group_name_bng', $bg)->first();
                            $r['blood_group_id'] = $b ? $b->id : 0;
                        } else {
                            $r['blood_group_id'] = 0;
                        }
                    } else if ($key == 'designation_text') {
                        DB::enableQueryLog();
                        $vdp_des = VdpDesignation::where(DB::raw("INSTR(\"$value\",designation_name_bng)"), '>', 0)
                            ->first();
                        if ($vdp_des) {
                            Log::info("$value found");
                            $r["designation"] = $vdp_des->id;
                        } else{

                        }
                    }else if ($key == 'date_of_birth') {
                        $r['date_of_birth'] = $this->parseDate($value);
                    } else if ($key == 'division') {
                        DB::enableQueryLog();
                        $div = Division::where(DB::raw("INSTR(\"$value\",division_name_bng)"), '>', 0)
                            ->first();
                        if ($div) {
                            Log::info("$value found");
                            $r["division_id"] = $div->id;
                        } else{

                        }
                    }
                    else if ($key == 'own_district') {
                        DB::enableQueryLog();
                        $own_dis = Division::where(DB::raw("INSTR(\"$value\",division_name_bng)"), '>', 0)
                            ->first();
                        if ($own_dis) {
                            Log::info("$value found");
                            $r["own_district_id"] = $own_dis->id;
                        } else{

                        }
                    }else if ($key == 'unit') {
                        DB::enableQueryLog();
                        $dis = District::where('division_id', $r["division_id"])
						    ->where(DB::raw("INSTR(\"$value\",unit_name_bng)"), '>', 0)
                            ->first();
                        if ($dis) {
                            Log::info("$value found");
                            $r["unit_id"] = $dis->id;
                        } else{

                        }
                    }else if ($key == 'thana') {
                        DB::enableQueryLog();
                        $thana = Thana::where('division_id', $r["division_id"])
                            ->where('unit_id', $r["unit_id"])
						    ->where(DB::raw("INSTR(\"$value\",thana_name_bng)"), '>', 0)
                            ->first();
                        if ($thana) {
                            Log::info("$value found");
                            $r["thana_id"] = $thana->id;
                        } else{

                        }
                    }
					else if ($key == 'mobile_no_self') {
                        $split = str_split($value);
                        if (intval($split[0]) > 0) {
                            $mobile_no = "0" . $value;
                        } else {
                            $mobile_no = $value;
                        }
                        $r['mobile_no_self'] = $mobile_no;
                    } else if ($key == 'union_word_id') {
                        $uwi = intval(LanguageConverterFacades::bngToEng($value));
                        $r["union_word_id"] = is_nan($uwi)?0:$uwi;
                    } else if ($key == 'union') {
                        DB::enableQueryLog();
                        $uni = Unions::where('division_id', $r["division_id"])
                            ->where('unit_id', $r["unit_id"])
                            ->where('thana_id', $r["thana_id"])
                            ->where(DB::raw("INSTR(\"$value\",union_name_bng)"), '>', 0)
                            ->first();
                        if ($uni) {
                            Log::info("$value found");
                            $r["union_id"] = $uni->id;
                        } else{
                            $uni = Unions::where('division_id', $r["division_id"])
                                ->where('unit_id', $r["unit_id"])
                                ->where('thana_id', $r["thana_id"])
                                ->where("union_name_bng",'=','নাই')
                                ->first();
                            $r["union_id"] = $uni->id;
                        }
                    }
                }
                $insertData[] = $r;

            }
            $res = [
                "success" => 0,
                "fail" => 0
            ];

            $error_data = [];
            $index = 0;
            foreach ($insertData as $i) {

                $valid = Validator::make($i, [
                    'ansar_name_bng' => 'required',
                    'father_name_bng' => 'required',
                    'designation' => 'required',
                    'date_of_birth' => 'required',
                    'mobile_no_self' => 'required|unique:operation.tbl_vdp_ansar_info',
                    'blood_group_id' => 'required',
                    'division_id' => 'required|numeric|min:1',
                    'thana_id' => 'required|numeric|min:1',
                    'union_word_text' => 'required',
                    //'geo_id' => 'required|unique:operation.tbl_vdp_ansar_info',

                ]);
                if ($valid->fails()) {
                    if (count($all_data[$index]) == count($fields)) array_push($error_data, ["dd" => array_merge(array_combine($fields, array_values($all_data[$index])), ["errors" => $this->validationErrorsToString($valid->messages())]), "err" => array_keys(collect($valid->messages())->toArray())]);
                    $res["fail"]++;
                } else {
                    $response = $this->infoRepository->create($i, auth()->user()->id);
                    if ($response['status']) $res["success"]++;
                    else {
                        $res["fail"]++;
                        if (count($all_data[$index]) == count($fields)) array_push($error_data, ["dd" => array_merge(array_combine($fields, array_values($all_data[$index])), ["errors" => $response['data']['message']]), "err" => []]);
                       // else if (count($all_data[$index]) == count($fields_extended)) array_push($error_data, ["dd" => array_merge(array_combine($fields_extended, array_values($all_data[$index])), ["errors" => $response['data']['message']]), "err" => []]);
//                        array_push($error_data, array_merge($all_data[$index],["errors"=>$response['data']['message']]));
                    }
                }
                $index++;

            }
            if (count($error_data) > 0) {
                for ($i = 0; $i < count($error_headers); $i++) {
                    array_push($error_headers[$i], "errors");
                }
                $file_name = 'error_date_' . time();
                Excel::create($file_name, function ($excel) use ($error_data, $error_headers) {

                    $excel->sheet('sheet1', function ($sheet) use ($error_data, $error_headers) {
                        $sheet->setAutoSize(false);
                        $sheet->setWidth('A', 5);
                        $sheet->loadView('operation::ansar_vdp_info.import_error', ['error_datas' => $error_data, 'headers' => $error_headers]);
                    });
                })->store('xls', storage_path());
              //  $this->sendEmailRaw("error data", "rintu@shurjomukhi.com.bd", "ERROR", storage_path($file_name . ".xls"));
            }

            return response()->json(['data' => $res, 'error' => isset($file_name) ? $file_name : false]);
        }
        return response()->json(['status' => false, 'msg' => 'Please upload excel u want to import']);
    }

    public function importImage()
    {
        return view('operation::ansar_vdp_info.bulk_image_import');
    }

    public function processImportedImageFile(Request $request)
    {

        $res = [
            "success" => 0,
            "fail" => 0
        ];

        if ($request->hasFile('images')) {
            $images = $request->file('images');
            $path = storage_path('operation/profile_pic');

            foreach ($images as $image) {
                $filename = $image->getClientOriginalName();
               // $path = $image->storeAs($path, $filename);

                if(Image::make($image)->save($path . '/' . $filename)){
                    $res["success"]++;


                }else{
                    $res["fail"]++;

                }                ;

            }

            return response()->json(['data' => $res, 'error' => isset($file_name) ? $file_name : false]);


        }
        return response()->json(['status' => false, 'msg' => 'Please select at least one image']);


    }

    private function parseDate($date)
    {
        $value = LanguageConverterFacades::bngToEng($date);
        $formats = [
            "d-m-y",
            "d/m/y",
            "d.m.y",
            "d-m-Y",
            "d/m/Y",
            "d.m.Y",
            "j-m-y",
            "j/m/y",
            "j.m.y",
            "j-m-Y",
            "j/m/Y",
            "j.m.Y",
            "d-n-y",
            "d/n/y",
            "d.n.y",
            "d-n-Y",
            "d/n/Y",
            "d.n.Y",
            "j-n-y",
            "j/n/y",
            "j.n.y",
            "j-n-Y",
            "j/n/Y",
            "j.n.Y"
        ];
        $validDate = false;
        if (!$value) return null;
        foreach ($formats as $format) {
            try {
                $d = Carbon::createFromFormat($format, $value)->format('Y-m-d');
                $validDate = $d;
                break;
            } catch (\Exception $e) {

            }
        }
        return $validDate ? $validDate : null;
    }



    public function downloadFile($file_name)
    {
        $path = storage_path($file_name . ".xls");
        if (File::exists($path)) {
            return response()->download($path)->deleteFileAfterSend(true);
        }
        abort(404);
    }

    private function validationErrorsToString($errArray)
    {
        $valArr = array();
        $errStrFinal = '';
        foreach ($errArray->toArray() as $key => $value) {
            $errStr = $key . ' ' . $value[0];
            array_push($valArr, $errStr);
        }
        if (!empty($valArr)) {
            $errStrFinal = implode(',', $valArr);
        }
        return $errStrFinal;
    }

    public function getAllVdpDesignation()
    {
        $alldesignation= VdpDesignation::orderBy('id', 'priority')->get();
        return Response::json($alldesignation);
    }

    function operationPrintIdCardView()
    {
       return View::make('operation::Report.member_id_card_view');
    }

    function getMemberIDHistory(Request $request)
    {
        $memberIdHistory = MemberIdCard::where('geo_id', $request->geo_id)->get();
        return $memberIdHistory;
    }

    function printIdCard()
    {
//        return Input::all();
        $id = Input::get('geo_id');
        $issue_date = Input::get('issue_date');
        $expire_date = Input::get('expire_date');
        $rules = [
            'geo_id' => 'required',
            'issue_date' => 'required|date_format:d-M-Y',
            'expire_date' => 'required|date_format:d-M-Y',
        ];
        $message = [
            'required' => 'This field is required',
            'regex' => 'Enter a valid geo id',
            'numeric' => 'Geo id must be numeric',
            'date_format' => 'Invalid date format',
        ];

        $bng_data = ["title" => "বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী",
            "id_no" => "আইডি নং",
            "name" => "নাম",
            "rank" => "পদবী",
            "bg" => "রক্তের গ্রুপ",
            "unit" => "জেলা",
            "division" => "বিভাগ",
            "thana" => "উপজেলা",
            "union" => "ইউনিয়ন/ওয়ার্ড",
            "dob" => "জন্ম তারিখ",
            "id" => "প্রদানের তারিখ",
            "ed" => "মেয়াদোর্ত্তীনের তারিখ",
            "bs" => "বাহকের স্বাক্ষর",
            "is" => "কতৃপক্ষের স্বাক্ষর",
            "footer_title " => ""
          ];

        $validation = Validator::make(Input::all(), $rules, $message);

        if ($validation->fails()) {
            return Response::json(['validation' => true, 'messages' => $validation->messages()]);
        }

        $report_data = ${"bng_data"};
        $member = DB::table('tbl_vdp_ansar_info AS vi')
            ->join('tbl_vdp_designation AS d', 'd.id', '=', 'vi.designation')
            ->join('db_amis.tbl_units AS u', 'u.id', '=', 'vi.unit_id')
            ->join('db_amis.tbl_thana AS t', 't.id', '=', 'vi.thana_id')
            ->join('db_amis.tbl_division AS dn', 'dn.id', '=', 'vi.division_id')
            ->join('db_amis.tbl_blood_group AS bg', 'bg.id', '=', 'vi.blood_group_id')
            ->where('vi.geo_id', '=', $id)
            ->select('vi.id','vi.geo_id', 'vi.ansar_name_bng as name', 'd.designation_name_bng as rank', 'd.card_color as card_color', 'bg.blood_group_name_bng as blood_group', 'u.unit_name_bng as unit_name', 'u.unit_code', 'dn.division_name_bng as division_name','dn.division_code','t.thana_name_bng as thana_name','vi.union_word_text','vi.profile_pic', 'vi.date_of_birth','vi.sign_pic')->first();
        if ($member) {
            $ansarIdHistory = MemberIdCard::where('geo_id', $id)->get();
            $id_card = new MemberIdCard;
            $id_card->geo_id = $id;
            $id_card->member_id = $member->id;
            $id_card->issue_date = Carbon::parse($issue_date)->format("Y-m-d");
            $id_card->expire_date = Carbon::parse($expire_date)->format("Y-m-d");
            $id_card->status = 1;
            //$id_card->save();
            //print_r($id_card); exit;
            if (!$id_card->saveOrFail()) {
                return View::make('operation::Report.no_member_found')->with('id', $id);
            }
            return View::make('operation::Report.member_id_card_font', ['rd' => $report_data, 'ad' => $member, 'id' => Carbon::parse($issue_date)->format("d/m/Y"), 'ed' => Carbon::parse($expire_date)->format("d/m/Y"), 'type' => 'bng']);
            $path = public_path("{$id}.jpg");
            SnappyImage::loadView('operation::Report.member_id_card_font', ['rd' => $report_data, 'ad' => $member, 'id' => Carbon::parse($issue_date)->format("d/m/Y"), 'ed' => Carbon::parse($expire_date)->format("d/m/Y"), 'type' => 'bng'])->setOption('quality', 100)
                ->setOption('crop-x', 0)->setOption('crop-y', 0)->setOption('crop-h', 292)->setOption('crop-w', 340)->setOption('encoding', 'utf-8')->save($path);
            $image = Image::make($path)->encode('data-url');
            File::delete($path);
            return View::make('operation::Report.id_card_print')->with(['image' => $image->encode('data-url'), 'history' => $memberIdHistory]);
        }
        return View::make('operation::Report.no_member_found')->with('id', $id);
    }


    public function printIdList()
    {
        return View::make('operation::Report.member_print_id_list');
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

        $ansars = MemberIdCard::whereBetween('created_at', [$f_date, $t_date])->get();

        return Response::json(['ansars' => $ansars]);
    }

    public function memberCardStatusChange()
    {
        $rules = [
            'action' => 'required|regex:/^[a-z]+$/',
            'geo_id' => 'required',
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid request(400)", 400);
        }
        switch (Input::get('action')) {
            case 'block':
                $member = MemberIdCard::where('geo_id', Input::get('geo_id'))->first();
                $member->status = 0;
                if ($member->save()) {
                    return Response::json(['status' => 1]);
                } else {
                    return Response::json(['status' => 0]);
                }
                break;
            case 'active':
                $member = MemberIdCard::where('geo_id', Input::get('geo_id'))->first();
                $member->status = 1;
                if ($member->save()) {
                    return Response::json(['status' => 1]);
                } else {
                    return Response::json(['status' => 0]);
                }
                break;
            default:
                return response("Invalid request(400)", 400);
        }
    }
}
