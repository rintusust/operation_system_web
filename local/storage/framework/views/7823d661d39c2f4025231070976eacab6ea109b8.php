<?php $__env->startSection('title','Dashboard'); ?>
<?php /*<?php $__env->startSection('small_title','Control panel'); ?>*/ ?>
<?php $__env->startSection('content'); ?>
    <!-- Main content -->
    <style>
        ul.warning {
            list-style: lower-alpha;
            font-size: 18px;
        }

        ul.warning > li:not(:last-child) {
            margin-bottom: 10px;

        }
    </style>
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <a href="<?php echo e(URL::to('operation')); ?>" class="small-box-footer">
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3 style="color: #ffffff">Operation</h3>

                            <p style="color: #ffffff">Ansar Operation Management</p>
                        </div>
                        <div class="icon" style="color: #ffffff">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="small-box-footer" style="height: 15px"></div>
                    </div>
                </div>
            </a>

            <!-- ./col -->
        </div>

    <!-- /.row -->
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>