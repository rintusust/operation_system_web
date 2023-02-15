<?php

namespace App\modules\operation\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\models\Login;
use App\modules\HRM\Models\AnsarStatusInfo;
use App\modules\operation\Models\Blood;
use App\modules\operation\Models\CustomQuery;
use App\modules\operation\Models\Designation;
use App\modules\operation\Models\District;
use App\modules\operation\Models\Division;
use App\modules\operation\Models\Organization;
use App\modules\operation\Models\Edication;
use App\modules\operation\Models\Nominee;
use App\modules\operation\Models\PersonalInfo;
use App\modules\operation\Models\Thana;
use App\modules\operation\Models\TrainingInfo;
use App\modules\operation\Repositories\data\OperationDataInterface;
use Carbon\Carbon;
use Hash;
use Illuminate\Contracts\Pagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Mockery\Exception;

class FormSubmitHandler extends Controller
{
    private $dataRepo;

    /**
     * FormSubmitHandler constructor.
     */
    public function __construct(OperationDataInterface $dataRepo)
    {
        $this->dataRepo = $dataRepo;
    }

    public function handleLogin(Request $request)
    {
        if ($request->isMethod('post')) {
            $email = $request->input('email');
            echo $email;
        }
    }

    public function handlesignup(Request $request)
    {
        $rules = array(
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6|max:12',
            'repeat_password' => 'required|same:password'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('signup')->withErrors($validator)->withInput(Input::except(array('password', 'repeat_password')))
                ->with('message', 'There is some problem in your input');
        } else {
            if ($request->isMethod('post')) {
                $table = new Login();
                $table->name = $request->input('name');
                $table->email = $request->input('email');
                $table->password = Hash::make($request->input('password'));
                $table->save();
            }
        }
    }


    


    public function DivisionName(Request $request)
    {
              

        return Response::json($this->dataRepo->getDivisions($request->id,$request->oz));
    }

    public function DistrictName(Request $request)
    {
//        return $request->all();
        return Response::json($this->dataRepo->getUnits(is_array($request->id)||!$request->id?$request->id:explode(",",$request->id), $request->unit_id));
    }

    public function ThanaName(Request $request)
    {


        return Response::json($this->dataRepo->getThanas($request->division_id, $request->id));
    }

    public function testregistration(Request $request)
    {
        if ($request->isMethod('post')) {

            $personalinfo = $request->input('name_of_degree');
            print_r($personalinfo);
            die();
        }
    }

    public function getBloodName()
    {
        $bloodName = $this->dataRepo->getBloodGroup();
        return Response::json($bloodName);
    }



