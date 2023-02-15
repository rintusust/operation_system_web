@if(count($logs)>0)
    <ul class="timeline">
        @forelse($logs as $date=>$log)
            <li class="time-label">
                            <span class="bg-green">
                                {{$date}}
                            </span>
            </li>
            @foreach($log as $item)
                <li>

                    <!-- timeline icon -->
                    <i class="fa fa-cog bg-blue"></i>

                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> {{$item->time}}</span>

                        <h3 class="timeline-header" style="background: rgba(0, 120, 112, 0.15);"><a
                                    href="#">{{$item->action_type or 'UNDEFINED'}}</a></h3>
                        @if($item->action_type=="TRANSFER")
                            <div class="timeline-body">
                                <?php
                                //echo ("Anik");exit;
                                if($item->getAnsar->designation_id == 1){
                                    echo 'Ansar';
                                }elseif($item->getAnsar->designation_id == 2){
                                    echo 'APC';
                                }else{
                                    echo 'PC';
                                }
                                ?>
                                ({{$item->ansar_id}}) transferred from <b>{{$item->getFromKpi()}}</b> kpi to
                                <b>{{$item->getToKpi()}}</b>
                            </div>
                        @else
                            <div class="timeline-body">
                                <?php
                                //print_r($item);exit;
                                if($item->action_type == 'EDIT USER PERMISSION')
                                {
                                    echo 'User ('.$item->ansar_id. ') Updated';
                                }
                                if($item->action_type == 'EMBODIED' || $item->action_type == 'PANELED' || $item->action_type == 'SEND OFFER' || $item->action_type == 'CANCEL OFFER' || $item->action_type == 'DISEMBODIMENT' || $item->action_type == 'BLOCKED' || $item->action_type == 'BLACKED' || $item->action_type == 'FREEZE' || $item->action_type == 'CANCEL PANEL' || $item->action_type == 'SAVE DRAFT' || $item->action_type == 'VERIFIED' || $item->action_type == 'REJECT' || $item->action_type == 'UNBLOCKED' || $item->action_type == 'UNBLACKED' || $item->action_type == 'TRANSFER' || $item->action_type == 'DIRECT OFFER' || $item->action_type == 'DIRECT PANEl' || $item->action_type == 'DIRECT EMBODIMENT' || $item->action_type == 'DIRECT DISEMBODIMENT' || $item->action_type == 'DIRECT TRANSFER' || $item->action_type == 'DIRECT CANCEL PANEL' || $item->action_type == 'BLOCK USER' || $item->action_type == 'UNBLOCK USER' || $item->action_type == 'CREATE USER' || $item->action_type == 'DISEMBODIMENT DATE CORRECTION')
                                {
                                    if($item->getAnsar){
                                        if($item->getAnsar->designation_id == 1){
                                            echo 'Ansar ('.$item->ansar_id. ') transferred to status ' .$item->to_state;
                                        }elseif($item->getAnsar->designation_id == 2){
                                            echo 'APC ('.$item->ansar_id. ') transferred to status ' .$item->to_state;
                                        }else{
                                            echo 'PC ('.$item->ansar_id. ') transferred to status ' .$item->to_state;
                                        }

                                    }}
                                if($item->action_type == 'ADD KPI' || $item->action_type == 'WITHDRAW KPI' || $item->action_type == 'REDUCE KPI' || $item->action_type == 'EDIT KPI')
                                {
                                    echo 'KPI ('.$item->ansar_id. ') Updated';
                                }

                                if($item->action_type == 'ADD ENTRY' || $item->action_type == 'EDIT ENTRY')
                                {
                                    echo 'Ansar ID ( '.$item->ansar_id. ' ) transferred to status ' .$item->to_state;
                                }

                                if($item->action_type == 'DISEMBODIMENT REASON CORRECTION')
                                {
                                    echo 'Ansar ID ( '.$item->ansar_id. ' ) Disembodiment Reason Correction';
                                }
                                ?>
                                {{-- ({{$item->ansar_id}}) transferred to status {{$item->to_state}} --}}
                            </div>
                        @endif
                    </div>
                </li>
            @endforeach
        @empty

        @endforelse
    </ul>
@else
    <div class="alert alert-warning">
        <i class="fa fa-warning"></i>&nbsp;No Activity Available
    </div>
@endif