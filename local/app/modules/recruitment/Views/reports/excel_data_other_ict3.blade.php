<table style="width: 100%" border="1">
    <tr>
        <th style="width: 10px">ক্রমিক নং</th>
        <th>আইডি নং</th>
        <th>রোল নং</th>
        <th>নাম, পিতার নাম ও <br>মাতার নাম</th>
        <th>জাতীয়<br> পরিচয়পত্র নং</th>
        <th>গ্রাম, ডাকঘর ও <br>থানা/উপজেলা</th>
        <th>জেলা</th>
        <th>বিভাগ</th>
        <th>মোবাইল নং</th>
        <th>শিক্ষাগত<br> যোগ্যতা</th>
        <th>জন্ম তারিখ</th>
        <th>প্রশিক্ষকদের <br>ক্ষেত্রে উচ্চতা</th>
        <th>ছবি</th>
        <th colspan="4" align="center">কোটা</th>
        <th>মন্তব্য</th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>এতিম ও<br> শারিরীক <br>প্রতিবন্ধী</th>
        <th>মুক্তিযোদ্ধা/মুক্তিযোদ্ধার<br> সন্তান/মুক্তিযোদ্ধার <br>নাতি-নাতনী</th>
        <th>ক্ষুদ্র নৃ-গোষ্ঠী</th>
        <th>আনসার-ভিডিপি</th>
        <th></th>

    </tr>

    @if(count($applicants))
        @foreach($applicants as $a)
            <?php $pic =$a->profile_pic ?>
            <tr>
                <td style="width: 10px">{{($index++).''}}</td>
                <td>{{$a->applicant_id}}</td>
                <td>{{$a->roll_no}}</td>
                <td>{{$a->applicant_name_bng}},
                    {{$a->father_name_bng}},
                    {{$a->mother_name_bng}},
                <td>{{$a->national_id_no}}</td>
                <td>{{$a->village_name_bng}},
                    {{$a->post_office_name_bng}},
                    {{$a->thana->thana_name_bng}}
                </td>
                <td>{{$a->district->unit_name_bng}}</td>
                <td>{{$a->division->division_name_bng}}</td>
                <td>{{$a->mobile_no_self}}</td>
                <th>{{$a->education()->orderBy('priority','desc')->first()->education_deg_eng}}</th>
                <td>{{$a->date_of_birth}}</td>
                <td>@if($a->job_circular_id == 138 || $a->job_circular_id == 139){{$a->height_feet}} feet {{$a->height_inch}} inch @endif</td>
                <td>@if($pic&&file_exists($pic)&&getimagesize($pic))<img src="{{$pic}}" width="100" height="100">@endif</td>
                <td>@if(($a->circular_applicant_quota_id == 8) ||($a->circular_applicant_quota_id == 12))<b>YES</b>@endif</td>
                <td>@if(($a->circular_applicant_quota_id == 7) ||($a->circular_applicant_quota_id == 13)||($a->circular_applicant_quota_id == 14))<b>YES</b>@endif</td>
                <td>@if($a->circular_applicant_quota_id == 9)<b>YES</b>@endif</td>
                <td>@if($a->circular_applicant_quota_id == 10)<b>YES</b>@endif</td>
                <td></td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="12" class="bg-warning">
                No applicants found
            </td>
        </tr>
    @endif

</table>