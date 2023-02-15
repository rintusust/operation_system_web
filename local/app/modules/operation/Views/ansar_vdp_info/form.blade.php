<style>
    .control-label {
        text-align: left !important;
    }
    p.text-danger{
        margin-bottom: 5px !important;
    }
    table{
        margin-bottom: 5px !important;
    }
</style>
<script>
    var formData = new FormData();
    GlobalApp.controller('InfoController', function ($scope, $http, httpService, $q, notificationService,$rootScope) {
        $scope.info = {urL: '', form: {}};
        $scope.errors = {};
        $scope.subTraining = [];
        $scope.info.url = '{{$url}}'
        $scope.educationDegrees = [1];
        $scope.training_info = [1];
        $scope.allLoading = true

        $q.all([
            httpService.range(),
            httpService.bloodGroup(),
            httpService.VdpDesignation()
            @if(isset($id))
            , $http.get("{{URL::route('operation.info.edit',['id'=>$id])}}")
            @endif

        ]).then(function (response) {
            $scope.divisions = response[0];
            $scope.bloodGroups = response[1];
            $scope.vdpDesignations = response[2];

            @if(isset($id))
                $scope.info.form = response[3].data;
            $scope.info.form['_method'] = 'patch';
            $scope.info.form['division_id'] += '';
            $scope.info.form['unit_id'] += '';
            $scope.info.form['thana_id'] += '';
            $scope.info.form['union_id'] += '';

            $scope.info.form['blood_group_id'] += '';
            $scope.loadUnit($scope.info.form['division_id']);
            $scope.loadThana($scope.info.form['division_id'], $scope.info.form['unit_id']);
            $scope.loadUnion($scope.info.form['division_id'], $scope.info.form['unit_id'], $scope.info.form['thana_id']);
            Object.keys($scope.info.form).forEach(function (key) {
                if(!$scope.info.form[key]) delete $scope.info.form[key];
            })
            @endif
                $scope.allLoading = false
        })
        $scope.loadUnit = function (rangeId) {
            $scope.units = $scope.thanas = $scope.unions = [];
            httpService.unit(rangeId).then(function (response) {
                $scope.units = response;
            })
        }
        $scope.loadThana = function (rangeId, unitId) {
            $scope.thanas = $scope.unions = [];
            httpService.thana(rangeId, unitId).then(function (response) {
                $scope.thanas = response;
            })
        }
        $scope.loadUnion = function (rangeId, unitId, thanaId) {
            $scope.unions = [];
            httpService.union(rangeId, unitId, thanaId).then(function (response) {
                $scope.unions = response;
            })
        }


        $scope.submitForm = function (event) {
            $scope.allLoading = true;
            event.preventDefault();
            var data = new FormData();
            console.log($scope.info.form)
            Object.keys($scope.info.form).forEach(function (key) {
                console.log();
                data.append(key, $scope.info.form[key]);
            })
            $http({
                method: 'post',
                url: $scope.info.url,
                data: data,
                headers: {
                    'content-type': undefined
                }
            }).then(function (response) {
//                if($rootScope.ws) $rootScope.ws.send(JSON.stringify({type:'notification',data:{to:[1],message:response.data.message}}))
                window.location.href = '{{URL::route('operation.info.index')}}'
            }, function (response) {
                $scope.allLoading = false
                if (response.status === 422) {
                    $scope.errors = response.data;
                }
                else {
                    notificationService.notify("error", response.data.message)
                }
            })
        }
    })
    GlobalApp.directive('fileParse', function () {
        return {
            restrict: 'A',
            link: function (scope, elem, attrs) {
                $(elem).on('change', function () {
                    scope.info.form['profile_pic'] = elem[0].files[0];
                })
            }
        }
    })
