@extends('template.master')
@section('title','Edit User')
@section('breadcrumb')
    {!! Breadcrumbs::render('edit_user',$id) !!}
@endsection
@section('content')
    <script>
        $(document).ready(function () {
            $("#user-name-form").ajaxForm({
                beforeSubmit: function (data) {
                    $("#user-name-form .submit").slideUp(100)
                    $("#user-name-form .submitting").slideDown(100)
                },
                success: function (response) {
                    $("#user-name-form .submit").slideDown(100)
                    $("#user-name-form .submitting").slideUp(100)
                    console.log(response)
                    if (response.validation) {
                        $("#user-name-form p").css('display', 'block')
                    }
                    else if (response.submit) {
                        $('body').notifyDialog({type: 'success', message: 'User name change successfully'}).showDialog()
                        $("#user-name-form p").css('display', 'none')
                    }
                    else {
                        $('body').notifyDialog({
                            type: 'error',
                            message: 'An error occur.Please try again later'
                        }).showDialog()
                        $("#user-name-form p").css('display', 'none')
                    }
                },
                error: function (response, statusText) {
                    $("#user-name-form .submit").slideDown(100)
                    $("#user-name-form .submitting").slideUp(100)
                    $('body').notifyDialog({
                        type: 'error',
                        message: 'An server error occur.ERROR CODE:' + statusText
                    }).showDialog()
                }

            })
            $("#user-password-form").ajaxForm({
                beforeSubmit: function (data) {
                    $("#user-password-form .submit").slideUp(100)
                    $("#user-password-form .submitting").slideDown(100)
                },
                success: function (response) {
                    $("#user-password-form .submit").slideDown(100)
                    $("#user-password-form .submitting").slideUp(100)
                    console.log(response)
                    if (response.validation) {
                        $("#user-password-form p").css('display', 'none')
                        if (response.error.password != undefined) {
                            $("#user-password-form p:eq(0) span").text(response.error.password)
                            $("#user-password-form p:eq(0)").css('display', 'block')
                        }
                        if (response.error.c_password != undefined) {
                            $("#user-password-form p:eq(1) span").text(response.error.c_password)
                            $("#user-password-form p:eq(1)").css('display', 'block')
                        }
                    }
                    else if (response.submit) {
                        $('body').notifyDialog({
                            type: 'success',
                            message: 'User password change successfully'
                        }).showDialog()
                        $("#user-password-form p").css('display', 'none')
                    }
                    else {
                        $('body').notifyDialog({
                            type: 'error',
                            message: 'An error occur.Please try again later'
                        }).showDialog()
                        $("#user-password-form p").css('display', 'none')
                    }
                },
                error: function (response, statusText) {
                    $("#user-password-form .submit").slideDown(100)
                    $("#user-password-form .submitting").slideUp(100)
                    $('body').notifyDialog({
                        type: 'error',
                        message: 'An server error occur.ERROR CODE:' + statusText
                    }).showDialog()
                }

            })
            $("#user-unit-form").ajaxForm({
                beforeSubmit: function (data) {
                    $("#user-unit-form .submit").slideUp(100)
                    $("#user-unit-form .submitting").slideDown(100)
                },
                success: function (response) {
                    $("#user-unit-form .submit").slideDown(100)
                    $("#user-unit-form .submitting").slideUp(100)
                    console.log(response)
                    if (response.validation) {
                        $("#user-unit-form p").css('display', 'none')
                        $("#user-unit-form p:eq(0) span").text(response.error.password)
                        $("#user-unit-form p:eq(0)").css('display', 'block')
                    }
                    else if (response.submit) {
                        $('body').notifyDialog({
                            type: 'success',
                            message: 'User rec unit change successfully'
                        }).showDialog()
                        $("#user-unit-form p").css('display', 'none')
                    }
                    else {
                        $('body').notifyDialog({
                            type: 'error',
                            message: 'An error occur.Please try again later'
                        }).showDialog()
                        $("#user-unit-form p").css('display', 'none')
                    }
                },
                error: function (response, statusText) {
                    $("#user-unit-form .submit").slideDown(100)
                    $("#user-unit-form .submitting").slideUp(100)
                    $('body').notifyDialog({
                        type: 'error',
                        message: 'An server error occur.ERROR CODE:' + statusText
                    }).showDialog()
                }

            })
            $("#user-unit-range-form").ajaxForm({
                beforeSubmit: function (data) {
                    $("#user-unit-range-form .submit").slideUp(100)
                    $("#user-unit-range-form .submitting").slideDown(100)
                },
                success: function (response) {
                    $("#user-unit-range-form .submit").slideDown(100)
                    $("#user-unit-range-form .submitting").slideUp(100)
                    console.log(response)
                    if (response.validation) {
                        $("#user-unit-range-form p").css('display', 'none')
                        $("#user-unit-range-form p:eq(0)").css('display', 'block')
                    }
                    else if (response.submit) {
                        $('body').notifyDialog({
                            type: 'success',
                            message: 'User range and unit change successfully'
                        }).showDialog()
                        $("#user-unit-range-form p").css('display', 'none')
                    }
                    else {
                        $('body').notifyDialog({
                            type: 'error',
                            message: 'An error occur.Please try again later'
                        }).showDialog()
                        $("#user-unit-range-form p").css('display', 'none')
                    }
                },
                error: function (response, statusText) {
                    $("#user-unit-range-form .submit").slideDown(100)
                    $("#user-unit-range-form .submitting").slideUp(100)
                    $('body').notifyDialog({
                        type: 'error',
                        message: 'An server error occur.ERROR CODE:' + statusText
                    }).showDialog()
                }

            })
            $(".range").on('change',function(){
                var v = $(this).val();
                console.log(v)
                if(this.checked){
                    $("*[data-division-id='"+v+"']").css('display','block')
                    $("*[data-division-id='"+v+"']>input[type='checkbox']").prop('checked',true)
                } else {
                    $("*[data-division-id='"+v+"']>input[type='checkbox']").prop('checked',false)
                    $("*[data-division-id='"+v+"']").css('display','none')
                }
            })
        })
    </script>
    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-4">
                        <h4 style="border-bottom: 1px solid #ababab">Change user name</h4>
                        <form id="user-name-form" action="{{action('UserController@changeUserName')}}" method="post">
                            <input type="hidden" name="user_id" value="{{$id}}">
                            <div class="form-group has-feedback">
                                <input type="text" name="user_name" value="" class="form-control"
                                       placeholder="user name"/>
                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                <p style="display: none" class="alert-danger-custom"><i class="fa fa-warning"></i><span>This user name already exists</span>
                                </p>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <div class="submit">
                                        Change
                                    </div>
                                    <div class="submitting">
                                        <i class="fa fa-spinner fa-spin"></i><span
                                                class="blink-animation">Changing...</span>
                                    </div>
                                </button>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-4" style="margin-bottom: 20px">
                        <h4 style="border-bottom: 1px solid #ababab">Change user password</h4>
                        <form id="user-password-form" action="{{action('UserController@changeUserPassword')}}"
                              method="post">
                            <input type="hidden" name="user_id" value="{{$id}}">
                            <div class="form-group has-feedback">
                                <input type="password" name="password" value="" class="form-control"
                                       placeholder="Enter password"/>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                <p style="display: none" class="alert-danger-custom"><i class="fa fa-warning"></i><span>Password mis-match</span>
                                </p>
                            </div>
                            <div class="form-group has-feedback">
                                <input type="password" name="c_password" value="" class="form-control"
                                       placeholder="Type password again"/>
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                <p style="display: none" class="alert-danger-custom"><i class="fa fa-warning"></i><span>Password mis-match</span>
                                </p>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <div class="submit">
                                        Change
                                    </div>
                                    <div class="submitting">
                                        <i class="fa fa-spinner fa-spin"></i><span
                                                class="blink-animation">Changing...</span>
                                    </div>
                                </button>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>

                </div>
                @if($user->type==22)
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-4">
                            <h4 style="border-bottom: 1px solid #ababab">Change user District</h4>
                            <form id="user-unit-form" action="{{action('UserController@changeUserDistrict')}}"
                                  method="post">
                                <input type="hidden" name="user_id" value="{{$id}}">
                                <div class="form-group has-feedback">
                                    <label for="">Change recruitment unit</label>
                                    {!! Form::select('rec_district_id',$units,$user->rec_district_id,['class'=>'form-control']) !!}
                                    <p style="display: none" class="alert-danger-custom"><i
                                                class="fa fa-warning"></i><span>This unit already assign</span></p>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <div class="submit">
                                            Change
                                        </div>
                                        <div class="submitting">
                                            <i class="fa fa-spinner fa-spin"></i><span class="blink-animation">Changing...</span>
                                        </div>
                                    </button>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif($user->type==111)
                    <?php
                    $ranges = $user->divisions?$user->divisions->pluck('id')->toArray():[];
                    $units = $user->districts?$user->districts->pluck('id')->toArray():[];
                    $cat = $user->recruitmentCatagories?$user->recruitmentCatagories->pluck('id')->toArray():[];
                    ?>
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-4">
                            <h4 style="border-bottom: 1px solid #ababab">Change user division & district</h4>
                            <form id="user-unit-range-form" action="{{action('UserController@changeUserDistrictDivision')}}"
                                  method="post">
                                <input type="hidden" name="user_id" value="{{$id}}">
                                <div class="form-group has-feedback">
                                    <label for="">Select Range</label>
                                    <div style="max-height:150px;overflow: auto">
                                        @foreach($divisions as $key=>$value)
                                            <label style="display:block">
                                                <input type="checkbox" class="range" name="divisions[]"
                                                       value="{{$key}}" {{in_array($key,$ranges)?"checked":""}}>&nbsp;{{$value}}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="">Select District</label>
                                    <div style="max-height:150px;overflow: auto">
                                        @foreach($districts as $d)
                                            <label data-division-id="{{$d->division_id}}" {{!in_array($d->division_id,$ranges)?'style=display:none':'style=display:block'}}>
                                                <input type="checkbox" name="districts[]"
                                                       value="{{$d->id}}" {{in_array($d->id,$units)?"checked":""}}>&nbsp;{{$d->unit_name_bng}}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="">Select Circular Category</label>
                                    <div style="max-height:150px;overflow: auto">
                                        @foreach($categories as $d)
                                            <label style="display: block">
                                                <input type="checkbox" name="categories[]"
                                                       value="{{$d->id}}" {{in_array($d->id,$cat)?"checked":""}}>&nbsp;{{$d->category_name_bng}}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <p style="display: none" class="alert-danger-custom"><i
                                            class="fa fa-warning"></i><span>Please select correct value</span></p>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <div class="submit">
                                            Change
                                        </div>
                                        <div class="submitting">
                                            <i class="fa fa-spinner fa-spin"></i><span class="blink-animation">Changing...</span>
                                        </div>
                                    </button>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>
@stop