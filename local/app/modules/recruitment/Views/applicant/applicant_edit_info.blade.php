@extends('template.master')
@section('title','Edit Applicant')
@section('breadcrumb')
    {!! Breadcrumbs::render('recruitment.applicant.edit_applicant') !!}
@endsection
@section('content')
    <script>
        GlobalApp.controller('applicantSearch', function ($scope, $http, $q, httpService, $sce,$rootScope) {
            var p = '50'
            $scope.categories = [];
            $scope.q = '';
            $scope.selectMessage = '';
            $scope.educations = [];
            $scope.circulars = [];
            $scope.applicants = $sce.trustAsHtml(`<div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <caption style="font-size: 20px;color:#111111">All applicants</caption>
                            <tr>
                                <th>Sl. No</th>
                                <th>Applicant Name</th>
                                <th>Gender</th>
                                <th>Birth Date</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Thana</th>
                                <th>Height</th>
                                <th>Chest</th>
                                <th>Weight</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td colspan="11" class="bg-warning">No applicant available
                                .Select <strong>Circular</strong> and <strong>status</strong> to load applicant
                                </td>
                            </tr>
                        </table>
                    </div>`);
            $scope.allStatus = {'': '--Select a status', 'applied': 'Applied', 'selected': 'Selected','accepted':'Accepted'}
            $scope.param = {};
            $scope.limitList = '50';

            httpService.circular({status: 'running'}).then(function (response) {
                $scope.circular = 'all';
                $scope.circulars = response.data;
                $scope.allLoading = false;
            }, function (response) {
                $scope.circular = 'all';
                $scope.circulars = [];
                $scope.allLoading = false;
            })
            $scope.$watch('limitList', function (n, o) {
                if (n == null) {
                    $scope.limitList = o;
                }
                else if (p != n && p != null) {
                    p = n;
                    $scope.loadApplicant();
                }
            })
            $scope.loadApplicant = function (url) {
                //alert($scope.limitList)
                $scope.allLoading = true;
                if($scope.param['limit']===undefined){
                    $scope.param['limit'] = '50';
                }
                $http({
                    url:url||'{{URL::route('recruitment.applicant.info')}}',
                    method:'post',
                    data:$scope.param
                }).then(function (response) {
                    $scope.applicants = $sce.trustAsHtml(response.data);
                    $scope.allLoading = false;
                }, function (response) {
                    $scope.applicants = $sce.trustAsHtml(`<div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <caption style="font-size: 20px;color:#111111">All applicants</caption>
                            <tr>
                                <th>Sl. No</th>
                                <th>Applicant Name</th>
                                <th>Gender</th>
                                <th>Birth Date</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Thana</th>
                                <th>Height</th>
                                <th>Chest</th>
                                <th>Weight</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <td colspan="11" class="bg-warning">
                                An error occur while loading. try again later or
                                <button class="btn btn-xs btn-primary" ng-click="loadApplicant()"><i class="fa fa-refresh"></i>&nbsp;Refresh now</button>
                                </td>
                            </tr>
                        </table>
                    </div>`);
                    $scope.allLoading = false;
                })
            }
            var v = '<div class="text-center" style="margin-top: 20px"><i class="fa fa-spinner fa-pulse"></i></div>'
            $scope.editApplicant = function (url) {
                $("#edit-form").modal('show');
                $rootScope.detail = $sce.trustAsHtml(v);
                $http.get(url).then(function (response) {
                    $rootScope.detail = $sce.trustAsHtml(response.data.view);
                    $rootScope.applicant_id = response.data.id;
                })
            }
            $scope.submitComplete = function () {
                $("#edit-form").modal('hide');
            }


        })
        GlobalApp.controller('fullEntryFormController', function ($scope, $q, $http, httpService, notificationService,$rootScope) {
            $scope.isAdmin = parseInt('{{Auth::user()->type}}')
            $scope.formData = {};
            $scope.fields = [];
            $scope.eduRows = [];
            $scope.eduEngRows = [];
            $scope.allLoading = true;
            $scope.relations = {
                '': '--সম্পর্ক নির্বাচন করুন--',
                'father': 'Father',
                'mother': 'Mother',
                'brother': 'Brother',
                'sister': 'Sister',
                'cousin': 'Cousin',
                'uncle': 'Uncle',
                'aunt': 'Aunt',
                'neighbour': 'Neighbour'
            };

            $scope.profile_pic = " ";
            $scope.formSubmitResult = {};
            $scope.ppp = [];
            $scope.disableDDT = false;
            $scope.calling = function () {
                alert($scope.profile_pic);
            }
            $scope.disableDDT = true;
            $scope.loadApplicantDetail = function () {
                $q.all([
                    $http({method: 'get', url: '{{URL::to('recruitment/applicant/detail')}}/'+$rootScope.applicant_id}),
                    httpService.range(),
                    httpService.education(),
                    $http({method: 'get', url: '{{URL::route('recruitment.applicant.getfieldstore')}}'})
                ]).then(function (response) {
                    console.log(response)
                    $scope.allLoading = false;
                    $scope.formData = response[0].data.data;
                    $scope.district = response[0].data.units;
                    $scope.thana = response[0].data.thanas;
                    $scope.division = response[1];
                    $scope.ppp = response[2];
                    $scope.fields = response[3].data['field_value'].split(',');
                    $scope.disableDDT = false;
                    $scope.formData.division_id += '';
                    $scope.formData.unit_id += '';
                    $scope.formData.thana_id += '';
                    $scope.formData.appliciant_education_info.forEach(function (d, i) {

                        $scope.formData.appliciant_education_info[i].job_education_id += '';
                    })
                });
            }
            $scope.SelectedItemChanged = function () {
                $scope.disableDDT = true;
                httpService.unit($scope.formData.division_id).then(function (response) {
                    $scope.district = response;
                    $scope.thana = [];
                    $scope.formData.unit_id = '';
                    $scope.formData.thana_id = '';

                    $scope.disableDDT = false;
                })
            };
            $scope.SelectedDistrictChanged = function () {
                $scope.disableDDT = true;
                httpService.thana($scope.formData.division_id, $scope.formData.unit_id).then(function (response) {
                    $scope.thana = response;
                    $scope.formData.thana_id = "";
                    $scope.disableDDT = false;
                })
            };

            $scope.eduDeleteRows = function (index) {
                $scope.formData.appliciant_education_info.splice(index, 1);
            }
            $scope.addEducation = function () {
                $scope.formData.appliciant_education_info.push({
                    job_education_id: '',
                    job_applicant_id: $scope.formData.id,
                    institute_name: '',
                    gade_divission: '',
                    passing_year: ''
                })
            }
            $scope.updateData = function () {
                $scope.allLoading = true;
                $http({
                    method: 'post',
                    data: $scope.formData,
                    url: '{{URL::route('recruitment.applicant.update')}}'
                }).then(function (response) {
                    $scope.allLoading = false;
                    notificationService.notify(response.data.status, response.data.message)
                    $("#edit-form").modal('toggle');
                    $rootScope.$emit('refreshData',{})
                }, function (response) {
                    $scope.allLoading = false;
                    if (response.status == 422) {
                        $scope.formSubmitResult['error'] = response.data;
                    }
                    else {
                        notificationService.notify('error', 'An unknown error occur. Please try again later')
                    }
                })
            }
            $scope.isEditable = function (s) {

                if($scope.isAdmin!=11&&($scope.fields==undefined||$scope.fields.indexOf(s)<0)) return false;
                return true;
            }
        });
        GlobalApp.directive('compileHtml', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newScope;
                    scope.$watch('applicants', function (n) {

                        if (attr.ngBindHtml) {
                            if(newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        })
        GlobalApp.directive('compileHtmll', function ($compile) {
            return {
                restrict: 'A',
                link: function (scope, elem, attr) {
                    var newScope;
                    scope.$watch('detail', function (n) {

                        if (attr.ngBindHtml) {
                            if(newScope) newScope.$destroy();
                            newScope = scope.$new();
                            $compile(elem[0].children)(newScope)
                        }
                    })

                }
            }
        })
    </script>
    <section class="content" ng-controller="applicantSearch">
        <div class="box box-solid">
            <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Job Circular</label>
                            <select name="" ng-model="param.circular"
                                    class="form-control">
                                <option value="">--Select a circular</option>
                                <option ng-repeat="c in circulars" value="[[c.id]]">[[c.circular_name]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label">Applicant status</label>
                            <select name="" ng-model="param.status"
                                    class="form-control">
                                <option ng-repeat="(k,v) in allStatus" value="[[k]]">[[v]]</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="" class="control-label " style="display: block;">&nbsp;</label>
                            <button ng-disabled="!(param.status&&param.circular)" class="btn btn-primary" ng-click="loadApplicant()">Load Applicant</button>
                        </div>
                    </div>
                </div>
                <filter-template
                        show-item="['range','unit','thana']"
                        type="all"
                        data="param"
                        start-load="range"
                        field-name="{unit:'unit'}"
                        range-change="loadApplicant()"
                        unit-change="loadApplicant()"
                        thana-change="loadApplicant()"
                        unit-field-disabled="!(param.circular&&param.status)"
                        range-field-disabled="!(param.circular&&param.status)"
                        thana-field-disabled="!(param.circular&&param.status)"
                        field-width="{unit:'col-sm-4',range:'col-sm-4',thana:'col-sm-4'}"
                >
                </filter-template>
                <div ng-bind-html="applicants" compile-html>

                </div>
            </div>
        </div>
        <div class="modal fade" id="edit-form">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit form</h4>
                    </div>
                    <div class="modal-body" ng-bind-html="detail" compile-htmll>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
