@extends('template.master')
@section('title','Disembodiment Reason Correction')
{{-- @section('breadcrumb')
    {!! Breadcrumbs::render('disembodiment_date_correction') !!}
@endsection --}}
@section('content')
    <script>
        GlobalApp.controller('DisembodimentDateCorrectionController', function ($scope, $http, $sce) {
            $scope.ansarId = "";
            $scope.ansarDetail = {};
            $scope.ansar_ids = [];
            $scope.totalLength = 0;
            $scope.loadingAnsar = false;
            $scope.loadAnsarDetail = function (id) {
                $scope.loadingAnsar = true;
                $http({
                    method: 'get',
                    url: '{{URL::route('load_ansar_for_disembodiment_reason_correction')}}',
                    //method: 'get',
                    params: {ansar_id: id}
                }).then(function (response) {
                    $scope.ansarDetail = response.data;
                    $scope.loadingAnsar = false;
                    $scope.totalLength--;
                })
            };
{{--            $scope.verifyMemorandumId = function () {--}}
{{--                var data = {--}}
{{--                    memorandum_id: $scope.memorandumId--}}
{{--                }--}}
{{--                $scope.isVerified = false;--}}
{{--                $scope.isVerifying = true;--}}
{{--                $http.post('{{action('UserController@verifyMemorandumId')}}', data).then(function (response) {--}}
{{--//                    alert(response.data.status)--}}
{{--                    $scope.isVerified = response.data.status;--}}
{{--                    $scope.isVerifying = false;--}}
{{--                }, function (response) {--}}

{{--                })--}}
{{--            }--}}
            $scope.makeQueue = function (id) {
                $scope.ansar_ids.push(id);
                $scope.totalLength += 1;
            };
            $scope.$watch('totalLength', function (n, o) {
                if (!$scope.loadingAnsar && n > 0) {
                    $scope.loadAnsarDetail($scope.ansar_ids.shift())
                } else {
                    if (!$scope.ansarId) $scope.ansarDetail = {}
                }
            })
        })
    </script>
    <div ng-controller="DisembodimentDateCorrectionController">
        @if(Session::has('success_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        @if(Session::has('error_message'))
            <div style="padding: 10px 20px 0 20px;">
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <span class="glyphicon glyphicon-ok"></span> {{Session::get('success_message')}}
                </div>
            </div>
        @endif
        <section class="content" style="position: relative;">
            <notify></notify>
            <div class="box box-solid">
                {!! Form::open(array('route' => 'new-disembodiment-reason-entry', 'id' => 'new-disembodiment-reason-entry')) !!}
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group required" ng-init="ansarId='{{Request::old('ansar_id')}}'">
                                <label for="ansar_id" class="control-label">Ansar ID</label>
                                <input type="text" name="ansar_id" value="{{Request::old('ansar_id')}}" id="ansar_id"
                                       class="form-control" placeholder="Enter Ansar ID" ng-model="ansarId"
                                       ng-change="makeQueue(ansarId)">
                                @if($errors->has('ansar_id'))
                                    <p class="text-danger">{{$errors->first('ansar_id')}}</p>
                                @endif
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Memorandum no.&nbsp;&nbsp;&nbsp;</label>
                                <input  ng-model="memorandumId"
                                       type="text" class="form-control" name="memorandum_id"
                                       placeholder="Enter Memorandum no." required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">New Reason</label>
                                <select name="dis-reason" class="form-control" required>
                                    <option value="">--Select Reason--</option>
                                    <option ng-repeat="r in ansarDetail.reasons" value = "[[r.id]]">
                                        [[r.reason_in_bng]]
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button id="new-disembodiment-reason-entry" class="btn btn-primary">
                                    <img ng-show="loadingSubmit" src="{{asset('dist/img/facebook-white.gif')}}" width="16"
                                         style="margin-top: -2px">Correct Reason
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-6 col-sm-offset-2" style="min-height: 400px;border-left: 1px solid #CCCCCC">
                            <div id="loading-box" ng-if="loadingAnsar"></div>
                            <div ng-if="ansarDetail.ansar_details.name==undefined">
                                <input type="hidden" name="ansarExist" value="0">
                                <h3 style="text-align: center">No Ansar Found</h3>
                            </div>
                            <div ng-if="ansarDetail.ansar_details.name!=undefined">
                                <input type="hidden" name="ansarExist" value="1">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <p>[[ansarDetail.ansar_details.name]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Rank</label>
                                    <p>[[ansarDetail.ansar_details.rank]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Gender</label>
                                    <p>[[ansarDetail.ansar_details.sex]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Date of Birth</label>
                                    <p>[[ansarDetail.ansar_details.dob]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Own Unit</label>
                                    <p>[[ansarDetail.ansar_details.unit]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Own Thana</label>
                                    <p>[[ansarDetail.ansar_details.thana]]</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Dis-Embodiment Reason</label>
                                    <p>[[ansarDetail.ansar_details.disembodiment_reason]]</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </section>
    </div>
    {{-- <script>
        $("#confirm-new-disembodiment-reason").confirmDialog({
            message: 'Are you sure to Correct the Dis-Embodiment Reason',
            ok_button_text: 'Confirm',
            cancel_button_text: 'Cancel',
            ok_callback: function (element) {
                $("#new-disembodiment-reason-entry").submit()
            },
            cancel_callback: function (element) {
            }
        })
    </script> --}}
@endsection