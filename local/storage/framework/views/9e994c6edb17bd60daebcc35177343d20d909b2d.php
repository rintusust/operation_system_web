<aside class="main-sidebar" ng-controller="MenuController">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo e(URL::to('/HRM')); ?>">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <?php echo $__env->make('HRM::Partial_view.partial_menu',['menus'=>config('menu.hrm')], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-level-up"></i>
                    <span>Ansar Promotion Process</span>
                    <i class="fa fa-angle-right pull-right"></i>
                 </a>
                <ul class="treeview-menu">
				   
                    <li>
                        <?php if(UserPermission::userPermissionExists('promotion')): ?>
                        <a href="<?php echo e(URL::to('/HRM/promotion')); ?>">
                            <i class="fa fa-upload"></i>
                            <span>Initial Batch Upload</span>
                        </a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if(UserPermission::userPermissionExists('promotionList')): ?>
                        <a href="<?php echo e(URL::to('/HRM/promotionList')); ?>">
                            <i class="fa fa-users"></i>
                            <span>Ansar Promotion List</span>
                        </a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if(UserPermission::userPermissionExists('BackToPreviousBatchUploadView')): ?>
                        <a href="<?php echo e(URL::to('/HRM/BackToPreviousBatchUploadView')); ?>">
                            <i class="fa fa-upload"></i>
                            <span>Back to Previous-Batch Upload</span>
                        </a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if(UserPermission::userPermissionExists('MakeVarifiedBatchUploadView')): ?>
                        <a href="<?php echo e(URL::to('/HRM/MakeVarifiedBatchUploadView')); ?>">
                            <i class="fa fa-upload"></i>
                            <span>Make Varified-Batch Upload</span>
                        </a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if(UserPermission::userPermissionExists('RankUpdateBatchUploadView')): ?>
                        <a href="<?php echo e(URL::to('/HRM/RankUpdateBatchUploadView')); ?>">
                            <i class="fa fa-upload"></i>
                            <span>Rank Update-Batch Upload</span>
                        </a>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if(UserPermission::userPermissionExists('SendToPanelBatchUploadView')): ?>
                        <a href="<?php echo e(URL::to('/HRM/SendToPanelBatchUploadView')); ?>">
                            <i class="fa fa-upload"></i>
                            <span>Send to Panel-Batch Upload</span>
                        </a>
                        <?php endif; ?>
                    </li>
                    
                    
					
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-chrome"></i>
                    <span>Newly Added Menu</span>
                    <i class="fa fa-angle-right pull-right"></i>
                 </a>
                <ul class="treeview-menu">
				   
				   
                    <li>
                        <a href="<?php echo e(URL::to('/HRM/show_ansar_list/same_kpi_six_month_ansar')); ?>">
                            <i class="fa fa-dashboard"></i>
                            <span>6 Months Over In Guard</span>
                        </a>
                    </li>
					
					<?php if((auth()->user()->id == 348) || (auth()->user()->id == 360) || (auth()->user()->id == 351) || (auth()->user()->id == 361)): ?>

					<li>
                        <a href="<?php echo e(URL::to('/HRM/test_print_card_id_view')); ?>">
                            <i class="fa fa-dashboard"></i>
                            <span>Test ID Card Print</span>
                        </a>
                    </li>
					<?php endif; ?>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
    <script>
        $(window).load(function(){
            var l = $('.sidebar-menu').children('li');
            
            function removeMenu(m){

                m.each(function () {
                    //console.log({parent: $.trim($(this).parents('li').eq($(this).parents('li').length-1).children('a').text()),children: m.text()})
                    //alert($(this).children('ul').length+" "+$(this).children('ul').children('li').length)
                    if($(this).children('ul').length>0) {
                        if ($(this).children('ul').children('li').length > 0) {
                            removeMenu($(this).children('ul').children('li'));
                        }
                        else if ($(this).children('ul').children('li').length <= 0) {
                            // alert(m.length)
                            $(this).remove();
                        }
                    }
                })
            }
            removeMenu(l)
        })
    </script>
</aside>