</script>
<div ng-controller="InfoController">
    <div class="overlay" style="z-index:100;position: absolute;width: 100%;height: 100%;" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
    </div>
    <form class="form-horizontal" ng-submit="submitForm($event)">

        <fieldset>
            <legend>জিও কোড ভিত্তিক আইডির জন্য তথ্য</legend>
            <div class="form-group">
                <label for="division_id" class="control-label col-sm-4">বিভাগ<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.division_id" id="division_id"
                            ng-change="loadUnit(info.form.division_id)">
                        <option value="">--বিভাগ নির্বাচন করুন--</option>
                        <option ng-repeat="d in divisions" value="[[d.id]]">[[d.division_name_bng]]</option>
                    </select>
                    <p ng-if="errors.division_id&&errors.division_id.length>0" class="text text-danger">
                        [[errors.division_id[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="unit_id" class="control-label col-sm-4">জেলা<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.unit_id" id="unit_id"
                            ng-change="loadThana(info.form.division_id,info.form.unit_id)">
                        <option value="">--জেলা নির্বাচন করুন--</option>
                        <option ng-repeat="u in units" value="[[u.id]]">[[u.unit_name_bng]]</option>
                    </select>
                    <p ng-if="errors.unit_id&&errors.unit_id.length>0" class="text text-danger">[[errors.unit_id[0]
                        ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="thana_id" class="control-label col-sm-4">উপজেলা<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" ng-model="info.form.thana_id" id="thana_id">
                        <option value="">--উপজেলা নির্বাচন করুন--</option>
                        <option ng-repeat="t in thanas" value="[[t.id]]">[[t.thana_name_bng]]</option>
                    </select>
                    <p ng-if="errors.thana_id&&errors.thana_id.length>0" class="text text-danger">[[errors.thana_id[0]
                        ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="union_id" class="control-label col-sm-4">ইউনিয়ন<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">

                    <input type="text" class="form-control" placeholder="ইউনিয়ন/ওয়ার্ড" ng-model="info.form.union_word_text"
                           id="union_word_text">
                    <p ng-if="errors.union_word_text&&errors.union_word_text.length>0" class="text text-danger">
                        [[errors.union_word_text[0] ]]</p>
                </div>
            </div>

        </fieldset>
        <fieldset>
            <legend>ব্যক্তিগত ও পারিবারিক তথ্য</legend>

            <div class="form-group">
                <label for="ansar_name_bng" class="control-label col-sm-4">নাম<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="নাম" ng-model="info.form.ansar_name_bng"
                           id="ansar_name_bng">
                    <p ng-if="errors.ansar_name_bng&&errors.ansar_name_bng.length>0" class="text text-danger">
                        [[errors.ansar_name_bng[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="rank" class="control-label col-sm-4">বর্তমান পদবী<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" placeholder="বর্তমান পদবী" ng-model="info.form.designation"
                            id="rank">
                        <option value="">--পদবী নির্বাচন করুন--</option>
                            <option ng-repeat="vd in vdpDesignations" value="[[vd.id]]">[[vd.designation_name_bng]]</option>
                    </select>
                    {{--<input type="text" class="form-control" placeholder="বর্তমান পদবী" ng-model="info.form.designation"
                           id="rank">--}}
                    <p ng-if="errors.designation&&errors.designation.length>0" class="text text-danger">
                        [[errors.designation[0] ]]</p>
                </div>
            </div>
            <div class="form-group">
                <label for="father_name_bng" class="control-label col-sm-4">পিতার নাম<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="পিতার নাম" ng-model="info.form.father_name_bng"
                           id="father_name_bng">
                    <p ng-if="errors.father_name_bng&&errors.father_name_bng.length>0" class="text text-danger">
                        [[errors.father_name_bng[0] ]]</p>
                </div>
            </div>

            <div class="form-group">
                <label for="blood_group_id" class="control-label col-sm-4">রক্তের গ্রুপ<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <select id="blood_group_id" ng-model="info.form.blood_group_id" class="form-control">
                        <option value="">--রক্তের গ্রুপ নির্বাচন করুন</option>
                        <option ng-repeat="b in bloodGroups" value="[[b.id]]">[[b.blood_group_name_bng]]</option>
                    </select>
                    <p ng-if="errors.blood_group_id&&errors.blood_group_id.length>0" class="text text-danger">
                        [[errors.blood_group_id[0] ]]</p>
                </div>
            </div>

            <div class="form-group">
                <label for="date_of_birth" class="control-label col-sm-4">জন্ম তারিখ<sup class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" date-picker class="form-control" ng-model="info.form.date_of_birth"
                           id="date_of_birth" date-picker-big="[[info.form.date_of_birth]]" placeholder="জন্ম তারিখ">
                    <p ng-if="errors.date_of_birth&&errors.date_of_birth.length>0" class="text text-danger">
                        [[errors.date_of_birth[0] ]]</p>
                </div>
            </div>
            {{--        <div class="form-group">
                        <label for="base_of_birth_date" class="control-label col-sm-4">জন্মতারিখের ভিত্তি<sup class="text-red">*</sup>
                            <span class="pull-right">:</span>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="জন্মতারিখের ভিত্তি" ng-model="info.form.base_of_birth_date" id="base_of_birth_date">
                        </div>
                    </div>--}}

        </fieldset>
        <fieldset>
            <legend>যোগাযোগ</legend>
            <div class="form-group">
                <label for="mobile_no_self" class="control-label col-sm-4">মোবাইল নম্বর(নিজ)<sup
                            class="text-red">*</sup>
                    <span class="pull-right">:</span>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="মোবাইল নম্বর"
                           ng-model="info.form.mobile_no_self" id="mobile_no_self">
                    <p ng-if="errors.mobile_no_self&&errors.mobile_no_self.length>0" class="text text-danger">
                        [[errors.mobile_no_self[0] ]]</p>
                </div>
            </div>


        </fieldset>


        <fieldset>
            <legend>ছবি সমূহ</legend>
            <div class="form-group">
                <label for="profile_pic" class="control-label col-sm-4">প্রোফাইল পিকচার<span class="pull-right">:</span></label>
                <div class="col-sm-8">
                    <input type="file" file-parse>
                </div>
            </div>
        </fieldset>
        <button type="submit" class="btn btn-primary pull-right">
            <i class="fa fa-file"></i>&nbsp;Submit
        </button>
    </form>
</div>