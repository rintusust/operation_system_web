<?php $__env->startSection('title','Dashboard'); ?>
<?php /*<?php $__env->startSection('small_title','Human Resource Management'); ?>*/ ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('hrm'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title','Dashboard'); ?>
<?php /*<?php $__env->startSection('small_title','Human Resource Management'); ?>*/ ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('hrm'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <script>
        GlobalApp.controller('TotalAnsar', function ($http, $scope) {

            $scope.allAnsar = [];
            $scope.loadingAnsar = true;
            $scope.embodimentData = {};
            $scope.graphData = [];

        })
    </script>
    <section class="content" ng-controller="TotalAnsar">

        <!-- =========================================================== -->
        <div class="row">

        </div>
    </section>
    <!-- /.content-wrapper -->

<?php $__env->stopSection(); ?>
      
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>