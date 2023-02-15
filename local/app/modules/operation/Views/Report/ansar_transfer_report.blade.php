@extends('template.master')
@section('title','Ansar Transfer Report')
{{-- @section('breadcrumb')
{!! Breadcrumbs::render('embodiment_report_view') !!}
@endsection --}}
@section('content')
<script>
    GlobalApp.controller('ReportAnsarEmbodiment', function ($scope, $http, $sce) {
        $scope.total = 0;
        $scope.numOfPage = 0;
        $scope.param = {limit: parseInt("{{config('app.item_per_page')}}") + ''};
        $scope.reportType = 'eng';
        $scope.selectedDistrict = "all";
        $scope.selectedThana = "all";
        $scope.districts = [];
        $scope.thanas = [];
        $scope.allLoading = false;
        $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}") + '';
        $scope.currentPage = 0;
        $scope.ansars = [];
        $scope.pages = [];
        $scope.loadingDistrict = true;
        $scope.loadingThana = false;
        $scope.loadingPage = [];
        $scope.dcDistrict = parseInt('{{Auth::user()->district_id}}');
        $scope.from_date = moment().subtract(1, 'years').format("D-MMM-YYYY");
        $scope.to_date = moment().format("D-MMM-YYYY");
        $scope.loadPagination = function () {
            $scope.pages = [];
            for (var i = 0; i < $scope.numOfPage; i++) {
                $scope.pages.push({
                    pageNum: i,
                    offset: i * $scope.itemPerPage,
                    limit: $scope.itemPerPage
                });
                $scope.loadingPage[i] = false;
            }
        };
        $scope.loadPage = function (page, $event) {
            if ($scope.param && $scope.param.limit) $scope.itemPerPage = $scope.param.limit;
            $scope.allLoading = true;
            if ($event != undefined) $event.preventDefault();
            $scope.currentPage = page == undefined ? 0 : page.pageNum;
            $scope.loadingPage[$scope.currentPage] = true;
            $http({
                url: '{{URL::route('transfer_ansar_info')}}',
                method: 'get',
                params: {
                    offset: page == undefined ? 0 : page.offset,
                    limit: page == undefined ? $scope.itemPerPage : page.limit,
                    unit_id: $scope.param.unit,
                    division_id: $scope.param.range,
                    thana_id: $scope.param.thana,
                    from_date: $scope.from_date,
                    to_date: $scope.to_date,
                    rank: $scope.param.rank == undefined ? 'all' : $scope.param.rank,
                    gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                    q: $scope.q
                }
            }).then(function (response) {
                $scope.ansars = response.data;
                $scope.allLoading = false;
                $scope.loadingPage[$scope.currentPage] = false;
                $scope.total = response.data.total;
                $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                $scope.loadPagination();
            })
        };
        $scope.exportData = function (type) {
            var page = $scope.exportPage;
            if (type == 'page') $scope.export_page = true;
            else $scope.export_all = true;
            $http({
                url: '{{URL::route('transfer_ansar_info')}}',
                method: 'get',
                params: {
                    offset: type == 'all' ? -1 : (page == undefined ? 0 : page.offset),
                    limit: type == 'all' ? -1 : (page == undefined ? $scope.itemPerPage : page.limit),
                    unit_id: $scope.param.unit,
                    division_id: $scope.param.range,
                    thana_id: $scope.param.thana,
                    from_date: $scope.from_date,
                    to_date: $scope.to_date,
                    export: type
                }
            }).then(function (res) {
                $scope.export_data = res.data;
                $scope.generating = true;
                generateReport();
                $scope.export_page = $scope.export_all = false;
            }, function (res) {
                $scope.export_page = $scope.export_all = false;
            })
        };
        $scope.file_count = 1;

        function generateReport() {
            $http({
                url: '{{URL::to('HRM/generate/file')}}/' + $scope.export_data.id,
                method: 'post'
            }).then(function (res) {
                if ($scope.export_data.total_file > $scope.file_count) {
                    setTimeout(generateReport, 1000);
                    if (res.data.status) $scope.file_count++;
                } else {
                    $scope.generating = false;
                    $scope.file_count = 1;
                    window.open($scope.export_data.download_url, '_blank')
                }
            }, function (res) {
                if ($scope.export_data.file_count > $scope.file_count) {
                    setTimeout(generateReport, 1000)
                }
            })
        }

        $scope.filterMiddlePage = function (value, index, array) {
            var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
            var maxPage = minPage + 7;
            if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                return true;
            }
        };
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
        $scope.loadReportData("ansar_embodiment_report", "eng");
    });
    $(function () {
        function beforePrint() {
            $("#print-area").remove();
            $('body').append('<div id="print-area">' + $("#print_ansar_embodiment_report").html() + '</div>');
        }

        function afterPrint() {
            $("#print-area").remove()
        }

        if (window.matchMedia) {
            var mediaQueryList = window.matchMedia('print');
            mediaQueryList.addListener(function (mql) {
                if (mql.matches) {
                    beforePrint();
                } else {
                    afterPrint();
                }
            });
        }
        window.onbeforeprint = beforePrint;
        window.onafterprint = afterPrint;
        $("#print-report").on('click', function (e) {
            e.preventDefault();
            window.print();
        })
    })
