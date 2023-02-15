<table class="table table-bordered">
    <tr>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        @if(UserPermission::userPermissionExists('entryverify'))
            <th class="print-hide">Action</th>
        @endif
    </tr>
    <tbody>
    <tr ng-repeat="ansar in data.ansars">
        <td><a href="{{URL::to('HRM/entryreport')}}/[[ansar.id]]">[[ansar.id]]</a></td>
        <td>[[ansar.rank]]</td>
        <td>[[ansar.name]]</td>
        <td>[[ansar.birth_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td>[[ansar.unit]]</td>
        <td>[[ansar.thana]]</td>
        @if(UserPermission::userPermissionExists('entryverify'))
            <td class="print-hide">
                <form action="{{URL::to('HRM/entryVerify/')}}" method="post" form-submit confirm-box="1"
                      message="Are you want to verify this Ansar?" loading="loading[$index]" on-reset="loadPage()">
                    <input type="hidden" value="[[ansar.id]]" name="verified_id">
                    <button class="btn btn-primary btn-xs" title="verify" ng-disabled="loading[$index]">
                        <i ng-hide="loading[$index]" class="fa fa-check"></i>
                        <i ng-show="loading[$index]" class="fa fa-spinner fa-pulse"></i>
                        &nbsp;Verify
                    </button>
                </form>
            </td>
        @endif
    </tr>
    <tr ng-if="data.ansars.length<=0">
        @if(UserPermission::userPermissionExists('entryverify'))
            <td class="warning" colspan="8">No Ansar Found</td>
        @else
            <td class="warning" colspan="8">No Ansar Found</td>
        @endif
    </tr>
    </tbody>
</table>