<?php /*User: Shreya*/ ?>
<?php /*Date: 12/5/2015*/ ?>
<?php /*Time: 12:23 PM*/ ?>

<?php $__env->startSection('title','Edit Thana Information'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('thana_information_edit',$thana_info->id); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>

        GlobalApp.controller('ThanaController', function ($scope,httpService) {
            $scope.thana_name_eng = '<?php echo e($thana_info->thana_name_eng); ?>';
            $scope.thana_name_bng = '<?php echo e($thana_info->thana_name_bng); ?>';
            $scope.thana_code = '<?php echo e($thana_info->thana_code); ?>';
            $scope.division_id = '<?php echo e($thana_info->division_id); ?>'
            $scope.unit_id = '<?php echo e($thana_info->unit_id); ?>'
            var b = '<?php echo e($thana_info->division_id); ?>'
            httpService.range().then(function (result) {
                $scope.range = result;
//                $scope.unit_id = ''
                $scope.units = [];
            })
            $scope.loadUnit = function (id) {
                httpService.unit(id).then(function (result) {
                    $scope.units = result;
                    if(b!=$scope.division_id) $scope.unit_id = ''
                })
            }
            $scope.$watch('division_id', function (n,o) {
                if(n){
                    $scope.loadUnit(n)
                }
            })
        });
    </script>

    <section class="content">
        <?php if($errors->has('id')): ?>
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    Invalid Request
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <!-- left column -->
            <div class="col-lg-6 col-centered">
                <?php /*<div class="label-title-session-entry">
                    <h4 style="text-align:center; padding:2px">Edit Thana Form</h4>
                </div>*/ ?>
                <div class="box box-info">
                    <div class="box-body">

                        <?php echo Form::model($thana_info,array('route' => 'operation.thana_update', 'class' => 'form-horizontal', 'name' => 'thanaForm', 'ng-controller' => 'ThanaController', 'novalidate')); ?>

                        <div class="box-body">
                            <div class="form-group">
                                <?php echo Form::label('division_id', 'Range:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                <div class="col-sm-8">
                                    <select name="division_id" id="" class="form-control" ng-model="division_id" >
                                        <option value="">--Select Range</option>
                                        <option ng-repeat="d in range" value="[[d.id]]">[[d.division_name_eng]]</option>
                                    </select>
                                    <?php if($errors->has('division_id')): ?>
                                        <p class="text-danger"><?php echo e($errors->first('division_id')); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <input type="hidden" name="id" class="form-control" value="<?php echo e($thana_info->id); ?>">
                            <div class="form-group">
                                <?php echo Form::label('unit_id', 'Unit:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                <div class="col-sm-8">
                                    <select name="unit_id" id="" class="form-control" ng-model="unit_id">
                                        <option value="">--Select Unit</option>
                                        <option ng-repeat="d in units" value="[[d.id]]">[[d.unit_name_eng]]</option>
                                    </select>
                                    <?php if($errors->has('unit_id')): ?>
                                        <p class="text-danger"><?php echo e($errors->first('unit_id')); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group required">
                                <?php echo Form::label('thana_name_eng', 'Thana Name:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                <div class="col-sm-8 <?php if($errors->has('thana_name_eng')): ?> has-error <?php endif; ?>">
                                    <?php echo Form::text('thana_name_eng', null, $attributes = array('class' => 'form-control', 'id' => 'thana_name_eng', 'placeholder' => 'Enter Thana Name in English')); ?>

                                    <?php if($errors->has('thana_name_eng')): ?>
                                        <p class="text-danger"><?php echo e($errors->first('thana_name_eng')); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group required">
                                <?php echo Form::label('thana_name_bng', '??????????????? ?????????:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                <div class="col-sm-8 <?php if($errors->has('thana_name_bng')): ?> has-error <?php endif; ?>">
                                    <?php echo Form::text('thana_name_bng',null, $attributes = array('class' => 'form-control', 'id' => 'thana_name_bng', 'placeholder' => '??????????????? ????????? ??????????????? ??????????????????')); ?>

                                    <?php if($errors->has('thana_name_bng')): ?>
                                        <p class="text-danger"><?php echo e($errors->first('thana_name_bng')); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group required">
                                <?php echo Form::label('thana_code', 'Thana Code:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                <div class="col-sm-8 <?php if($errors->has('thana_code')): ?> has-error <?php endif; ?>">
                                    <?php echo Form::text('thana_code', null, $attributes = array('class' => 'form-control', 'id' => 'thana_code', 'placeholder' => 'Enter Thana Code')); ?>

                                    <?php if($errors->has('thana_code')): ?>
                                        <p class="text-danger"><?php echo e($errors->first('thana_code')); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box-body -->

                </div>
                <!-- /.box -->
                <div>
                    <button type="submit" class="btn btn-info pull-right">
                        Update
                    </button>
                </div>
                <!-- /.box-footer -->
                <?php echo Form::close(); ?>


            </div>
            <!--/.col (left) -->
            <!-- right column -->

        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>