@extends('template.master')
@section('title','View Entry Detail')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry.list.view') !!}
@endsection
@section('content')
    <style>
        .form-control {
            margin-bottom: 10px;
        }
    </style>
    <section class="content">
        <div class="box box-solid" ng-controller="VDPController">
            <div class="box-header">

            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="row">
                            <div class="col-sm-2 col-sm-offset-10">

                                <img src="{!!URL::route('AVURP.info.image',['id'=>$info->id]) !!}" alt=""
                                     class="img-responsive img-thumbnail pull-right" style="margin-bottom: 10px">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-sm-4">ভিডিপি আইডি
                                <span class="pull-right">:</span>
                            </label>
                            <div class="col-sm-8">
                                <div class="form-control">
                                    {{$info->geo_id}}
                                </div>
                            </div>
                        </div>
                        <fieldset>
                            <legend>জিও কোড ভিত্তিক আইডির জন্য তথ্য</legend>
                            <div class="form-group">
                                <label class="control-label col-sm-4">বিভাগ
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        {{$info->division->division_name_bng}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">জেলা
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        {{$info->unit->unit_name_bng}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">উপজেলা
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        {{$info->thana->thana_name_bng}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">ইউনিয়ন/ওয়ার্ড
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        {{$info->union_word_text}}
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>ব্যক্তিগত ও পারিবারিক তথ্য</legend>

                            <div class="form-group">
                                <label class="control-label col-sm-4">নাম
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        {{$info->ansar_name_bng}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">বর্তমান পদবী
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        {{$info->designationData->designation_name_bng}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">পিতার নাম
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        {{$info->father_name_bng}}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">রক্তের গ্রুপ
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        {{$info->bloodGroup?$info->bloodGroup->blood_group_name_bng:'--'}}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">জন্ম তারিখ
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        {{$info->date_of_birth}}
                                    </div>
                                </div>
                            </div>


                        </fieldset>
                        <fieldset>
                            <legend>যোগাযোগ</legend>
                            <div class="form-group">
                                <label class="control-label col-sm-4">মোবাইল নম্বর(নিজ)
                                    <span class="pull-right">:</span>
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-control">
                                        {{$info->mobile_no_self}}
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                    </div>
                </div>
                <div style="display: flex;justify-content: center;align-items: center">
                    @if((Auth::user()->type==22||Auth::user()->type==44||Auth::user()->type==11)&&UserPermission::userPermissionExists('operation.info.verify')&&$info->status=='new')
                        {!! Form::open(['route'=>['operation.info.verify',$info->id],'style'=>'align-self:center;margin-left:10px']) !!}
                        <button class="btn btn-primary">
                            <i class="fa fa-check"></i>&nbsp;Verify
                        </button>
                        {!! Form::close() !!}
                    @endif


                </div>
            </div>
        </div>
    </section>

@endsection