<div class="container-fluid">
    <img class="pull-right profile-image"
         src="<?php echo e(action('UserController@getImage',['file'=>$ansarAllDetails->profile_pic])); ?>"
         alt="">
    <table class="entry-table" style="width: 100%">
        <tr>
            <td>আইডি কার্ড নম্বর<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->ansar_id); ?></div>
            </td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold">বাক্তিগত ও পারিবারিক তথ্য</caption>
        <tr>
            <td>*Name<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->ansar_name_eng); ?></div>
            </td>
        </tr>
        <tr>
            <td>*নাম<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->ansar_name_bng); ?></div>
            </td>
        </tr>
        <tr>
            <td>*বর্তমান পদবী <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->designation->name_bng); ?></div>
            </td>
        </tr>
        <tr>
            <td>*Father's name <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->father_name_eng); ?></div>
            </td>
        </tr>
        <tr>
            <td>*পিতার নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->father_name_bng); ?></div>
            </td>
        </tr>
        <tr>
            <td>*Mother's Name <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->mother_name_eng); ?></div>
            </td>
        </tr>
        <tr>
            <td>*মাতার নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->mother_name_bng); ?></div>
            </td>
        </tr>
        <tr>
            <td>*Date of birth <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e(\Carbon\Carbon::parse($ansarAllDetails->data_of_birth)->format("d-m-Y")); ?></div>
            </td>
        </tr>
        <tr>
            <td>*Marital status <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e(strcasecmp($ansarAllDetails->marital_status,"married")==0?"বিবাহিত":(strcasecmp($ansarAllDetails->marital_status,"unmarried")==0?"অবিবাহিত":"তালাকপ্রাপ্ত")); ?></div>
            </td>
        </tr>
        <tr>
            <td>*Spouse Name <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->spouse_name_eng?$ansarAllDetails->spouse_name_eng: '&nbsp;'); ?></div>
            </td>
        </tr>
        <tr>
            <td>*স্ত্রী/স্বামীর নাম <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e(isset($ansarAllDetails->spouse_name_bng) ? $ansarAllDetails->spouse_name_bng : ' '); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>*National Id no <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->national_id_no?$ansarAllDetails->national_id_no:'&nbsp;'); ?></div>
            </td>
        </tr>
        <tr>
            <td>*Birth Certificate no <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->birth_certificate_no?$ansarAllDetails->birth_certificate_no:' '); ?></div>
            </td>
        </tr>
        <tr>
            <td>দীর্ঘ মেয়াদি অসুখ <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e(isset($ansarAllDetails->alldisease->disease_name_bng) ? $ansarAllDetails->alldisease->disease_name_bng : $ansarAllDetails->own_disease?$ansarAllDetails->own_disease:'&nbsp;'); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>নির্দিষ্ট দক্ষতা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e(($ansarAllDetails && isset($ansarAllDetails->allskill) && isset($ansarAllDetails->allskill->skill_name_bng))? $ansarAllDetails->allskill->skill_name_bng:""); ?></div>
            </td>
        </tr>
        <tr>
            <td>Criminal Case<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->criminal_case?$ansarAllDetails->criminal_case:"&nbsp;"); ?></div>
            </td>
        </tr>
        <tr>
            <td>ফৌজদারি মামলা<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->criminal_case_bng?$ansarAllDetails->criminal_case_bng:"&nbsp;"); ?></div>
            </td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold">স্থায়ী ঠিকানা</caption>
        <tr>
            <td>Village/House No<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->village_name); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>গ্রাম/বাড়ি নং<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->village_name_bng); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>Road No <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e(isset($ansarAllDetails->road_no) ? $ansarAllDetails->road_no : '&nbsp;'); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>Post office <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->post_office_name); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>ডাকঘর <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->post_office_name_bng); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>Union/Word <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->union_name_eng); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>ইউনিয়ন নাম/ওয়ার্ড <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->union_name_bng); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>*বিভাগ <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->division->division_name_bng); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>*জেলা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->district->unit_name_bng); ?></div>
            </td>
        </tr>
        <tr>
            <td>*থানা <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->thana->thana_name_bng); ?></div>
            </td>
        </tr>
    </table>
    <table class="entry-table" style="width: 100%">
        <caption style="text-align: center;font-size: 1em;font-weight: bold">শারীরিক যোগ্যতার তথ্য</caption>
        <tr>
            <td>*Height(উচ্চতা)<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:0 5px;font-size:14px;border:1px solid #ababab">
                    <span style="padding: 5px 20px"><?php echo e($ansarAllDetails->hight_feet); ?></span>
                    <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ফিট</span>
                    <span style="padding: 5px 20px"><?php echo e($ansarAllDetails->hight_inch); ?></span>
                    <span style="padding: 0 5px;border: 1px solid #ababab;border-top: none;border-bottom: none">ইঞ্চি</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>*রক্তের গ্রুপ<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e(($ansarAllDetails && isset($ansarAllDetails->blood) && isset($ansarAllDetails->blood->blood_group_name_bng))? $ansarAllDetails->blood->blood_group_name_bng:""); ?></div>
            </td>
        </tr>
        <tr>
            <td>Eye color<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->eye_color); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>চোখের রং <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->eye_color_bng); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>Skin color<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->skin_color); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>গায়ের রং<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->skin_color_bng); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>*Gender<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->sex); ?>&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>Identification mark <span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->identification_mark); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td>সনাক্তকরন চিহ্ন<span class="pull-right">:</span></td>
            <td style="padding-left: 20px">
                <div style="padding:5px;font-size:14px;border:1px solid #ababab"><?php echo e($ansarAllDetails->identification_mark_bng); ?>

                    &nbsp;
                </div>
            </td>
        </tr>
    </table>
    <table class="entry-table border-table">
        <caption>Educational Information*</caption>
        <tbody>
        <tr>
            <td><b>Education Qualification</b></td>
            <td><b>Institute Name</b></td>
            <td><b>Passing Year</b></td>
            <td><b>Division/Grade</b></td>
        </tr>
        <?php foreach($ansarAllDetails->education as $singleeducation): ?>
            <tr>
                <td><?php echo e($singleeducation->educationName->education_deg_eng); ?></td>
                <td><?php echo e($singleeducation->institute_name_eng); ?></td>
                <td><?php echo e(isset($singleeducation->passing_year) ? $singleeducation->passing_year : LanguageConverter::engToBng($singleeducation->passing_year)); ?></td>
                <td><?php echo e($singleeducation->gade_divission); ?></td>
            </tr>
        <?php endforeach; ?>
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
        <?php foreach($ansarAllDetails->education as $singleeducation): ?>
            <tr>
                <td><?php echo e($singleeducation->educationName->education_deg_bng); ?></td>
                <td><?php echo e($singleeducation->institute_name); ?></td>
                <td><?php echo e(LanguageConverter::engToBng($singleeducation->passing_year)); ?></td>
                <td><?php echo e($singleeducation->gade_divission); ?></td>
            </tr>
        <?php endforeach; ?>
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
        <?php foreach($ansarAllDetails->training as $singletraining): ?>
            <tr>
                <td><?php echo e($singletraining->rank->name_eng); ?></td>
                <td><?php echo e($singletraining->training_institute_name_eng); ?></td>
                <td>[['<?php echo e($singletraining->training_start_date); ?>'|dateformat:"DD-MMM-YYYY"]]
                <td>[['<?php echo e($singletraining->training_end_date); ?>'|dateformat:"DD-MMM-YYYY"]]</td>
                <td><?php echo e($singletraining->trining_certificate_no); ?></td>
            </tr>
        <?php endforeach; ?>
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
        <?php foreach($ansarAllDetails->training as $singletraining): ?>
            <tr>
                <td><?php echo e($singletraining->rank->name_bng); ?></td>
                <td><?php echo e($singletraining->training_institute_name); ?></td>
                <td>[['<?php echo e($singletraining->training_start_date); ?>'|dateformat:"DD-MMMM-YYYY":"bn"]]
                <td>[['<?php echo e($singletraining->training_end_date); ?>'|dateformat:"DD-MMMM-YYYY":"bn"]]</td>
                <td><?php echo e($singletraining->trining_certificate_no); ?></td>
            </tr>
        <?php endforeach; ?>
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
        <?php foreach($ansarAllDetails->nominee as $singlenominee): ?>
            <tr>
                <td><?php echo e($singlenominee->name_of_nominee_eng); ?></td>
                <td><?php echo e($singlenominee->relation_with_nominee_eng); ?></td>
                <td><?php echo e($singlenominee->nominee_parcentage_eng); ?></td>
                <td><?php echo e($singlenominee->nominee_contact_no); ?></td>
            </tr>
        <?php endforeach; ?>
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
        <?php foreach($ansarAllDetails->nominee as $singlenominee): ?>
            <tr>
                <td><?php echo e($singlenominee->name_of_nominee); ?></td>
                <td><?php echo e($singlenominee->relation_with_nominee); ?></td>
                <td><?php echo e($singlenominee->nominee_parcentage); ?></td>
                <td><?php echo e(LanguageConverter::engToBng($singlenominee->nominee_contact_no)); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


    <table class="entry-table other-table">
        <caption>অন্যান্য তথ্য</caption>
        <tr>
            <td>Mobile No. (Self) নিজ* <span class="pull-right">:</span></td>
            <td>
                <div style="font-size:14px;"><?php echo e($ansarAllDetails->mobile_no_self); ?></div>
            </td>
        </tr>
        <tr>
            <td>Mobile No. (Alternative) <span class="pull-right">:</span></td>
            <td>
                <div style="font-size:14px;"><?php echo e($ansarAllDetails->mobile_no_request); ?></div>
            </td>
        </tr>
        <tr>
            <td>Email (Self) <span class="pull-right">:</span></td>
            <td>
                <div style="padding:5px;font-size:14px;"><?php echo e($ansarAllDetails->email_self); ?></div>
            </td>
        </tr>
    </table>
    <table class="entry-table border-table image-table">
        <tr>
            <td>তথ্য প্রদানকারীরস্বাক্ষর</td>
            <td>বাম হাতের বৃদ্ধা আঙ্গুলের ছাপ</td>
        </tr>
        <tr>
            <td><img src="<?php echo e(URL::route('sign_image',['id'=>$ansarAllDetails->ansar_id])); ?>"/></td>
            <td><img src="<?php echo e(URL::route('thumb_image',['id'=>$ansarAllDetails->ansar_id])); ?>"/></td>
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