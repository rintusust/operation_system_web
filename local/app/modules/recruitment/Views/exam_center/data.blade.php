<?php $i=1 ?>
<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>SL. No</th>
            <th>Job Circular Name</th>
            <th>Selection Date</th>
            <th>Selection Place</th>
            <th>Written Date</th>
            <th>Viva Date</th>
            <th>Written Viva Place</th>
            <th>Units</th>
            <th style="width: 100px;">Action</th>
        </tr>
        @forelse($data as $d)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$d->circular->circular_name}}</td>
                <td>{{$d->selection_date.' '.$d->selection_time}}</td>
                <td>{{$d->selection_place}}</td>
                <td>{{$d->written_date.' '.$d->written_time}}</td>
                <td>{{$d->viva_date.' '.$d->viva_time}}</td>
                <td>{{$d->written_viva_place}}</td>
                <td>
                @foreach($d->units()->pluck('unit_name_bng') as $u)
                    {{$u}},
                    @endforeach
                </td>
                <td>
                    <a class="btn btn-primary btn-xs" href="{{URL::route('recruitment.exam-center.edit',['id'=>$d->id])}}">
                        <i class="fa fa-edit"></i>
                    </a>
                    {!! Form::open(['route'=>['recruitment.exam-center.destroy',$d->id],'method'=>'delete','style'=>'display:inline-block']) !!}
                        <button class="btn btn-danger btn-xs">
                            <i class="fa fa-trash"></i>
                        </button>
                    {!! Form::close() !!}
                </td>
            </tr>
        @empty
            <tr>
                <td class="bg-warning" colspan="9">No Exam Center Available</td>
            </tr>
        @endforelse
    </table>
</div>