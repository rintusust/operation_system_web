@extends('template.master')
@section('title','Edit Entry')
@section('breadcrumb')
    {!! Breadcrumbs::render('entry.list.edit') !!}
@endsection
@section('content')
    <section class="content">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        @include('operation::ansar_vdp_info.form',['url'=>URL::route('operation.info.update',['id'=>$id]),'id'=>$id])
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection