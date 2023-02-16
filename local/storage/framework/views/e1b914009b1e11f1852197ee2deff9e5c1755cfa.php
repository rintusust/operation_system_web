<?php $__env->startSection('title','Offer Report'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('offer_report'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('ReportGuardSearchController', function ($scope, $http, $sce) {
            $scope.isDc = parseInt('<?php echo e(Auth::user()->type); ?>') == 22 ? true : false
            $scope.districts = [];
            $scope.unit = {
                selectedDistrict: "",
                custom: "",
                type: "1"
            };
            $scope.customData = {
                "Today": 1,
                "Past 2 days": 2,
                "Past 3 days": 3,
                "Past 5 days": 5,
                "Past 7 days": 7,
                "Custom": -1,
            }
            $scope.total = 0;
            $scope.ansars = [];
            $scope.onr = [];
            $scope.or = [];
            $scope.orj = [];
            $scope.loadingUnit = false;
            $scope.report = {};
            $scope.selectedDate = "1"
            $scope.reportType = 'eng';
            $scope.errorFind = 0;
            $scope.allLoading = false;
            $scope.loadAnsar = function (t) {
                $scope.allLoading = true;
                var data = {};
                if ($scope.selectedDate == -1) {
                    data = {
                        unit: $scope.params.unit,
                        division: $scope.params.range,
                        report_past: isNaN(parseInt($scope.unit.custom)) ? 0 : $scope.unit.custom,
                        type: $scope.unit.type,
                        gender: $scope.params.gender,
                        rank: $scope.params.rank,
                        tab: $scope.params.tab
                    }
                } else {
                    data = {
                        unit: $scope.params.unit,
                        division: $scope.params.range,
                        report_past: $scope.selectedDate,
                        type: 0,
                        gender: $scope.params.gender,
                        rank: $scope.params.rank,
                        tab: $scope.params.tab
                    }
                }
                data['export'] = t || false
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::route('get_offered_ansar')); ?>',
                    params: data
                }).then(function (response) {
                    $scope.allLoading = false;
                    if (response.data.status) {
                        window.open(response.data.url, '_blank');
                        return;
                    }
                    $scope.errorFind = 0;
                    $scope.onr = response.data.onr
                    $scope.or = response.data.or
                    $scope.orj = response.data.orj
                    $scope.total = response.data.total;

                }, function (response) {
                    $scope.errorFind = 1;
                    $scope.onr = []
                    $scope.or = []
                    $scope.orj = []
                    $scope.errorMessage = $sce.trustAsHtml("<tr class='warning'><td colspan='" + $('.table').find('tr').find('th').length + "'>" + response.data + "</td></tr>");
                    $scope.allLoading = false;
                })
            }
            $scope.changeRank = function (i,j) {
                //echo "anik";exit;
                //alert(i);return;
                $scope.params.rank = i;
                $scope.params.tab = j;
                $scope.loadAnsar()
            }
            $scope.loadReportData = function (reportName, type) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::route('localize_report')); ?>',
                    params: {name: reportName, type: type}
                }).then(function (response) {
                    console.log(response.data)
                    $scope.report = response.data;
                    $scope.allLoading = false;
                })
            }
            $scope.dateConvert = function (date) {
                return (moment(date).format('DD-MMM-Y'));
            }
            $scope.loadReportData("ansar_in_guard_report", "eng")

        })
        $(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                $('#print-guard-in-ansar-report table tr td a').each(function () {
                    var v = $(this).text();
                    $(this).parents('td').append('<span>' + v + '</span>')
                    $(this).css('display', 'none')
                })
                $('body').append('<div id="print-area">' + $("#print-guard-in-ansar-report").html() + '</div>')
                window.print();
                $('#print-guard-in-ansar-report table tr td a').each(function () {
                    $(this).parents('td').children('span').remove()
                    $(this).css('display', 'block')
                })
                $("#print-area").remove()
            })
        })
    </script>
    <div ng-controller="ReportGuardSearchController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                <span class="fa">
                    <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                </span>
                </div>
                <div class="box-body">
                    <div class="pull-right">
                        <span class="control-label" style="padding: 5px 8px">

                            View report in&nbsp;&nbsp;&nbsp;<input type="radio" class="radio-inline"
                                                                   style="margin: 0 !important;" value="eng"
                                                                   ng-change="loadReportData('ansar_in_guard_report',reportType)"
                                                                   ng-model="reportType">&nbsp;<b>English</b>
                            &nbsp;<input type="radio" ng-change="loadReportData('ansar_in_guard_report',reportType)"
                                         class="radio-inline" style="margin: 0 !important;" value="bng"
                                         ng-model="reportType">&nbsp;<b>বাংলা</b>
                        </span>
                    </div>
                    <br>
                    <filter-template
                            show-item="['range','unit','gender']"
                            type="single"
                            start-load="range"
                            field-width="{range:'col-sm-4',unit:'col-sm-4',gender:'col-sm-4',custom:'col-sm-4'}"
                            data="params"
                            custom-field="true"
                            custom-model="selectedDate"
                            custom-label="Select an Option"
                            custom-data="customData"
                    ></filter-template>
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-8">
                            <div class="form-group row" ng-if="selectedDate==-1">
                                <div class="col-xs-7">
                                    <input type="text" class="form-control" ng-model="unit.custom"
                                           placeholder="No of day,month or year" style="margin-left: -200%;margin-top: -28%;">
                                </div>
                                <div class="col-xs-5" style="padding-left: 0;">
                                    <select class="form-control" ng-model="unit.type" style="margin-top: -37%;margin-left: -255%;">
                                        <option value="1">Days</option>
                                        <option value="2">Months</option>
                                        <option value="3">Years</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-sm-offset-6">
                            <div class="form-control" style="padding: 0;border:none;">

                                <button class="btn btn-primary pull-right" ng-click="loadAnsar(false)"><i
                                            class="fa fa-eye"></i>&nbsp;View Offer Report
                                </button>
                                <button class="btn btn-primary pull-right" ng-click="loadAnsar(true)"><i
                                            class="fa fa-download"></i>&nbsp;Download Offer Report
                                </button>
                                <button id="print-report" class="btn btn-default pull-right" style="margin-right:5px;"><i
                                            class="fa fa-print"></i>&nbsp;Print
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="print-guard-in-ansar-report" style="margin-top: 10px">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a data-toggle="tab" href="#offer_not_respond">Offer Not Responded</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#offer_send">Offer Accepted</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#offer_reject">Offer Rejected</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="offer_not_respond" class="tab-pane active">
                                    <?php /* <h4 class="text text-bold">
                                        PC([[onr.count.PC? onr.count.PC:0]]) APC([[onr.count.APC?onr.count.APC:0]])
                                        Ansar([[onr.count.ANSAR?onr.count.ANSAR:0]])
                                    </h4> */ ?>
                                    <h4 class="text text-bold">
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank('',1)">All </a>&nbsp;
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(3,1)">PC
                                            ([[onr.count.PC? onr.count.PC:0]])</a>&nbsp;
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(2,1)">APC
                                            ([[onr.count.APC?onr.count.APC:0]])</a>&nbsp;
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(1,1)">Ansar
                                            ([[onr.count.ANSAR?onr.count.ANSAR:0]])</a>
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>SL. No</th>
                                                <th>Ansar ID</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Memorandum</th>
                                                <th>Home District</th>
                                                <th>Offered District</th>
                                                <th>Offered Date</th>
                                            </tr>
                                            <tr ng-if="onr.data.length<=0&&errorFind==0">
                                                <th class="warning" colspan="5">No Ansar Found</th>
                                            </tr>
                                            <tbody ng-if="errorFind==1&&onr.length<=0"
                                                   ng-bind-html="errorMessage"></tbody>
                                            <tr ng-if="onr.data.length>0&&errorFind==0" ng-repeat="a in onr.data">
                                                <td>[[$index+1]]</td>
                                                <td>[[a.ansar_id]]</td>
                                                <td>[[a.ansar_name_eng]]</td>
                                                <td>[[a.code]]</td>
                                                <td>[[a.memo_id]]</td>
                                                <td>[[a.home_district]]</td>
                                                <td>[[a.unit_name_bng]]</td>
                                                <td>[[a.sms_send_datetime|dateformat:'DD-MMM-YYYY hh:mm:ss A']]</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div id="offer_send" class="tab-pane">
                                    <?php /* <h4 class="text text-bold">
                                        PC([[or.count.PC?or.count.PC:0]]) APC([[or.count.APC?or.count.APC:0]])
                                        Ansar([[or.count.ANSAR?or.count.ANSAR:0]])
                                    </h4> */ ?>
                                    <h4 class="text text-bold">
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank('',2)">All </a>&nbsp;
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(3,2)">PC
                                            ([[or.count.PC? or.count.PC:0]])</a>&nbsp;
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(2,2)">APC
                                            ([[or.count.APC?or.count.APC:0]])</a>&nbsp;
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(1,2)">Ansar
                                            ([[or.count.ANSAR?or.count.ANSAR:0]])</a>
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>SL. No</th>
                                                <th>Ansar ID</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Memorandum</th>
                                                <th>Home District</th>
                                                <th>Offered Date</th>
                                                <th>Offer Accepted Date</th>
                                            </tr>
                                            <tr ng-if="or.data.length<=0&&errorFind==0">
                                                <th class="warning" colspan="5">No Ansar Found</th>
                                            </tr>
                                            <tbody ng-if="errorFind==1&&or.length<=0"
                                                   ng-bind-html="errorMessage"></tbody>
                                            <tr ng-if="or.data.length>0&&errorFind==0" ng-repeat="a in or.data">
                                                <td>[[$index+1]]</td>
                                                <td>[[a.ansar_id]]</td>
                                                <td>[[a.ansar_name_eng]]</td>
                                                <td>[[a.code]]</td>
                                                <td>[[a.memo_id]]</td>
                                                <td>[[a.home_district]]</td>
                                                <td>[[a.offered_date|dateformat:'DD-MMM-YYYY hh:mm:ss A']]</td>
                                                <td>[[a.sms_received_datetime|dateformat:'DD-MMM-YYYY hh:mm:ss A']]</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div id="offer_reject" class="tab-pane">
                                    <?php /* <h4 class="text text-bold">
                                        PC([[orj.count.PC?orj.count.PC:0]]) APC([[orj.count.APC?orj.count.APC:0]])
                                        Ansar([[orj.count.ANSAR?orj.count.ANSAR:0]])
                                    </h4> */ ?>
                                    <h4 class="text text-bold">
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank('',3)">All </a>&nbsp;
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(3,3)">PC
                                            ([[orj.count.PC? orj.count.PC:0]])</a>&nbsp;
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(2,3)">APC
                                            ([[orj.count.APC?orj.count.APC:0]])</a>&nbsp;
                                        <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(1,3)">Ansar
                                            ([[orj.count.ANSAR?orj.count.ANSAR:0]])</a>
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>SL. No</th>
                                                <th>Ansar ID</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Memorandum</th>
                                                <th>Home District</th>
                                                <th>Offered Date</th>
                                                <th>Reject Date</th>
                                            </tr>
                                            <tr ng-if="orj.length<=0&&errorFind==0">
                                                <th class="warning" colspan="5">No Ansar Found</th>
                                            </tr>
                                            <tbody ng-if="errorFind==1&&orj.data.length<=0"
                                                   ng-bind-html="errorMessage"></tbody>
                                            <tr ng-if="orj.data.length>0&&errorFind==0" ng-repeat="a in orj.data">
                                                <td>[[$index+1]]</td>
                                                <td>[[a.ansar_id]]</td>
                                                <td>[[a.ansar_name_eng]]</td>
                                                <td>[[a.code]]</td>
                                                <td>[[a.memo_id]]</td>
                                                <td>[[a.home_district]]</td>
                                                <td>[[a.offered_date|dateformat:'DD-MMM-YYYY hh:mm:ss A']]</td>
                                                <td>[[a.reject_date|dateformat:'DD-MMM-YYYY hh:mm:ss A']]</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>