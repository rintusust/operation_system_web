<?php /*User: Shreya*/ ?>
<?php /*Date: 12/3/2015*/ ?>
<?php /*Time: 12:34 PM*/ ?>

<?php $__env->startSection('title','Thana Information'); ?>
<?php $__env->startSection('small_title'); ?>
    <a style="background: #3c8dbc; color: #FFFFFF;" class="btn btn-primary btn-sm" href="<?php echo e(URL::to('operation/thana_form')); ?>">
        <span class="glyphicon glyphicon-plus"></span> Add New Thana
    </a>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('thana_information_list'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('ThanaViewController', function ($scope, $http, $sce, $compile) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.selectedDistrict = "all";
            $scope.selectedDivision = "all";
            $scope.isLoading = false;
            $scope.districts = [];
            $scope.division = [];
            $scope.thanas = [];
            $scope.itemPerPage = 20;
            $scope.currentPage = 0;
            $scope.pages = [];
            $scope.loadingDistrict = false;
            $scope.loadingDivision = true;
            $scope.loadingPage = [];
            $scope.allLoading = true;
            $scope.errorFound=0;
            $scope.loadPagination = function () {
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    })
                    $scope.loadingPage[i] = false;
                }
                if ($scope.numOfPage > 0)$scope.loadPage($scope.pages[0]);
                else $scope.loadPage({pageNum: 0, offset: 0, limit: $scope.itemPerPage, view: 'view'});
            }
            $scope.loadPage = function (page, $event) {
                if ($event != undefined)  $event.preventDefault();
                $scope.currentPage = page.pageNum;
                $scope.loadingPage[page.pageNum] = true;
                $http({
                    url: '<?php echo e(URL::to('operation/thana_view_details')); ?>',
                    method: 'get',
                    params: {
                        offset: page.offset,
                        limit: page.limit,
                        division: $scope.selectedDivision,
                        unit: $scope.selectedDistrict,
                        view: 'view'
                    }
                }).then(function (response) {
                    $scope.thanas = response.data.thanas;
                    //console.log($scope.thanas)
//                    $compile($scope.ansars)
                    $scope.loadingPage[page.pageNum] = false;
                })
            }
            $scope.loadTotal = function () {
                $scope.allLoading = true;
                //alert($scope.selectedDivision)
                $http({

                    url: '<?php echo e(URL::to('operation/thana_view_details')); ?>',
                    method: 'get',
                    params: {
                        division: $scope.selectedDivision,
                        unit: $scope.selectedDistrict,
                        view: 'count'
                    }
                }).then(function (response) {

                    $scope.total = response.data.total;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);
                    $scope.loadPagination();
                    $scope.isLoading = false;
                    $scope.errorFound=0;
                    $scope.allLoading = false;
                    //alert($scope.total)
                },function(response){
                    $scope.errorFound=1;
                    $scope.total = 0;
                    $scope.allLoading = false;
                    $scope.thanas = $sce.trustAsHtml("<tr class='warning'><td colspan='"+$('.table').find('tr').find('th').length+"'>"+response.data+"</td></tr>");
                    //alert($(".table").html())
                    $scope.pages = [];

                })
            }
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
            $http({
                method: 'get',
                url: '<?php echo e(URL::to('operation/DivisionName')); ?>'
//                params: {id: d_id}
            }).then(function (response) {
                $scope.division = response.data;
                $scope.loadingDivision = false;
                $scope.loadingDistrict = true;
            })

            $scope.loadDistrict = function (d_id) {
                $scope.allLoading = true;
                $http({
                    method: 'get',
                    url: '<?php echo e(URL::to('operation/DistrictName')); ?>',
                    params: {id: d_id}
                }).then(function (response) {

                    $scope.districts = response.data;
                    $scope.selectedDistrict = "all";
                    $scope.loadingDistrict = false;
                    $scope.loadTotal()
                })
            }
            $scope.loadTotal()

        })
    </script>
    <div ng-controller="ThanaViewController">
        <?php if(Session::has('success_message')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> <?php echo e(Session::get('success_message')); ?>

                </div>
            </div>
        <?php endif; ?>
        <?php if(Session::has('error_message')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php echo e(Session::get('error_message')); ?>

                </div>
            </div>
        <?php endif; ?>
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="nav-tabs-custom">
                    <?php /*<ul class="nav nav-tabs">
                        <li class="active">
                            <a> Thana Information</a>
                        </li>
                        <li>
                            <a style="background: #3c8dbc; color: #FFFFFF;" class="btn btn-primary btn-sm"
                               href="<?php echo e(URL::to('operation/thana_form')); ?>">
                                <span class="glyphicon glyphicon-plus"></span> Add New Thana
                            </a>
                            */ ?><?php /*<a data-toggle="tab" href="#pc">Transfer Ansar</a>*/ ?><?php /*
                        </li>
                    </ul>*/ ?>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo app('translator')->get('title.range'); ?>&nbsp;
                                            <img ng-show="loadingDivision" src="<?php echo e(asset('dist/img/facebook.gif')); ?>"
                                                 width="16"></label>
                                        <select class="form-control" ng-model="selectedDivision"
                                                ng-change="loadDistrict(selectedDivision)">
                                            <option value="all">All</option>
                                            <option ng-repeat="d in division" value="[[d.id]]">[[d.division_name_eng]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo app('translator')->get('title.unit'); ?>&nbsp;
                                            <img ng-show="loadingThana" src="<?php echo e(asset('dist/img/facebook.gif')); ?>"
                                                 width="16">
                                        </label>
                                        <select class="form-control" ng-model="selectedDistrict"
                                                ng-change="loadTotal(selectedDistrict)">
                                            <option value="all">All</option>
                                            <option ng-repeat="t in districts" value="[[t.id]]">[[t.unit_name_eng]]
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>SL. No</th>
                                        <th>Thana Name</th>
                                        <th>Thana Name in Bangla</th>
                                        <th>Thana Code</th>
                                        <th>Unit Name</th>
                                        <th>Division Name</th>
                                        <th>Action</th>
                                    </tr>
                                    <tbody>
                                    <tr ng-if="thanas.length==0">
                                        <td colspan="8" class="warning no-ansar">
                                            No thana available to see
                                        </td>
                                    </tr>
                                    <tbody ng-if="errorFound==1" ng-bind-html="thanas"></tbody>
                                    <tr ng-if="thanas.length>0" ng-repeat="a in thanas">
                                        <td>
                                            [[((currentPage)*itemPerPage)+$index+1]]
                                        </td>
                                        <td>
                                            [[a.thana_name_eng]]
                                        </td>
                                        <td>
                                            [[a.thana_name_bng]]
                                        </td>
                                        <td>
                                            [[a.thana_code]]
                                        </td>
                                        <td>
                                            [[a.unit_name_eng]]
                                        </td>
                                        <td>
                                            [[a.division_name_eng]]
                                        </td>

                                        <td>
                                            <div class="col-xs-1">
                                                <a href="<?php echo e(URL::to('operation/thana_edit/'.'[[a.id]]')); ?>"
                                                   class="btn btn-primary btn-xs" title="Edit"><span
                                                            class="glyphicon glyphicon-edit"></span></a>
                                            </div>
                                            <div class="col-xs-1">
                                                <?php /*<a href="<?php echo e(URL::to('operation/thana_delete/'.'[[a.id]]')); ?>"
                                                   class="btn btn-primary btn-xs" title="Delete" style="background: #a41a20; border-color: #80181E"><span
                                                            class="glyphicon glyphicon-trash"></span></a>*/ ?>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>