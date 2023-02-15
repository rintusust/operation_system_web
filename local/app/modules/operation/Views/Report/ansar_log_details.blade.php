<div class="container-fluid">
    <img class="pull-right profile-image"
         src="{{action('UserController@getImage',['file'=>$data->profile_pic])}}"
         alt="">
    <table class="entry-table" style="width: 100%">
        <tr>
            <td>আইডি কার্ড নম্বর<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->ansar_id}}</div>
            </td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold">বাক্তিগত ও পারিবারিক তথ্য</caption>
        <tr>
            <td>*Name<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->ansar_name_eng}}</div>
            </td>
        </tr>
        <tr>
            <td>*নাম<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->ansar_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>*বর্তমান পদবী <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->designation->name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>*Father's name <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->father_name_eng}}</div>
            </td>
        </tr>
        <tr>
            <td>*পিতার নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->father_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>*Mother's Name <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->mother_name_eng}}</div>
            </td>
        </tr>
        <tr>
            <td>*মাতার নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->mother_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>*Date of birth <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">
                    @if(empty($data->data_of_birth))
                    
                    @else
                     {{\Carbon\Carbon::parse($data->data_of_birth)->format("d-m-Y")}}
                     @endif 
                </div>
            </td>
        </tr>
        <tr>
            <td>*Marital status <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{strcasecmp($data->marital_status,"married")==0?"বিবাহিত":(strcasecmp($data->marital_status,"unmarried")==0?"অবিবাহিত":"")}}</div>
            </td>
        </tr>
        <tr>
            <td>*Spouse Name <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->spouse_name_eng?$data->spouse_name_eng: '&nbsp;'}}</div>
            </td>
        </tr>
        <tr>
            <td>*স্ত্রী/স্বামীর নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->spouse_name_bng or ' '}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>*National Id no <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->national_id_no?$data->national_id_no:'&nbsp;'}}</div>
            </td>
        </tr>
        <tr>
            <td>*Birth Certificate no <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{ $data->birth_certificate_no?$data->birth_certificate_no:' '}}</div>
            </td>
        </tr>
        <tr>
            <td>দীর্ঘ মেয়াদি অসুখ <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->alldisease->disease_name_bng or $data->own_disease?$data->own_disease:'&nbsp;'}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>নির্দিষ্ট দক্ষতা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{($data && isset($data->allskill) && isset($data->allskill->skill_name_bng))? $data->allskill->skill_name_bng:""}}</div>
            </td>
        </tr>
        <tr>
            <td>Criminal Case<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->criminal_case?$data->criminal_case:"&nbsp;"}}</div>
            </td>
        </tr>
        <tr>
            <td>ফৌজদারি মামলা<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->criminal_case_bng?$data->criminal_case_bng:"&nbsp;"}}</div>
            </td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold">স্থায়ী ঠিকানা</caption>
        <tr>
            <td>Village/House No<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->village_name}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>গ্রাম/বাড়ি নং<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->village_name_bng}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>Road No <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->road_no or '&nbsp;'}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>Post office <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->post_office_name}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>ডাকঘর <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->post_office_name_bng}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>Union/Word <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->union_name_eng}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>ইউনিয়ন নাম/ওয়ার্ড <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->union_name_bng}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>*বিভাগ <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->division->division_name_bng}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>*জেলা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->district->unit_name_bng}}</div>
            </td>
        </tr>
        <tr>
            <td>*থানা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->thana->thana_name_bng}}</div>
            </td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold">শারীরিক যোগ্যতার তথ্য</caption>
        <tr>
            <td>*Height(উচ্চতা)<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:0 5px;font-size:14px;border:1px solid #ababab">
                    <span style="padding: 5px 20px">{{$data->hight_feet}}</span>
                    <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ফিট</span>
                    <span style="padding: 5px 20px">{{$data->hight_inch}}</span>
                    <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ইঞ্চি</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>*রক্তের গ্রুপ<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{($data && isset($data->blood) && isset($data->blood->blood_group_name_bng))? $data->blood->blood_group_name_bng:""}}</div>
            </td>
        </tr>
        <tr>
            <td>Eye color<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->eye_color}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>চোখের রং <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->eye_color_bng}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>Skin color<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->skin_color}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>গায়ের রং<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->skin_color_bng}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>*Gender<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->sex}}&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>Identification mark <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->identification_mark}}
                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>সনাক্তকরন চিহ্ন<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab">{{$data->identification_mark_bng}}
                    &nbsp;
                </div>
            </td>
        </tr>
    </table>
    <!--
    <table class="entry-table border-table">
        <caption>Educational Information*</caption>
        <tbody>
        <tr>
            <td><b>Education Qualification</b></td>
            <td><b>Institute Name</b></td>
            <td><b>Passing Year</b></td>
            <td><b>Division/Grade</b></td>
        </tr>
        @foreach($data->education as $singleeducation)
            <tr>
                <td>{{ $singleeducation->educationName->education_deg_eng  }}</td>
                <td>{{ $singleeducation->institute_name_eng }}</td>
                <td>{{ $singleeducation->passing_year or LanguageConverter::engToBng($singleeducation->passing_year)}}</td>
                <td>{{ $singleeducation->gade_divission }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="entry-table border-table">
        <caption>শিক্ষাগত যোগ্যতার তথ্য*</caption>
        <tbody>
        <tr>
            <td><b>শিক্ষাগত যোগ্যতা</b></td>
            <td><b>শিক্ষা প্রতিষ্ঠানের নাম</b></td>
            <td><b>পাসের সাল</b></td>
            <td><b>বিভাগ / শ্রেণী</b></td>
        </tr>
        @foreach($data->education as $singleeducation)
            <tr>
                <td>{{ $singleeducation->educationName->education_deg_bng  }}</td>
                <td>{{ $singleeducation->institute_name }}</td>
                <td>{{ $singleeducation->passing_year}}</td>
                <td>{{ $singleeducation->gade_divission }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="entry-table border-table">
        <caption>Training Information</caption>
        <tbody>
        <tr>
            <td><b>Rank</b></td>
            <td><b>Institute Name</b></td>
            <td><b>Training Starting Date</b></td>
            <td><b>Training Ending Date</b></td>
            <td><b>Certificate No.</b></td>
        </tr>
        @foreach ($data->training as $singletraining)
            <tr>
                <td>{{ $singletraining->rank->name_eng }}</td>
                <td>{{$singletraining->training_institute_name_eng}}</td>
                <td>{{ $singletraining->training_start_date}}</td>
                <td>{{ $singletraining->training_end_date }}</td>
                <td>{{ $singletraining->trining_certificate_no }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="entry-table border-table">
        <caption>প্রশিক্ষন সংক্রান্ত তথ্য্</caption>
        <tbody>
        <tr>
            <td><b>পদবী</b></td>
            <td><b>প্রতিষ্ঠান </b></td>
            <td><b>প্রশিক্ষন শুরুর তারিখ </b></td>
            <td><b>প্রশিক্ষন শেষের তারিখ </b></td>
            <td><b>সনদ নং </b></td>
        </tr>
        @foreach ($data->training as $singletraining)
            <tr>
                <td>{{ $singletraining->rank->name_bng }}</td>
                <td>{{$singletraining->training_institute_name}}</td>
                <td>{{ $singletraining->training_start_date}}
                <td>{{ $singletraining->training_end_date }}</td>
                <td>{{ $singletraining->trining_certificate_no }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="entry-table border-table">
        <caption>Nominee Information</caption>
        <tbody>
        <tr>
            <td><b>Name</b></td>
            <td><b>Relation</b></td>
            <td><b>Percentage</b></td>
            <td><b>Mobile No.</b></td>
        </tr>
        @foreach ($data->nominee as $singlenominee)
            <tr>
                <td>{{$singlenominee->name_of_nominee_eng}}</td>
                <td>{{$singlenominee->relation_with_nominee_eng}}</td>
                <td>{{$singlenominee->nominee_parcentage_eng}}</td>
                <td>{{$singlenominee->nominee_contact_no}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <table class="entry-table border-table">
        <caption>
            উত্তরাধিকারীর তথ্য
        </caption>
        <tbody>
        <tr>
            <td><b>নাম</b></td>
            <td><b>সম্পর্ক</b></td>
            <td><b>অংশ(%)</b></td>
            <td><b>মোবাইল নং</b></td>
        </tr>
        @foreach ($data->nominee as $singlenominee)
            <tr>
                <td>{{$singlenominee->name_of_nominee}}</td>
                <td>{{$singlenominee->relation_with_nominee}}</td>
                <td>{{$singlenominee->nominee_parcentage}}</td>
                <td>{{LanguageConverter::engToBng($singlenominee->nominee_contact_no)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
  -->

    <table class="entry-table other-table">
        <caption>অন্যান্য তথ্য</caption>
        <tr>
            <td>Mobile No. (Self) নিজ* <span class="pull-right">:</span></td>
            <td>
                <div style="font-size:14px;">{{$data->mobile_no_self}}</div>
            </td>
        </tr>
        <tr>
            <td>Mobile No. (Alternative) <span class="pull-right">:</span></td>
            <td>
                <div style="font-size:14px;">{{$data->mobile_no_request}}</div>
            </td>
        </tr>
        <tr>
            <td>Email (Self) <span class="pull-right">:</span></td>
            <td>
                <div style="padding:5px;font-size:14px;">{{$data->email_self}}</div>
            </td>
        </tr>
    </table>
  
</div>
<style>
    .entry-table {
        border: none !important;
        page-break-after: auto !important;
        page-break-inside: avoid;
    !important;
    }

    .entry-table td {
        border: none !important;
        padding: 5px 0 0 0 !important;
        text-align: left !important;
    }

    .entry-table tr td:first-child {
        width: 20%;
    }

    .entry-table tr td:last-child {
        width: 80%;
        padding-left: 20px !important;
    }

    .entry-table.border-table, .entry-table.other-table {
        width: 100%;
        border: 1px solid #ababab !important;

    }

    .entry-table.border-table td, .entry-table.border-table th {
        border: 1px solid #ababab !important;
        border-collapse: collapse !important;
        width: auto !important;
        text-align: center !important;
    }

    .entry-table caption {
        text-align: center !important;
        font-size: 1em !important;
        font-weight: bold !important;

    }

    .entry-table.other-table td, .entry-table.other-table th {
        border: 1px solid #ababab !important;
        border-collapse: collapse !important;
        padding: 5px 10px !important;
    }

    .entry-table.border-table.image-table {
        margin-top: 10px;
    }

    .entry-table.border-table.image-table tr:first-child td {
        width: 50% !important;
    }

    .entry-table.border-table.image-table tr:not(:first-child) td {
        width: 50% !important;
        height: 100px !important;
        vertical-align: middle;
    }

    .entry-table.border-table.image-table td > img {
        width: auto !important;
        height: 80px !important;
        vertical-align: middle;
    }
</style>