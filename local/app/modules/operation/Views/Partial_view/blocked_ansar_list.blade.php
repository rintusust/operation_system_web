<table class="table table-bordered">
    <tr>
        <th>Ansar ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth Date</th>
        <th>Home District</th>
        <th>Thana</th>
        <th>Block Reason</th>
        <th>Block Date</th>
    </tr>
    <tbody>
    <tr ng-repeat="ansar in data.ansars">
        <td><a href="{{URL::to('HRM/entryreport')}}/[[ansar.id]]">[[ansar.id]]</a></td>
        <td>[[ansar.rank]]</td>
        <td>[[ansar.name]]</td>
        <td>[[ansar.birth_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td>[[ansar.unit]]</td>
        <td>[[ansar.thana]]</td>
        <td>[[ansar.comment_for_block]]</td>
        <td>[[ansar.date_for_block|dateformat:"DD-MMM-YYYY"]]</td>
    </tr>
    <tr ng-if="data.ansars.length<=0">
        <td class="warning" colspan="9">No Ansar Found</td>
    </tr>
    </tbody>
</table>