@extends('template.master')
@section('title','Promotion Ansar List')
@section('breadcrumb')
    
@endsection
@section('content')
    <script>
        GlobalApp.controller('PromotionController', function ($scope, $http, $sce, $parse, notificationService) {
            
            $scope.rank = 'all';
            $scope.queue = [];
            $scope.selectRank = true;
            $scope.exportPage = '';
            $scope.makeVerifiedCheckBox = false;
            $scope.rankUpdateOption = false;
            $scope.goToPanelBtn = false;
            $scope.defaultPage = {pageNum: 0, offset: 0, limit: $scope.itemPerPage, view: 'view'};
            $scope.total = 0;
            $scope.param = {};
            $scope.numOfPage = 0;
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.allPromotionAnsar = [];
            $scope.ansar_id = [];
            $scope.ranks = '';
            $scope.pages = [];
            $scope.loadingPage = [];
            $scope.allLoading = true;
            $scope.showLoadScreen1 = true;
            $scope.orderBy = "";
            $scope.values = [{id: 1,name: 'first'}, {id: 2,name: 'second'}];
            $scope.from_date = '';
            $scope.to_date = '';
            $scope.isDisabled = false;
            
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

                if ($event != undefined) $event.preventDefault();
                $scope.exportPage = page;
                $scope.currentPage = page == undefined ? 0 : page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                $scope.allLoading = true;

                $http({
                    url: '{{URL::to('HRM/getPromotionList')}}',
                    method: 'get',
                    params: 
                    {
                        offset: page == undefined ? 0 : page.offset,
                        limit: page == undefined ? $scope.itemPerPage : page.limit,
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        rank: $scope.param.rank == undefined ? 'all' : $scope.param.rank,
                        thana: $scope.param.thana == undefined ? 'all' : $scope.param.thana,
                        q: $scope.q,
                        sortBy: $scope.orderBy,

                    }
                }).then(function (response) {					
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadPage();
                    $scope.allPromotionAnsar = response.data.allPromotionAnsar;
					$scope.sl = response.data.index;
                    console.log($scope.allPromotionAnsar); 
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.allLoading = false;
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                })
            };

            $scope.getPromotionList = function (url) {
                var data = $scope.params;
                $scope.allLoading = true;
                $http({
                    url: url || "{{URL::route('getPromotionList')}}",
                    method: 'get',
                    params: data
                }).then(function (response) {
                    $scope.response = response.data.data;
                    $scope.allPromotionAnsar = response.data.data.data;
                    $scope.view = $sce.trustAsHtml(response.data.view);
                    $scope.checked = Array.apply(null, Array($scope.allPromotionAnsar.length)).map(Boolean.prototype.valueOf, false);
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.allLoading = false;
                })
            };

            $scope.sendToPanel = function (id) {
                if (id) {
                    $scope.submitting = true;
                    $http({
                        url: "{{URL::to('HRM/SendToPanelFromAnsarList')}}",
                        method: 'post',
                        data: angular.toJson({
                            request_id: id,
                            memorandum_id:$scope.memorandumId,
                            panel_date:$scope.panel_date
                        })
                    }).then(function (response) {
                        console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('success', response.data.message);
                            $("#send-to-panel-modal").modal('hide');
                            $scope.loadPage();
                        } else {
                            notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                        }
                    }, function (response) {

                    })
                }
            };

            $scope.verifyAnsar = function () {
                                
                 $scope.allLodaing =true;
                 $http({
                    url: '{{URL::to('HRM/checkUnitAnsarEligibility')}}',
                    method: 'post',
                    params: {
                        ansar_id: $scope.ansar_id,                        
                        unit: $scope.param.unit,
                        request_comment: $scope.request_comment
                    }
                }).then(function (response) {
                    
                    console.log(response);
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message);
                        $("#confirm-panel-modal").modal('hide');
                        $scope.loadPage();
                    } else {
                        notificationService.notify('error', response.data.message)
                        }
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
            };

            $scope.makeVerified = function (id) {
                
                if (id) {
                    
                    $scope.submitting = true;
                    $http({
                        url: "{{URL::to('HRM/makeVerified')}}",
                        method: 'post',
                        data: angular.toJson({
                            request_id: id,
                            makeVerified: $scope.makeVerifiedCheckBox
                        })
                    }).then(function (response) {
                        
                        console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            
                            notificationService.notify('success', response.data.message);
                            $("#make-varified-modal").modal('hide');
                            $scope.loadPage();
                        } 
                        else {
                            notificationService.notify('error', response.data.message)
                        }
                        
                    }, function (response) {
                        
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
                }
            };

            $scope.rankUpdate = function (id) {
                if (id) {
                    $scope.submitting = true;
                    $http({
                        url: "{{URL::to('HRM/rankUpdate')}}",
                        method: 'post',
                        data: angular.toJson({
                            request_id: id,
                            rankUpdate: $scope.rankUpdateOption,
                            ranks:$scope.ranks
                        })
                    }).then(function (response) {
                        console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('success', response.data.message);
                            $("#rank-update-modal").modal('hide');
                            $scope.loadPage();
                        } else {
                            notificationService.notify('error', response.data.message);
                            
                        }
                        
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
                  
                }
            };

            $scope.backtoPrevious = function (id) {
                if (id) {
                    $scope.submitting = true;
                    $http({
                        url: "{{URL::to('HRM/backtoPrevious')}}",
                        method: 'post',
                        data: angular.toJson({
                            request_id: id
                        })
                    }).then(function (response) {
                        console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('success', response.data.message);
                            $("#back-to-previous-modal").modal('hide');
                            $scope.loadPage();
                        } else {
                            notificationService.notify('error', response.data.message)
                        }
                        $scope.allPromotionAnsar.splice($scope.allPromotionAnsar.indexOf($scope.getSingleRow), 1)
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
                }
            };
 
            $scope.exportData = function (type) {
                var page = $scope.exportPage;
                if (type == 'page') $scope.export_page = true;
                else $scope.export_all = true;
                $http({
                    url: '{{URL::to('HRM/get_available_ansar_list')}}',
                    method: 'get',
                    params: {
                        type: $scope.ansarType,
                        offset: type == 'all' ? -1 : (page == undefined ? 0 : page.offset),
                        limit: type == 'all' ? -1 : (page == undefined ? $scope.itemPerPage : page.limit),
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        thana: $scope.param.thana == undefined ? 'all' : $scope.param.thana,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        filter_mobile_no: $scope.param.filter_mobile_no == undefined ? 0 : $scope.param.filter_mobile_no,
                        filter_age: $scope.param.filter_age == undefined ? 0 : $scope.param.filter_age,
                        q: $scope.q,
                        rank: $scope.rank,
                        export: type,
                        from_date: $scope.from_date,
                        to_date: $scope.to_date
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

            $scope.search = function () {
            };
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            };
            $scope.changeRank = function (i) {
                $scope.rank = i;
                $scope.loadPage()
            };
            
            $scope.modal = function (data) {
                 console.log(data);
                $scope.printLetter = false;
                $scope.getSingleRow = data;
                
            }

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
        });
        $(function () {
            $("#print-report").on('click', function (e) {
                $("#print-area").remove();
                $("#print_table table").removeClass('table table-bordered');
                $('body').append('<div id="print-area">' + $("#print_table").html() + '</div>');
                window.print();
                $("#print_table table").addClass('table table-bordered');
                $("#print-area").remove()
            })
        })
    </script>
    <div ng-controller="PromotionController">
        <section class="content">
            <div>
                <div class="box box-solid">
                    <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                    </div>
                    <div class="box-body">
                        <div class="box-body" id="change-body">
                            <filter-template
                                    show-item="['range','unit','thana','rank','gender']"
                                    type="all"
                                    range-change="loadPage()"
                                    unit-change="loadPage()"
                                    thana-change="loadPage()"
                                    kpi-change="loadPage()"
                                    rank-change="loadPage()"
                                    gender-change="loadPage()"
                                    on-load="loadPage()"
                                    data="param"
                                    start-load="range"
                                    field-width="{range:'col-sm-2',unit:'col-sm-2',thana:'col-sm-2',kpi:'col-sm-2',rank:'col-sm-2',gender:'col-sm-2'}"
                                    
                                    
                            ></filter-template>
                            
                            <button id="print-report" class="btn btn-default"><i
                                class="fa fa-print" ></i>&nbsp;Print
                            </button>
                            
                            <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                            </div>

                            <div id="print_table">
                            <div class="table-responsive">
                                <table class="table  table-bordered table-striped" id="ansar-table">
                                    <caption>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <span class="text text-bold" style="color:#000000;font-size: 1.1em">Total : [[total.toLocaleString()]]</span>
                                                
                                            </div>
                                            
                                            <div class="col-md-4 col-sm-12" style="margin-top: 10px">
                                                <database-search q="q" queue="queue" on-change="loadPage()"></database-search>
                                            </div>
                                        </div>
                                    </caption>
                                    <tr>
                                        
                                        <th class="text-center">SL</th>
                                        <th class="text-center">Ansar ID</th>
                                        <th class="text-center">Ansar Name</th>
                                        <th class="text-center">Rank</th>
                                        <th class="text-center">Circular Name</th>
                                        <th class="text-center">Verified Step</th>
                                        <th class="text-center">Rank Update Step</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                        
                                    </tr>
                                    <tr ng-show="allPromotionAnsar.length>0"
                                        ng-repeat="promotionAnsar in allPromotionAnsar">
                                        
                                        <td class="text-center">[[sl+$index]]</td>
                                        <td class="text-center">[[promotionAnsar.ansar_id]]</td>
                                        <td class="text-center">[[promotionAnsar.name]]</td>
                                        <td class="text-center">[[promotionAnsar.rank]]</td>
                                        <td class="text-center">[[promotionAnsar.circular_name]]</td>
                                        <td ng-if="[[promotionAnsar.not_verified_status]]==1"class="text-center">✔</td>
                                        <td ng-if="[[promotionAnsar.not_verified_status]]==0"class="text-center">--</td>
                                        <td ng-if="[[promotionAnsar.promoted_status]]==1"class="text-center">✔</td>
                                        <td ng-if="[[promotionAnsar.promoted_status]]==0"class="text-center">--</td>
                                        <td class="text-center">[[promotionAnsar.status]]</td>
                                        <td>
                                            <div class="col-xs-1">
                                                @if(UserPermission::userPermissionExists('backtoPrevious'))
                                                <a href="" data-toggle="modal" ng-click="ppp(a.id,$index)"
                                                data-toggle="modal" modal-show data="promotionAnsar" callback="modal(data)" target="#back-to-previous-modal"
                                                data-target="#back-to-previous-modal" ng-disabled="promotionAnsar.status=='Completed'||promotionAnsar.promoted_status==1"
                                                   class="btn btn-danger btn-xs" title="Back to Previous Position">
                                                    <i class="fa fa-backward"></i>
                                                </a>
                                                @endif
                                            </div>
                                            <div class="col-xs-1">
                                                @if(UserPermission::userPermissionExists('makeVerified'))
                                                <a href="" data-toggle="modal" ng-click="ppp(promotionAnsar.id)"
                                                ng-disabled="promotionAnsar.promoted_status==1||promotionAnsar.not_verified_status==1"
                                                data-toggle="modal" modal-show data="promotionAnsar" callback="modal(data)" target="#make-varified-modal"
                                                data-target="#make-varified-modal" class="btn btn-success btn-xs"
                                                   title="Make Verified ">
                                                    <i class="fa fa-check-square-o"></i>
                                                </a>
                                                @endif
                                            </div>
                                            <div class="col-xs-1">
                                                @if(UserPermission::userPermissionExists('rankUpdate'))
                                                <a href="" data-toggle="modal" ng-click="ppp(promotionAnsar.id)"
                                                ng-disabled="promotionAnsar.not_verified_status==0||promotionAnsar.promoted_status==1||promotionAnsar.status=='Completed'"
                                                data-toggle="modal" modal-show data="promotionAnsar" callback="modal(data)" target="#verify-rank-promotion-modal"
                                                data-target="#rank-update-modal" class="btn btn-warning btn-xs"
                                                   title="Rank Update ">
                                                    <i class="fa fa-angle-double-up"></i>
                                                </a>
                                                @endif
                                            </div>
                                            
                                            <div class="col-xs-1">
                                                @if(UserPermission::userPermissionExists('SendToPanelFromAnsarList'))
                                                <a href="" data-toggle="modal" ng-click="ppp(promotionAnsar.id)"
                                                ng-disabled="promotionAnsar.not_verified_status==0||promotionAnsar.promoted_status==0"
                                                class="btn btn-info btn-xs" data-toggle="modal" modal-show data="promotionAnsar" callback="modal(data)" target="#send-to-panel-modal"
                                                data-target="#send-to-panel-modal" title="Send to Panel">
                                                    <i class="fa fa-paper-plane"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    <tr ng-show="allPromotionAnsar.length==0">
                                        <td class="warning" colspan="11">No information found</td>
                                    </tr>
                                </table>
                            </div>
                            </div>
                            
                           <div class="row" style="margin-top:10px;">
                                <div class="col-sm-4">
                                    <label for="item_par_page">Show :</label>
                                    <select name="item_per_page" ng-change="loadPage()" id="item_par_page"
                                            ng-model="itemPerPage">
                                        <option value="10" ng-selected="true">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        
                                    </select>
                                </div>

                                <div class="col-sm-8"> 
                                    <div class="table_pagination" ng-if="pages.length>1">
                                        <ul class="pagination" style="margin: 0">
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
            </div>

            <div id="back-to-previous-modal" class="modal fade" role="dialog" @keydown.esc="closeModalLogin" tabindex="0">
                <div class="modal-dialog" style="width: 40%;overflow: auto;">
                    <div class="modal-content">
                      
            <form class="form" role="form" method="post" ng-submit="backtoPrevious(getSingleRow.row_id)">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            ng-click="modalOpen = false">&times;
                    </button>
                    <h3 class="modal-title">Back to Previous Position Confirmation</h3>
                    {{-- <h4 class="modal-title">Row ID:[[getSingleRow.row_id]]</h4> --}}
                </div>
                <div class="modal-body">
                    <div class="register-box" style="width: auto;margin: 0">
                        <div class="register-box-body  margin-bottom">
                            <div class="row">
                                <div class="col-sm-6">
                                </div>
                            </div>
                       
                            <button class="btn btn-primary pull-right" type="submit">
                    <i ng-show="submitting" class="fa fa-spinner fa-pulse"></i>&nbsp;Confirm
                </button>
                
                
                        </div>
                    </div>

                </div>
            
            </form>
                    </div>
                </div>
            </div>

            <div id="make-varified-modal" class="modal fade" role="dialog"  @keydown.esc="closeModalLogin" tabindex="0">
                <div class="modal-dialog" style="width: 40%;overflow: auto;">
                    <div class="modal-content">
                    
            <form class="form" role="form" method="post" ng-submit="makeVerified(getSingleRow.row_id)">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" 
                            ng-click="modalOpen = false" >&times;
                    </button>
                    <h3 class="modal-title">Verified Confirmation</h3>
                    {{-- <h4 class="modal-title">Row ID:[[getSingleRow.row_id]]</h4> --}}
                    {{-- <h4 ng-if="getSingleRow.verified==0">This Ansar is not verified! </h4>
                    <h4 ng-if="getSingleRow.verified!=0">This Ansar is verified! </h4> --}}
                    {{-- <h4 class="modal-title">Verified Status:[[getSingleRow.ansar_id]]</h4> --}}

                </div>
                <div class="modal-body">
                    <div class="register-box" style="width: auto;margin: 0">
                        <div class="register-box-body  margin-bottom">
                            <div class="row">
                                <div class="col-sm-10" style="font-size: initial;">
                                    
                                               {{-- <label ng-if="getSingleRow.verified==0" for="makeVerifiedCheckBox">
                                                <input id="makeVerifiedCheckBox" type="checkbox" value="true"
                                                       ng-model="makeVerifiedCheckBox">&nbsp;&nbsp;Do you want to Verified this Ansar?
                                                       
                                                </label> --}}
                                                <label>Do you want to Verified this Ansar?</label>
                                </div>
                            </div>
                            
                            <button class="btn btn-primary pull-right" type="submit" >
                                <i ng-show="submitting" class="fa fa-spinner fa-pulse"></i>&nbsp;Confirm
                            </button>
                            
                            
                        </div>
                    </div>
                </div>
            </form>
                    </div>
                </div>
            </div>

            <div id="rank-update-modal" class="modal fade" role="dialog" @keydown.esc="closeModalLogin" tabindex="0">
                <div class="modal-dialog" style="width: 40%;overflow: auto;">
                    <div class="modal-content">
                    
            <form class="form" role="form" method="post" ng-submit="rankUpdate(getSingleRow.row_id)">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" 
                            ng-click="modalOpen = false" >&times;
                    </button>
                    <h3 class="modal-title">Rank Update Confirmation</h3>
                    {{-- <h4 class="modal-title">Row ID:[[getSingleRow.row_id]]</h4> --}}
                    
                </div>
                <div class="modal-body">
                    <div class="register-box" style="width: auto;margin: 0">
                        <div class="register-box-body  margin-bottom">
                            <div class="row">
                                <div class="col-sm-12">
                                    {{-- <p ng-if="[[promotionAnsar.promoted_rank]]==2">Eligible</p>
                                    <p ng-if="[[promotionAnsar.promoted_rank]]==3">Not Eligible</p> --}}
                                    <p ng-if="getSingleRow.promoted_rank==2" style="margin-top: -20px;font-size: 19px;">Promoted this ansar as "Assistant Platoon Commander"! </p>
                                    <p ng-if="getSingleRow.promoted_rank==3" style="margin-top: -20px;font-size: 19px;">Promoted this ansar as "Platoon Commander"! </p>

                                    {{--Promoted this Ansar as  --}}
                                               {{--<label for="rankUpdateOption">
                                                 <input id="rankUpdateOption" type="checkbox" value="true"
                                                       ng-model="rankUpdateOption">&nbsp;&nbsp;Do you want to update Rank?
                                                       
                                                </label> --}}
                                                
                                               {{-- <div ng-show="rankUpdateOption == true" >
                                                   
                                                   <select name="" class="form-control"  ng-model="ranks">
                                                        <option value="">--Select a Rank--</option>
                                                        <option value="apc">APC</option>
                                                        <option value="pc">PC</option>
                                                    </select>
                                               </div> --}}
                                </div>
                            </div>
                            
                            <button class="btn btn-primary pull-right" type="submit" style="margin-top: 5px;">
                                <i ng-show="submitting" class="fa fa-spinner fa-pulse"></i>&nbsp;Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </form>
                    </div>
                </div>
            </div>

            <div id="send-to-panel-modal" class="modal fade" role="dialog" @keydown.esc="closeModalLogin" tabindex="0">
                <div class="modal-dialog" style="width: 40%;overflow: auto;">
                    <div class="modal-content">
                      
            <form class="form" role="form" method="post" ng-submit="sendToPanel(getSingleRow.row_id)">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            ng-click="modalOpen = false">&times;
                    </button>
                    <h3 class="modal-title">Confirmation for Send to Panel</h3>
                    {{-- <h4 class="modal-title">Row ID:[[getSingleRow.row_id]]</h4> --}}
                </div>
                <div class="modal-body">
                    <div class="register-box" style="width: auto;margin: 0">
                        <div class="register-box-body  margin-bottom">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;<span
                                            ng-show="isVerifying"><i
                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                            class="text-danger"
                                            ng-if="isVerified&&!memorandumId">Memorandum no. is required.</span><span
                                            class="text-danger"
                                            ng-if="isVerified&&memorandumId">This id already taken.</span></label>
                                <input ng-blur="verifyMemorandumId()" ng-model="memorandumId"
                                       type="text" class="form-control" name="memorandum_id"
                                       placeholder="Enter Memorandum no." required>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Panel Date <span class="text-danger"
                                                                                      ng-show="panelForm.panel_date.$touched && panelForm.panel_date.$error.required"> Date is required.</span></label>
                                        &nbsp;&nbsp;&nbsp;</label>
                                        {!! Form::text('panel_date', $value = null, $attributes = array('class' => 'form-control', 'id' => 'panel_date', 'ng_model' => 'panel_date', 'required','date-picker'=>'moment().format("DD-MMM-YYYY")')) !!}
                                    </div>
                                </div>
                            </div>
                       
                            <button class="btn btn-primary pull-right" type="submit">
                                <i ng-show="submitting" class="fa fa-spinner fa-pulse"></i>&nbsp;Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </form>
                    </div>
                </div>
            </div>

         
        </section>
    </div>
@stop