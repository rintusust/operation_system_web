<table>

        @foreach($headers as $header)
        <tr>
            @foreach($header as $h)
                <th>{{$h}}</th>
            @endforeach
        </tr>
        @endforeach

    @foreach($error_datas as $error_data)
        <tr>
            @foreach($error_data["dd"] as $key=>$data)
                @if(in_array($key,$error_data["err"]))
                    <td style="background-color: #ff000f !important;color:#ffffff !important;">{{$data}}</td>
                @else
                    <td>{{$data}}</td>
                @endif
            @endforeach
        </tr>
    @endforeach
</table>