    public function submitEditEntry(Request $request)
    {
        //return $request->all();
        $ansarId = $request->input('ID');
        $i = PersonalInfo::where('ansar_id', $ansarId)->select('id')->first();
        //return $i;
        if ($request->ajax()) {

            $rules = [
                'ansar_name_eng' => 'required',
                'ansar_name_bng' => 'required',
                'recent_status' => 'required',
                'father_name_eng' => 'required',
                'father_name_bng' => 'required',
                'mother_name_eng' => 'required',
                'mother_name_bng' => 'required',
                'data_of_birth' => 'required',
                'marital_status' => 'required',
                'national_id_no' => 'required|numeric|regex:/^[0-9]{10,17}$/',
                'division_name_eng' => 'required',
                'unit_name_eng' => 'required',
                'thana_name_eng' => 'required',
                'blood_group_name_bng' => 'required',
                'hight_feet' => 'required',
                'sex' => 'required',
                'mobile_no_self' => 'required|min:11|unique:tbl_ansar_parsonal_info,mobile_no_self,' . $i->id,
            ];

            $messages = [
                'required' => 'This field is required',
                'national_id_no.regex' => 'National id no must be numeric and between 10 to 17 digits'
            ];
            $validator = Validator:: make($request->all(), $rules, $messages);
            $bank_info = $request->only([
                'bank_name', 'branch_name', 'account_no', 'mobile_bank_type', 'mobile_bank_account_no'
                , 'prefer_choice']);
            if ($validator->fails()) {
//                return Redirect::to('entryform')->withErrors($validator)->withInput();
                return Response::json(['error' => $validator->errors(), 'status' => false]);
            } else {

                $personalinfo = PersonalInfo::where('ansar_id', $ansarId)->first();
                $education = [];
//                $designation = new Designation();
                $training = [];
                $nominee = [];

//            get division and district values
                $division = new Division();
                $unit = new District();
                $personalinfo->session_id = $request->input('session_id');
                $personalinfo->ansar_name_eng = $request->input('ansar_name_eng');

                $personalinfo->ansar_name_bng = $request->input('ansar_name_bng');
                $personalinfo->designation_id = $request->input('recent_status');
//            $personalinfo->recent_Status_bl=$request->input('recent_Status_bl');
                $personalinfo->father_name_eng = $request->input('father_name_eng');
                $personalinfo->father_name_bng = $request->input('father_name_bng');
                $personalinfo->mother_name_eng = $request->input('mother_name_eng');
                $personalinfo->mother_name_bng = $request->input('mother_name_bng');
                $personalinfo->data_of_birth = Carbon::parse($request->input('data_of_birth'))->format("Y-m-d");
                $personalinfo->marital_status = $request->input('marital_status');
//                $personalinfo->marital_status_bng = $request->input('marital_status_bng');
                $personalinfo->spouse_name_eng = $request->input('spouse_name_eng');
                $personalinfo->spouse_name_bng = $request->input('spouse_name_bng');
                $personalinfo->national_id_no = $request->input('national_id_no');
                $personalinfo->birth_certificate_no = $request->input('birth_certificate_no');

                $personalinfo->disease_id = $request->input('long_term_disease');

                if ($request->input('long_term_disease') == '1') {
                    $personalinfo->own_disease = $request->input('own_disease');
                } else {
                    $personalinfo->own_disease = NULL;
                }

                $personalinfo->skill_id = $request->input('particular_skill');

                if ($request->input('particular_skill') == '1') {
                    $personalinfo->own_particular_skill = $request->input('own_particular_skill');
                } else {
                    $personalinfo->own_particular_skill = NULL;
                }

                $personalinfo->criminal_case = $request->input('criminal_case');
                $personalinfo->criminal_case_bng = $request->input('criminal_case_bng');
//                $personalinfo->certificate_no = $request->input('certificate_no');
                $personalinfo->village_name = $request->input('village_name');
                $personalinfo->village_name_bng = $request->input('village_name_bng');
                $personalinfo->post_office_name = $request->input('post_office_name');
                $personalinfo->post_office_name_bng = $request->input('post_office_name_bng');
                $divisionid = $personalinfo->division_id = $request->input('division_name_eng');
                $unitid = $personalinfo->unit_id = $request->input('unit_name_eng');
                $thanaid = $personalinfo->thana_id = $request->input('thana_name_eng');
                $personalinfo->union_name_eng = $request->input('union_name_eng');
                $personalinfo->union_name_bng = $request->input('union_name_bng');
                $personalinfo->hight_feet = $request->input('hight_feet');
//                $personalinfo->hight_feet_bng = $request->input('hight_feet_bng');
                $personalinfo->hight_inch = $request->input('hight_inch');
//                $personalinfo->hight_inch_bng = $request->input('hight_inch_bng');
                $personalinfo->blood_group_id = $request->input('blood_group_name_bng');

                $personalinfo->eye_color = $request->input('eye_color');
                $personalinfo->eye_color_bng = $request->input('eye_color_bng');
                $personalinfo->skin_color = $request->input('skin_color');
                $personalinfo->skin_color_bng = $request->input('skin_color_bng');
                $personalinfo->sex = $request->input('sex');
//                $personalinfo->sex_bng = $request->input('sex_bng');
                $personalinfo->identification_mark = $request->input('identification_mark');
                $personalinfo->identification_mark_bng = $request->input('identification_mark_bng');
                $personalinfo->land_phone_self = $request->input('land_phone_self');
//                $personalinfo->land_phone_self_bng = $request->input('land_phone_self_bng');
                $personalinfo->land_phone_request = $request->input('land_phone_request');
//                $personalinfo->land_phone_request_bng = $request->input('land_phone_request_bng');
                $mobile_no = $request->input('mobile_no_self');

                if (!ctype_digit($mobile_no)) {
                    return Response::json(['numeric_value' => 'Need numeric', 'status' => 'numeric']);
                } else {
                    $getFirstTowDigit = substr($mobile_no, 0, 2);
                    if ($getFirstTowDigit == 88) {
                        return Response::json(['numeric_value' => 'Need numeric', 'status' => 'eight']);
//                           $number = $personalinfo->mobile_no_self =  substr($mobile_no,2);
                    } else
                        $number = $personalinfo->mobile_no_self = $mobile_no;
                }


//                $personalinfo->mobile_no_self_bng = $request->input('mobile_no_self_bng');
                $personalinfo->mobile_no_request = $request->input('mobile_no_request');
//                $personalinfo->mobile_no_request_bng = $request->input('mobile_no_request_bng');
                $personalinfo->email_self = $request->input('email_self');
                $personalinfo->email_request = $request->input('email_request');

                $personalinfo->user_id = Auth::user()->id;
//
//                list($ppwidth, $ppheight, $type, $attr) = getimagesize($request->file('profile_pic'));
//                list($signwidth, $signheight, $type, $attr) = getimagesize($request->file('sign_pic'));
//                list($thumbwidth, $thumbheight, $type, $attr) = getimagesize($request->file('thumb_pic'));
//                if ($ppwidth > 220 || $ppheight > 200)
//                    return redirect('entryform')->with('status', 'Profile picture must be less than width 220px and height 200px')->withInput();
//
//                if ($signwidth > 120 || $signheight > 50)
//                    return redirect('entryform')->with('signstatus', 'Sign image must be less than width 120px and height 50px')->withInput();
//
//                if ($thumbwidth > 120 || $thumbheight > 50)
//                    return redirect('entryform')->with('thumbstatus', 'Thumb image must be less than width 120px and height 50px')->withInput();
                //


                $name_of_degree = $request->input('educationIdBng');
                $institute_name = $request->input('institute_name');
                $passing_year = $request->input('passing_year');
                $gade_divission = $request->input('gade_divission');
                $institute_name_eng = $request->input('institute_name_eng');
                $passing_year_eng = $request->input('passing_year_eng');
                $gade_divission_eng = $request->input('gade_divission_eng');
                $edulength = count($name_of_degree);


//
                $training_designation = $request->input('training_designation');
                $training_institute_name = $request->input('institute');
                $training_start_date = $request->input('training_start');
                $training_end_date = $request->input('training_end');
                $trining_certificate_no = $request->input('training_sanad');
                $training_designation_eng = $request->input('training_designation_eng');
                $training_institute_name_eng = $request->input('institute_eng');
                $training_start_date_eng = $request->input('training_start_eng');
                $training_end_date_eng = $request->input('training_end_eng');
                $trining_certificate_no_eng = $request->input('training_sanad_eng');
                $length = count($training_institute_name);


                $name_of_nominee = $request->input('nominee_name');
                $relation_with_nominee = $request->input('relation');
                $nominee_parcentage = $request->input('percentage');
                $nominee_contact_no = $request->input('nominee_mobile');
                $name_of_nominee_eng = $request->input('nominee_name_eng');
                $relation_with_nominee_eng = $request->input('relation_eng');
                $nominee_parcentage_eng = $request->input('percentage_eng');
                $nominee_contact_no_eng = $request->input('nominee_mobile_eng');
                $nomineelength = count($name_of_nominee);


//
                $personalinfo->ansar_id = $ansarId;


                DB::beginTransaction();
                try {
                    if ($length > 0)
                        TrainingInfo::where('ansar_id', $ansarId)->delete();
                    if ($nomineelength > 0)
                        Nominee::where('annsar_id', $ansarId)->delete();
                    if ($edulength > 0)
                        Edication::where('ansar_id', $ansarId)->delete();


                    for ($i = 0; $i < $length; $i++) {
                        if (strlen($training_designation[$i]) != 0) {
                            $training[$i] = new TrainingInfo();
                            $training[$i]->ansar_id = $ansarId;
                            $training[$i]->training_designation = $training_designation[$i];
                            $training[$i]->training_institute_name = $training_institute_name[$i];
                            $training[$i]->training_start_date = $training_start_date[$i] ? Carbon::parse($training_start_date[$i])->format("Y-m-d") : '0000-00-00';
                            $training[$i]->training_end_date = $training_end_date[$i] ? Carbon::parse($training_end_date[$i])->format("Y-m-d") : '0000-00-00';
                            $training[$i]->trining_certificate_no = $trining_certificate_no[$i];
                            $training[$i]->training_designation_eng = $training_designation_eng[$i];
                            $training[$i]->training_institute_name_eng = $training_institute_name_eng[$i];
                            $training[$i]->training_start_date_eng = $training_start_date_eng[$i];
                            $training[$i]->training_end_date_eng = $training_end_date_eng[$i];
                            $training[$i]->trining_certificate_no_eng = $trining_certificate_no_eng[$i];
                            $successtraining = $training[$i]->save();
                        }
                    }


                    for ($i = 0; $i < $edulength; $i++) {
                        if (strlen($name_of_degree[$i]) != 0) {
                            $education[$i] = new Edication();
                            $education[$i]->ansar_id = $ansarId;
                            $education[$i]->education_id = $name_of_degree[$i];
                            $education[$i]->institute_name = $institute_name[$i];
                            $education[$i]->passing_year = $passing_year[$i];
                            $education[$i]->gade_divission = $gade_divission[$i];
                            $education[$i]->institute_name_eng = $institute_name_eng[$i];
                            $education[$i]->passing_year_eng = $passing_year_eng[$i];
                            $education[$i]->gade_divission_eng = $gade_divission_eng[$i];
                            $successeducation = $education[$i]->save();
                        }
                    }


                    for ($i = 0; $i < $nomineelength; $i++) {
                        if (strlen($name_of_nominee[$i]) != 0) {
                            $nominee[$i] = new Nominee();
                            $nominee[$i]->annsar_id = $ansarId;
                            $nominee[$i]->name_of_nominee = $name_of_nominee[$i];
                            $nominee[$i]->relation_with_nominee = $relation_with_nominee[$i];
                            $nominee[$i]->nominee_parcentage = $nominee_parcentage[$i];
                            $nominee[$i]->nominee_contact_no = $nominee_contact_no[$i];
                            $nominee[$i]->name_of_nominee_eng = $name_of_nominee_eng[$i];
                            $nominee[$i]->relation_with_nominee_eng = $relation_with_nominee_eng[$i];
                            $nominee[$i]->nominee_parcentage_eng = $nominee_parcentage_eng[$i];
                            $nominee[$i]->nominee_contact_no_eng = $nominee_contact_no_eng[$i];
                            $successnominee = $nominee[$i]->save();
                        }
                    }


//            get the images
//                profile picture
                    //profile picture
                    if ($request->file('profile_pic')) {
                        $profileextension = $request->file('profile_pic')->getClientOriginalExtension();
                        $path = storage_path('data/photo');
                        if (!File::exists($path)) File::makeDirectory($path, 0777, true);
                        if (File::exists($path . '/' . $ansarId . '.' . $profileextension)) {
                            File::delete($path . '/' . $ansarId . '.' . $profileextension);
                        }
                        Image::make($request->file('profile_pic'))->resize(240, 260)->save($path . '/' . $ansarId . '.' . $profileextension);
                        $personalinfo->profile_pic = 'data/photo/' . $ansarId . '.' . $profileextension;
                    } else $personalinfo->profile_pic = 'data/photo/' . $ansarId . '.jpg';
////                Sign picture
                    if ($request->file('sign_pic')) {

                        $signextension = $request->file('sign_pic')->getClientOriginalExtension();
                        $path = storage_path('data/signature');
                        //Log::info(File::exists($path. '/' . $ansarid . '.' . $signextension)?"true":"false");
                        if (!File::exists($path)) File::makeDirectory($path, 0777, true);
                        if (File::exists($path . '/' . $ansarId . '.' . $signextension)) {
                            File::delete($path . '/' . $ansarId . '.' . $signextension);
                        }
                        Image::make($request->file('sign_pic'))->resize(220, 90)->save($path . '/' . $ansarId . '.' . $signextension);
                        $personalinfo->sign_pic = 'data/signature/' . $ansarId . '.' . $signextension;
                    } else $personalinfo->sign_pic = 'data/signature/' . $ansarId . '.jpg';
////                Thumb image
                    if ($request->file('thumb_pic')) {
                        $thumbextension = $request->file('thumb_pic')->getClientOriginalExtension();
                        $path = storage_path('data/fingerprint');
                        if (!File::exists($path)) File::makeDirectory($path, 0777, true);
                        if (File::exists($path . '/' . $ansarId . '.' . $thumbextension)) {
                            File::delete($path . '/' . $ansarId . '.' . $thumbextension);
                        }
                        Image::make($request->file('thumb_pic'))->resize(220, 90)->save($path . '/' . $ansarId . '.' . $thumbextension);
                        $personalinfo->thumb_pic = 'data/fingerprint/' . $ansarId . '.' . $thumbextension;
                    } else $personalinfo->thumb_pic = 'data/fingerprint/' . $ansarId . '.jpg';
                    $personalinfo->account()->delete();
                    $personalinfo->account()->create($bank_info);
                    $successpersonal = $personalinfo->save();
                    if ($successpersonal) {
                        DB::commit();
                        \Illuminate\Support\Facades\Session::flash('edit_success', $ansarId);
                        CustomQuery::addActionlog(['ansar_id' => $ansarId, 'action_type' => 'EDIT ENTRY', 'from_state' => '', 'to_state' => 'ENTRY', 'action_by' => auth()->user()->id]);
                        return Response::json(['status' => true, 'data' => 'value updated successfully']);
                    }
                    throw new Exception();
                } catch (\Exception $rollback) {
                    DB::rollback();
                    return response($rollback->getTraceAsString(), 400, ['Content-type' => 'text/html']);
                }
            }

        } else {
            abort(401);
        }
    }


