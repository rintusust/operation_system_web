<?php $__env->startSection('title','Entry List'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('entry.list'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('VDPController',function ($scope, $http, httpService,$q, $sce) {
            $scope.param = {};

            $q.all([
                httpService.VdpDesignation()

            ]).then(function (response) {
                $scope.vdpDesignations = response[0];

            })
            console.log($scope.vdpDesignations);

            $scope.vdpList = $sce.trustAsHtml(`<div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <caption style="font-size: 20px;color:#111111">All VDP Member</caption>
                            <tr>
                                <th>#</th>
                                <th>VDP ID</th>
                                <th>Name(English)</th>
                                <th>Name(Bangla)</th>
                                <th>Date of Birth</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Thana</th>
                                <th>Union</th>
                                <th>Ward</th>
                                <th>Action</th>

                            </tr>
                            <tr>
                                <td colspan="11" class="bg-warning">No VDP info available
                                </td>
                            </tr>
                        </table>
                    </div>`);
            $scope.allLoading = false;
            $scope.loadPage = function (url) {
                $scope.allLoading = true;
                $http({
                    url:url||'<?php echo e(URL::route('operation.info.index')); ?>',
                    method:'get',
                    params:$scope.param
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.vdpList = $sce.trustAsHtml(response.data);
                },function (response) {
                    $scope.allLoading = false;
                })

            }

        })
        GlobalApp.directive('compileHtml',function ($compile) {
            return {
                restrict:'A',
                link:function (scope,elem,attr) {
                    var newScope;
                    scope.$watch('vdpList', function (n) {

                        if (attr.ngBindHtml) {
                            if(newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        })
    </script>
    <section class="content">
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
        <div class="box box-solid" ng-controller="VDPController">
            <div class="box-header">
                <filter-template
                        show-item="['range','unit','thana']"
                        type="all"
                        range-change="loadPage()"
                        unit-change="loadPage()"
                        thana-change="loadPage()"
                        data="param"
                        start-load="range"
                        on-load="loadPage()"
                        field-width="{range:'col-sm-4',unit:'col-sm-4',thana:'col-sm-4'}"
                >

                </filter-template>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="entry_unit" class="control-label">ইউনিট নির্বাচন করুন<sup class="text-red">*</sup>
                                <span class="pull-right">:</span>
                            </label>
                            <select class="form-control" name="vdpdesignation" ng-model="param.vdpdesignation" id="vdpdesignation" ng-change="loadPage()">
                                <option value="">--পদবী নির্বাচন করুন--</option>
                                <option ng-repeat="v in vdpDesignations" value="[[v.id]]">[[v.designation_name_bng]]</option>
                            </select>
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>