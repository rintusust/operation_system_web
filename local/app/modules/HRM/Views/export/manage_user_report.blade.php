
<table>
    <tr>
        <th>Sl. No.</th>
        <th>User Name</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile Number</th>
        <th>Rank</th>
        <th>Unit Name</th>
        <th>Division Name</th>
        <th>User Type</th>
		<th>Status</th>
    </tr>
    
    @forelse($ansars as $user)
        <tr ng-repeat="user in ansars">
            <td>{{$index++}}</td>
            <td>
                {{$user->user_name}}
            </td>
            <td>
                {{$user->first_name.' '.$user->last_name}} 
            </td>

            <td>{{$user->email}}</td>
            <td>{{$user->mobile_no}}</td>
            <td>{{$user->rank}}</td>
            <td>{{$user->unit_name_bng}}</td>
            <td>{{$user->division_name_bng}}</td>
            <td>{{$user->type_name}}</td>
			 <td>
			 @if($user->status == 1)
				 ACTIVE
			 @else
				 DEACTIVE
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