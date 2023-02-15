<table style="width: 100%" border="1">
    <caption>Monthly savings</caption>

    <tr>
        <th colspan="9" align="left" style="margin-left: 100px;">
             হাজিরা শিট
        </th>
    </tr>
    <tr>
        <th colspan="9" align="left" style="margin-left: 100px;">
        কেন্দ্রের নামঃ
        </th>
    </tr>
    <tr>
        <th colspan="9" align="left" style="margin-left: 100px;">
           পদের নামঃ {{$applicants[0]->circular->circular_name}}
        </th>
    </tr>
    <tr>
        <th colspan="9" align="left" style="margin-left: 100px;">
            কক্ষ নম্বরঃ
        </th>
    </tr>
    <tr>
        <th colspan="9" align="left" style="margin-left: 100px;">
            জেলাঃ {{$applicants[0]->district->unit_name_bng}}
        </th>
    </tr>
    <tr>
        <th style="width: 10px">ক্রমিক নং</th>
        <th>রোল নং</th>
        <th>প্রার্থীর নাম, পিতার নাম</th>
        <th>জেলা</th>
        <th>আবেদনে প্রদত্ত স্বাক্ষর</th>
        <th>ছবি</th>
        <th>স্বাক্ষর</th>
    </tr>


    @if(count($applicants))
        @foreach($applicants as $a)
            <?php $pic =$a->profile_pic;
             $signature_pic =$a->signature_pic; ?>

            <tr>
                <td style="width: 10px">{{($index++).''}}</td>
                <td>{{$a->roll_no}}</td>
                <td>{{$a->applicant_name_bng}}<br>
                    {{$a->father_name_bng}}
                <td>{{$a->district->unit_name_bng}}</td>
                <td>@if($signature_pic&&file_exists($signature_pic)&&getimagesize($signature_pic))<img src="{{$signature_pic}}" width="100" height="100">@endif</td>
                <td>@if($pic&&file_exists($pic)&&getimagesize($pic))<img src="{{$pic}}" width="100" height="100">@endif</td>
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