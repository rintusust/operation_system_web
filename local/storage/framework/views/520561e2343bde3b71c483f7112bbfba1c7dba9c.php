<?php $__env->startSection('title','Edit Range'); ?>
<?php /*<?php $__env->startSection('small_title','DG'); ?>*/ ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('range.edit',$data->id); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="row">
            <div class="col-sm-6 col-centered">
                <div class="box box-solid">
                    <div class="box-body">
                        <?php echo Form::model($data,['route'=>['operation.range.update',$data->id],'method'=>'patch']); ?>

                        <div class="form-group">
                            <?php echo Form::label('division_name_eng','Range name eng : '); ?>

                            <?php echo Form::text('division_name_eng',null,['class'=>'form-control','placeholder'=>'Enter Range Name']); ?>

                            <?php echo $errors->first('division_name_eng','<p class="text text-danger">:message</p>'); ?>

                        </div>
                        <div class="form-group">
                            <?php echo Form::label('division_name_bng','Range name bng : '); ?>

                            <?php echo Form::text('division_name_bng',null,['class'=>'form-control','placeholder'=>'রেঞ্জের নাম লিখুন']); ?>

                            <?php echo $errors->first('division_name_bng','<p class="text text-danger">:message</p>'); ?>

                        </div>
                        <div class="form-group">
                            <?php echo Form::label('division_code','Range code : '); ?>

                            <?php echo Form::text('division_code',null,['class'=>'form-control','placeholder'=>'Enter Range code']); ?>

                            <?php echo $errors->first('division_code','<p class="text text-danger">:message</p>'); ?>

                        </div>
                        <div class="form-group">
                            <?php echo Form::submit('Create Range',['class'=>'btn btn-info pull-right']); ?>

                        </div>
                        <?php echo Form::close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>