    public function getNotVerifiedAnsar()
    {
        $rules = [
            'limit' => 'numeric|regex:/^[0-9]+$/',
            'offset' => 'numeric|regex:/^[0-9]+$/',
            'sort' => 'regex:/^[a-z]+$/',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid request(400)", 400);
        }
       if (Input::exists('chunk')) return response()->json(CustomQuery::getNotVerifiedChunkAnsar(Input::get('limit'), Input::get('offset'), Input::get('division'), Input::get('unit'), Input::get('thana'), Input::get('from_ansar'), Input::get('to_ansar')));
        return response()->json(CustomQuery::getNotVerifiedAnsar(Input::get('limit'), Input::get('offset'), Input::get('sort'), Input::get('division'), Input::get('unit'), Input::get('thana'), Input::get('type')));
    }

    public function getVerifiedAnsar()
    {
        $rules = [
            'limit' => 'numeric|regex:/^[0-9]+$/',
            'offset' => 'numeric|regex:/^[0-9]+$/',
            'sort' => 'regex:/^[a-z]+$/',
            'thana' => ['regex:/^(all)$|^[0-9]+$/'],
            'unit' => ['regex:/^(all)$|^[0-9]+$/'],
            'division' => ['regex:/^(all)$|^[0-9]+$/'],
        ];
        $valid = Validator::make(Input::all(), $rules);
        if ($valid->fails()) {
            return response("Invalid request(400)", 400);
        }
        return response()->json(CustomQuery::getVerifiedAnsar(Input::get('limit'), Input::get('offset'), Input::get('sort'), Input::get('division'), Input::get('unit'), Input::get('thana'), Input::get('type')));
    }

