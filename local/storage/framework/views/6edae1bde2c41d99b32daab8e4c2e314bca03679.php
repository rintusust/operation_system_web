<?php $i = (intVal($vdp_infos->currentPage() - 1) * $vdp_infos->perPage()) + 1; ?>
<div class="table-responsive">

    <table class="table table-bordered table-condensed">
        <caption><span style="font-size: 20px;">Total Member(<?php echo e($vdp_infos->total()); ?>)</span>
            <a href="<?php echo e(URL::route('operation.info.create')); ?>" class="btn btn-primary btn-xs pull-right">
                <i class="fa fa-plus"></i>&nbsp;Create New Entry
            </a>

        </caption>

        <tr>
            <th>#</th>
            <th>GEO ID</th>
            <th>Name</th>
            <th>Rank</th>
            <th>Date of Birth</th>
            <th>Division</th>
            <th>District</th>
            <th>Thana</th>
            <th>Union/Ward</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php if(count($vdp_infos)): ?>
            <?php foreach($vdp_infos as $info): ?>
                <tr>
                    <td><?php echo e($i++); ?></td>
                    <td><?php echo e($info->geo_id); ?></td>
                    <td><?php echo e($info->ansar_name_bng); ?></td>
                    <td><?php echo e($info->designationData->designation_name_bng); ?></td>
                    <td><?php echo e($info->date_of_birth); ?></td>
                    <td><?php echo e($info->division->division_name_bng); ?></td>
                    <td><?php echo e($info->unit->unit_name_bng); ?></td>
                    <td><?php echo e($info->thana->thana_name_bng); ?></td>
                    <td><?php echo e($info->union_word_text); ?></td>
                    <?php if($info->status=='new'): ?>
                        <td>
                            <span class="label label-danger">Unverified</span>
                        </td>
                    <?php elseif($info->status=='verified'): ?>
                        <td>
                            <span class="label label-warning">Verified</span>
                        </td>
                    <?php else: ?>
                        <td>
                            <span class="label label-success">Approved</span>
                        </td>
                    <?php endif; ?>
                    <td>
                        <a href="<?php echo e(URL::route('operation.info.show',$info->id)); ?>" class="btn btn-xs btn-primary">
                            <i class="fa fa-eye"></i>&nbsp;View
                        </a>
                        <a href="<?php echo e(URL::route('operation.info.edit',$info->id)); ?>" class="btn btn-xs btn-primary">
                            <i class="fa fa-edit"></i>&nbsp;Edit
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="12" class="bg-warning">
                    No VPD member found
                </td>
            </tr>
        <?php endif; ?>

    </table>
</div>
<?php if(count($vdp_infos)): ?>
    <div style="overflow: hidden">
        <div class="pull-left">
            <select name="" id="" ng-model="param.limit" ng-change="loadPage()">
                <option value="30">30</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="500">500</option>
            </select>
        </div>
        <div class="pull-right" style="margin: -20px 0" paginate ref="loadPage(url)">
            <?php echo e($vdp_infos->render()); ?>

        </div>
    </div>
<?php endif; ?>
