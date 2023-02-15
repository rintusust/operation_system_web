@extends('template.master')
@section('title','Ansar Promotion')
{{-- @section('breadcrumb')
    {!! Breadcrumbs::render('HRM.Ansar Promotion.Ansar Promotion') !!}
@endsection --}}
@section('content')
    <script>
        
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce, notificationService) {
            $scope.isCatOther = false;
            $scope.circulars = [];
            $scope.ranks = [];
            $scope.param = {};

            var init = function () {
                $http({
                    method: 'get',
                    url: '{{URL::route('HRM.promotion.circulars')}}',
                    params: {
                        "status":"running",
                        "category":5
                    }
                }).then(function (response) {
                    console.log(response);
                  $scope.circulars = response.data;
                }, function (response) {
                    $scope.allLoading = false;
                });

            };
            
            init();
           
            $scope.loadCircular = function () {
             
            };

            $scope.loadApplicant = function () {
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('HRM.promotion.promotion')}}',
                    data: angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    $scope.applicants = $sce.trustAsHtml(response.data);
                }, function (response) {
                    $scope.allLoading = false;
                })
            };
            $scope.confirmPromotion = function () {
                
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('HRM.promotion.confirm_promotion')}}',
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
                    url:"{{URL::route('HRM.promotion.confirm_promotion_by_uploading_file')}}",
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
                    return;
                    $scope.message = response.data;
                    $scope.allLoading = false;
                    console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                            
                        } else {
                            if (response.data.status == false){
                                //alert("False");exit;
                            notificationService.notify('error', "No Ansar is eligible!" );
                            }else{
                                //alert("Elsee");exit;
                            notificationService.notify('success', "Successfully Uploaded...");}
                            window.location.reload();
                        }
                },function (response) {
                    $scope.submitting = false;
                    notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                })
            }

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
                            scope.confirmPromotion()
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
                                    class="form-control" >
                                <option value="" >--Select a circular--</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <form method="post" enctype="multipart/form-data" id="uploadFileForm" ng-submit="uploadFileForm()">
                                {!! csrf_field() !!}
                                <input type="hidden" name="circular" ng-value="param.circular">
                                <div class="form-group">
                                    <label for="" class="control-label">Promoted Rank</label>
                                    <select name="promoted_rank" class="form-control" required>
                                        <option value="" >--Select a rank--</option>
                                        <option value="2">Asst. Platoon Commander</option>
                                        <option value="3">Platoon Commander</option>                            
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="" class="control-label">Upload File</label>
                                    <input type="file" name="applicant_id_list" class="form-control" >
                                </div>
                                <div class="form-group">
                                    <label for="" class="control-label">Comment</label>
                                    <input type="text" name="comment" class="form-control" placeholder="Enter comment" >
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary pull-right">Upload file</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                
        </div>   
    </section>
@endsection
