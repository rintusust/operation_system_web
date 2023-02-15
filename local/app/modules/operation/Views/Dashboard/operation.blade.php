@extends('template.master')
@section('title','Dashboard')
{{--@section('small_title','Human Resource Management')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('hrm') !!}
@endsection
@section('title','Dashboard')
{{--@section('small_title','Human Resource Management')--}}
@section('breadcrumb')
    {!! Breadcrumbs::render('hrm') !!}
@endsection
@section('content')
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

@endsection
      