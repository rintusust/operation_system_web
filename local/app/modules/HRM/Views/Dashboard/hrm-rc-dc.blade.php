@extends('template.master')
@section('title','Dashboard')
@section('breadcrumb')
    {!! Breadcrumbs::render('hrm') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('TotalAnsar', function ($http, $scope) {
            $scope.allAnsar = [];
            $scope.loadingAnsar = true;
            $scope.embodimentData = {};
            $scope.graphData = [];
            $scope.loadAnsar = function () {
                $http({
                    url: "{{URL::to('HRM/getTotalAnsar')}}",
                    method: 'get'
                }).then(function (response) {
                    $scope.allAnsar = formatNumber(response.data);
                    $scope.loadingAnsar = false;
                }, function (response) {
                    $scope.loadingAnsar = false;
                    switch (response.status) {
                        case 404:
                            response.data = "Not found(404)";
                            break;
                        case 500:
                            response.data = "Server error(500)";
                            break;
                    }
                    $scope.allAnsar = {
                        totalAnsar: response.data,
                        totalPanel: response.data,
                        totalEmbodiedOwn: response.data,
                        totalOffered: response.data,
                        totalFreeze: response.data,
                        totalBlockList: response.data,
                        totalBlackList: response.data,
                        totalRest: response.data,
                        totalEmbodiedDiff: response.data,
                        totalRetire: response.data,
                        totalOfferBlock: response.data,
                        totalOfferBlockOwnDistrict: response.data
                    }
                })
            };
            $scope.loadAnsar();
            $scope.loadRecentAnsar = function () {
                $http({
                    url: "{{URL::to('HRM/getrecentansar')}}",
                    method: 'get'
                }).then(function (response) {
                    $scope.recentAnsar = formatNumber(response.data);
                }, function (response) {
                    switch (response.status) {
                        case 404:
                            response.data = "Not found(404)";
                            break;
                        case 500:
                            response.data = "Server error(500)";
                            break;
                    }
                    $scope.recentAnsar = {
                        recentAnsar: response.data,
                        recentPanel: response.data,
                        recentEmbodiedDiff: response.data,
                        recentEmbodiedOwn: response.data,
                        recentFreeze: response.data,
                        recentBlockList: response.data,
                        recentBlackList: response.data,
                        recentRest: response.data,
                        recentOffered: response.data,
                        recentRetire: response.data,
                        totalOfferBlockOwnDistrict: response.data

                    }
                })
            };
            $scope.loadRecentAnsar();
            $scope.progressInfo = [];
            $scope.loadingProgressInfo = true;
            $scope.progressData = function () {
                $http({
                    url: "{{URL::to('HRM/progress_info')}}",
                    method: 'get'
                }).then(function (response) {
                    $scope.progressInfo = formatNumber(response.data);
                    $scope.loadingProgressInfo = false;
                }, function (response) {
                    $scope.loadingProgressInfo = false;
                    switch (response.status) {
                        case 404:
                            response.data = "Not found(404)";
                            break;
                        case 500:
                            response.data = "Server error(500)";
                            break;
                    }
                    $scope.progressInfo = {
                        totalServiceEndedInThreeYears: response.data,
                        totalAnsarReachedFiftyYearsOfAge: response.data,
                        totalNotInterestedMembersUptoTenTimes: response.data
                    }
                })
            };
            $scope.fiveDaysOffer = function () {
               // alert('sss');
                $http({
                    url: "{{URL::route('offer_accept_last_5_day_data')}}",
                    method: 'get',
                    params: {
                        type: 'count',
                        unit: 'all',
                        thana: 'all',
                        division: 'all',
                        rank: 'all',
                        sex: 'all'
                    }
                }).then(function (response) {
                    $scope.offerAcceptLastFiveDays = sum(response.data.total);
                    $scope.loadingProgressInfo = false;
                }, function (response) {
                    $scope.loadingProgressInfo = false;
                })
            };
            
            
            
            $scope.vacancyKPI = function () {
                $http({
                    url: "{{URL::route('vacancy_kpi_view_details')}}",
                    method: 'get',
                    params: {
                        type: 'count',
                        unit:'all',
                        thana:'all',
                        division:'all',
                        rank:'all',
                        sex:'all',
                        organization:'all'
                    }
                }).then(function (response) {
                    console.log(response);
                    $scope.vacancyKPICount = response.data.total[0].total_vacancy;
                    $scope.loadingProgressInfo = false;
                }, function (response) {
                    $scope.loadingProgressInfo = false;
                    switch (response.status) {
                        case 404:
                            response.data = "Not found(404)";
                            break;
                        case 500:
                            response.data = "Server error(500)";
                            break;
                    }
                    $scope.vacancyKPICount = response.data.total[0].total_vacancy;
                })
            }

            function sum(t) {
                var s = 0;
                for (var i in t) {
                    //alert(i)
                    for (var j = 0; j < t[i].length; j++) {
                        s = parseInt(s) + parseInt(t[i][j].total);
                    }
                }
                return s;
            }

            $scope.progressData();
            $scope.fiveDaysOffer();
            $scope.vacancyKPI();
            
            $http({
                url: "{{URL::to('HRM/graph_embodiment')}}",
                method: 'get'
            }).then(function (response) {
                $scope.graphData = response.data
            });
            $scope.graphDisembodiment = [];

            function formatNumber(data) {
                Object.keys(data).forEach(function (key) {
                    data[key] = data[key].toLocaleString();
                });
                return data;
            }
        });
        GlobalApp.directive('graph', function () {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    scope.$watch('graphData', function (n, o) {
                        var labels = [], ea = [], ed = [];
                        if (Object.keys(n).length > 0) {
                            n.ea.forEach(function (item) {
                                labels.push(item.month);
                                ea.push(item.total)
                            });
                            n.da.forEach(function (item) {
                                ed.push(item.total)
                            });
                            $.getScript('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js', function () {
                                var data = {
                                    labels: labels,
                                    datasets: [
                                        {
                                            label: 'Embodiment',
                                            backgroundColor: "rgba(0,60,100,1)",
                                            borderColor: "black",
                                            borderWidth: 1,
                                            data: ea
                                        },
                                        {
                                            label: 'Dis-Embodiment',
                                            backgroundColor: "rgba(151,187,205,0.5)",
                                            borderColor: "rgba(151,187,205,1)",
                                            borderWidth: 1,
                                            data: ed
                                        }
                                    ]
                                };
                                var options = {
                                    responsive: true
                                };
                                var c = $('#graph-embodiment');
                                var ct = c.get(0).getContext('2d');
                                var ctx = document.getElementById("graph-embodiment").getContext("2d");
                                new Chart(ctx, {
                                    type: 'bar',
                                    data: data,
                                    options: options
                                });
                            })
                        }
                    }, true)
                }
            }
        })
    </script>
    <section class="content" ng-controller="TotalAnsar">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12"></div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="line-bar-top"></div>
                <div class="info-box bg-aqua">
                <span class="info-box-icon">
                    <img src="{{asset('dist/img/free.png')}}">
                </span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/free_ansar"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.tfd') @else @lang('title.tfrr') @endif</span>
                            <span class="info-box-number" style="font-weight: normal">[[allAnsar.totalFree]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/free_ansar"
                           style="color:#FFFFFF" class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentFree]]</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><img src="{{asset('dist/img/queue.png')}}"></span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/paneled_ansar"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.tpd') @else @lang('title.tpr') @endif</span>
                            <span class="info-box-number">[[allAnsar.totalPanel]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/paneled_ansar"
                           style="color:#FFFFFF" class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentPanel]]</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><img src="{{asset('dist/img/embodiment2.png')}}"></span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/own_embodied_ansar"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.teo') @else @lang('title.teor') @endif</span>
                            <span class="info-box-number">[[allAnsar.totalEmbodiedOwn]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/own_embodied_ansar"
                           style="color:#FFFFFF" class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentEmbodiedOwn]]</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><img src="{{asset('dist/img/ansars.png')}}"></span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/all_ansar" class="btn-link"
                           style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.tad') @else @lang('title.tar') @endif</span>
                            <span class="info-box-number">[[allAnsar.totalAnsar]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/all_ansar" style="color:#FFFFFF"
                           class="btn-link">
                    <span class="progress-description">
                        Recent-[[recentAnsar.recentAnsar]]
                    </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon">
                        <img src="{{asset('dist/img/embodiment2.png')}}">
                    </span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/embodied_ansar_in_different_district"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.ted') @else @lang('title.tedr') @endif</span>
                            <span class="info-box-number">[[allAnsar.totalEmbodiedDiff]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/embodied_ansar_in_different_district"
                           style="color:#FFFFFF" class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentEmbodiedDiff]]</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-envelope"></i></span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/offerred_ansar"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.tod') @else @lang('title.tor') @endif</span>
                            <span class="info-box-number">[[(allAnsar.totalOffered|num)+(allAnsar.totalOfferedReceived|num)]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/offerred_ansar" style="color:#FFFFFF"
                           class="btn-link">
                            <span class="progress-description" style="color:#FFFFFF">
                        Recent-[[(recentAnsar.recentOffered|num)+(recentAnsar.recentOfferedReceived|num)]]</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
                <div class="info-box bg-aqua"><span class="info-box-icon"><i class="fa fa-bed"></i></span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/rest_ansar"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.trd') @else @lang('title.trr') @endif</span>
                            <span class="info-box-number">[[allAnsar.totalRest]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/rest_ansar"
                           style="color:#FFFFFF" class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentRest]]</span>
                        </a>
                    </div>
                </div>
            </div>


            <div class="col-md-3 col-sm-6 col-xs-12"></div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="line-bar-bottom"></div>
                <div class="info-box bg-aqua">
                    <span class="info-box-icon">
                        <img src="{{asset('dist/img/freeze.png')}}">
                    </span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/freezed_ansar"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.tfrd') @else @lang('title.tfrrr') @endif</span>
                            <span class="info-box-number">[[allAnsar.totalFreeze]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/freezed_ansar"
                           style="color:#FFFFFF" class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentFreeze]]</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><img src="{{asset('dist/img/freeze.png')}}"></span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/freezed_ansar_other"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.tfrdo') @else @lang('title.tfrrro') @endif</span>
                            <span class="info-box-number">[[allAnsar.totalFreezeOther]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/freezed_ansar_other"
                           style="color:#FFFFFF" class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentFreezeOther]]</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><img src="{{asset('dist/img/blacklist.png')}}"></span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/blacked_ansar"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.tbad') @else @lang('title.tbar') @endif</span>
                            <span class="info-box-number">[[allAnsar.totalBlackList]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/blacked_ansar"
                           style="color:#FFFFFF" class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentBlackList]]</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12"></div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="line-bar-bottom2"></div>
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><img src="{{asset('dist/img/blocklist.png')}}"></span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/blocked_ansar"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@if(Auth::user()->type==22) @lang('title.tbd') @else @lang('title.tbr') @endif</span>
                            <span class="info-box-number">[[allAnsar.totalBlockList]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/blocked_ansar"
                           style="color:#FFFFFF" class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentBlockList]]</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon">
                        <img src="{{asset('dist/img/queue.png')}}">
                    </span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/offer_block"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@lang('title.tobd')</span>
                            <span class="info-box-number" style="font-weight: normal">[[allAnsar.totalOfferBlock]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/offer_block"
                           style="color:#FFFFFF" class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentOfferBlock]]</span></a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 line-bar-middle col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><img src="{{asset('dist/img/embodiment2.png')}}"></span>
                    <div class="info-box-content">
                        <a href="{{URL::to('HRM/show_ansar_list')}}/retire_ansar"
                           class="btn-link" style="color: #FFFFFF !important;">
                            <span class="info-box-text">@lang('title.tred')</span>
                            <span class="info-box-number" style="font-weight: normal">[[allAnsar.totalRetire]]
                                <img src="{{asset('dist/img/facebook-white.gif')}}" width="20" ng-show="loadingAnsar">
                            </span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <a href="{{URL::to('HRM/show_recent_ansar_list')}}/retire_ansar" style="color:#FFFFFF"
                           class="btn-link">
                            <span class="progress-description">Recent-[[recentAnsar.recentRetire]]</span></a></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-9 col-xs-12 pull-right">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Progress Information</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" style="padding-left: 0;padding-right: 0">
                        <div class="label-hrm" style="border-bottom: 1px solid rgba(153, 153, 153, 0.52)">
                            <div class="label-hrm-title">
                            <span class="info-box-text"
                                  style="color: #000000;white-space: normal;overflow: auto;text-overflow: initial">Total number of Ansars who will complete 3 years of service within the next 2 months</span>
                            </div>

                            <div class="label-hrm-calculation">
                                <span class="info-box-text"
                                      style="color: #000000;white-space: normal;overflow: auto;text-overflow: initial">
                                    <a href="{{URL::to('HRM/service_ended_in_three_years')}}/[[progressInfo.totalServiceEndedInThreeYears]]"
                                       class="btn-link">[[progressInfo.totalServiceEndedInThreeYears]]</a><img
                                            src="{{asset('dist/img/facebook.gif')}}" width="20"
                                            ng-show="loadingProgressInfo">
                                     </span>
                            </div>
                            <br style="clear: left;"/>
                        </div>
                        <div class="label-hrm" style="border-bottom: 1px solid rgba(153, 153, 153, 0.52)">
                            <div class="label-hrm-title">
                            <span class="info-box-text"
                                  style="color: #000000;white-space: normal;overflow: auto;text-overflow: initial">Total number of Ansars who will reach 50 years of age within the next 3 months</span>
                            </div>

                            <div class="label-hrm-calculation">
                            <span class="info-box-text" style="color: #000000">
                                <a href="{{URL::to('HRM/ansar_reached_fifty_years')}}/[[progressInfo.totalAnsarReachedFiftyYearsOfAge]]"
                                   class="btn-link">[[progressInfo.totalAnsarReachedFiftyYearsOfAge]]</a><img
                                        src="{{asset('dist/img/facebook.gif')}}" width="20"
                                        ng-show="loadingProgressInfo"></span>
                            </div>
                            <br style="clear: left;"/>
                        </div>
                        <div class="label-hrm">
                            <div class="label-hrm-title">
                            <span class="info-box-text"
                                  style="color: #000000;white-space: normal;overflow: auto;text-overflow: initial">Total number of Ansars who accept the offer last 5 days </span>
                            </div>

                            <div class="label-hrm-calculation">
                            <span class="info-box-text" style="color: #000000">
                                <a href="{{URL::route('offer_accept_last_5_day')}}" class="btn-link">[[offerAcceptLastFiveDays==undefined?0:offerAcceptLastFiveDays]]</a>
                                <img src="{{asset('dist/img/facebook.gif')}}" width="20" ng-show="loadingProgressInfo"></span>
                            </div>
                            <br style="clear: left;"/>
                        </div>
                        
                        <div class="label-hrm" style="border-bottom: 1px solid rgba(153, 153, 153, 0.52)">
                        <div class="label-hrm-title">
                            <span class="info-box-text"
                                  style="color: #000000;white-space: normal;overflow: auto;text-overflow: initial">Total number of vacancy in active KPI</span>
                        </div>

                        <div class="label-hrm-calculation">
                                <span class="info-box-text" style="color: #000000;white-space: normal;overflow: auto;text-overflow: initial">
                                    <a href="{{URL::to('HRM/vacancy_in_kpi')}}/[[vacancyKPICount]]"
                                            class="btn-link">[[vacancyKPICount]]</a><img
                                            src="{{asset('dist/img/facebook.gif')}}" width="20"
                                            ng-show="loadingProgressInfo">
                                     </span>
                        </div>
                        <br style="clear: left;"/>
                    </div>
                        
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <div class="col-sm-12 col-md-9 col-xs-12 pull-right">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Total number of Ansars who have
                            been Embodied and
                            Disembodied in recent years</h3>
                    </div>
                    <div class="box-body">
                        <div id="graph-level" class="col-md-8 col-sm-12 col-xs-12 col-centered"
                             style="text-align: center">
                            <div class="col-md-4 col-sm-6 col-xs-12">
                            <span style="color: #000000"><i class="fa fa-lg fa-circle"
                                                            style="color: rgba(0,60,100,1) !important;"></i>  Embodied</span>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                            <span style="color: #000000"><i class="fa fa-lg fa-circle"
                                                            style="color: rgba(151,187,205,0.5) !important;"></i>  Disembodied</span>
                            </div>
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <canvas id="graph-embodiment" graph style="width: 100%; height: 160px;" class="well"></canvas>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>

            {{--<div class="col-sm-4 col-md-4">--}}
            {{--<div class="box box-solid">--}}
            {{--<div class="box-header with-border">--}}
            {{--<h3 class="box-title">Total Ansar Disemboded in recent Year</h3>--}}
            {{--</div><!-- /.box-header -->--}}
            {{--<div class="box-body">--}}
            {{--<canvas id="graph-disembodiment" style="width: 100%" class="well"></canvas>--}}
            {{--</div><!-- /.box-body -->--}}
            {{--</div>--}}
            {{--</div>--}}
        </div>
    </section>
@endsection
      