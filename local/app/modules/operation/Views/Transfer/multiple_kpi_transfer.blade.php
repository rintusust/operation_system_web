@extends('template.master')
@section('title','Transfer Ansars')
@section('breadcrumb')
    {!! Breadcrumbs::render('multiple_transfer') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('TransferController', function ($scope, $http, notificationService) {
            $scope.ansar_id = '';
            $scope.transfering = false;
            $scope.printLetter = false;
            $scope.memId = '';
            $scope.submitData = [];
            $scope.search = false;
            $scope.units = [];
            $scope.thanas = [];
            $scope.addToTrnsInvalidDate = false;
            $scope.kpis = [];
            $scope.tAnsars = [];
            $scope.tempJoiningDate = '';
            $scope.formData = {
                unit: '',
                thana: '',
                kpi: '',
                joining_date: ''
            };
            $scope.searchAnsar = function (event) {
                if (event.type == 'keypress' && event.which != 13) return;
                $scope.search = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('search_kpi_by_ansar')}}',
                    data: {ansar_id: $scope.ansar_id, unit: $scope.param.unit}
                }).then(function (response) {
                                   
                    $scope.search = false;
                    $scope.data = response.data;
                }, function (response) {
                })
            };
            $scope.addToCart = function () {

                var given = moment($scope.tempJoiningDate, "DD-MMM-YYYY");
                var current = moment().startOf('day');

                //Difference in number of days
                var day_diff = moment.duration(given.diff(current)).asDays();

                if(day_diff < 0 || day_diff > 7){
                    alert('No Back Date or Upcoming days exceeding 7 days not allowed for transfer date.')
                    return;
                }


                var d = angular.copy({
                    id: $scope.data.data.ansar_id,
                    name: $scope.data.data.ansar_name_eng,
                    tkn: $scope.kpiName,
                    tktn: $scope.thanaName,
                    ckn: $scope.data.data.kpi_name,
                    tkjd: $scope.tempJoiningDate
                });
                
                if($scope.data.data.kpi_id == $scope.formData.kpi){
                     alert("This ansar can not be transferred to same kpi");
                     return;
                }
                var s = $scope.tAnsars.find(function (v) {
                    return v.id == d.id;
                });
                if (s) {
                    alert("This ansar already in transfer list");
                    return;
                }
                var b = angular.copy({
                    ansarId: $scope.data.data.ansar_id,
                    currentKpi: $scope.data.data.kpi_id,
                    transferKpi: $scope.formData.kpi,
                    tKpiJoinDate: $scope.tempJoiningDate
                });
                
                $scope.submitData.push(b);
                $scope.tAnsars.push(d);
                $scope.data.data = {};
                $scope.data.status = false;
                $scope.ansar_id = '';
                $scope.tempJoiningDate = ''
            };
            $scope.transferAnsar = function () {
                $scope.error = undefined;
                $scope.transfering = true;
                $http({
                    method: 'post',
                    url: '{{URL::route('confirm_transfer')}}',
                    data: angular.toJson({ansars: $scope.submitData, memId: $scope.memId, mem_date: $scope.memDate})
                }).then(function (response) {
                    var newValue = response.data;
                    if (Object.keys(newValue).length > 0) {
                        if (!newValue.status) {
                            notificationService.notify('error', newValue.message)
                        }
                        if (newValue.data.success.count > 0) {
                            $scope.printLetter = true;
                            for (i = 0; i < newValue.data.success.count; i++) {
                                notificationService.notify(
                                    'success', "Ansar(" + newValue.data.success.data[i] + ") successfully transfered"
                                )
                            }
                        } else {
                            $scope.printLetter = false;
                        }
                        if (newValue.data.error.count > 0) {
                            for (i = 0; i < newValue.data.error.count; i++) {
                                notificationService.notify(
                                    'error', newValue.data.error.data[i]
                                )
                            }
                        }
                    }
                    $scope.tm = newValue.memId;
                    $scope.uid = angular.copy($scope.param.unit);
                    $scope.transfering = false;
                    reset();
                }, function (response) {
                    $scope.error = response.data;
                    if ($scope.error.message) {
                        notificationService.notify('error', response.data.message);
                        reset1();
                    }
                    $scope.transfering = false;
                })
            };
            $scope.remove = function (i) {
                $scope.submitData.splice(i, 1);
                $scope.tAnsars.splice(i, 1);
            };

            function reset() {
                $scope.ansar_id = '';
                $scope.data = '';
                $scope.memId = '';
                $scope.reset1 = {thana: true, kpi: true}
            }

            function reset1() {
                $scope.reset = '';
                $scope.reset1 = '';
                $scope.submitData = [];
                $scope.tAnsars = [];
                $scope.ansar_id = '';
                $scope.data = '';
                $scope.memId = '';
                $scope.reset = {range: true, unit: true};
                $scope.reset1 = {thana: true, kpi: true}
            }
        })
    </script>
    <div ng-controller="TransferController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="transfering">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body">
                    <filter-template
                            show-item="['range','unit']"
                            type="single"
                            start-load="range"
                            reset="reset"
                            field-width="{range:'col-sm-4',unit:'col-sm-4'}"
                            data="param"
                    ></filter-template>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <h4> Enter Ansar ID to transfer</h4>
                                <div class="input-group">
                                    <input type="text" name="ansar_id" ng-keypress="searchAnsar($event)"
                                           ng-disabled="!param.unit"
                                           ng-model="ansar_id" placeholder="Ansar ID" class="form-control">
                                    <span class="input-group-btn">
                                        <button class="btn btn-secondary" ng-click="searchAnsar($event)">
                                            <i class="fa fa-search" ng-if="!search"></i>
                                            <i class="fa fa-spinner fa-pulse" ng-if="search"></i>
                                        </button>
                                    </span>
                                </div>
                                <p class="text text-danger" ng-if="data.status==0">
                                    [[data.messages[0] ]]
                                </p>
                                <ul ng-if="data.status" style="list-style: none;margin-top: 10px;padding-left: 0">
                                    <li><h4 style="text-decoration: underline;display: inline-block">Ansar
                                            Name:</h4>&nbsp;
                                        [[data.data.ansar_name_eng]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline;display: inline-block">Kpi
                                            Name:</h4>&nbsp;
                                        [[data.data.kpi_name]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline;display: inline-block">Kpi
                                            Unit:</h4>&nbsp;
                                        [[data.data.unit_name_eng]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline;display: inline-block">Kpi
                                            Thana:</h4>&nbsp;
                                        [[data.data.thana_name_eng]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline;display: inline-block">Embodiment
                                            Date:</h4>&nbsp;
                                        [[data.data.joining_date|dateformat:"DD-MMM-YYYY"]]
                                    </li>
                                    <li>
                                        <h4 style="text-decoration: underline;display: inline-block">Last Transfer
                                            Date:</h4>&nbsp;
                                        [[data.data.transfered_date|dateformat:"DD-MMM-YYYY"]]
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h4>Transfer Option</h4>
                            <filter-template
                                    show-item="['thana','kpi']"
                                    type="single"
                                    start-load="range"
                                    layout-vertical="1"
                                    load-watch="param.unit"
                                    reset="reset1"
                                    watch-change="thana"
                                    get-kpi-name="kpiName"
                                    get-thana-name="thanaName"
                                    thana-field-disabled="data==undefined||!data.status||!param.unit"
                                    kpi-field-disabled="data==undefined||!data.status||!param.unit"
                                    data="formData"
                            ></filter-template>
                            <div class="form-group">
                                <datepicker-separate-fields label="Transfer Date:" notify="addToTrnsInvalidDate"
                                                            rdata="tempJoiningDate"></datepicker-separate-fields>
                                <input type="hidden"
                                       ng-value="tempJoiningDate" name="joining_date">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" ng-click="addToCart()"
                                        ng-disabled="addToTrnsInvalidDate||data==undefined||!data.status||!formData.kpi||!formData.thana||!param.unit||addToTrnsInvalidDate">
                                    <i class="fa fa-plus"></i>&nbsp;Add to transfer list
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>#</th>
                                        <th>Ansar Id</th>
                                        <th>Name</th>
                                        <th>Current Kpi Name</th>
                                        <th>Transfer Kpi Name</th>
                                        <th>Transfer Kpi Thana</th>
                                        <th>Transfer Kpi Embodiment Date</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr ng-if="tAnsars.length>0" ng-repeat="t in tAnsars">
                                        <td>[[$index+1]]</td>
                                        <td>[[t.id]]</td>
                                        <td>[[t.name]]</td>
                                        <td>[[t.ckn]]</td>
                                        <td>[[t.tkn]]</td>
                                        <td>[[t.tktn]]</td>
                                        <td>[[t.tkjd]]</td>
                                        <td>
                                            <button class="btn btn-danger btn-xs" ng-click="remove($index)">
                                                <i class="fa fa-remove"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr ng-if="tAnsars.length<=0">
                                        <td colspan="5">No Ansar Available</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" style="margin-bottom: 10px">
                            <div class="form-group">
                                <label for="">Memorandum No.:</label>
                                <input type="text" name="mem_id" ng-model="memId" class="form-control"
                                       placeholder="Enter Memorandum no.">
                                <p ng-if="error!=undefined&&error.memId!=undefined" class="text text-danger">
                                    [[error.memId[0] ]]
                                </p>
                            </div>
                            <div class="form-group">
                                <datepicker-separate-fields label="Memorandum Date" notify="memoInvalidDate"
                                                            rdata="memDate"></datepicker-separate-fields>
                                <input ng-value="memDate" type="hidden" name="mem_date">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label for="" style="display: block;">&nbsp;</label>
                            <button ng-disabled="memoInvalidDate||!memId||submitData.length<=0"
                                    class="btn btn-primary btn-md"
                                    ng-click="transferAnsar()">Transfer
                            </button>
                            {!! Form::open(['route'=>'print_letter','target'=>'_blank','ng-if'=>'printLetter','style'=>'display:inline-block']) !!}
                            {!! Form::hidden('option','memorandumNo') !!}
                            {!! Form::hidden('id','[[tm]]') !!}
                            {!! Form::hidden('type','TRANSFER') !!}
                            @if(auth()->user()->type!=22)
                                {!! Form::hidden('unit','[[uid]]') !!}
                            @else
                                {!! Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:'') !!}
                            @endif
                            <button class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;Print Transfer Letter
                            </button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        $("#datepicker").datepicker({dateFormat: 'dd-M-yy'})();
    </script>
@stop