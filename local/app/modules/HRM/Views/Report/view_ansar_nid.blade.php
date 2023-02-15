@extends('template.master')
@section('title','Verify NID Data')
@section('breadcrumb')
@endsection
@section('content')
    <script>

        GlobalApp.controller("ViewAnsarNIDController", function ($rootScope, $scope, $http, $sce) {
            $scope.ansarDetail = {};
            $scope.allLoading = false;
            $scope.dob = '';
            $scope.loadAnsarDetail = function (id) {
               // var nid_data = "{\r\n    \"requestId\": \"9edbd02b-c6bd-42d2-9246-de7892bf971f\",\r\n    \"name\": \"মোঃ আবু বকর ছিদ্দিক\",\r\n    \"nameEn\": \"Md. Abu Bakar Siddiq\",\r\n    \"bloodGroup\": \"A+\",\r\n    \"dateOfBirth\": \"1998-09-21\",\r\n    \"father\": \"মোঃ হুমায়ুন কবির\",\r\n    \"mother\": \"মোছাঃ ওয়াশিমা আক্তার\",\r\n    \"spouse\": \"\",\r\n    \"nationalId\": \"3764985739\",\r\n    \"occupation\": \"ছাত্র/ছাত্রী\",\r\n    \"permanentAddress\": {\r\n        \"division\": \"চট্টগ্রাম\",\r\n        \"district\": \"ব্রাহ্মণবাড়িয়া\",\r\n        \"rmo\": \"1\",\r\n        \"upozila\": \"বাঞ্ছারামপুর\",\r\n        \"unionOrWard\": \"ফরদাবাদ\",\r\n        \"postOffice\": \"রামকৃষ্ণপুর\",\r\n        \"postalCode\": \"৩৫৪১\",\r\n        \"wardForUnionPorishod\": 1,\r\n        \"additionalMouzaOrMoholla\": \"পূর্বহাটি\",\r\n        \"additionalVillageOrRoad\": \"চরলহনীয়া\",\r\n        \"homeOrHoldingNo\": \".\",\r\n        \"region\": \"কুমিল্লা\"\r\n    },\r\n    \"photo\": \"https://prportal.nidw.gov.bd/file-1f/1/d/f/6f084425-ce86-4544-a4fe-eac85dec23fe/Photo-6f084425-ce86-4544-a4fe-eac85dec23fe.jpg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=fileobj%2F20221219%2Fus-east-1%2Fs3%2Faws4_request&X-Amz-Date=20221219T083800Z&X-Amz-Expires=120&X-Amz-SignedHeaders=host&X-Amz-Signature=6bf7dcbd1ea867a91445108c007355f06859f015e8eb8f59d106e7db15502463\",\r\n    \"presentAddress\": {\r\n        \"division\": \"রাজশাহী\",\r\n        \"district\": \"পাবনা\",\r\n        \"rmo\": \"2\",\r\n        \"upozila\": \"পাবনা সদর\",\r\n        \"cityCorporationOrMunicipality\": \"পাবনা পৌরসভা\",\r\n        \"unionOrWard\": \"ওয়ার্ড নং-০৩\",\r\n        \"postOffice\": \"পাবনা সদর\",\r\n        \"postalCode\": \"৬৬০০\",\r\n        \"wardForUnionPorishod\": 0,\r\n        \"additionalMouzaOrMoholla\": \"দিলালপুর\",\r\n        \"additionalVillageOrRoad\": \"বেলতলা\",\r\n        \"homeOrHoldingNo\": \"১০৯৪০\",\r\n        \"region\": \"রাজশাহী\"\r\n    },\r\n    \"signature\": \"https://prportal.nidw.gov.bd/file-1f/1/d/f/6f084425-ce86-4544-a4fe-eac85dec23fe/Signature-6f084425-ce86-4544-a4fe-eac85dec23fe.jpg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=fileobj%2F20221219%2Fus-east-1%2Fs3%2Faws4_request&X-Amz-Date=20221219T083800Z&X-Amz-Expires=120&X-Amz-SignedHeaders=host&X-Amz-Signature=3ecf9553a3d243acc910c84c099fc2d2994312c274ab9a0e1873137bfb7e7175\"\r\n}";
                // //console.log(JSON.parse(nid_data));return;
               // $scope.ansarDetail = JSON.parse(nid_data);return;

                $scope.allLoading = true;
                $scope.errorFound = 0;
                $scope.errorMessage = "";
                $http({
                    method: 'get',
                    url: '{{URL::route('view_ansar_nid_report')}}',
                    params: {ansar_id: id, dob: $scope.dob}
                }).then(function (response) {
                    console.log(response.data);
                    if(response.data.status == 'OK'){
                        $scope.ansarDetail = response.data.success.data;
                    }else{
                        $scope.ansarDetail = {};
                    }

                    $scope.allLoading = false;
                }, function (response) {
                    $scope.ansarDetail = {};
                    $scope.errorFound = 1;
                    $scope.errorMessage = "Please enter a valid NID";
                    $scope.allLoading = false;
                })
            };

            $scope.convertDateObj = function (dateStr) {
                if (dateStr) {
                    return new Date(dateStr);
                }
                return '';
            };
            $scope.convertDate = function (d) {
                return moment(d).format("DD-MMM-YYYY")
            }
            var v = '<div class="text-center" style="margin-top: 20px"><i class="fa fa-spinner fa-pulse"></i></div>'

            $(function () {
                $("#print-report").on('click', function (e) {
                    $("#print-area").remove();
                    //$("#print_table table").removeClass('table table-bordered');
                    //$('body').append('<div id="print-area">' + $("#print_table").html() + '</div>');
                    window.print();
                    //$("#print_table table").addClass('table table-bordered');
                    $("#print-area").remove();
                })
            })

        });
    </script>
    <style></style>
    <div ng-controller="ViewAnsarNIDController">
        <section class="content">
            <div class="box box-solid">
                <div class="overlay" ng-if="allLoading">
                    <span class="fa">
                        <i class="fa fa-refresh fa-spin"></i> <b>Loading...</b>
                    </span>
                </div>
                <div class="box-body"><br>
                    <div class="row displayNone">
                        <div id = "hidden" class="col-md-6 col-centered ">
                            <form  class="row">
                                <div class="col-md-8 col-sm-12 col-xs-12">

                                    <div class="form-group">
                                        <input type="text" name="name" id="name" ng-model="ansar_id" class="form-control"
                                               placeholder="Enter NID">
                                    </div>
                                    <div class="form-group">
                                        {{--                                        <input type="text" name="dob" id="dob_date" class="form-control" ng-model="dob" placeholder="Enter DOB">--}}
                                        <input type="text" name="dob" date-picker="" date-format="yy-mm-dd" id="dob" class="form-control"
                                               placeholder="Enter DOB" ng-model="dob">
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary" ng-click="loadAnsarDetail(ansar_id)">Generate Data
                                        </button> </div>

                                </div>
                            </form>

                        </div>
                        <div class="col-sm-6 col-sm-offset-6">
                            <div class="form-control" style="padding: 0;border:none;">
                                <button id="print-report" class="btn btn-default pull-right" style="margin-right:5px;"><i
                                            class="fa fa-print"></i>&nbsp;Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="print_table" style="overflow: hidden;">

                <div class="col-md-3 personalTab" ng-if="ansarDetail.photo" style="border-radius: 24px;background-color: #f7e4f1;background-image: url('/dist/img/user.png');background-repeat: no-repeat;background-size: 152px;background-position-x: 100%;background-position-y: 104%;background-blend-mode: soft-light;">

                    <div class="box-body">
                        <div class="row">
                            <div id="widget_box" style="margin-top:15px;">
                                <img src="/image?file=[[ansarDetail.photo]]"style="display: block;width: 125px;margin-left: auto;margin-right: auto;" />
                                <h3 style="text-align: center;"><b>[[ansarDetail.nameEn]]</b></h3>
                                <h2 style="text-align: center;font-size: large;"><b>[[ansarDetail.name]]</b></h2>

                                <div class="col-md-12">
                                    <h4><b>Father Name</b></h4>
                                    <h4>[[ansarDetail.father]]</h4>

                                    <h4><b>Mother Name</b></h4>
                                    <h4>[[ansarDetail.mother]]</h4>

                                    <h4><b>Spouse</b></h4>
                                    <h4 ng-if="[[ansarDetail.spouse]] == ''">N/A</h4>
                                    <h4 ng-if="[[ansarDetail.spouse]] != ''">[[ansarDetail.spouse]]</h4>

                                    {{-- <h4><b>Gender</b></h4>
                                    <h4>[[ansarDetail.gender]]</h4> --}}

                                    <h4><b>Occupation</b></h4>
                                    <h4>[[ansarDetail.occupation]]</h4>

                                    <h4><b>Blood Group</b></h4>
                                    <h4>[[ansarDetail.bloodGroup]]</h4>

                                    <h4><b>Date of Birth</b></h4>
                                    <h4>[[ansarDetail.dateOfBirth]]</h4>
                                </div>



                            </div>

                        </div>

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="box permanentAddress" ng-if="ansarDetail.permanentAddress.division" style="border-radius: 24px;background-color: #b6cfbc;background-image: url('/dist/img/permanentAddress.png');background-repeat: no-repeat;background-size: 152px;background-position-x: 100%;background-position-y: 120%;background-blend-mode: soft-light;">
                        <div class="box-body">
                            <div class="row">
                                <h3 style="text-align: center;"><b>Permanent Address</b></h3>
                                <h5 style="text-align: center;"><b>
                                        [[ansarDetail.permanentAddress.villageOrRoad]][[ansarDetail.permanentAddress.additionalVillageOrRoad]],
                                        [[ansarDetail.permanentAddress.postOffice]],
                                        [[ansarDetail.permanentAddress.unionOrWard]],
                                        [[ansarDetail.permanentAddress.upozila]],
                                        [[ansarDetail.permanentAddress.district]],
                                        [[ansarDetail.permanentAddress.division]]</b></h5>

                                <div class="col-md-10">
                                    <table style="width: -webkit-fill-available;">
                                        <thead style="font-size: 15px" class="font_change">
                                        <tr>
                                            <th>Division</th>
                                            <th>Post Office</th>
                                            <th>RMO</th>
                                        </tr>
                                        <tr>
                                            <td class="font_change">[[ansarDetail.permanentAddress.division]]</td>
                                            <td class="font_change">[[ansarDetail.permanentAddress.postOffice]] </td>
                                            <td class="font_change">[[ansarDetail.permanentAddress.rmo]] </td>
                                        </tr>
                                        <tr>
                                            <th>District</th>
                                            <th>Postal Code</th>
                                            <th>Region</th>
                                        </tr>
                                        <tr>
                                            <td class="font_change">[[ansarDetail.permanentAddress.district]]</td>
                                            <td class="font_change">[[ansarDetail.permanentAddress.postalCode]] </td>
                                            <td class="font_change">[[ansarDetail.permanentAddress.region]] </td>
                                        </tr>
                                        <tr>
                                            <th>Union / Ward</th>
                                            <th>Village / Road</th>
                                        </tr>
                                        <tr>
                                            <td class="font_change">[[ansarDetail.permanentAddress.unionOrWard]]</td>
                                            <td class="font_change">[[ansarDetail.permanentAddress.villageOrRoad]][[ansarDetail.permanentAddress.additionalVillageOrRoad]]</td>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="box presentAddress" ng-if="ansarDetail.permanentAddress.division" style="border-radius: 24px;background-color: #B8DDDB;height: 50%;background-image: url('/dist/img/presentAddress.png');background-repeat: no-repeat;background-size: 152px;background-position-x: 100%;background-position-y: 104%;background-blend-mode: soft-light;">
                        <div class="box-body">
                            {{-- <span style=""><img src="https://localhost/ansarErpLive/dist/img/user.png" style="opacity: 10%;width: 55%;position: absolute;margin-left: 43%;margin-top: 151%;"></span> --}}
                            <div class="row">
                                <h3 style="text-align: center;"><b>Present Address</b></h3>
                                <h5 style="text-align: center;"><b>
                                        [[ansarDetail.permanentAddress.villageOrRoad]][[ansarDetail.presentAddress.additionalVillageOrRoad]],
                                        [[ansarDetail.presentAddress.postOffice]],
                                        [[ansarDetail.presentAddress.unionOrWard]],
                                        [[ansarDetail.presentAddress.upozila]],
                                        [[ansarDetail.presentAddress.district]],
                                        [[ansarDetail.presentAddress.division]]</b></h5>

                                <div class="col-md-10">
                                    <table style="width: -webkit-fill-available;">
                                        <thead style="font-size: 15px" class="font_change">
                                        <tr>
                                            <th>Division</th>
                                            <th>Post Office</th>
                                            <th>RMO</th>
                                        </tr>
                                        <tr>
                                            <td class="font_change">[[ansarDetail.presentAddress.division]]</td>
                                            <td class="font_change">[[ansarDetail.presentAddress.postOffice]] </td>
                                            <td class="font_change">[[ansarDetail.presentAddress.rmo]] </td>
                                        </tr>
                                        <tr>
                                            <th>District</th>
                                            <th>Postal Code</th>
                                            <th>Region</th>
                                        </tr>
                                        <tr>
                                            <td class="font_change">[[ansarDetail.presentAddress.district]]</td>
                                            <td class="font_change">[[ansarDetail.presentAddress.postalCode]] </td>
                                            <td class="font_change">[[ansarDetail.presentAddress.region]] </td>
                                        </tr>
                                        <tr>
                                            <th>Union / Ward</th>
                                            <th>Village / Road</th>
                                        </tr>
                                        <tr>
                                            <td class="font_change">[[ansarDetail.presentAddress.unionOrWard]]</td>
                                            <td class="font_change">[[ansarDetail.permanentAddress.villageOrRoad]][[ansarDetail.presentAddress.additionalVillageOrRoad]]</td>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="box nid" ng-if="ansarDetail.permanentAddress.division" style="border-radius: 24px;background-color: #fad8be;height: 50%;background-image: url('/dist/img/idCard.png');background-repeat: no-repeat;background-size: 120px;background-position-x: 100%;background-position-y: 40%;background-blend-mode: soft-light;">
                        <div class="box-body">
                            <div class="row">
                                <h3 ></h3>
                                <div class="col-md-6">
                                    <table style="width: -webkit-fill-available;">
                                        <thead style="font-size: 15px" >

                                        <tr><th><img src="/dist/img/nidOld.jpg" style="width: 120px;"></th>
                                            <th><img src="/dist/img/smartCard.jpeg" style="width: 120px;"></th>
                                        </tr>
                                        <tr><td><b class="font_change">Old NID Number</b></td>
                                            <td><b class="font_change">New NID Number</b></td>
                                        </tr>
                                        <tr><td><b class="font_change" ng-if="(ansar_id.length !== 10)" style="color: blue;" id="nidColor">[[ansar_id]]</b></td>
                                            <td><b class="font_change" style="color: blue;" id="nidColor">[[ansarDetail.nationalId]]</b></td>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <style> @media print {
                body > * {
                    display: block;
                    print-color-adjust: exact;
                    -webkit-print-color-adjust: exact;
                    column-gap: 40px!important;
                }

                .col-md-3 {
                    width: 25%;
                }

                .col-md-10 {
                    width: 80%;
                }
                .col-md-6 {
                    width: 70%;
                }

                .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9 {
                    float: left;
                }

                .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
                    position: relative;
                    min-height: 1px;
                    padding-right: 15px;
                    padding-left: 15px;
                }

                div {
                    display: block;

                }

                body {
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                    font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
                    font-weight: 400;
                    overflow-x: hidden;
                    overflow-y: auto;
                }

                body {

                    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                    font-size: 14px;
                    line-height: 1.42857143;
                    background-color: #fff;
                }

                table {
                    /*border-collapse: hidden!important;*/
                    /*border:hidden!important; */
                    /*border-style: none!important; */
                    /*width: -webkit-fill-available!important;*/
                }

                tr, td, th {
                    border: hidden!important;
                    /*border:hidden!important;*/
                    /*width: -webkit-fill-available!important;*/
                }

                .font_change{
                    font-size: 18px!important;
                }


                #print_table{
                    size: 100mm 200mm landscape!important;
                    font-size: 15px!important;
                    display: block!important;
                    /* background-color: #0b736e!important;  */
                    print-color-adjust: exact!important;
                    -webkit-print-color-adjust: exact!important;

                }
                .personalTab{
                    display: block!important;
                    width:35%!important;
                    height: 850px!important;
                    background-color: #f7e4f1!important;
                    -webkit-print-color-adjust: exact!important;
                    background-image: url('/dist/img/user.png')!important;
                    background-repeat: no-repeat!important;
                    background-size: 150px!important;
                    background-position-x: 102%!important;
                    background-position-y: 99.5%!important;
                    background-blend-mode: soft-light!important;

                }
                .permanentAddress{
                    display: block!important;
                    font-size: 50px!important;
                    width:120%!important;
                    height: 300px!important;
                    background-color: #b6cfbc!important;
                    background-image: url('/dist/img/permanentAddress.png')!important;
                    background-repeat: no-repeat!important;
                    background-size: 110px!important;
                    background-position-x: 100%!important;
                    background-position-y: 105%!important;
                    background-blend-mode: soft-light!important;

                }
                .presentAddress{
                    display: block!important;
                    font-size: 20px!important;
                    width:120%!important;
                    height: 300px!important;
                    background-color: #B8DDDB!important;
                    background-image: url('/dist/img/presentAddress.png')!important;
                    background-repeat: no-repeat!important;
                    background-size: 110px!important;
                    background-position-x: 100%!important;
                    background-position-y: 98%!important;
                    background-blend-mode: soft-light!important;

                }
                .nid{
                    color: blue!important;
                    border-radius: 24px!important;
                    background-color: #fad8be!important;
                    height: 208px!important;
                    width:120%!important;
                    background-image: url('/dist/img/idCard.png')!important;
                    background-repeat: no-repeat!important;
                    background-size: 100px!important;
                    background-position-x: 100%!important;
                    background-position-y: 90%!important;
                    background-blend-mode: soft-light!important;
                }

                #hidden{
                    visibility: hidden!important;
                }
                .displayNone{
                    display: none!important;
                }
                #nidColor{
                    color: blue!important;
                }
            }
        </style>
    </div>
@endsection