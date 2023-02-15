@extends('template.master')
@section('title','Total number of pc/apc/ansar who will reach age limit within next 3 month')
@section('content')
    <script>
        GlobalApp.controller('AnsarFiftyYearsReachedListController', function ($scope, $http, $sce, $compile) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.ansarStatus = '1';
            $scope.param = {};
            $scope.queue = [];
            $scope.itemPerPage = 20;
            $scope.currentPage = 0;
            $scope.ansars = $sce.trustAsHtml("");
            $scope.pages = [];
            $scope.loadingPage = [];
            $scope.allLoading = true;
            $scope.errorFound = 0;
            $scope.unit = {
                selectedDistrict: "",
                custom: "",
                type: "1"
            };
            $scope.customData = {
                "Next 3 month": 3,
                "Next 4 month": 4,
                "Next 5 month": 5,
                "Next 6 month": 6,
                "Next 7 month": 7,
                "Custom": -1
            };
            $scope.param.selectedDate = '3';
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
                console.log($scope.ansarStatus);
                if ($event != undefined) $event.preventDefault();
                $scope.exportPage = page;
                $scope.currentPage = page == undefined ? 0 : page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $scope.allLoading = true;
                $http({
                    url: '{{URL::route('ansar_reached_fifty_details')}}',
                    method: 'get',
                    params: {
                        offset: page == undefined ? 0 : page.offset,
                        limit: page == undefined ? $scope.itemPerPage : page.limit,
                        unit: $scope.param.unit,
                        thana: $scope.param.thana,
                        division: $scope.param.range,
                        selected_date: $scope.param.custom,
                        custom_date: $scope.param.selected,
                        status_data:$scope.ansarStatus,
                        q: $scope.q,
                        rank: $scope.param.rank || 'all',
                        gender:$scope.param.gender || 'all'
                    }
                }).then(function (response) {
                    $scope.ansars = response.data;
                    $scope.queue.shift();
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.allLoading = false;
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total;
                    if ($scope.queue.length > 1) $scope.loadPage();
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                })
            };
            $scope.changeRank = function (rank) {
                $scope.param.rank = rank;
                $scope.loadPage();
            };
            $scope.exportData = function (type) {
                var page = $scope.exportPage;
                if (type == 'page') $scope.export_page = true;
                else $scope.export_all = true;
                $http({
                    url: '{{URL::route('ansar_reached_fifty_details')}}',
                    method: 'get',
                    params: {
                        offset: type == 'all' ? -1 : (page == undefined ? 0 : page.offset),
                        limit: type == 'all' ? -1 : (page == undefined ? $scope.itemPerPage : page.limit),
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        thana: $scope.param.thana == undefined ? 'all' : $scope.param.thana,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        q: $scope.q,
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

            function sum(t) {
                var s = 0;
                for (var i in t) {
                    s += parseInt(t[i])
                }
                return s;
            }
        })
    </script>
    <div ng-controller="AnsarFiftyYearsReachedListController">
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
                    <filter-template
                            show-item="['range','unit','thana','gender']"
                            type="all"
                            range-change="loadPage()"
                            unit-change="loadPage()"
                            thana-change="loadPage()"
                            gender-change="loadPage()"
                            on-load="loadPage()"
                            start-load="range"
                            data="param"
                            custom-field="true"
                            custom-model="param.selectedDate"
                            custom-label="Select an Option"
                            custom-data="customData"
                            custom-change="loadPage()"
                            field-width="{range:'col-sm-2',unit:'col-sm-3',thana:'col-sm-2',gender:'col-sm-3',custom:'col-sm-2'}">
                    </filter-template>
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-8">
                            <div class="form-group row" ng-if="param.selectedDate==-1">
                                <div class="col-xs-5">
                                    <input type="text" class="form-control" ng-model="param.selected.custom"
                                           placeholder="No of day,month or year">
                                </div>
                                <div class="col-xs-5" style="padding-left: 0;" ng-init="param.selected.type='1'">
                                    <select class="form-control" ng-model="param.selected.type">
                                        <option value="1">Days</option>
                                        <option value="2">Week</option>
                                        <option value="3">Months</option>
                                        <option value="4">Years</option>
                                    </select>
                                </div>
                                <div class="col-xs-2" style="padding-left: 0;">
                                    <button class="btn btn-primary pull-right" ng-click="loadPage()">
                                        <i class="fa fa-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3" >
                            <select  class="form-control"  name="ansarStatus" ng-change="loadPage()" id="ansarStatus"
                                     ng-model="ansarStatus">
                                <option value="1" ng-selected="true">All</option>
                                <option value="2">Embodied</option>
                                <option value="3">Non Embodied</option>
                            </select>


                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-bottom: 5px">
                            <div class="btn-group btn-group-sm pull-right">
                                <button id="print-report" class="btn btn-default"><i
                                            class="fa fa-print"></i>&nbsp;Print
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
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text text-bold">
                                <button class="btn btn-primary" ng-click="changeRank('all')">Total Ansars([[total]])</button>
                                <button class="btn btn-primary" ng-click="changeRank('PC')">
                                    PC([[gCount.PC!=undefined?gCount.PC.toLocaleString():0]])
                                </button>
                                <button class="btn btn-primary" ng-click="changeRank('APC')">&nbsp;APC([[gCount.APC!=undefined?gCount.APC.toLocaleString():0]])</button>
                                <button class="btn btn-primary" ng-click="changeRank('Ansar')">&nbsp;Ansar([[gCount.ANSAR!=undefined?gCount.ANSAR.toLocaleString():0]])</button>
                            </h4>
                        </div>
                        <div class="col-md-4">
                            <database-search q="q" queue="queue" on-change="loadPage()"></database-search>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <template-list data="ansars" key="selected_ansar_fifty_age"></template-list>
                        <div class="table_pagination" ng-if="pages.length>1">
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
                                    <a href="#" ng-click="loadPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop