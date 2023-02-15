@extends('template.master')
@section('title','Rank Update-Batch Upload')
{{-- @section('breadcrumb')
    {!! Breadcrumbs::render('HRM.Ansar Promotion.Ansar Promotion') !!}
@endsection --}}
@section('content')
    <script>
        
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce, notificationService) {
            $scope.isCatOther = false;
            $scope.makeVerifiedCheckBox = false;
            $scope.rankUpdateOption = false;
            $scope.currentPage = 0;
            $scope.orderBy = "";
            $scope.loadingPage = [];
            $scope.circulars = [];
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

            $scope.makeVerified = function (id) {
                if (id) {
                    $scope.submitting = true;
                    $http({
                        url: "{{URL::to('HRM/makeVerifiedByFile')}}",
                        method: 'post',
                        data: angular.toJson({
                            request_id: id,
                            makeVerified: $scope.makeVerifiedCheckBox
                        })
                    }).then(function (response) {
                        console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('success', response.data.message);
                            $scope.loadPage();
                        } else {
                            notificationService.notify('error', response.data.message)
                        }
                    }, function (response) {
                        $scope.submitting = false;
                        notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                    })
                }
            };
            $scope.loadPagination = function () {
                
                $scope.pages = [];
                for (var i = 0; i < $scope.numOfPage; i++) {
                    $scope.pages.push({
                        pageNum: i,
                        offset: i * $scope.itemPerPage,
                        limit: $scope.itemPerPage
                    });
                    $scope.loadingPage[i] = false;
                }
            };
            
                $scope.loadPage = function () {
                    $http({
                        method: 'get',
                        url: "{{URL::to('/HRM/RankUpdateBatchUploadView')}}"
                    }).then(function (response) {
                            
                    }, function (response) {
                            
                        });
                    
                    };
                    
            $scope.uploadFileForm = function(){
                var index = 0;
                var fd = new FormData(document.getElementById("uploadFileForm"))
                $scope.allLoading = true;
                $http({
                    url: "{{URL::to('HRM/rankUpdateByFile')}}",
                    data: fd,
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
                    console.log(response);
                        $scope.submitting = false;
                        if (response.data.status) {
                            notificationService.notify('error', "An unexpected error occur. Error code :" + response.status);
                            
                        } 
                        else {
                            if (response.data.status == false){
                                //alert("False");exit;
                            notificationService.notify('error', "No Ansar is eligible!" );
                            }else{
                                //alert("Elsee");exit;
                            notificationService.notify('success', "Applicants Rank Status Successfully Updated...");}
                            window.location.reload();
                        }
                        
                },function (response) {
                    
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
        @if(Session::has('success_message'))
        <div style="padding: 10px 20px 0 20px;">
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
            </div>
        </div>
    @endif
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
                                <input type="hidden" name="circular" ng-value="param.circular" required>
                                <div class="form-group">
                                    <label for="" class="control-label">Upload File</label>
                                    <input type="file" name="applicant_id_list" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary pull-right" >Upload file</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </section>
@endsection
