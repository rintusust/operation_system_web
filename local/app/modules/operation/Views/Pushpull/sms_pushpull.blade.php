@extends('template.master')
@section('title','Manual SMS Push Pull')
@section('breadcrumb')
    {{--!! Breadcrumbs::render('recruitment.applicant.send_sms_to_applicant') !!--}}
@endsection
@section('content')



    <script>

        GlobalApp.controller('SMSController', function ($scope, $q, $http, httpService, notificationService) {
            $scope.param = {};
            $scope.allLoading = false;

            $scope.submitData = function () {
                $scope.allLoading = true;

                $http({
                    url:'{{URL::route('HRM.Pushpull.process_sms_pushpull')}}',
                    method:'post',
                    data:angular.toJson($scope.param)
                }).then(function (response) {
                    $scope.allLoading = false;
                    notificationService.notify(response.data.status,response.data.message);
                },function (response) {
                    $scope.allLoading = false;
                    notificationService.notify('error',response.statusText);
                })
            }

        });
    </script>
    <section class="content" ng-controller="SMSController">
        <div class="box box-info">
            <div class="overlay" ng-if="allLoading">
                <span class="fa">
                    <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6 col-centered">

                        <div class="form-group">
                            <label for="" class="control-label">
                                Enter your message([[param.messages?param.messages.length:0]]/160):
                            </label>
                            <textarea ng-model="param.messages" name="" id="" cols="30" rows="10" class="form-control" placeholder="Type your message(max 160 character)"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="" class="control-label">
                                Mobile Number&nbsp;
                            </label>
                            <input type="text" class="form-control" style="margin-bottom: 5px" placeholder="Enter mobile no"  ng-model="param.mobile_number"/>
                        </div>
                        <div class="form-group">
                            <button ng-disabled="!(param.messages)" ng-click="submitData()" class="btn btn-primary btn-block">Send SMS</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@stop
