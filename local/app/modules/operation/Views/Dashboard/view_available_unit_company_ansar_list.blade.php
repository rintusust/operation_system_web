@extends('template.master')
@section('title',$pageTitle)
@section('breadcrumb')

@endsection
@section('content')
    <script>
        GlobalApp.controller('AnsarListController', function ($scope, $http, $sce, $parse, notificationService) {

            $scope.rank = 'all';
            $scope.queue = [];
            $scope.retiredAnsarData = [];
            $scope.addAnsarBtn = false;
            $scope.exportPage = '';
            var p = '';
            $scope.pageTitle = '';
            for (var i = 0; i < p.length; i++) {
                $scope.pageTitle += capitalizeLetter(p[i]);
                if (i < p.length - 1) $scope.pageTitle += " ";
            }
            $scope.defaultPage = {pageNum: 0, offset: 0, limit: $scope.itemPerPage, view: 'view'};
            $scope.total = 0;
            $scope.param = {};
            $scope.numOfPage = 0;
            $scope.itemPerPage = parseInt("{{config('app.item_per_page')}}");
            $scope.currentPage = 0;
            $scope.ansars = [];
            $scope.ansar_id = [];
            $scope.pages = [];
            $scope.loadingPage = [];
            $scope.allLoading = true;
            $scope.showLoadScreen1 = true;
            $scope.orderBy = "";
            //$scope.from_date = moment().subtract(1, 'years').format("D-MMM-YYYY");
            //$scope.to_date = moment().format("D-MMM-YYYY");
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
                    url: '{{URL::to('HRM/get_available_unit_company_ansar_list')}}',
                    method: 'get',
                    params: {
                        offset: page == undefined ? 0 : page.offset,
                        limit: page == undefined ? $scope.itemPerPage : page.limit,
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        q: $scope.q,
                        rank: $scope.rank,
                        sortBy: $scope.orderBy,
                    }
                }).then(function (response) {
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadPage();
                    $scope.ansars = response.data.ansars;
                    $scope.retiredAnsarData = response.data.retiredAnsarData;
                    console.log($scope.ansars);
                    if($scope.ansars){
                        var unitAnsarCount = $scope.ansars.length;
                        if(unitAnsarCount < 115 ){
                            $scope.addAnsarBtn = true;
                        }
                    }
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.allLoading = false;
                    $scope.total = sum(response.data.total);
                    $scope.gCount = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                })
            };
            $scope.exportData = function (type) {
                var page = $scope.exportPage;
                if (type == 'page') $scope.export_page = true;
                else $scope.export_all = true;
                $http({
                    url: '{{URL::to('HRM/get_available_unit_company_ansar_list')}}',
                    method: 'get',
                    params: {
                        offset: page == undefined ? 0 : page.offset,
                        limit: page == undefined ? $scope.itemPerPage : page.limit,
                        unit: $scope.param.unit == undefined ? 'all' : $scope.param.unit,
                        division: $scope.param.range == undefined ? 'all' : $scope.param.range,
                        gender: $scope.param.gender == undefined ? 'all' : $scope.param.gender,
                        q: $scope.q,
                        rank: $scope.rank,
                        sortBy: $scope.orderBy,
                        export: type,
                        type: 'available_unit'
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
            $scope.uploadFileForm = function(){
                var index = 0;
                var fd = new FormData(document.getElementById("uploadFileForm"))
                $scope.allLoading = true;
                $http({
                    url:"{{URL::route('HRM.Dashboard.add_unit_company_by_uploading_file')}}",
                    data:fd,
                    method:'post',
                    headers:{
                        "Content-Type":undefined
                    },
                    eventHandlers:{
                        progress:function (event) {
                            var response = event.currentTarget.response;
                            $scope.message = response.substr(index,response.length-index);
                            console.log(response.substr(index,response.length-index))
                            index = response.length;
                        }
                    }
                }).then(function (response) {
                    console.log(response);
                    if (response.data.status) {
                        notificationService.notify('success', response.data.message);
                        $("#batch-upload-modal").modal('hide');
                        $scope.loadPage();

                    } else {
                        notificationService.notify('error', response.data.message);
                        $("#batch-upload-modal").modal('hide');
                        $scope.loadPage();
                    }
                }, function (response) {
                    // $scope.submitting = false;
                    notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                })
            }

            $scope.addAnsar = function () {
                //if($scope.ansar_id == "") {alert("Please Enter a Ansar ID");}

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

                    } else {
                        notificationService.notify('error', response.data.message)
                    }
                }, function (response) {
                    $scope.submitting = false;
                    notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                })
            };

            $scope.deleteAnsarRequest = function (id,comment) {
                //if($scope.ansar_id == "") {alert("Please Enter a Ansar ID");}
                //alert("Anik");

                if (id) {
                    $scope.submitting = true;
                    $http({
                        url: "{{URL::to('HRM/deleteAnsarRequest')}}",
                        method: 'post',
                        data: angular.toJson({
                            request_id: id,
                            comment:  comment
                        })
                    }).then(function (response) {
                        console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('success', response.data.message);
                            $("#confirm-panel-modal").modal('hide')
                        } else {
                            notificationService.notify('error', response.data.message)
                        }
                        $scope.ansars.splice($scope.ansars.indexOf($scope.getSingleRow), 1)
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
                    // alert($scope.comment );
                }
            };


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
                //  alert($scope.comment );
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
    <div ng-controller="AnsarListController" style="position: relative;">
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
                    {!! $custom_filter !!}
                    <div>
                        {!! $custom_view !!}
                    </div>
                    <div class="row">
                        <div class="col-xs-12">

                            <div class="btn-group btn-group-sm pull-right">
                                <!--<button id="print-report" class="btn btn-default"><i
                                            class="fa fa-print"></i>&nbsp;Print
                                </button>
                                <button id="export-report" ng-disabled="export_page||export_all"
                                        ng-click="exportData('page')" class="btn btn-default ">
                                    <i ng-show="!export_page" class="fa fa-file-excel-o"></i><i ng-show="export_page"
                                                                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Export
                                    this page
                                </button>
                                <button ng-disabled="export_page||export_all" ng-click="exportData('all')"
                                        id="export-report-all" class="btn btn-default">
                                    <i ng-show="!export_all" class="fa fa-file-excel-o"></i><i ng-show="export_all"
                                                                                               class="fa fa-spinner fa-pulse"></i>&nbsp;Export
                                    all
                                </button>-->
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

                            </h4>
                        </div>
                        <div class="col-md-4 col-sm-12" style="margin-top: 10px">
                            <database-search q="q" queue="queue" on-change="loadPage()"></database-search>
                        </div>
                    </div>

                    <section class="content">

                        <div class="box box-solid" style="min-height: 200px;">
                            <div class="nav-tabs-custom">

                                <div class="tab-content">
                                    <div class="tab-pane active">
                                        <div class="row" style="margin-left:0; margin-right: 0;padding-bottom: 10px">

                                            <div class="col-md-4 pull-left">


                                                <div ng-if="retired.length > 0 >
                                                Retired Ansars in Unit :

                                                    <span ng-repeat="retired in retiredAnsarData">
                                                [[retired.ansar_id]]
                                                </span>

                                                </div>



                                            </div>

                                        @if((auth()->user()->type ==11) || (auth()->user()->type ==22) )

                                                <div class="col-md-4 pull-right">

                                                    <a class="btn btn-warning pull-right" ng-show="addAnsarBtn" data-toggle="modal"
                                                       data-target="#confirm-panel-modal" id="confirm-panel" style="margin-left:5px"><span
                                                                open-hide-modal disabled> Add Ansar</span></a>&nbsp;
                                                    <a class="btn btn-info pull-right" ng-show="addAnsarBtn" data-toggle="modal"
                                                       data-target="#batch-upload-modal" id="confirm-panel" ><span
                                                                open-hide-modal disabled> Batch Upload</span></a>&nbsp;
                                                </div>

                                            @endif
                                        </div>

                                        <div id="print_table">
                                            <div class="table-responsive">
                                                <div>
                                                    <h4 class="text text-center print-open">{{$pageTitle}}</h4>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered" id="pc-table">
                                                            <tr class="info">
                                                                <th class="text-center">SL</th>
                                                                <th class="text-center">Ansar ID</th>
                                                                <th class="text-center">Ansar Name</th>
                                                                <th class="text-center">Rank</th>
                                                                <th class="text-center">Added Date</th>
{{--                                                                <th class="text-center">Status</th>--}}
                                                                <th class="text-center">Operation</th>
                                                            </tr>
                                                            <tr ng-show="ansars.length==0">
                                                                <td colspan="12" class="warning no-ansar">No Ansar is available to show</td>
                                                            </tr>
                                                            <tbody ng-if="errorFound==1" ng-bind-html="ansars"></tbody>

                                                            <tr ng-show="ansars.length > 0" ng-repeat="a in ansars">
                                                                <td class="text-center">[[$index+1]]</td>
                                                                <td class="text-center">[[a.id]]</td>
                                                                <td class="text-center">[[a.name]]</td>
                                                                <td class="text-center">[[a.rank]]</td>
                                                                <td class="text-center">[[a.created_at|dateformat:"DD MMM, YYYY"]]</td>
{{--                                                                <td class="text-center">[[a.status]]</td>--}}

                                                                <td class="text-center">

                                                                    @if((auth()->user()->type ==11) || (auth()->user()->type ==22) )
                                                                    <a class="btn btn-danger btn-xs verification" title="Delete Request" data-toggle="modal"
                                                                       modal-show data="a" callback="modal(data)" target="#delete-ansar-modal"
                                                                       data-target="#delete-ansar-modal">
                                                                        <span class="fa fa-trash"></span>
                                                                    </a>
                                                                    @endif
                                                                    {{-- confirm event="click" message="Are you sure want to delete this request?" --}}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label for="item_par_page">Show :</label>
                                                <select name="item_per_page" ng-change="loadPage()" id="item_par_page"
                                                        ng-model="itemPerPage">
                                                    <option value="20" ng-selected="true">20</option>
                                                    <option value="40">40</option>
                                                    <option value="60">60</option>
                                                    <option value="80">80</option>
                                                    <option value="100">100</option>
                                                    <option value="115">115</option>

                                                </select>
                                            </div>

                                            <div class="table_pagination pull-right" ng-if="pages.length>1">
                                                <ul class="pagination" style="margin: 0">
                                                    <li ng-class="{disabled:currentPage == 0}">
                                                        <a href="#" ng-click="loadPage(pages[0],$event)">&laquo;&laquo;</a>
                                                    </li>
                                                    <li ng-class="{disabled:currentPage == 0}">
                                                        <a href="#"
                                                           ng-click="loadPage(pages[currentPage-1],$event)">&laquo;</a>
                                                    </li>
                                                    <li ng-repeat="page in pages|filter:filterMiddlePage"
                                                        ng-class="{active:page.pageNum==currentPage&&!loadingPage[page.pageNum],disabled:!loadingPage[page.pageNum]&&loadingPage[currentPage]}">
                                                        <span ng-show="currentPage == page.pageNum&&!loadingPage[page.pageNum]">[[page.pageNum+1]]</span>
                                                        <a href="#" ng-click="loadPage(page,$event)"
                                                           ng-hide="currentPage == page.pageNum||loadingPage[page.pageNum]">[[page.pageNum+1]]</a>
                                                        <span ng-show="loadingPage[page.pageNum]"
                                                              style="position: relative"><i class="fa fa-spinner fa-pulse"
                                                                                            style="position: absolute;top:10px;left: 50%;margin-left: -9px"></i>[[page.pageNum+1]]</span>
                                                    </li>
                                                    <li ng-class="{disabled:currentPage==pages.length-1}">
                                                        <a href="#"
                                                           ng-click="loadPage(pages[currentPage+1],$event)">&raquo;</a>
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
                        <!-- /.box
                        -footer -->

                        <!--Modal Open-->
                        <div id="delete-ansar-modal" class="modal fade" role="dialog">
                            <div class="modal-dialog" style="width: 40%;overflow: auto;">
                                <div class="modal-content">

                                    <form class="form" role="form" method="post" ng-submit="deleteAnsarRequest(getSingleRow.row_id,comment)">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                    ng-click="modalOpen = false">&times;
                                            </button>
                                            <h3 class="modal-title">Reason for Delete</h3>
                                            <h4 class="modal-title">Ansar ID:[[getSingleRow.id]]</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="register-box" style="width: auto;margin: 0">
                                                <div class="register-box-body  margin-bottom">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Comment.&nbsp;&nbsp;&nbsp;<span
                                                                            ng-show="isVerifying"><i
                                                                                class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span><span
                                                                            class="text-danger"
                                                                            ng-if="isVerified&&!comment">Comment is required.</span><span>
                                                                    {{-- class="text-danger"
                                                                    ng-if="isVerified&&comment">This id already taken.</span>--}}
                                                                </label>
                                                                <input ng-model="comment"
                                                                       type="text" class="form-control" name="comment"
                                                                       placeholder="Enter Comments." required>
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

                        <!--Modal Close-->
                        <!--Modal Open-->
                        <div id="confirm-panel-modal" class="modal fade" role="dialog">
                            <div class="modal-dialog" style="width: 40%;overflow: auto;">
                                <div class="modal-content">
                                    <div class="modal-header">

                                        <button type="button" class="close" data-dismiss="modal"
                                                ng-click="modalOpen = false">&times;</button>
                                        <h3 class="modal-title">Add to Unit Company</h3>
                                    </div>
                                    <div class="modal-body">
                                        <div class="register-box" style="width: auto;margin: 0">
                                            <div class="register-box-body  margin-bottom">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Ansar ID.&nbsp;&nbsp;&nbsp;</label>
                                                            <input ng-model="ansar_id"
                                                                   type="text" class="form-control" name="ansar_id"
                                                                   placeholder="Enter Ansar ID." required>


                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Comment.&nbsp;&nbsp;&nbsp;</label>
                                                            <input ng-model="request_comment"
                                                                   type="text" class="form-control" name="request_comment"
                                                                   placeholder="Comment" required>
                                                        </div>
                                                    </div>

                                                </div>
                                                <button class="btn btn-primary pull-right" ng-click="addAnsar()">
                                                    <i class="fa fa-check"></i>&nbsp;Confirm
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="batch-upload-modal" class="modal fade" role="dialog">
                            <div class="modal-dialog" style="width: 40%;overflow: auto;">
                                <div class="modal-content">
                                    <div class="modal-header">

                                        <button type="button" class="close" data-dismiss="modal"
                                                ng-click="modalOpen = false">&times;</button>
                                        <h3 class="modal-title">Unit Company Batch Upload</h3>
                                    </div>
                                    <div class="modal-body">
                                        <div class="register-box" style="width: auto;margin: 0">
                                            <div class="register-box-body  margin-bottom">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <form method="post" enctype="multipart/form-data" id="uploadFileForm" ng-submit="uploadFileForm()">
                                                                {!! csrf_field() !!}
                                                                <input type="hidden" name="range" ng-value="param.range">
                                                                <input type="hidden" name="unit" ng-value="param.unit">
                                                                <div class="form-group">
                                                                    <label for="" class="control-label">Upload File</label>
                                                                    <input type="file" name="applicant_id_list" class="form-control" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="control-label">Comment</label>
                                                                    <input type="text" name="comment" class="form-control" placeholder="Enter comment" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <button class="btn btn-primary pull-right" >Upload file</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>
                                                {{-- <button class="btn btn-primary pull-right" ng-click="addAnsar()">
                                                    <i class="fa fa-check"></i>&nbsp;Confirm
                                                </button> --}}
                                                {{-- <button class="btn btn-primary pull-right">Upload file</button> --}}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
@stop