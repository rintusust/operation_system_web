<table class="table table-bordered table-striped">
    <caption>
        <div class="row">
            <div class="col-sm-8">Total : {{$data->total()}}</div>
            <div class="col-sm-4">
                <div class="input-group">
                    <input type="text" ng-model="q" class="form-control" placeholder="search by mem. ID">
                    <span class="input-group-addon">
                        <a href="#" onclick="return false" ng-click="loadData(undefined,q)">
                            <i class="fa fa-search"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </caption>
    <tr>
        <th>#</th>
        <th>Memorandum no.</th>
        <th>Memorandum Date</th>
        @if((auth()->user()->type==11) && $type=='EMBODIMENT')
        @else
            @if($type!='FREEZE')
                <th>Unit</th>
            @endif
        @endif
        <th>Action</th>
    </tr>
    <?php $i = (intVal($data->currentPage()-1)*$data->perPage())+1; ?>
    @foreach($data as $mem)
        <tr>
            <td>{{$i++}}</td>
            <td>
                {{$mem->memorandum_id}}
            </td>
            <td>{{$mem->freez_date?($mem->freez_date):'n/a'}}</td>
            @if((auth()->user()->type==11) && $type=='EMBODIMENT')

            @else
                @if($type!='FREEZE')
                    <td>
                        @if(auth()->user()->type!=22)
                            <select class="form-control" ng-model="unit_mem[{{$i-1}}]" name="unit_list">
                                <option value="">--@lang('title.unit')--</option>
                                @foreach($units as $u)
                                    <option value="{{$u->id}}">{{$u->unit_name_bng}}</option>
                                @endforeach
                            </select>
                        @else
                            <div>
                                {{auth()->user()->district?auth()->user()->district->unit_name_eng:''}}

                            </div>
                        @endif
                    </td>
                @endif
            @endif
            <td>
                {!! Form::open(['route'=>'print_letter','target'=>'_blank']) !!}
                {!! Form::hidden('option','memorandumNo') !!}
                {!! Form::hidden('id',$mem->memorandum_id) !!}
                {!! Form::hidden('type',$type) !!}
                @if((auth()->user()->type==11) && $type=='EMBODIMENT')
                    {!! Form::hidden('unit','0') !!}
                @else
                    {!! Form::hidden('unit',auth()->user()->district?auth()->user()->district->id:'[[unit_mem['.($i-1).'] ]]',['id'=>'unit_mem']) !!}
                @endif
                <button class="btn btn-primary">Generate Letter</button>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    @if($i==1)
        <tr>
            <td class="warning" colspan="5">No Memorandum no. available</td>
        </tr>
    @endif
</table>
<div class="pull-right" paginate ref="loadData(url)">
    {!! $data->render() !!}
</div>
{{--
<script>
    $(document).ready(function () {
        $('select[name="unit_list"]').on('change',function (event) {
            var i = this;
            $("#unit_mem").val(this.value);
        })
    })
</script>--}}
