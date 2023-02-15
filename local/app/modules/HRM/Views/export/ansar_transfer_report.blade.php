<table class="table table-bordered full">
    <tr>
        <th>SL</th>
        <th>Ansar ID</th>
        <th>Ansar Name</th>
        <th>Rank</th>
        <th>Home District</th>
        <th>KPI Name (From)</th>
        <th>KPI Name (To)</th>
        <th>Transfer Date</th>
        <th>Service Days</th>
    </tr>
    @forelse($ansars as $a)
        <tr ng-repeat="a in ansars.ansars">
            <td>{{$index++}}</td>
            <td>{{$a->ansar_id}}</td>
            <td>{{$a->name}}</td>
            <td>{{$a->rank}}</td>
            <td>{{$a->homeDistrict}}</td>
            <td>{{$a->presentKPI}}</td>
            <td>{{$a->transferedKPI}}</td>
            <td>{{\Carbon\Carbon::parse($a->t_date)->format('d-M-Y')}}</td>
            <td>{{$a->service_time}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="warning">
                No Ansar available
            </td>
        </tr>
    @endforelse
</table>