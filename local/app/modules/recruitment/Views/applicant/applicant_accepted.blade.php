@extends('template.master')
@section('title','Final Applicant List')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.final_accepted_applicant') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce, notificationService) {
            $scope.isCatOther = false;
            $scope.circulars = [];
            $scope.param = {};
            httpService.circular({status: 'running'}).then(function (response) {
                $scope.circulars = response.data;
            });
            $scope.loadApplicant = function () {
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('recruitment.applicant.final_list_load')}}',
                    data: angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml(response.data);
                }, function (response) {
                    $scope.allLoading = false;
                })
            };
            $scope.confirmSelectionAsAccepted = function () {
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('recruitment.applicant.confirm_accepted')}}',
                    data: angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml('');
                    notificationService.notify(response.data.status, response.data.message)
                }, function (response) {
                    $scope.allLoading = false;
                    notificationService.notify('error', response.statusText)
                })
            };

            //===========~~~
            $scope.changeCircular = function () {
                var keepGoing = true;
                $scope.circulars.forEach(function (value) {
                    if (keepGoing) {
                        if (value.id === parseInt($scope.param.circular) && value.category.category_type === "other") {
                            $scope.isCatOther = true;
                            keepGoing = false;
                        } else {
                            $scope.isCatOther = false;
                        }
                    }
                });
            };
            $scope.uploadFileForm = function(){
                var index = 0;
                var fd = new FormData(document.getElementById("uploadFileForm"))
                $scope.allLoading = true;
                $http({
                    url:"{{URL::route('recruitment.applicant.confirm_accepted_by_uploading_file')}}",
                    data:fd,
                    method:'post',
                    headers:{
                        "Content-Type":undefined
                    },
                    eventHandlers:{
                        progress:function (event) {
                            var response = event.currentTarget.response;
                            $scope.message = response.substr(index,response.length-index);
                            console.log(response.substr(index,response.length-index))
                            index = response.length;
                        }
                    }
                }).then(function (response) {
                    $scope.message = response.data;
                    $scope.allLoading = false;
                },function (response) {

                })
            }
            //===========^^^^

        });
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newScope;
                    scope.$watch('applicants', function (n) {

                        if (attr.ngBindHtml) {
                            if (newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        });
        GlobalApp.directive('confirmDialog', function () {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    $(element).confirmDialog({
                        message: scope.message || "Are u sure?",
                        ok_button_text: 'Confirm',
                        cancel_button_text: 'Cancel',
                        event: 'click',
                        ok_callback: function (element) {
                            scope.confirmSelectionAsAccepted()
                        },
                        cancel_callback: function (element) {
                        }
                    })

                }
            }
        });
    </script>
    <section class="content" ng-controller="applicantSearch">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span><br>
                <span>[[message]]</span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="param.circular" ng-change="changeCircular()"
                                    class="form-control">
                                <option value="">--Select a circular--</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Selection Process</label>
                            <select name="" ng-model="param.selectionProcess"
                                    class="form-control">
                                <option value="">--Select a process--</option>
                                <option value="manual">Manual</option>
                                <option value="file">File</option>
                            </select>
                        </div>
                        <div ng-if="param.selectionProcess=='manual'">
                            <filter-template
                                    show-item="['range','unit']"
                                    type="single"
                                    data="param"
                                    start-load="range"

                                    unit-field-disabled="!param.circular"
                                    range-field-disabled="!param.circular"
                                    field-width="{unit:'col-sm-12',range:'col-sm-12'}"
                            >
                            </filter-template>

                            <div class="form-group" ng-if="isCatOther">
                                <label class="control-label" for="nra">No of required applicants</label>
                                <input id="nra" type="text" ng-model="param.cat_other_no_applicant"
                                       class="form-control">
                            </div>
                            <div class="form-group">
                                <button ng-click="loadApplicant()"
                                        ng-disabled="!(param.circular&&(param.unit||param.range))"
                                        class="btn btn-primary btn-block">
                                    Load short listed applicant
                                </button>
                            </div>
                        </div>
                        <div ng-if="param.selectionProcess=='file'">
                            <form method="post" enctype="multipart/form-data" id="uploadFileForm" ng-submit="uploadFileForm()">
                                {!! csrf_field() !!}
                                <input type="hidden" name="circular" ng-value="param.circular">
                                <div class="form-group">
                                    <label for="" class="control-label">Upload File</label>
                                    <input type="file" name="applicant_id_list" class="form-control">

                                </div>
                                <div class="form-group">
                                    <label for="" class="control-label">Comment</label>
                                    <input type="text" name="comment" class="form-control" placeholder="Enter comment">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary pull-right">Upload file</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div ng-if="param.selectionProcess=='manual'" ng-bind-html="applicants" compile-html>

                </div>
            </div>
        </div>
    </section>

@endsection
