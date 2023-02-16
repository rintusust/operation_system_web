<?php $__env->startSection('title','Manage User'); ?>
<?php $__env->startSection('small_title'); ?>
    <a href="<?php echo e(action('UserController@userRegistration')); ?>" class="btn btn-primary btn-sm">
        <span class="glyphicon glyphicon-user"></span> Add New User
    </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('all_user'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <style>
        .content-header h1::after {
            content: '';
            display: block;
            clear: both;
        }
    </style>
    <script>

        GlobalApp.controller('UserController', function ($scope, $http, $sce) {
            var totalCount = parseInt('<?php echo e(config('app.item_per_page')); ?>');
            $scope.limit = totalCount;
            $scope.total = '<?php echo e($total_user); ?>';
            $scope.totalPages = Math.ceil(parseInt($scope.total) / totalCount);
            $scope.pages = [];
            $scope.currentPage = 0;
            $scope.users = $sce.trustAsHtml("");
            $scope.showDialog = false;
            $scope.result = '';
            $scope.blockStatus = [];
            $scope.allLoading = false;
            $scope.confirmURL = "";
            $scope.isSearching = false;
            $scope.noFound = false;
            $scope.searchUserName = '';
//            alert($scope.showDialog)
            $scope.loadPagination = function () {
                for (var i = 0; i < $scope.totalPages; i++)
                    $scope.pages[i] = {pageNum: i, totalCount: totalCount}
            }
            $scope.loadPage = function (pageNum, event) {
                if (event != null) event.preventDefault();
                $scope.allLoading = true;
                $scope.currentPage = pageNum==undefined?0:pageNum;
                $http({
                    url: '<?php echo e(action('UserController@getAllUser')); ?>',
                    method: 'get',
                    params: {limit: totalCount, offset: pageNum * totalCount,user_name: $scope.searchUserName}
                }).then(function (response) {
                    $scope.users = response.data.users;
                    $scope.blockStatus = [];
                    $scope.total = response.data.total
                    $scope.totalPages = Math.ceil(parseInt($scope.total) / totalCount);
                    $scope.loadPagination();
                    $scope.users.forEach(function (v) {
                        $scope.blockStatus.push(parseInt(v.status)==1?true:false);
                    })
                    $scope.allLoading = false;
                })
            }
            $scope.blockUser = function (id, index) {
                $http({
                    method: 'post',
                    url: '<?php echo e(URL::to('/block_user')); ?>',
                    data: {user_id: id}
                }).then(function (response) {
                    $scope.result = response.data.status;
                    if (response.data.status)$scope.blockStatus[index] = 0
                })
            }
            $scope.unblockUser = function (id, index) {
                $http({
                    method: 'post',
                    url: '<?php echo e(URL::to('/unblock_user')); ?>',
                    data: {user_id: id}
                }).then(function (response) {
                    $scope.result = response.data.status;
                    if (response.data.status)$scope.blockStatus[index] = 1
                })
            }
            $scope.searchId = function () {
                $scope.noFound = false;
                if(!$scope.searchUserName){
                    $scope.isSearching = false;
                    $scope.loadPage(0, null);
                    return;
                }
                $scope.allLoading = true;
                $scope.loading = true;
                $scope.isSearching = true;
                $http({
                    url: "<?php echo e(URL::to('/user_search')); ?>",
                    method: 'get',
                    params: {user_name: $scope.searchUserName}
                }).then(function (response) {
                    $scope.blockStatus = []
                    $scope.loading = false;
                    $scope.searchedUser = response.data;
                    $scope.searchedUser.forEach(function (v) {
                        $scope.blockStatus.push(v.status)
                    })
                    $scope.allLoading = false;
                    // console.log($scope.searchedUser);
                })
            }
            $scope.exportData = function () {
                var page = $scope.exportPage;

                $http({
                    url: '<?php echo e(action('UserController@manageUserInfoDetails')); ?>',
                    method: 'get',
                    params: {
                        q: $scope.q
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
            function generateReport() {
                $http({
                    url: '<?php echo e(URL::to('generate/file')); ?>/' + $scope.export_data.id,
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
            $scope.comarator = function (v1, v2) {
                if(isNaN(v1)) return -1
            }
            $scope.loadPage(0, null);
        })
        GlobalApp.directive('confirmDialog', function () {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var d = JSON.parse(attr.confirmDialog)
                    $(elem).confirmDialog({
                        message: 'Are you sure want to ' + d.type + ' this user',
                        ok_callback: function (element) {

                            switch (d.type) {
                                case 'block':
                                    scope.blockUser(d.id, d.index)
                                    break;
                                case 'unblock':
                                    scope.unblockUser(d.id, d.index)
                                    break;
                            }
                            //scope.blockUser(attr.confirmDialog);
                        },
                        cancel_callback: function (element) {
                        }
                    })
                }
            }

        })
    </script>

    <div ng-controller="UserController">
        <?php if(Session::has('success_message')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> <?php echo e(Session::get('success_message')); ?>

                </div>
            </div>
        <?php endif; ?>
        <section class="content">
            <div class="box box-primary">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="row">
                    <div class="col-sm-9">
                        <h4 style="padding-left: 8px;padding-top: 6px">Total users : [[total]]</h4>

                        <button id="export-report" ng-disabled="export_page||export_all"
                                ng-click="exportData()" class="btn btn-default" style="margin-left: 10px">
                            <i ng-show="!export_page" class="fa fa-file-excel-o"></i><i ng-show="export_page"
                                                                                        class="fa fa-spinner fa-pulse"></i>&nbsp;Export

                        </button>
                    </div>

                    <div class="col-sm-3">

                        <form ng-submit="loadPage(0)" class="sidebar-form">
                            <div class="input-group">

                                <input type="text" name="q" ng-model="searchUserName" class="form-control"
                                       placeholder="Search by user name...">
                                <span class="input-group-btn">
                                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i
                                                class="fa fa-search"></i></button>
                                 </span>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-condensed" id="user-table">

                            <tr>
                                <th>SL. No</th>
                                <th>User Name</th>
                                <th>User Parent</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Activity</th>
                                <th>Action</th>
                            </tr>
                            <tr ng-if="users==undefined||users.length==0">
                                <td colspan="7">No user found</td>
                            </tr>
                            <tr ng-repeat="user in users">
                                <td>[[(limit*currentPage)+$index+1]]</td>
                                <td><i ng-if="user.total_time>0" style="vertical-align: middle;" class="fa fa-circle text-success"></i><span style="padding-left: 5px">[[user.user_name]]</span> </td>
                                <td>
                                    [[user.user_parent.user_name]]
                                </td>
                                <td>
                                    [[user.user_profile.first_name+" "+user.user_profile.last_name]]
                                </td>

                                <td>[[user.user_profile.email]]</td>
                                <td ng-switch on="user.user_log.user_status">
                                    <span ng-switch-when="0"> New. Not login yet</span>
                                    <span ng-switch-when="1"> Last Login at&nbsp;[[user.user_log.last_login]]</span>
                                    <span ng-switch-default>Blocked</span>
                                </td>
                                <td style="width: 121px">
                                    <div class="row" style="margin-right: 0;min-width: 100px">
                                        <div class="col-xs-1">
                                            <a class="btn btn-primary btn-xs"
                                               href="<?php echo e(URL::to('/edit_user')); ?>/[[user.id]]" title="edit"><span
                                                        class="glyphicon glyphicon-edit"></span></a>
                                        </div>

                                        <div class="col-xs-1">
                                            <a class="btn btn-danger btn-xs" ng-show="blockStatus[$index]"
                                               confirm-dialog='{"id":[[user.id]],"index":[[$index]],"type":"block"}'
                                               class="block-user" title="block">
                                                <span class="fa fa-ban"></span>
                                            </a>
                                            <a ng-show="!blockStatus[$index]" class="btn btn-success btn-xs"
                                               confirm-dialog='{"id":[[user.id]],"index":[[$index]],"type":"unblock"}'
                                               class="block-user" title="unblock">
                                                <span class="fa fa-unlock"></span>
                                            </a>
                                        </div>
                                        <div class="col-xs-1">
                                            <a class="btn btn-success btn-xs"
                                               href="<?php echo e(URL::to('/edit_user_permission')); ?>/[[user.id]]"
                                               title="edit permission"><span
                                                        class="glyphicon glyphicon-lock"></span></a>
                                        </div>
                                        <div class="col-xs-1">
                                            <a class="btn btn-success btn-xs"
                                               href="<?php echo e(URL::to('/action_log')); ?>/[[user.id]]"
                                               title="User Action Log">
                                                <i class="fa fa-file"></i></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="table_pagination" ng-show="totalPages>1&& !isSearching">
                        <ul class="pagination">
                            <li ng-class="{disabled:currentPage==0}">
                                <span ng-show="currentPage==0">&laquo;</span>
                                <a href="#" ng-click="loadPage(currentPage-1,$event)"
                                   ng-hide="currentPage==0">&laquo;</a>
                            </li>
                            <li ng-repeat="page in pages" ng-class="{active:currentPage==page.pageNum}">
                                <span ng-show="currentPage==page.pageNum">[[page.pageNum+1]]</span>
                                <a href="#" ng-click="loadPage(page.pageNum,$event)"
                                   ng-hide="currentPage==page.pageNum">[[page.pageNum+1]]</a>
                            </li>
                            <li ng-class="{disabled:currentPage==totalPages-1}">
                                <span ng-show="currentPage==totalPages-1">&raquo;</span>
                                <a href="#" ng-click="loadPage(currentPage+1,$event)"
                                   ng-hide="currentPage==totalPages-1">&raquo;</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>