    public function getTotalAnsar()
    {
        return "pppp";
        DB::enableQueryLog();
//        $p = DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('tbl_ansar_status_info.block_list_status', 0)->where('tbl_ansar_status_info.embodied_status', 1)->where('tbl_kpi_info.unit_id', 9)->distinct()->count('tbl_embodiment.ansar_id');
//        return $p;
        if (Input::exists('division_id')) {
            $units = District::where('division_id', Input::get('division_id'))->select('id')->get();
            $unit = [];
            foreach ($units as $u) array_push($unit, $u->id);
            $allStatus = array(
                'totalAnsar' => DB::table('tbl_ansar_parsonal_info')->where('division_id', Input::get('division_id'))->count('ansar_id'),
                'totalNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('tbl_ansar_parsonal_info.division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_ansar_status_info.offer_sms_status', 1)->where('block_list_status', 0)->whereIn('tbl_sms_offer_info.district_id', $unit)->distinct()->count('tbl_ansar_status_info.ansar_id'),
                //'offerReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->whereIn('tbl_sms_offer_info.district_id', $unit)->count(),
                'totalEmbodied' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('embodied_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->whereIn('tbl_kpi_info.unit_id', $unit)->distinct()->count('tbl_embodiment.ansar_id'),
                'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->whereIn('tbl_kpi_info.unit_id', '!=', $unit)->distinct()->count('tbl_embodiment.ansar_id'),
                'totalFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('freezing_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('rest_status', 1)->where('block_list_status', 0)->where('division_id', Input::get('division_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
            );
        } else if (Input::exists('district_id')) {

            $allStatus = array(
                'totalAnsar' => DB::table('tbl_ansar_parsonal_info')->where('unit_id', Input::get('district_id'))->count('ansar_id'),
                'totalNotVerified' => DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalFree' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('free_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalPanel' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('pannel_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalOffered' => DB::table('tbl_ansar_status_info')->join('tbl_sms_offer_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('offer_sms_status', 1)->where('block_list_status', 0)->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->distinct()->count('tbl_ansar_status_info.ansar_id'),
                //'offerReceived' => DB::table('tbl_sms_receive_info')->join('tbl_sms_offer_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_sms_offer_info.ansar_id')->where('tbl_sms_receive_info.sms_status', 'ACCEPTED')->where('tbl_sms_offer_info.district_id', Input::get('district_id'))->count(),
                'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 0)->where('embodied_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalEmbodiedOwn' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->where('tbl_kpi_info.unit_id', Input::get('district_id'))->distinct()->count('tbl_embodiment.ansar_id'),
//                'totalEmbodiedDiff' => DB::table('tbl_ansar_status_info')->join('tbl_embodiment', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_embodiment.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('block_list_status', 0)->where('embodied_status', 1)->where('tbl_kpi_info.unit_id','!=', Input::get('district_id'))->distinct()->count('tbl_embodiment.ansar_id'),
                'totalFreeze' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_embodiment', 'tbl_embodiment.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->join('tbl_kpi_info', 'tbl_embodiment.kpi_id', '=', 'tbl_kpi_info.id')->where('freezing_status', 1)->where('block_list_status', 0)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_embodiment.ansar_id'),
                'totalBlockList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalBlackList' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('black_list_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalRest' => DB::table('tbl_ansar_status_info')->join('tbl_ansar_parsonal_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->where('block_list_status', 0)->where('rest_status', 1)->where('unit_id', Input::get('district_id'))->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
            );
        } else {
            $allStatus = array(
                'totalAnsar' => $totalAnsar = DB::table('tbl_ansar_parsonal_info')->distinct()->count('ansar_id'),
                'totalNotVerified' => $notVerified = DB::table('tbl_ansar_parsonal_info')->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')->whereIn('tbl_ansar_parsonal_info.verified', [0, 1])->where('block_list_status', 0)->distinct()->count('tbl_ansar_parsonal_info.ansar_id'),
                'totalFree' => $totalFreeStatus = AnsarStatusInfo::where('free_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
                'totalPanel' => $totalpPanel = AnsarStatusInfo::where('pannel_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
                'totalOffered' => $totalOfferred = AnsarStatusInfo::where('offer_sms_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
                //'offerReceived' => $totalOfferedStatus = ReceiveSMSModel::where('sms_status', 'ACCEPTED')->count(),
                'totalEmbodied' => $totalEmbodied = AnsarStatusInfo::where('embodied_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
                'totalFreeze' => $totalFreeze = AnsarStatusInfo::where('freezing_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
                'totalBlockList' => $totalBlockList = AnsarStatusInfo::where('block_list_status', 1)->distinct()->count('ansar_id'),
                'totalBlackList' => $totalBlackList = AnsarStatusInfo::where('black_list_status', 1)->distinct()->count('ansar_id'),
                'totalRest' => $totalBlackList = AnsarStatusInfo::where('rest_status', 1)->where('block_list_status', 0)->distinct()->count('ansar_id'),
            );
        }
//        return DB::getQueryLog();
        return Response::json($allStatus);
    }

    public function advancedEntrySearchSubmit(Request $request)
    {
//
//        return $request->all();
        DB::enableQueryLog();
        $ansarAdvancedSearch = DB::table('tbl_ansar_parsonal_info')
            ->join('tbl_division', 'tbl_ansar_parsonal_info.division_id', '=', 'tbl_division.id')
            ->join('tbl_designations', 'tbl_ansar_parsonal_info.designation_id', '=', 'tbl_designations.id')
            ->join('tbl_units', 'tbl_ansar_parsonal_info.unit_id', '=', 'tbl_units.id')
            ->select('tbl_ansar_parsonal_info.ansar_id', 'tbl_ansar_parsonal_info.ansar_name_eng', 'tbl_ansar_parsonal_info.father_name_eng', 'tbl_ansar_parsonal_info.sex', 'tbl_ansar_parsonal_info.mobile_no_self', 'tbl_ansar_parsonal_info.data_of_birth', 'tbl_division.division_name_eng', 'tbl_designations.name_eng', 'tbl_units.unit_name_eng');

        if (Auth::user()->type == 55) {
            $ansarAdvancedSearch->where('tbl_ansar_parsonal_info.user_id', Auth::user()->id);
        }
        foreach ($request->except(['page', 'action_user_id']) as $key => $value) {
            $value = (object)($value);
//            return $value;
            if ($key == 'smart_card_no') {
                if ($value->value) {
                    $l = strlen($value->value) - 6;
                    $id = substr($value->value, $l < 0 ? 0 : $l);
                    $ansarAdvancedSearch->where('ansar_id', intval($id));
                }
            } else if ($key == 'education') {
                if ($value->value) $ansarAdvancedSearch->join('tbl_ansar_education_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_ansar_education_info.ansar_id')
                    ->where('tbl_ansar_education_info.education_id', $value->compare, $value->value);
            } else if ($key == 'disease_id') {
                if ($value->value && strcasecmp($value->value, 'type')) $ansarAdvancedSearch->where('tbl_ansar_parsonal_info.' . $key, $value->compare, $value->value);
            } else if ($key == 'ansar_name') {
                if ($value->value) {
                    global $v;
                    global $c;
                    $v = $value->value;
                    $c = $value->compare;
                    $ansarAdvancedSearch->where(function ($q) {
                        global $v, $c;
                        $q->where('tbl_ansar_parsonal_info.ansar_name_eng', $c, $c == 'LIKE' ? "%" . $v . "%" : $v);
                        $q->orWhere('tbl_ansar_parsonal_info.ansar_name_bng', $c, $c == 'LIKE' ? "%" . $v . "%" : $v);
                    });
                }
            } else if ($key == 'father_name') {
                if ($value->value) {
                    global $v;
                    global $c;
                    $v = $value->value;
                    $c = $value->compare;
                    $ansarAdvancedSearch->where(function ($q) {
                        global $v, $c;
                        $q->where('tbl_ansar_parsonal_info.father_name_eng', $c, $c == 'LIKE' ? "%" . $v . "%" : $v);
                        $q->orWhere('tbl_ansar_parsonal_info.father_name_bng', $c, $c == 'LIKE' ? "%" . $v . "%" : $v);
                    });
                }
            } else if ($key == "data_of_birth") {
                if ($value->value) {
                    $ansarAdvancedSearch->where('tbl_ansar_parsonal_info.' . $key, $value->compare, Carbon::parse($value->value)->format("y-m-d"));
                }
            } else if ($key == "hight_feet") {
                if ($value->value) {
                    if ($value->compare == '>') $ansarAdvancedSearch->where('tbl_ansar_parsonal_info.' . $key, ">=", $value->value);
                    else if ($value->compare == '<') $ansarAdvancedSearch->where('tbl_ansar_parsonal_info.' . $key, "<=", $value->value);
                    else $ansarAdvancedSearch->where('tbl_ansar_parsonal_info.' . $key, "=", $value->value);
                }
            } else if ($key == "hight_inch") {
                if ($request->get('hight_feet')['value']) {
                    $ansarAdvancedSearch->where('tbl_ansar_parsonal_info.' . $key, $value->compare, $value->value ? $value->value : 0);
                } else if ($value->value) {
                    $ansarAdvancedSearch->where('tbl_ansar_parsonal_info.' . $key, $value->compare, $value->value);
                }

            }else if ($key == "status") {
                $selectedStatus = array_filter($value->value);
                $allStatus = [
                    "free_status",
                    "pannel_status",
                    "offer_sms_status",
                    "offered_status",
                    "embodied_status",
                    "offer_block_status",
                    "freezing_status",
                    "early_retierment_status",
                    "block_list_status",
                    "black_list_status",
                    "retierment_status",
                    "expired_status"
                ];
                $noStatus = array_diff($allStatus,$selectedStatus);
                if($selectedStatus&&count($selectedStatus)>0){
                    $ansarAdvancedSearch->join('tbl_ansar_status_info','tbl_ansar_status_info.ansar_id','=','tbl_ansar_parsonal_info.ansar_id');
                    foreach ($selectedStatus as $status){
                        $ansarAdvancedSearch->where($status,1);
                    }
                    foreach ($noStatus as $status){
                        $ansarAdvancedSearch->where($status,0);
                    }
                }


            } else {
                if ($value->value) {
                    $ansarAdvancedSearch->where('tbl_ansar_parsonal_info.' . $key, $value->compare, $value->compare == 'LIKE' ? "%" . $value->value . "%" : $value->value);
                }
            }
        }
        $data = $ansarAdvancedSearch->paginate(20);
        //return DB::getQueryLog();
        return Response::json($data);
    }

    public function handleEdit($param)
    {
        echo "something";
    }

//    Searching the id for original info
    public function idsearch(Request $request)
    {
        $ansarID = $request->input('ansarId');
        $find = PersonalInfo::where('ansar_id', $ansarID)->select('id', 'ansar_id');
        if (Auth::user()->type == 55) {
            $find->where('user_id', Auth::user()->id);
        }
        if ($find->exists()) {
            $file_font = $find->first()->ansar_id;
            $file_back = $find->first()->ansar_id;
            return Response::json(['status' => true, 'url' => [
                'font' => URL::route('view_image', ['type' => 'font', 'file' => $file_font]),
                'back' => URL::route('view_image', ['type' => 'back', 'file' => $file_back])
            ]]);
        } else
            return Response::json(['status' => false, 'url' => null]);
    }

    public function chunkVerify()
    {
        return View::make('operation::Entryform.chunk_verification');
    }

    public function getAnsarRank()
    {
        return Response::json(Designation::orderBy('id', 'desc')->get());
    }

    public function getImage($type, $file)
    {
        if ($type == 'font')
            $file = storage_path('data/orginalinfo/frontside/' . $file.'.jpg');
        else  $file = storage_path('data/orginalinfo/backside/' . $file.'.jpg');
        if (File::exists($file)) {
            try {
                $image = Image::make($file);
                return $image->response();
            } catch (\Exception $e) {

//                return $e->getMessage();
                return Image::make(public_path('dist/img/image-not-found.png'))->response();
            }
        } else {
            return Image::make(public_path('dist/img/image-not-found.png'))->response();
        }
    }


    public function picSignature(Request $request)
    {
        $ansarID = $request->input('ansarId');
        $find = PersonalInfo::where('ansar_id', $ansarID)->select('id', 'ansar_id');
        if (Auth::user()->type == 55) {
            $find->where('user_id', Auth::user()->id);
        }
        if ($find->exists()) {
            $profile_pic = $find->first()->ansar_id;
            $signature = $find->first()->ansar_id;
            return Response::json(['status' => true, 'url' => [
                'profile_pic' => URL::route('view_pic_signature', ['type' => 'profile_pic', 'file' => $profile_pic]),
                'signature' => URL::route('view_pic_signature', ['type' => 'signature', 'file' => $signature])
            ]]);
        } else
            return Response::json(['status' => false, 'url' => null]);
    }

    public function getPicSignature($type, $file)
    {
        if ($type == 'profile_pic')
            $file = storage_path('data/photo/' . $file.'.jpg');
        else  $file = storage_path('data/signature/' . $file.'.jpg');
        if (File::exists($file)) {
            try {
                $image = Image::make($file);
                return $image->response();
            } catch (\Exception $e) {

//                return $e->getMessage();
                return Image::make(public_path('dist/img/image-not-found.png'))->response();
            }
        } else {
            return Image::make(public_path('dist/img/image-not-found.png'))->response();
        }
    }
}