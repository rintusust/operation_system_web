@extends('template.master')
@section('title','Disembodied period correction')
@section('breadcrumb')
    {!! Breadcrumbs::render('transfer') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller("DisembodiedPeriodCorrectionController", function ($scope, $http, $timeout, $rootScope) {
            $scope.reasons = [];
            $scope.params = {};
            $scope.reset = {};
            $scope.embodimentData = {};
            $http({
                method: 'get',
                url: '{{URL::route('load_disembodiment_reason')}}'
            }).then(function (response) {
                $scope.reasons = response.data;
            }, function (response) {
                $scope.reasons = [];
            });
            $scope.loadAnsar = function () {
                $http({
                    method: 'post',
                    url: window.location.href,
                    data: $scope.params
                }).then(function (response) {
                    $scope.ansars = response.data;
                }, function (response) {
                    $scope.ansars = [];
                })
            };
            $scope.embodiedAnsar = function (id) {
                $scope.embodimentData['ansar_id'] = id;
                $("#embodied-option").modal('show');
                $scope.reset.reset();
            };
            $scope.postEmbodimentData = function () {
                console.log($scope.embodimentData)
            }
        })

    </script>
    <div notification-message ng-controller="DisembodiedPeriodCorrectionController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <filter-template
                            show-item="['range','unit','thana','kpi']"
                            type="single"
                            kpi-change="loadAnsar()"
                            start-load="range"
                            field-width="{range:'col-sm-3',unit:'col-sm-3',thana:'col-sm-3',kpi:'col-sm-3'}"
                            data="params"
                            call-func="reset"
                    ></filter-template>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="control-label">Select Reason</label>
                                <select ng-model="params.reason" class="form-control" ng-change="loadAnsar()">
                                    <option value="">--Select a reason--</option>
                                    <option ng-repeat="r in reasons" value="[[r.id]]">[[r.reason_in_bng]]</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="pc-table">
                            <caption>
                                <table-search q="q" results="results"></table-search>
                            </caption>
                            <tr>
                                <th>SL. No</th>
                                <th>ID</th>
                                <th>Designation</th>
                                <th>Name</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Last Embodied KPI</th>
                                <th>Total Service Days</th>
                                <th>Disembodied Date</th>
                                <th>Disembodied Reason</th>
                                <th>Action</th>
                            </tr>
                            <tr class="warning" ng-if="ansars.length<=0">
                                <td colspan="9">No Ansar Found to Transfer</td>
                            </tr>
                            <tr ng-repeat="ansar in ansars|filter:q as results" ng-if="ansars.length>0">
                                <td>[[$index+1]]</td>
                                <td>[[ansar.ansar_id]]</td>
                                <td>[[ansar.name_bng]]</td>
                                <td>[[ansar.ansar_name_bng]]</td>
                                <td>[[ansar.division_name_bng]]</td>
                                <td>[[ansar.unit_name_bng]]</td>
                                <td>[[ansar.kpi_name]]</td>
                                <td>[[(ansar.total_service_days/365).toFixed(1)]]&nbsp; years</td>
                                <td>[[ansar.rest_date|dateformat:'DD-MMM-YYYY']]</td>
                                <td>[[ansar.reason_in_bng]]</td>
                                <td>
                                    <button class="btn btn-primary btn-xs" ng-click="embodiedAnsar(ansar.ansar_id)">
                                        Re-embodied
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        {!! Form::open(['route'=>'print_letter','target'=>'_blank','ng-if'=>'letterOption.status','class'=>'pull-left']) !!}
                        <input type="hidden" ng-repeat="(key,value) in letterOption" name="[[key]]" value="[[value]]">
                        <button class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;Print Transfer Letter</button>
                        {!! Form::close() !!}
                        <button class="pull-right btn btn-primary" open-hide-modal ng-click="modalOpen=true">
                            <i class="fa fa-send"></i>&nbsp;&nbsp;Transfer
                        </button>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div id="embodied-option" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <strong>Embodiment Option</strong>
                            <button type="button" class="close" data-dismiss="modal" ng-click="modalOpen = false">
                                &times;
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="register-box" style="margin: 0;width: auto">
                                <div class="register-box-body  margin-bottom" style="padding: 0;padding-bottom: 10px">
                                    <filter-template
                                            show-item="['range','unit','thana','kpi']"
                                            type="single"
                                            start-load="range"
                                            layout-vertical="true"
                                            data="embodimentData"
                                    ></filter-template>
                                    <div class="form-group">
                                        <label for="memId" class="control-label">Memorandum no.: <span
                                                    ng-show="isVerifying"><i class="fa fa-spinner fa-pulse"></i>&nbsp;Verifying</span>
                                            <span class="text-danger"
                                                  ng-if="isVerified">This id already taken</span></label>
                                        <input ng-blur="verifyMemorandumId()" type="text" class="form-control"
                                               ng-model="embodimentData.memorandumId" name="memorandum_id"
                                               id="memId" placeholder="Enter memorandum id">
                                    </div>
                                    <div class="form-group">
                                        <datepicker-separate-fields label="Memorandum Date:" notify="memoInvalidDate"
                                                                    rdata="memDate"></datepicker-separate-fields>
                                        <input ng-model="memDate" type="hidden" name="mem_date">
                                    </div>
                                    <div class="form-group">
                                        <datepicker-separate-fields label="Embodiment date:" notify="emboInvalidDate"
                                                                    rdata="embodimentData.joinDate"></datepicker-separate-fields>
                                        <input type="hidden" ng-value="embodimentData.joinDate" name="memorandum_id">
                                    </div>
                                    <button class="btn btn-primary pull-right" ng-click="postEmbodimentData()"
                                            ng-disabled="memoInvalidDate || emboInvalidDate">
                                        <i class="fa fa-check"></i>&nbsp;Confirm
                                    </button>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop