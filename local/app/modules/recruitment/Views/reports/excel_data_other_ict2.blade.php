<table style="width: 100%" border="1">
    <tr>
        <th colspan="9" align="center" valign="center">
            বাংলাদেশ আনসার ও গ্রাম প্রতিরক্ষা বাহিনী
        </th>
    </tr>
    <tr>
        <th colspan="9" align="center" valign="center">
            সদর দপ্তর, খিলগাঁও, ঢাকা।
        </th>
    </tr>
    <tr>
        <th colspan="9" align="center" valign="center">
            {{$applicants[0]->circular->circular_name}} পদের লিখিত ও মৌখিক পরীক্ষার প্রাপ্ত নম্বরের তালিকা
        </th>
    </tr>
    <tr>
        <th>ক্রমিক নং</th>
        <th>রোল নং</th>
        <th>প্রার্থীর নাম, পিতার নাম ও মাতার নাম</th>
        <th>জেলা</th>
        <th>কোটা</th>
        <th>লিখিত পরীক্ষার নম্বর</th>
        <th>মৌখিক পরীক্ষার নম্বর</th>
        <th>মোট নম্বর</th>
        <th>মন্তব্য</th>
    </tr>

    @if(count($applicants))
        @foreach($applicants as $a)
            <?php $pic =$a->profile_pic ?>
            <tr>
                <td style="width: 10px">{{($index++).''}}</td>
                <td>{{$a->roll_no}}</td>
                <td>{{$a->applicant_name_bng}},
                    {{$a->father_name_bng}},
                    {{$a->mother_name_bng}}
                </td>
                <td>{{$a->district->unit_name_bng}}</td>
                <td>{{$a->circular_applicant_quota_id?$a->circularQuota->quota_name_bng:'N\A'}}</td>
                <td></td>
                <td></td>
                <td></td>
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