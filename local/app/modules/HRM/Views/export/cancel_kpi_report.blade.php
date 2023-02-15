
<table>
    <tr>
        <th>Sl. No.</th>
        <th>KPI Name</th>
        <th>Division</th>
        <th>Unit</th>
        <th>Thana</th>
        <th>Withdraw Status</th>
    </tr>

    @forelse($ansars as $kpi)
        <tr>
            <td>{{$index++}}</td>
            <td>{{$kpi->kpi_name}}</td>
            <td>
                {{$kpi->division}}
            </td>
            <td>{{$kpi->unit}}</td>
            <td>{{$kpi->thana}}</td>
            <td>
                @if($kpi->withdraw_status==1)
                    Already Withdraw
                @elseif($kpi->date != null)
                    Withdraw on {{$kpi->date}}
                @else
                    Inactive
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="warning">
                No Ansar available
            </td>
        </tr>
    @endforelse
</table>