</script>
<div ng-controller="ReportAnsarEmbodiment">
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
                                                                   ng-change="loadReportData('ansar_embodiment_report',reportType)"
                                                                   ng-model="reportType">&nbsp;<b>English</b>
                            &nbsp;<input type="radio"
                                         ng-change="loadReportData('ansar_embodiment_report',reportType)"
                                         class="radio-inline" style="margin: 0 !important;" value="bng"
                                         ng-model="reportType">&nbsp;<b>বাংলা</b>
                        </span>
                </div>
                <br>
                <filter-template
                        show-item="['range','unit','rank','gender']"
                        type="all"
                        range-change="loadPage()"
                        unit-change="loadPage()"
                        thana-change="loadPage()"
                        rank-change="loadPage()"
                        gender-change="loadPage()"
                        start-load="range"
                        field-width="{range:'col-sm-3',unit:'col-sm-3',rank:'col-sm-3',gender:'col-sm-3'}"
                        data="param"
                        on-load="loadPage()">
                </filter-template>
                <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label class="control-label">
                                Select Date Range
                            </label></br>
                            <div class="col-md-4 col-sm-12 col-xs-12"
                                 style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px">
                                <input type="text" name="from_date" id="from_date" date-picker=""
                                       class="form-control" placeholder="From Date" ng-model="from_date">
                            </div>
                            <div class="col-md-1 col-sm-12 col-xs-12"
                                 style="margin-left: 0px; padding-left: 0px;margin-right: 0px; padding-right: 0px;">
                                <div class="" style="text-align: center; padding:5px">to</div>
                            </div>
                            <div class="col-md-4 col-sm-12 col-xs-12"
                                 style="margin-right: 0px; padding-right: 0px;margin-left: 0px; padding-left: 0px">
                                <input type="text" name="to_date" date-picker="" id="to_date" class="form-control"
                                       placeholder="To Date" ng-model="to_date">
                            </div>
                            <div class="col-md-2 col-sm-12 col-xs-12">
                                <a class="btn btn-primary" ng-click="loadPage()">Load Result</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="btn-group btn-group-sm pull-right print-hide">
                            <button id="print-report" class="btn btn-default">
                                <i class="fa fa-print"></i>&nbsp;Print
                            </button>
                            <button id="export-report" ng-disabled="export_page||export_all"
                                    ng-click="exportData('page')" class="btn btn-default ">
                                <i ng-show="!export_page" class="fa fa-file-excel-o"></i>
                                <i ng-show="export_page" class="fa fa-spinner fa-pulse"></i>&nbsp;Export this page
                            </button>
                            <button ng-disabled="export_page||export_all" ng-click="exportData('all')"
                                    id="export-report-all" class="btn btn-default">
                                <i ng-show="!export_all" class="fa fa-file-excel-o"></i>
                                <i ng-show="export_all" class="fa fa-spinner fa-pulse"></i>&nbsp;Export all
                            </button>
                        </div>
                    </div>
                </div>
                <div id="print_ansar_embodiment_report">
                    <div class="row">
                        <div class="col-md-8"><h3 id="report-header">Ansar Transfer Info([[total]])</h3></div>
                        <div class="col-md-4" style="padding-top: 20px;padding-bottom: 10px;">
                            <database-search q="q" queue="queue" on-change="loadPage()"></database-search>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered full">
                            <tr>
                                <th>[[report.ansar.sl_no]]</th>
                                <th>[[report.ansar.id]]</th>
                                <th>[[report.ansar.name]]</th>
                                <th>[[report.ansar.rank]]</th>
                                <th>Home District</th>
                                <th>KPI Name (From)</th>
                                <th>KPI Name (To)</th>
                                <th>Transfer Date</th>
                                <th>Service Days</th>
                            </tr>
                            <tr ng-repeat="a in ansars.ansars">
                                <td>[[ansars.index+$index]]</td>
                                <td>[[a.ansar_id]]</td>
                                <td>[[a.name]]</td>
                                <td>[[a.rank]]</td>
                                <td>[[a.homeDistrict]]</td>
                                <td>[[a.presentKPI]]</td>
                                <td>[[a.transferedKPI]]</td>
                                <td>[[a.t_date|dateformat:'DD-MMM-YYYY']]</td>
                                <td>[[a.service_time]]</td>
                            </tr>
                            <tr ng-if="ansars.ansars==undefined||ansars.ansars.length<=0">
                                <td colspan="9" class="warning">No Ansar available</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="table_pagination" style="float: none !important;" ng-if="pages.length>1">
                    <div class="row">
                        <div class="col-sm-4">
                            <select ng-init="param.limit=itemPerPage" ng-model="param.limit" class="form-control"
                                    ng-change="loadPage()">
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="300">300</option>
                            </select>
                        </div>
                        <div class="col-sm-8">
                            <div class="pull-right">
                                <ul class="pagination">
                                    <li ng-class="{disabled:currentPage == 0}">
                                        <a href="#" ng-click="loadPage(pages[0],$event)">&laquo;&laquo;</a>
                                    </li>
                                    <li ng-class="{disabled:currentPage == 0}">
                                        <a href="#" ng-click="loadPage(pages[currentPage-1],$event)">&laquo;</a>
                                    </li>
                                    <li ng-repeat="page in pages|filter:filterMiddlePage"
                                        ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                        <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                        <a href="#" ng-click="loadPage(page,$event)"
                                           ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                        <span ng-show="loadingPage[page.pageNum]" style="position: relative"><i
                                                    class="fa fa-spinner fa-pulse"
                                                    style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                    </li>
                                    <li ng-class="{disabled:currentPage==pages.length-1}">
                                        <a href="#" ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
                                    </li>
                                    <li ng-class="{disabled:currentPage==pages.length-1}">
                                        <a href="#"
                                           ng-click="loadPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@stop