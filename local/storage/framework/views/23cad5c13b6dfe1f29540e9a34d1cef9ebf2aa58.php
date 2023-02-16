<?php $__env->startSection('title','Applicants'); ?>
<?php $__env->startSection('breadcrumb'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <script>
        GlobalApp.controller('ApplicantScreeningController', function ($scope, $http, $sce, $parse, notificationService) {

            $scope.rank = 'all';
            $scope.queue = [];
            $scope.exportPage = '';
            $scope.defaultPage = {pageNum: 0, offset: 0, limit: $scope.itemPerPage, view: 'view'};
            $scope.total = 0;
            $scope.param = {};
            $scope.numOfPage = 0;
            $scope.itemPerPage = parseInt("<?php echo e(config('app.item_per_page')); ?>");
            $scope.currentPage = 0;
            $scope.allApplicants = [];
            $scope.ansar_id = [];
            $scope.pages = [];
            $scope.loadingPage = [];
            $scope.allLoading = false;
            $scope.showLoadScreen1 = true;
            $scope.orderBy = "";
            $scope.values = [{id: 1,name: 'first'}, {id: 2,name: 'second'}];
            $scope.from_date = '';
            $scope.to_date = '';
            $scope.isDisabled = false;


            $scope.loadPage = function (page, $event) {

                if ($event != undefined) $event.preventDefault();
                $scope.exportPage = page;
                $scope.currentPage = page == undefined ? 0 : page.pageNum;
                $scope.loadingPage[$scope.currentPage] = true;
                if($scope.mobile_no_self=='' && $scope.spOrderID==''){
                    $scope.allApplicants = [];
                    return ;
                }
                $scope.allLoading = true;

                $http({
                    url: '<?php echo e(URL::to('recruitment/applicant_list')); ?>',
                    method: 'get',
                    params:
                        {
                            offset: page == undefined ? 0 : page.offset,
                            limit: page == undefined ? $scope.itemPerPage : page.limit,
                            mobile_no_self : $scope.mobile_no_self,
                            spOrderID : $scope.spOrderID

                        }
                }).then(function (response) {
                    $scope.queue.shift();
                    if ($scope.queue.length > 1) $scope.loadPage();
                    console.log(response);
                    $scope.allApplicants = response.data;
                    console.log($scope.allApplicants);
                    $scope.loadingPage[$scope.currentPage] = false;
                    $scope.allLoading = false;
                    $scope.total = response.data.length;
                    console.log($scope.total);
                    $scope.gCount = response.data;
                    $scope.numOfPage = Math.ceil($scope.total / $scope.itemPerPage);

                })
            };


            $scope.search = function () {
            };
            $scope.filterMiddlePage = function (value, index, array) {
                var minPage = $scope.currentPage - 3 < 0 ? 0 : ($scope.currentPage > array.length - 4 ? array.length - 8 : $scope.currentPage - 3);
                var maxPage = minPage + 7;
                if (value.pageNum >= minPage && value.pageNum <= maxPage) {
                    return true;
                }
            };

            $scope.modal = function (allApplicants) {
                console.log(allApplicants);
                $scope.printLetter = false;
                $scope.getSingleRow = allApplicants;

            }

            function capitalizeLetter(s) {
                return s.charAt(0).toUpperCase() + s.slice(1);
            }

            function sum(t) {
                var s = 0;
                for (var i in t) {
                    s += parseInt(t[i])
                }
                return s;
            }
        });
        $(function () {
            $("#print-report").on('click', function (e) {
                $("#print-area").remove();
                $("#print_table table").removeClass('table table-bordered');
                $('body').append('<div id="print-area">' + $("#print_table").html() + '</div>');
                window.print();
                $("#print_table table").addClass('table table-bordered');
                $("#print-area").remove()
            })
        })
    </script>
    <div ng-controller="ApplicantScreeningController">
        <section class="content">
            <div>
                <div class="box box-solid">
                    <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                    </div>
                    <div class="box-body">
                        <div class="box-body" id="change-body">
                            <filter-template
                                    show-item="[]"
                                    type="all"
                                    allApplicants="param"
                                    start-load="range"
                            ></filter-template>


                            <div class="loading-data"><i class="fa fa-4x fa-refresh fa-spin loading-icon"></i>
                            </div>

                            <div id="print_table">

                                <div class="table-responsive">
                                    <table class="table  table-bordered table-striped" id="ansar-table">
                                        <caption>
                                            <div class="row">

                                                <div class="col-md-4 col-sm-12" style="float:right;">
                                                    <database-search q="spOrderID" queue="queue" on-change="loadPage()" place-holder="Search by SP Order ID"></database-search>
                                                </div>
                                                <div class="col-md-4 col-sm-12" style="float:right;">
                                                    <database-search q="mobile_no_self" queue="queue" on-change="loadPage()" place-holder="Search by Mobile Number"></database-search>
                                                </div>
                                            </div>

                                        </caption>
                                        <tr>

                                            <th class="text-center">Circular Name</th>
                                            <th class="text-center">Applicant Name</th>
                                            <th class="text-center">Applicant ID</th>
                                            <th class="text-center">Applicant Password</th>
                                            <th class="text-center">spOrderID</th>
                                            <th class="text-center">NID</th>
                                            <th class="text-center">Birth Date</th>
                                            <th class="text-center">Mobile no</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>

                                        </tr>
                                        <tr ng-show="allApplicants.length>0"
                                            ng-repeat="applicant in allApplicants">

                                            <td class="text-center">[[applicant.circular_name]]</td>
                                            <td class="text-center">[[applicant.applicant_name_bng]]</td>
                                            <td class="text-center">[[applicant.applicant_id]]</td>
                                            <td class="text-center">[[applicant.applicant_password]]</td>
                                            <td class="text-center">[[applicant.spOrderID]]</td>
                                            <td class="text-center">[[applicant.national_id_no]]</td>
                                            <td class="text-center">[[applicant.date_of_birth]]</td>
                                            <td class="text-center">[[applicant.mobile_no_self]]</td>
                                            <td class="text-center">[[applicant.status]]</td>
                                            <td ng-if="[[applicant.status]]=='paid' || [[applicant.status]]=='selected'" class="text-center">
                                                <a class="btn btn-sm btn-primary"
                                                   href="<?php echo e(URL::route('recruitment.applicant.send_msg',['id'=> "applicant.applicant_id",'circular_id'=> "applicant.job_circular_id"])); ?>">Send MSG
                                                </a>
                                            </td>
                                            <td ng-show="[[applicant.status]]!='paid' || [[applicant.status]]!='selected'" class="text-center"></td>


                                        </tr>
                                        <tr ng-show="allApplicants.length==0">
                                            <td class="warning" colspan="11">No information found</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>