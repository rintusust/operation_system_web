<table class="table table-bordered">
    <tr>
        <th>SL</th>
        <th>Ansar<br>ID</th>
        <th>Rank</th>
        <th>Name</th>
        <th>Birth<br>Date</th>
        <th>Home<br>District</th>
        <th>Thana</th>
        <th ng-click="sortList('panel_date')">Global Panel<br>Date & Time</th>
        <th ng-click="sortList('re_panel_date')">Regional Panel<br>Date & Time</th>
        @if(Auth::user()->type==11)
            <th>Global<br>Position</th>
            <th>Regional<br>Position</th>
            <th>Offer<br>Count</th>
        @endif
    </tr>
    <tbody>
    <tr ng-repeat="ansar in data.ansars">
        <td>[[$index+data.index]]</td>
        <td style="text-align: center;width: 10px;"><a href="{{URL::to('HRM/entryreport')}}/[[ansar.id]]">[[ansar.id]]</a></td>
        <td style="text-align: center;width: 10px;">[[ansar.rank]]</td>
        <td style="text-align: center">[[ansar.name]]</td>
        <td style="text-align: center">[[ansar.birth_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td style="text-align: center">[[ansar.unit]]</td>
        <td style="text-align: center">[[ansar.thana]]</td>
        <td style="text-align: center">[[ansar.panel_date|dateformat:"DD-MMM-YYYY"]]</td>
        <td style="text-align: center">[[ansar.re_panel_date|dateformat:"DD-MMM-YYYY"]]</td>
        @if(Auth::user()->type==11)
            <td style="text-align: center;width: 10px;" ng-style="(ansar.locked==1 && ansar.last_offer_region=='RE' && ansar.go_panel_position >0)? {'background': 'red','color':'white'} : (ansar.go_panel_position==null) ? {'background': 'orange','color':'white'} : {'background': 'transparent'}">
                [[(ansar.go_panel_position==null)?'Offer Blocked':(ansar.sms_offer_district>0 && ansar.last_offer_region=='GB')?'Offered':(ansar.offered_district>0 && ansar.last_offer_region=='GB')?'Offer  Accepted':ansar.go_panel_position]]
            </td>
            <td style="text-align: center;width: 10px;" ng-style="(ansar.locked==1 && ansar.last_offer_region=='GB' && ansar.re_panel_position >0)? {'background': 'red','color':'white'} : (ansar.re_panel_position==null) ? {'background': 'orange','color':'white'} : {'background': 'transparent'}">
            
                [[(ansar.re_panel_position==null)?'Offer Blocked':(ansar.sms_offer_district>0 && ansar.last_offer_region=='RE')?'Offered':(ansar.offered_district>0 && ansar.last_offer_region=='RE')?'Offer Accepted':ansar.re_panel_position]]
            </td>
            <td style="text-align: center">[[ansar.offer_type.split('DG').join('GB').split('CG').join('GB')]]</td>
        @endif
    </tr>
    <tr ng-if="data.ansars.length<=0">
        <td class="warning" colspan="9">No Ansar Found</td>
    </tr>
    </tbody>
</table>