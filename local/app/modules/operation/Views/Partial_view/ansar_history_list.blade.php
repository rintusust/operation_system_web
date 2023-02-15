<style>
    .font-bolddd *{
        font-weight: bold !important;
        font-size: 17px;
    }
</style>

<div ng-if="!data.apid">
    <h4 style="text-align: center">No Ansar is available to show</h4>
</div>
<div ng-if="data.apid">
    <div class="form-group font-bolddd">
        <div class="col-sm-12 col-xs-12 col-centered">
            <div class="table-responsive">
                <table class="table table-bordered" style="margin: 0 auto;width: auto !important;">
                    <tr>
                        <td rowspan="12"  style="vertical-align: middle;width: 130px;height: 150px;background: #ffffff">
                            <img  style="width: 120px;height: 150px" src="{{URL::to('image').'?file='}}[[data.apid.profile_pic]]" alt="">
                        </td>
                        <th style="background: #ffffff">ID</th>
                        <td style="background: #ffffff">[[data.apid.ansar_id]]</td>
                    </tr>
                    <tr>

                        <th style="background: #ffffff">Name</th>
                        <td style="background: #ffffff">[[data.apid.ansar_name_bng]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Rank</th>
                        <td style="background: #ffffff">[[data.apid.name_bng]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Blood Group</th>
                        <td style="background: #ffffff">[[data.apid.blood_group_name_bng]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Mobile No.</th>

                        <td style="background: #ffffff">[[data.apid.mobile_no_self|checkpermission:"view_mobile_no":"embodied":data.apid.ansar_id ]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Home District</th>
                        <td style="background: #ffffff">[[data.apid.unit_name_bng]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Home Thana</th>
                        <td style="background: #ffffff">[[data.apid.thana_name_bng]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Date of birth</th>
                        <td style="background: #ffffff">[[data.apid.dob]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Age</th>
                        <td style="background: #ffffff">[[data.apid.age]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Account No</th>
                        <td style="background: #ffffff">[[data.apid.prefer_choice=='general'?data.apid.account_no:data.apid.mobile_bank_account_no]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">Bank name/Mobile account type</th>
                        <td style="background: #ffffff">[[data.apid.prefer_choice=='general'?data.apid.bank_name:data.apid.mobile_bank_type]]</td>
                    </tr>
                    <tr>
                        <th style="background: #ffffff">AVUB Share ID</th>
                        <td style="background: #ffffff">[[data.apid.avub_share_id]]</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group font-bolddd">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <caption>পানেল্ভুক্তির ও অফারের বিবরণ</caption>
                    <tr>
                        <td class="text-center">গ্লোবাল<br>প্যানেলভুক্তি তারিখ</td>
                        <td class="text-center">রিজিওনাল<br>প্যানেলভুক্তি তারিখ</td>
                        <td class="text-center">প্যানেল স্মারক নং</td>
                        <td class="text-center">বর্তমান অবস্থা</td>
                        <td class="text-center">অফারের তারিখ</td>
                        <td class="text-center">অফারের জেলা</td>
                        <td class="text-center">অফার বাতিলের তারিখ</td>
                    </tr>
                    <tr>
                        <td class="text-center">[[data.api.panel_date?(data.api.panel_date|dateformat:'DD-MMMM-YYYY':'bn'):"--"]]</td>
                        <td class="text-center">[[data.api.re_panel_date?(data.api.re_panel_date|dateformat:'DD-MMMM-YYYY':'bn'):"--"]]</td>
                        <td class="text-center">[[data.api.memorandum_id?data.api.memorandum_id:"--"]]</td>
                        <td class="text-center">
                            <span>[[data.status]]</span>
                            <span ng-if="data.apid.go_offer_count>=3">(গ্লোবাল ব্লক)</span>
                            <span ng-if="data.apid.re_offer_count>=3">(রিজিওনাল ব্লক)</span>
                        </td>
                        <td class="text-center">[[data.aod.offerDate?(data.aod.offerDate|dateformat:'DD-MMMM-YYYY':'bn'):'--']] ([[data.offer_zone]])</td>
                        <td class="text-center">[[data.aod.offerUnit?data.aod.offerUnit:'--']]</td>
                        <td class="text-center">[[data.aoci.offerCancel?(data.aoci.offerCancel|dateformat:'DD-MMMM-YYYY':'bn'):'--']]</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group font-bolddd">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <td style="background: #ffffff">
                            <table class="table table-bordered">
                                <caption>সর্বশেষ অঙ্গিভুতির বিবরণ</caption>
                                <tr>
                                    <td class="text-center">অঙ্গিভুতির  তারিখ</td>
                                    <td class="text-center">অঙ্গিভুতির স্মারক নং</td>
                                    <td class="text-center">জেলার নাম</td>
                                    <td class="text-center">থানার নাম</td>
                                    <td class="text-center">অঙ্গিভুতির সংস্থা</td>
                                </tr>
                                <tr>
                                    <td class="text-center">[[data.aei.joining_date?(data.aei.joining_date|dateformat:'DD-MMMM-YYYY':'bn'):"--"]]</td>
                                    <td class="text-center">[[data.aei.memorandum_id?data.aei.memorandum_id:"--"]]</td>
                                    <td class="text-center">[[data.aei.unit_name_bng?data.aei.unit_name_bng:"--"]]</td>
                                    <td class="text-center">[[data.aei.thana_name_bng?data.aei.thana_name_bng:"--"]]</td>
                                    <td class="text-center">[[data.aei.kpi_name?data.aei.kpi_name:"--"]]</td>
                                </tr>
                            </table>
                        </td>
                        <td style="background: #ffffff">
                            <table class="table table-bordered">
                                <caption>সর্বশেষ অ-অঙ্গিভুতির বিবরণ</caption>
                                <tr>
                                    <td class="text-center">অ-অঙ্গিভুতির  তারিখ</td>
                                    <td class="text-center">অ-অঙ্গিভুতির কারন</td>
                                </tr>
                                <tr>
                                    <td class="text-center">[[data.adei.disembodiedDate?(data.adei.disembodiedDate|dateformat:'DD-MMMM-YYYY':'bn'):"--"]]</td>
                                    <td class="text-center">[[data.adei.disembodiedReason?data.adei.disembodiedReason:"--"]]</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group font-bolddd">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <caption>বর্তমান কর্মস্থলের বিবরণ</caption>
                    <tr>
                        <td class="text-center">যোগদানের<br> তারিখ</td>
                        <td class="text-center">স্মারক নং</td>
                        <td class="text-center">জেলার নাম</td>
                        <td class="text-center">থানার নাম</td>
                        <td class="text-center">বর্তমান সংস্থা</td>
                    </tr>
                    <tr>
                        <td class="text-center">[[data.acei.transfered_date?(data.acei.transfered_date|dateformat:'DD-MMMM-YYYY':'bn'):"--"]]</td>
                        <td class="text-center">[[data.acei.memorandum_id?data.acei.memorandum_id:"--"]]</td>
                        <td class="text-center">[[data.acei.unit_name_bng?data.acei.unit_name_bng:"--"]]</td>
                        <td class="text-center">[[data.acei.thana_name_bng?data.acei.thana_name_bng:"--"]]</td>
                        <td class="text-center">[[data.acei.kpi_name?data.acei.kpi_name:"--"]]</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>