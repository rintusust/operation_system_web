@extends('template.master')
@section('title','401 Error')
@section('content')
    <div>
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="error-page">
                <h2 class="headline text-yellow" style="margin-top: -10px"> 401</h2>

                <div class="error-content">
                    <h3><i class="fa fa-warning text-yellow"></i> Oops! Unauthorized error.</h3>

                    <p style="margin-top: 20px">
                        You currently not authorized to view this page
                        Meanwhile, you may <a href="{{URL::to('HRM')}}">return to dashboard</a>
                    </p>

                </div>
                <!-- /.error-content -->
            </div>
            <!-- /.error-page -->
        </section>
        <!-- /.content -->
    </div><!-- /.content-wrapper -->
@stop