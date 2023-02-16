<?php /*User: Rintu*/ ?>


<?php $__env->startSection('title','Union Information'); ?>
<?php $__env->startSection('small_title'); ?>
    <a href="<?php echo e(URL::route('HRM.union.create')); ?>" class="btn btn-info btn-sm">
        <i class="fa fa-plus"></i>&nbsp;New Union
    </a>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('unit_information_list'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('UnionController', function ($scope, $http, $sce, $compile) {
            $scope.total = 0;
            $scope.numOfPage = 0;
            $scope.selectedDivision = "all";
            $scope.isLoading = false;
            $scope.division = [];
            $scope.unions = $sce.trustAsHtml(`<div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <caption style="font-size: 20px;color:#111111">All Unions</caption>
                            <tr>
            <th>#</th>
            <th>Union Name(English)</th>
            <th>Union Name(Bangla)</th>
            <th>Union Code</th>
            <th>Division</th>
            <th>District</th>
            <th>Thana</th>
            <th>Action</th>

        </tr>
                            <tr>
                                <td colspan="8" class="bg-warning">No union available
                                </td>
                            </tr>
                        </table>
                    </div>`);
            $scope.itemPerPage = parseInt('<?php echo e(config('app.item_per_page')); ?>');
            $scope.currentPage = 0;
            $scope.pages = [];
            $scope.loadingDivision = true;
            $scope.loadingPage = [];
            $scope.errorFound=0;
            $scope.allLoading = true;
            $scope.loadPage = function (url) {
                //if ($event !== undefined)  $event.preventDefault();
                $http({
                    url: url||'<?php echo e(URL::route('operation.union.index')); ?>',
                    method: 'get',
                    params: {
                        division_id: $scope.param.range,
                        unit_id: $scope.param.unit,
                        thana_id: $scope.param.thana
                    }
                }).then(function (response) {
                    $scope.unions = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
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
                    scope.$watch('unions', function (n) {

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
    <div ng-controller="UnionController">
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
        <div class="loading-report animated" ng-class="{fadeInDown:isLoading,fadeOutUp:!isLoading}">
            <img src="<?php echo e(asset('dist/img/ring-alt.gif')); ?>" class="center-block">
            <h4>Loading...</h4>
        </div>
        <section class="content">
            <div class="box box-solid">
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
                </div>
                <div class="box-body">
                    <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                    </div>

                    <div ng-bind-html="unions" compile-html>

                    </div>
                </div>
            </div>

        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>