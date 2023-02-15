@extends('template.master')
@section('title','View Leaves')
@section('breadcrumb')
    {!! Breadcrumbs::render('view_leaves') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("AttendanceController", function ($scope, $http, $sce) {
            var currentYear = $scope.currentMonth = parseInt(moment().format('YYYY'));
            var currentMonth = $scope.currentYear = parseInt(moment().format('M'));
            $scope.vdpList = $sce.trustAsHtml(`
            <p style="font-size: 16px;font-weight: bold;text-align: center;">
                Select guard or  Ansar ID to load data
            </p>
            `)
            $scope.param = {}
            $scope.allLoading = false;
            $scope.searchData = function () {
                console.log($scope.param)
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: "{{URL::route('SD.leave.index')}}",
                    params: $scope.param,
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.vdpList = $sce.trustAsHtml(response.data)
                    console.log(response.data)
                }, function (response) {
                    $scope.allLoading = false;
                    console.log(response.data)
                })
            }
            $scope.init = function () {
//                alert(1)
                $scope.param.month = currentMonth;
                $scope.param.year = currentYear;
                console.log($scope.param)
            }
        })
    </script>
    <section class="content" ng-controller="AttendanceController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{Session::get('error_message')}}
                </div>
            </div>
        @endif
        <div class="box box-solid">
            <div class="box-header">
                <filter-template
                        show-item="['range','unit','thana','kpi']"
                        type="single"
                        range-change="loadPage()"
                        unit-change="loadPage()"
                        thana-change="loadPage()"
                        data="param"
                        start-load="range"
                        on-load="loadPage()"
                        field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                >

                </filter-template>
                <div class="row" ng-init="init()">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="">Search by Ansar ID</label>
                            <input type="text" class="form-control" placeholder="Search by Ansar ID"
                                   ng-model="param.ansar_id">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label style="display: block" for="">&nbsp;</label>
                            <button class="btn btn-primary" ng-click="searchData()"
                                    ng-disabled="(!param.range||!param.unit||!param.thana||!param.kpi)&&(!param.ansar_id)"
                            >
                                <i class="fa fa-search"></i>&nbsp; Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>

                <div ng-bind-html="vdpList" compile-html>

                </div>
            </div>
        </div>
    </section>
@endsection