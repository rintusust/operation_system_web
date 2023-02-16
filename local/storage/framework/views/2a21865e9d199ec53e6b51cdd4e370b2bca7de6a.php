<?php $__env->startSection('title','Advanced Search'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <?php echo Breadcrumbs::render('entryadvancedsearch'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        $(document).ready(function () {
            $('#birth_from_name').datepicker({dateFormat:'dd-M-yy'});
        });
        GlobalApp.controller('advancedEntrySearch', function ($scope, $http, httpService,$location,$anchorScroll) {
            $scope.searchOption = {
                division_id: {compare: '=', value: ''},
                smart_card_no: {compare: '=', value: ''},
                unit_id: {compare: '=', value: ''},
                thana_id: {compare: '=', value: ''},
                ansar_name: {compare: 'LIKE', value: ''},
                father_name: {compare: 'LIKE', value: ''},
                blood_group_id: {compare: '=', value: ''},
                hight_feet: {compare: '=', value: ''},
                hight_inch: {compare: '=', value: ''},
                data_of_birth: {compare: '=', value: ''},
                mobile_no_self: {compare: '=', value: ''},
                mobile_no_request: {compare: '=', value: ''},
                national_id_no: {compare: '=', value: ''},
                disease_id: {compare: '=', value: ''},
                own_disease: {compare: 'LIKE', value: ''},
                education: {compare: '=', value: ''},
                sex: {compare: '=', value: ''},
                status:{compare:'=',value:[]}
            }
            $scope.ansarStatusList = [
                {value:"free_status",label:'Free Status'},
                {value:"pannel_status",label:'Panel Status'},
                {value:"offer_sms_status",label:'Offer Status'},
                {value:"embodied_status",label:'Embodied Status'},
                {value:"offer_block_status",label:'Offer Block Status'},
                {value:"freezing_status",label:'Freeze Status'},
                {value:"block_list_status",label:'Block Status'},
                {value:"black_list_status",label:'Black Status'},
//                "retierment_status",
//                "expired_status"
            ]
            $scope.loading = false;
            $scope.itemPerPage = parseInt('<?php echo e(config('app.item_per_page')); ?>')
            $scope.pages = [];
            $scope.name_type = "LIKE";
            $scope.father_name_type = "LIKE";
            $scope.blood_type = "LIKE";
            $scope.division_type = "=";
            $scope.district_type = "=";
            $scope.thana_type = "=";
            $scope.height_type = "=";
            $scope.birth_type = "=";
            $scope.loading = false;
            $scope.mobile_no_self_type = "=";
            $scope.mobile_no_req_type = "=";
            $scope.nid_type = "=";
            var sd = "";
            $scope.advancedSearchSubmit = function () {
                $scope.allLoading = true;
                $http({
                    url: "<?php echo e(URL::to('HRM/advancedentrysearchsubmit')); ?>",
                    method: 'post',
                    data: angular.toJson($scope.searchOption)
                }).then(function (response) {
                    console.log(response.data)
                    $scope.allLoading = false;
                    $scope.loading = false;
                    $scope.nowdata = JSON.stringify(response);
                    $scope.alldata = response.data.data;
                    makePagination(response.data.last_page, response.data.next_page_url)
//                alert($scope.nowdata);
                    $location.hash('search-result')
                    $anchorScroll();
                })

            }
            $scope.dateConvert = function (date) {
                return (moment(date).format('DD-MMM-Y'));
            }
            $scope.advancedSearchPage = function (p, $event) {
//            alert(p.pageNum);
                $scope.AllLoading = true;
                $scope.currentPage = p.pageNum;
//            $scope.currentPage = parseInt(url);
//            alert($scope.currentPage);
                console.log($scope.currentPage);
                $event.preventDefault();
                $http({
                    url: p.url,
                    method: 'post',
                    data: angular.toJson($scope.searchOption)
                }).then(function (response) {
                    $scope.AllLoading = false;
                    $scope.loading = false;
                    $scope.nowdata = JSON.stringify(response);
                    $scope.alldata = response.data.data;
                    console.log(response.data);
                })

            }
            function makePagination(lp, pageUrl) {
                $scope.pages = [];
                if (!pageUrl) return;
                var baseUrl = pageUrl.substring(0, pageUrl.indexOf('?'));
                $scope.pages[0] = {pageNum: 0, url: baseUrl + "?page=" + 1}
                for (var i = 1; i < lp; i++) {
                    $scope.pages[i] = {pageNum: i, url: baseUrl + "?page=" + (i + 1)}
                }
                $scope.currentPage = 0;
                //alert(baseUrl)
            }
            $scope.resetForm = function () {

                $scope.searchOption = {
                    smart_card_no: {compare: '=', value: ''},
                    division_id: {compare: '=', value: ''},
                    unit_id: {compare: '=', value: ''},
                    thana_id: {compare: '=', value: ''},
                    ansar_name: {compare: 'LIKE', value: ''},
                    father_name: {compare: 'LIKE', value: ''},
                    blood_group_id: {compare: '=', value: ''},
                    hight_feet: {compare: '=', value: ''},
                    hight_inch: {compare: '=', value: ''},
                    data_of_birth: {compare: '=', value: ''},
                    mobile_no_self: {compare: '=', value: ''},
                    mobile_no_request: {compare: '=', value: ''},
                    national_id_no: {compare: '=', value: ''},
                    disease_id: {compare: '=', value: ''},
                    own_disease: {compare: 'LIKE', value: ''},
                    education: {compare: '=', value: ''},
                    sex: {compare: '=', value: ''},
                    status:{compare:'=',value:[]}
                }
                $scope.district = [];
                $scope.thana = [];
            }
            httpService.range().then(function (response) {
                $scope.division = response;
            });
            $scope.SelectedItemChanged = function () {
                $scope.loading = true
                httpService.unit($scope.searchOption.division_id.value).then(function (response) {
                    $scope.district = response;
                    $scope.searchOption.unit_id.value = ''
                    $scope.searchOption.thana_id.value = ''
                    $scope.thana = [];
                    $scope.loading = false
                })
            };
            $scope.SelectedDistrictChanged = function () {
//            alert($scope.SelectedDistrict);
                $scope.loading = true
                httpService.thana($scope.searchOption.division_id.value,$scope.searchOption.unit_id.value).then(function (response) {
                    $scope.thana = response;
                    $scope.searchOption.thana_id.value = ''
                    $scope.loading = false
                })
            };
            httpService.bloodGroup().then(function (response) {
                $scope.blood = response;
            });
            httpService.disease().then(function (result) {
                $scope.diseases = result;
            })
            httpService.education().then(function (result) {
                $scope.educations = result;
            })
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            }
        })


        GlobalApp.factory('getNameService', function ($http) {
            return {
                getDivision: function () {
                    return $http.get("<?php echo e(URL::to('HRM/DivisionName')); ?>");
                },
                getDistric: function (data) {

                    return $http.get("<?php echo e(URL::to('HRM/DistrictName')); ?>", {params: {id: data}});
                },
                getThana: function (data) {
                    return $http.get("<?php echo e(URL::to('HRM/ThanaName')); ?>", {params: {id: data}});
                }
            }
        })

        GlobalApp.factory('getBloodService', function ($http) {
            return {
                getAllBloodName: function () {
                    return $http.get("<?php echo e(URL::to('HRM/getBloodName')); ?>")
                }
            }
        });
    </script>
    <?php
    $user = Auth::user();
    $userType = $user->type;
    $allData = [];
    ?>

    <div ng-controller="advancedEntrySearch">
        <?php /*<div class="breadcrumbplace">*/ ?>
        <?php /*<?php echo Breadcrumbs::render('entryadvancedsearch'); ?>*/ ?>
        <?php /*</div>*/ ?>
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-header with-border">
                    <h3 class="box-title">Search Option</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="change-body">
                    <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                    </div>
                    <form method="post">
                        <div class="table-responsive">
                            <table class="table table-condensed table-sm">

                                <thead class="thead-inverse">
                                <tr>
                                    <th style="width:16%;">Search Name</th>
                                    <th style="width:44%;"> Search Type</th>
                                    <th style="width:40%;">Search Value</th>
                                </tr>
                                </thead>
                                <tr>
                                    <td>Smart Card No.</td>
                                    <td><select name="division_type" class="ansaradvancedselect"
                                                ng-model="searchOption.smart_card_no.compare">
                                            <option value="=">EQUAL</option>
                                        </select></td>
                                    <td>
                                        <input type="text" style="width: 100%;" name="smart_card_no" placeholder="Search by Smart card no" ng-model="searchOption.smart_card_no.value">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td><select name="sex" class="ansaradvancedselect"
                                                ng-model="searchOption.sex.compare">
                                            <option value="=">EQUAL</option>
                                        </select></td>
                                    <td>
                                        <select name="sex" class="ansaradvancedname" ng-model="searchOption.sex.value" >
                                            <option value="">--Select an option--</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Division</td>
                                    <td>
                                        <select name="division_type" class="ansaradvancedselect"
                                                ng-model="searchOption.division_id.compare">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="division_name" ng-disabled="loading" class="ansaradvancedname" ng-model="searchOption.division_id.value"
                                                ng-change="SelectedItemChanged()">
                                            <option value="">--Select an option--</option>
                                            <option ng-repeat="d in division" value=[[d.id]]>[[d.division_name_eng]]
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>District</td>
                                    <td>
                                        <select name="district_type" class="ansaradvancedselect"
                                                ng-model="searchOption.unit_id.compare">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="district_name" ng-disabled="loading" class="ansaradvancedname" ng-model="searchOption.unit_id.value"
                                                ng-change="SelectedDistrictChanged()">
                                            <option value="">--Select an option--</option>
                                            <option ng-repeat="x in district" value=[[x.id]]>[[x.unit_name_eng]]
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thana</td>
                                    <td>
                                        <select name="thana_type" class="ansaradvancedselect" ng-model="searchOption.thana_id.compare">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="thana_name"  ng-disabled="loading" class="ansaradvancedname" ng-model="searchOption.thana_id.value">
                                            <option value="">--Select an option--</option>
                                            <option ng-repeat="x in thana" value=[[x.id]]>[[x.thana_name_eng]]</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Name</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="name_type" ng-model="searchOption.ansar_name.compare">
                                            <option value="LIKE">LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="ansaradvancedname" name="search_name" type="text"
                                               ng-model="searchOption.ansar_name.value"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Father Name</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="name_type"
                                                ng-model="searchOption.father_name.compare">
                                            <option value="LIKE">LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="ansaradvancedname" name="search_name" type="text"
                                               ng-model="searchOption.father_name.value"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Blood Group</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="blood_type" ng-model="searchOption.blood_group_id.compare">
                                            <option value="">--Select an option--</option>
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="blood_name" class="ansaradvancedname" ng-model="searchOption.blood_group_id.value">
                                            <option value="">Select an option</option>
                                            <option ng-repeat="x in blood" value=[[x.id]]>[[x.blood_group_name_eng]]
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Height</td>
                                    <td>
                                        <select name="height_type" class="ansaradvancedselect" ng-model="searchOption.hight_feet.compare" ng-change="searchOption.hight_inch.compare=searchOption.hight_feet.compare">
                                            <option value="">Select an option</option>
                                            <option value="=">EQUAL</option>
                                            <option value=">">GREATER THAN</option>
                                            <option value="<">SMALLER THAN</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="height_search" name="height_name" type="text"
                                               ng-model="searchOption.hight_feet.value" placeholder="Feet"/>
                                        <input class="height_search" name="inch_name" type="text" ng-model="searchOption.hight_inch.value"
                                               placeholder="Inch"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date Of Birth</td>
                                    <td>
                                        <select name="birth_type" class="ansaradvancedselect" ng-model="searchOption.data_of_birth.compare">

                                            <option value="=">EQUAL</option>
                                            <option value="<">BEFORE</option>
                                            <option value=">">AFTER</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div style="width:100%;">
                                            <input class="ansaradvancedname" name="birth_from_name" id="birth_from_name"
                                                   type="text"
                                                   ng-model="searchOption.data_of_birth.value" style="height:25px"/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mobile No. Self</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="mobile_no_self"
                                                ng-model="searchOption.mobile_no_self.compare">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="ansaradvancedname" name="mobile_no_self" type="text"
                                               ng-model="searchOption.mobile_no_self.value"
                                               placeholder="Enter mobile number; Example: 01710000000"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mobile No. Request</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="mobile_no_request"
                                                ng-model="searchOption.mobile_no_request.compare">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="ansaradvancedname" name="mobile_no_request" type="text"
                                               ng-model="searchOption.mobile_no_request.value"
                                               placeholder="Enter mobile number; Example: 01710000000"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>National ID</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="nid"
                                                ng-model="searchOption.national_id_no.compare">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="ansaradvancedname" name="nid" type="text"
                                               ng-model="searchOption.national_id_no.value" placeholder="Enter NID number"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Diseases</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="nid"
                                                ng-model="searchOption.disease_id.compare">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="disease_name" class="ansaradvancedname" ng-model="searchOption.disease_id.value"
                                                ng-change="SelectedItemChanged()">
                                            <option value="">--Select an option--</option>
                                            <option ng-repeat="d in diseases" value=[[d.id]]>[[d.disease_name_bng]]</option>
                                            <option value="type">Other</option>
                                        </select>
                                        <input class="ansaradvancedname" ng-if="searchOption.disease_id.value=='type'" name="nid" type="text"
                                               ng-model="searchOption.own_disease.value" placeholder="Enter disease"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Education</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="nid"
                                                ng-model="searchOption.education.compare">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="disease_name" class="ansaradvancedname" ng-model="searchOption.education.value">
                                            <option value="">--Select an option--</option>
                                            <option ng-repeat="d in educations" value=[[d.id]]>[[d.education_deg_bng]]</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>
                                        <select class="ansaradvancedselect" name="status"
                                                ng-model="searchOption.status.compare">
                                            <option value="=">EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div style="height: 150px;overflow-y: auto;width: 100%;border: 1px solid #cccccc;padding-left: 5px">
                                            <ul style="list-style: none;padding: 0">
                                                <li ng-repeat="s in ansarStatusList">
                                                    <input type="checkbox" ng-true-value="'[[s.value]]'" ng-false-value="0" ng-model="searchOption.status.value[$index]"/>[[s.label]]
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <button ng-click="advancedSearchSubmit()" class="btn btn-primary pull-right" style="margin-right:6px;">
                            Search
                        </button>
                        <button ng-click="resetForm()" class="btn btn-primary pull-right" style="margin-right:6px;">
                            Reset
                        </button>
                        <!--<button  class="default pull-right" style="margin-right:6px;">submit</button>-->

                    </form>
                </div>
            </div>
            <div class="box box-solid" id="search-result">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-header"><h3>Search Result</h3></div>

                <div class="box-body" id="change-body">
                    <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="ansar-table">

                            <tr>
                                <th>SL. No</th>
                                <th>ID No</th>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Father Name</th>
                                <th>Sex</th>
                                <th>District</th>
                                <th>Date of birth</th>
                                <th>Mobile No.(Self)</th>
                            </tr>

                            <tr ng-repeat="ansar in alldata">
                                <td>[[pages[currentPage]==undefined?$index+1:pages[currentPage].pageNum*itemPerPage+1+$index ]]</td>
                                <td><a href="<?php echo e(URL::to('HRM/entryreport/')); ?>/[[ansar.ansar_id]]">[[ansar.ansar_id]]</a>
                                </td>
                                <td>[[ansar.name_eng]]</td>
                                <td>[[ansar.ansar_name_eng]]</td>
                                <td>[[ansar.father_name_eng]]</td>
                                <td>[[ansar.sex]]</td>
                                <td>[[ansar.unit_name_eng]]</td>
                                <td>[[dateConvert(ansar.data_of_birth)]]</td>
                                <td>[[ansar.mobile_no_self|checkpermission:"view_mobile_no" ]]</td>
                            </tr>
                        </table>
                    </div>
                    <div class="table_pagination" ng-if="pages.length>1">
                        <ul class="pagination">
                            <li ng-class="{disabled:currentPage == 0}">
                                <a class="page-button"  ng-disabled="currentPage==0" ng-click="advancedSearchPage(pages[0],$event)">&laquo;&laquo;</a>
                            </li>
                            <li ng-class="{disabled:currentPage == 0}">
                                <a class="page-button"  ng-disabled="currentPage==0" ng-click="advancedSearchPage(pages[currentPage-1],$event)">&laquo;</a>
                            </li>
                            <li ng-repeat="page in pages|filter:filterMiddlePage"
                                ng-class="{active:page.pageNum==currentPage}">
                                <span ng-show="currentPage == page.pageNum">[[page.pageNum+1]]</span>
                                <a href="#" ng-click="advancedSearchPage(page,$event)"
                                   ng-hide="currentPage == page.pageNum">[[page.pageNum+1]]</a>

                            </li>
                            <li ng-class="{disabled:currentPage==pages.length-1}">
                                <a class="page-button" ng-disabled="currentPage==pages.length-1" ng-click="advancedSearchPage(pages[currentPage+1],$event)">&raquo;</a>
                            </li>
                            <li ng-class="{disabled:currentPage==pages.length-1}">
                                <a class="page-button"  ng-disabled="currentPage==pages.length-1"
                                   ng-click="advancedSearchPage(pages[pages.length-1],$event)">&raquo;&raquo;</a>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>


        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>