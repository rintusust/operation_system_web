@extends('template.master')
@section('title','View Ansar in Guard Report')
@section('breadcrumb')
    {!! Breadcrumbs::render('guard_report') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('ReportGuardSearchController', function ($scope, $http, $sce) {
            $scope.guardDetail = [];
            $scope.ansars = [];
            $scope.total_given = [];
            $scope.loadingUnit = false;
            $scope.loadingThana = false;
            $scope.loadingKpi = false;
            $scope.report = {};
            $scope.errorFound = 0;
            $scope.reportType = 'eng';
            $scope.total = 0;
            $scope.rank = 'all';
            
            $scope.loadAnsar = function () {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('guard_list')}}',
                    params: {
                        kpi_id: $scope.param.kpi,
                        unit: $scope.param.unit,
                        thana: $scope.param.thana,
                        division: $scope.param.range,
                        rank: $scope.rank,
                        q: $scope.q
                    }
                }).then(function (response) {
                   // alert('ttt');
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total;
                    
                    
                    $scope.errorFound = 0;
                    $scope.allLoading = false;
                    $scope.ansars = response.data.ansars;
                    $scope.total_given = response.data.total_given;
                    $scope.guardDetail = response.data.guard;
                }, function (response) {
                    $scope.errorFound = 1;
                    $scope.allLoading = false;
                    $scope.guardDetail = [];
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total;
                    console.log($scope.gCount);
                    
                    //alert('report');

                    $scope.ansars = $sce.trustAsHtml("<tr class='warning'><td colspan='" + $('.table').find('tr').find('th').length + "'>" + response.data + "</td></tr>");
                })
            };
            
            
            $scope.changeRank = function (i) {
               // alert('alert');
                $scope.rank = i;
                $scope.loadAnsar()
            };

            function capitalizeLetter(s) {
                return s.charAt(0).toUpperCase() + s.slice(1);
            }

            function sum(t) {
                var s = 0;
                for (var i in t) {
                    s += parseInt(t[i])
                }
                return s;
            }
            
            $scope.exportData = function (type) {
                $scope.allLoading = true;
                $http({
                    url: '{{URL::route('guard_list')}}',
                    method: 'get',
                    params: {
                        kpi_id: $scope.param.kpi,
                        unit: $scope.param.unit,
                        thana: $scope.param.thana,
                        division: $scope.param.range,
                        rank: $scope.rank,
                        export: type
                    }
                }).then(function (res) {
                    $scope.allLoading = false;
                    $scope.export_data = res.data;
                    $scope.generating = true;
                    generateReport();
                }, function (res) {
                    $scope.allLoading = false;
                })
            };
            $scope.file_count = 1;

            function generateReport() {
                $http({
                    url: '{{URL::to('HRM/generate/file')}}/' + $scope.export_data.id,
                    method: 'post',
                }).then(function (res) {
                    if ($scope.export_data.total_file > $scope.file_count) {
                        setTimeout(generateReport, 1000);
                        if (res.data.status) $scope.file_count++;
                    } else {
                        $scope.generating = false;
                        $scope.file_count = 1;
                        window.location.href = $scope.export_data.download_url;
                    }
                }, function (res) {
                    if ($scope.export_data.file_count > $scope.file_count) {
                        setTimeout(generateReport, 1000)
                    }
                })
            }

            $scope.loadReportData = function (reportName, type) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('localize_report')}}',
                    params: {name: reportName, type: type}
                }).then(function (response) {
                    $scope.report = response.data;
                    $scope.allLoading = false;
                })
            };
            $scope.dateConvert = function (date) {
                return (moment(date).format('DD-MMM-Y'));
            };
            $scope.loadReportData("ansar_in_guard_report", "eng")
        });
        $(function () {
            $("#print-report").on('click', function (e) {
                e.preventDefault();
                $('#print-guard-in-ansar-report table tr td a').each(function () {
                    var v = $(this).text();
                    $(this).parents('td').append('<span>' + v + '</span>');
                    $(this).css('display', 'none')
                });
                $("#print-area").remove();
                $('body').append('<div id="print-area">' + $("#print-guard-in-ansar-report").html() + '</div>');
                window.print();
                $("#print-area").remove();
                $('#print-guard-in-ansar-report table tr td a').each(function () {
                    $(this).parents('td').children('span').remove();
                    $(this).css('display', 'block')
                })
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
                <div class="overlay" ng-if="generating">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                        <span>[[(file_count)+'/'+export_data.total_file]]</span>
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
                            show-item="['range','unit','thana','kpi']"
                            type="single"
                            kpi-change="loadAnsar()"
                            start-load="range"
                            data="param"
                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}">
                    </filter-template>
                    <div id="print-guard-in-ansar-report">
                        <div style="margin:10px;">
                        <h3 style="text-align: center" id="report-header">[[report.report_header]]&nbsp;&nbsp;
                            <a href="#" title="print" id="print-report">
                                <span class="glyphicon glyphicon-print"></span>
                            </a>
                            <a href="#" title="export" ng-click="exportData('all')">
                                <i class="fa fa-file-excel-o"></i>
                            </a>
                        </h3>

                        <div class="report-heading">
                            <div class="report-heading-body">
                                <div class="report-heading-guard">
                                    <h4>[[report.guard.kpi_title]]</h4>

                                    <div>
                                        <ul class="guard-detail">
                                            <li class="guard-list-item-header">[[report.guard.kpi_name]]</li>
                                            <li>[[guardDetail.kpi_name]]&nbsp;</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <ul class="guard-detail">
                                            <li class="guard-list-item-header">[[report.guard.kpi_address]]</li>
                                            <li>[[guardDetail.kpi_address]], [[guardDetail.thana_name_bng]],
                                                [[guardDetail.unit_name_bng]]&nbsp;
                                            </li>
                                        </ul>
                                    </div>
                                    <div>
                                        <ul class="guard-detail">
                                            <li class="guard-list-item-header">[[report.guard.kpi_type]]</li>
                                            <li>--</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <ul class="guard-detail">
                                            <li class="guard-list-item-header">
                                                [[report.guard.kpi_ansar_given]]
                                            </li>
                                            <li>[[guardDetail.total_ansar_given]]&nbsp;</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <ul class="guard-detail">
                                            <li class="guard-list-item-header">
                                                [[report.guard.kpi_current_ansar]]
                                            </li>
                                            <li>[[total_given.length]]&nbsp;</li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                        
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <h4 class="text text-bold">
                               <a class="btn btn-primary text-bold" href="#" ng-click="changeRank('all')">Total
                                    Ansars ([[total]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(3)">PC
                                    ([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(2)">APC
                                    ([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])</a>&nbsp;
                                <a class="btn btn-primary text-bold" href="#" ng-click="changeRank(1)">Ansar
                                    ([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</a>
                                   
                                <!--<span class="btn btn-primary text-bold" >Total
                                    Ansars ([[total]])</span>&nbsp;
                                <span class="btn btn-primary text-bold">PC
                                    ([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])</span>&nbsp;
                                <span class="btn btn-primary text-bold">APC
                                    ([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])</span>&nbsp;
                                <span class="btn btn-primary text-bold">Ansar
                                    ([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</span>-->
                            
                            </h4>
                        </div>
                        <div class="col-md-4 col-sm-12" style="margin-top: 10px">
                            <database-search q="q" queue="queue" on-change="loadAnsar()"></database-search>
                        </div>
                    </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <caption class="table-caption"
                                         style="text-align: center;font-size: 1.5em;font-weight: bold">
                                    [[report.ansar.ansar_title]]([[ansars.length]])
                                </caption>
                                <tr>
                                    <th>[[report.ansar.sl_no]]</th>
                                    <th>[[report.ansar.id]]</th>
                                    <th>[[report.ansar.rank]]</th>
                                    <th>[[report.ansar.name]]</th>
                                    <th>[[report.ansar.dob]]</th>
                                    <th>[[report.ansar.height]]</th>
                                    <th>[[report.ansar.education]]</th>
                                    <th>[[report.ansar.district]]</th>
                                    <th>[[report.ansar.mobile]]</th>
                                    <th>[[report.ansar.avub_share_id]]</th>
                                    <th>[[report.ansar.embodiment_date]]</th>
                                    <th>[[report.ansar.join_date]]</th>
                                </tr>
                                <tr ng-show="ansars.length==0">
                                    <td colspan="12" class="warning no-ansar">No Ansar is available to show</td>
                                </tr>
                                <tbody ng-if="errorFound==1" ng-bind-html="ansars"></tbody>
                                <tr ng-show="ansars.length>0" ng-repeat="a in ansars">
                                    <td>[[$index+1]]</td>
                                    <td><a href="{{URL::to('HRM/entryreport')}}/[[a.ansar_id]]">[[a.ansar_id]]</a></td>
                                    <td>[[a.name_bng]]</td>
                                    <td>[[a.ansar_name_bng]]</td>
                                    <td>[[a.dob|dateformat:"DD MMM, YYYY"]]</td>
                                    <td>[[a.height]]</td>
                                    <td>[[a.education]]</td>
                                    <td>[[a.unit_name_bng]]</td>
                                    <td>[[a.mobile_no_self]]</td>
                                    <td>[[a.avub_share_id]]</td>
                                    <td>[[dateConvert(a.joining_date)]]</td>
                                    <td>[[a.transfered_date?dateConvert(a.transfered_date):'--']]</td>
                                </tr>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                @page {
    size: 9in 13in;
    margin: 27mm 16mm 27mm 16mm;
}
            </style>
        </section>
    </div>
@stop