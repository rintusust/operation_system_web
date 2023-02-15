@extends('template.master')
@section('title','Dashboard')
{{--@section('small_title','Control panel')--}}
@section('content')
    <!-- Main content -->
    <style>
        ul.warning {
            list-style: lower-alpha;
            font-size: 18px;
        }

        ul.warning > li:not(:last-child) {
            margin-bottom: 10px;

        }
    </style>
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <a href="{{URL::to('operation')}}" class="small-box-footer">
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3 style="color: #ffffff">Operation</h3>

                            <p style="color: #ffffff">Ansar Operation Management</p>
                        </div>
                        <div class="icon" style="color: #ffffff">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="small-box-footer" style="height: 15px"></div>
                    </div>
                </div>
            </a>

            <!-- ./col -->
        </div>

    <!-- /.row -->
    </section>
@endsection