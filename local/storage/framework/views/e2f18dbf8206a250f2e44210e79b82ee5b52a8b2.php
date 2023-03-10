<?php $__env->startSection('title','View Entry Detail'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('entry.list.view'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <style>
        .form-control {
            margin-bottom: 10px;
        }
    </style>
    <section class="content">
        <div class="box box-solid" ng-controller="VDPController">
            <div class="box-header">

            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="row">
                            <div class="col-sm-2 col-sm-offset-10">

                                <img src="<?php echo URL::route('AVURP.info.image',['id'=>$info->id]); ?>" alt=""
                                     class="img-responsive img-thumbnail pull-right" style="margin-bottom: 10px">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-sm-4">ভিডিপি আইডি
                                <span class="pull-right">:</span>
                            </label>
                            <div class="col-sm-8">
                                <div class="form-control">
                                    <?php echo e($info->geo_id); ?>

                                </div>
                            </div>
                        </div>
                        <fieldset>
                            <legend>জিও কোড ভিত্তিক আইডির জন্য তথ্য</legend>
                            <div class="form-group">
                                <label class="control-label col-sm-4">বিভাগ
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->division->division_name_bng); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">জেলা
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->unit->unit_name_bng); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">উপজেলা
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->thana->thana_name_bng); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">ইউনিয়ন
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->union->union_name_bng); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">ইউনিয়নের ওয়ার্ড
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->union_word_id); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">গ্রাম/বাড়ি নম্বর
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->village_house_no); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">ডাকঘর
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->post_office_name); ?>

                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>ব্যক্তিগত ও পারিবারিক তথ্য</legend>
                            <div class="form-group">
                                <label class="control-label col-sm-4">Name(CAP)
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->ansar_name_eng); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">নাম
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->ansar_name_bng); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">বর্তমান পদবী
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->designation); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">পিতার নাম
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->father_name_bng); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">মাতার নাম
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->mother_name_bng); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">জন্ম তারিখ
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->date_of_birth); ?>

                                    </div>
                                </div>
                            </div>
                            <?php /*        <div class="form-group">
                                        <label class="control-label col-sm-4">জন্মতারিখের ভিত্তি
                                            <span class="pull-right">:</span>
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" placeholder="জন্মতারিখের ভিত্তি" ng-model="info.form.base_of_birth_date" id="base_of_birth_date">
                                        </div>
                                    </div>*/ ?>
                            <div class="form-group">
                                <label class="control-label col-sm-4">বৈবাহিক অবস্থা
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->marital_status); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">স্ত্রী/স্বামীর নাম
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->spouse_name); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">জাতীয় পরিচয় পত্র নম্বর
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->national_id_no); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">স্মার্টকার্ড আইডি
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->smart_card_id); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">এভিইউবি আইডি
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->avub_id); ?>

                                    </div>
                                </div>
                            </div>

                        </fieldset>
                        <fieldset>
                            <legend>যোগাযোগ</legend>
                            <div class="form-group">
                                <label class="control-label col-sm-4">মোবাইল নম্বর(নিজ)
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->mobile_no_self); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">মোবাইল নম্বর(অনুরোধ)
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->mobile_no_request); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">ইমেইল আইডি
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->email_id); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">ফেসবুক আইডি
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->fb_id); ?>

                                    </div>
                                </div>
                            </div>

                        </fieldset>
                        <fieldset>
                            <legend>শারিরিক যোগ্যতার তথ্য</legend>
                            <div class="form-group">
                                <label class="control-label col-sm-4">উচ্চতা
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->height_feet.'\''.$info->height_inch.'\'\''); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">রক্তের গ্রুপ
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->bloodGroup?$info->bloodGroup->blood_group_name_bng:'--'); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">লিঙ্গ
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->gender); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">স্বাস্থ্যগত অবস্থা
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        <?php echo e($info->health_condition); ?>

                                    </div>
                                </div>
                            </div>

                        </fieldset>
                        <fieldset>
                            <legend>
                                শিক্ষাগত যোগ্যতার ও প্রশিক্ষনের তথ্য
                            </legend>
                            <div class="form-group">
                                <label class="control-label">শিক্ষাগত যোগ্যতা</label>
                                <div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-condensed">
                                            <tr>
                                                <th>শিক্ষাগত যোগ্যতা</th>
                                                <th>শিক্ষা প্রতিষ্ঠানের নাম</th>
                                                <th>পাশ করার সাল</th>
                                                <th>বিভাগ / শ্রেণী</th>
                                            </tr>
                                            <?php foreach($info->education as $e): ?>
                                                <tr>
                                                    <td><?php echo e($e->education->education_deg_bng); ?></td>
                                                    <td><?php echo e($e->institute_name); ?></td>
                                                    <td><?php echo e($e->passing_year); ?></td>
                                                    <td><?php echo e($e->gade_divission); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">প্রশিক্ষণ
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="table-reaponsive">
                                    <table class="table table-bordered table-condensed">
                                        <tr>
                                            <th>প্রধান প্রশিক্ষণ নাম</th>
                                            <th>উপ প্রশিক্ষণ নাম</th>
                                            <th>প্রতিষ্ঠানের নাম</th>
                                            <th>সনদ পত্র নং</th>
                                            <th>প্রশিক্ষণ শুরুর তারিখ</th>
                                            <th>প্রশিক্ষণ শেষের তারিখ</th>
                                        </tr>
                                        <?php foreach($info->training_info as $t): ?>
                                            <tr>
                                                <td><?php echo e($t->main_training->training_name_bng); ?></td>
                                                <td><?php echo e($t->sub_training?$t->sub_training->training_name_bng:'--'); ?></td>
                                                <td><?php echo e($t->institute_name); ?></td>
                                                <td><?php echo e($t->certificate_no); ?></td>
                                                <td><?php echo e($t->training_start_date); ?></td>
                                                <td><?php echo e($t->training_end_date); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div style="display: flex;justify-content: center;align-items: center">
                    <?php if((Auth::user()->type==22||Auth::user()->type==44||Auth::user()->type==11)&&UserPermission::userPermissionExists('AVURP.info.verify')&&$info->status=='new'): ?>
                        <?php echo Form::open(['route'=>['AVURP.info.verify',$info->id],'style'=>'align-self:center;margin-left:10px']); ?>

                        <button class="btn btn-primary">
                            <i class="fa fa-check"></i>&nbsp;Verify
                        </button>
                        <?php echo Form::close(); ?>

                    <?php endif; ?>
                        <?php if((Auth::user()->type==22||Auth::user()->type==11)&&UserPermission::userPermissionExists('AVURP.info.approve')&&($info->status=='verified')): ?>
                            <?php echo Form::open(['route'=>['AVURP.info.approve',$info->id],'style'=>'align-self:center;margin-left:10px']); ?>

                            <button class="btn btn-primary">
                                <i class="fa fa-check"></i>&nbsp;Approved
                            </button>
                            <?php echo Form::close(); ?>

                        <?php endif; ?>
                    <?php if((Auth::user()->type==22||Auth::user()->type==11)&&UserPermission::userPermissionExists('AVURP.info.verify_approve')&&$info->status=='new'): ?>
                        <?php echo Form::open(['route'=>['AVURP.info.verify_approve',$info->id],'style'=>'align-self:center;margin-left:10px']); ?>

                        <button class="btn btn-primary">
                            <i class="fa fa-check"></i>&nbsp;Verify & Approved
                        </button>
                        <?php echo Form::close(); ?>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>