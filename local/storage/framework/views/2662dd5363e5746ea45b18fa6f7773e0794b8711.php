<?php /*User: Shreya*/ ?>
<?php /*Date: 12/3/2015*/ ?>
<?php /*Time: 1:22 PM*/ ?>


<?php $__env->startSection('title','Entry of Unit Information'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('unit_information_entry'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('UnitEntryController', function () {
        })
    </script>
    <div>

        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <?php echo Form::model(Request::old(),array('route' => 'HRM.unit.store','method'=>'post','name' => 'unitForm', 'class' => 'form-horizontal')); ?>

            <div class="row">
                <!-- left column -->
                <div class="col-lg-6 col-centered">

                    <!-- general form elements -->

                    <!-- Input addon -->

                    <div class="box box-info">
                        <div class="box-body">
                            <div class="box-body">
                                <div class="form-group required">
                                    <?php echo Form::label('division_id', 'Division:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                    <div class="col-sm-8 <?php if($errors->has('division_id')): ?> has-error <?php endif; ?>">
                                        <?php echo Form::select('division_id',$range,null,['class'=>'form-control']); ?>

                                        <?php if($errors->has('division_id')): ?>
                                            <p class="text-danger"><?php echo e($errors->first('division_id')); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <?php echo Form::label('unit_name_eng', 'Unit Name:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                    <div class="col-sm-8 <?php if($errors->has('unit_name_eng')): ?> has-error <?php endif; ?>">
                                        <?php echo Form::text('unit_name_eng',null, $attributes = array('class' => 'form-control', 'id' => 'unit_name_eng', 'placeholder' => 'Enter Unit Name in English')); ?>

                                        <?php if($errors->has('unit_name_eng')): ?>
                                            <p class="text-danger"><?php echo e($errors->first('unit_name_eng')); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <?php echo Form::label('unit_name_bng', 'জেলার নাম:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                    <div class="col-sm-8 <?php if($errors->has('unit_name_bng')): ?> has-error <?php endif; ?>">
                                        <?php echo Form::text('unit_name_bng', null, $attributes = array('class' => 'form-control', 'id' => 'unit_name_bng', 'placeholder' => 'জেলার নাম লিখুন বাংলায়')); ?>

                                        <?php if($errors->has('unit_name_bng')): ?>
                                            <p class="text-danger"><?php echo e($errors->first('unit_name_bng')); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <?php echo Form::label('unit_code', 'Unit Code:', $attributes = array('class' => 'col-sm-4 control-label')); ?>

                                    <div class="col-sm-8 <?php if($errors->has('unit_code')): ?> has-error <?php endif; ?>">
                                        <?php echo Form::text('unit_code', null, $attributes = array('class' => 'form-control', 'id' => 'unit_code', 'placeholder' => 'Enter Unit Code in English')); ?>

                                        <?php if($errors->has('unit_code')): ?>
                                            <p class="text-danger"><?php echo e($errors->first('unit_code')); ?></p>
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
                            Submit
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
    </div><!-- /.content-wrapper -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>