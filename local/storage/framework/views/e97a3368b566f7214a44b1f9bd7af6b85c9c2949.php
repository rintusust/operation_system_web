<?php $__env->startSection('title','Edit Entry'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('entry.list.edit'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <?php echo $__env->make('AVURP::ansar_vdp_info.form',['url'=>URL::route('AVURP.info.update',['id'=>$id]),'id'=>$id], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>