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
            <?php if(auth()->user()->type!=111): ?>
                <a href="<?php echo e(URL::to('HRM')); ?>" class="small-box-footer">
                    <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3 style="color: #ffffff">HRM</h3>

                                <p style="color: #ffffff">Human Resource Management</p>
                            </div>
                            <div class="icon" style="color: #ffffff">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="small-box-footer" style="height: 15px"></div>
                        </div>
                    </div>
                </a>
                <!-- ./col -->
                <?php /*<div class="col-lg-4 col-xs-6">*/ ?>
                <?php /*<!-- small box -->*/ ?>
                <?php /*<div class="small-box bg-green">*/ ?>
                <?php /*<div class="inner">*/ ?>
                <?php /*<h3>PM</h3>*/ ?>

                <?php /*<p>Payroll Management</p>*/ ?>
                <?php /*</div>*/ ?>
                <?php /*<div class="icon">*/ ?>
                <?php /*<i class="fa fa-calculator"></i>*/ ?>
                <?php /*</div>*/ ?>
                <?php /*<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>*/ ?>
                <?php /*</div>*/ ?>
                <?php /*</div>*/ ?>
            <!-- ./col -->
                <a href="<?php echo e(URL::to('SD')); ?>" class="small-box-footer">
                    <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>PRSD</h3>

                                <p>Salary Disbursement</p>
                            </div>
                            <div class="icon">
                                <i class="fa  fa-money"></i>
                            </div>
                            <div class="small-box-footer disable-module"
                                 style="height: 15px; background: #ADADAD"></div>
                            <?php /*<a href="<?php echo e(URL::to('SD')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>*/ ?>
                        </div>
                    </div>
                </a>
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-red disable-module">
                        <div class="inner">
                            <h3>ADAPS</h3>

                            <p>Deployment Application Processing System</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-gears"></i>
                        </div>
                        <div class="small-box-footer disable-module" style="height: 15px; background: #ADADAD"></div>
                        <?php /*<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>*/ ?>
                    </div>
                </div>
            <?php endif; ?>
            <a href="<?php echo e(URL::to('recruitment')); ?>" class="small-box-footer">
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3><?php echo e(strtoupper("Recruitment")); ?></h3>

                            <p>Ansar Recruitment</p>
                        </div>
                        <div class="icon">
                            <i class="fa  fa-money"></i>
                        </div>
                        <div class="small-box-footer disable-module" style="height: 15px; background: #ADADAD"></div>
                        <?php /*<a href="<?php echo e(URL::to('SD')); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>*/ ?>
                    </div>
                </div>
            </a>
            <!-- ./col -->
        </div>
        <?php if(Auth::user()->type==22): ?>
            <div>
                <h2>Attention:</h2>
                <h2 class="text-danger" style="    padding: 10px;border: 2px solid red;">
                    Please update your profile. Name, official email, official cell numbers must be given in the
                    respective fields. These are necessary for time to time given system notification, two step
                    verification and OTP that are coming soon.
                </h2>
                <p style="font-size: 18px;font-weight: bold">
                    Your AVURP Admin credentials have been created. Please create necessary users for online data entry,
                    verify and data approve.
                    To create users do the following things:
                </p>
                <ul class="warning">
                    <li>
                        Click in on the 'Manage users' on the left panel of your dashboard
                    </li>
                    <li>
                        Getting access to your AVURP admin dashboard click on 'Create user '
                    </li>
                    <li>
                        Fill up the form with appropriate information
                    </li>
                    <li>
                        Please use valid email and cell number to receive notifications
                    </li>
                    <li>
                        Create users a 'Verifier' . Verifier should be your ADC/CA/competent UAVDO
                    </li>
                    <li>
                        At your credentials, you already have all power. Your main role at this system as
                        'authentication' . But you have the admin role to entry, edit, update, verify and authenticate.
                    </li>
                    <li>
                        Do not let users enter any fake data here. All data will go to our AVURP database.
                    </li>
                    <li>
                        If you find any trouble please notify system Admin.
                    </li>
                </ul>
            </div>
    <?php endif; ?>
    <!-- /.row -->
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>