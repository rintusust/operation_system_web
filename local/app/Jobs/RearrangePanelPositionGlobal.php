<?php
namespace App\Jobs;

use App\Helper\Facades\GlobalParameterFacades;
use App\Jobs\Job;
use App\modules\HRM\Models\OfferSMSStatus;
use App\modules\HRM\Models\PanelModel;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RearrangePanelPositionGlobal extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		//Log::info("Global RAR....");
        if (!DB::connection('hrm')->getDatabaseName()) {
            //Log::info("SERVER RECONNECTING....");
            DB::reconnect('hrm');
        }
        //Log::info("CONNECTION DATABASE : " . DB::connection('hrm')->getDatabaseName());
        DB::connection('hrm')->beginTransaction();

        try {	
            $go_offer_count = +GlobalParameterFacades::getValue('ge_offer_count');
            $data = DB::table('tbl_ansar_parsonal_info')
                ->leftJoin('tbl_offer_status', 'tbl_offer_status.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_panel_info', 'tbl_ansar_parsonal_info.ansar_id', '=', 'tbl_panel_info.ansar_id')
                ->join('tbl_ansar_status_info', 'tbl_ansar_status_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->join('tbl_designations', 'tbl_designations.id', '=', 'tbl_ansar_parsonal_info.designation_id')
                ->leftJoin('tbl_sms_offer_info', 'tbl_sms_offer_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->leftJoin('tbl_sms_receive_info', 'tbl_sms_receive_info.ansar_id', '=', 'tbl_ansar_parsonal_info.ansar_id')
                ->where('tbl_ansar_status_info.block_list_status', 0)
                ->where('tbl_ansar_status_info.black_list_status', 0)
                ->whereRaw('tbl_ansar_parsonal_info.mobile_no_self REGEXP "^(/+88)?01[0-9]{9}$"')
                ->select('tbl_panel_info.ansar_id', 'panel_date', 'tbl_panel_info.come_from', 'tbl_panel_info.id',
                    'locked', 'sex', 'division_id', 'tbl_designations.code', 'tbl_panel_info.go_panel_position',
                    'tbl_sms_offer_info.district_id', 'tbl_sms_receive_info.offered_district',
                    DB::raw('REPLACE(REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(offer_type,\',\',LENGTH(offer_type)
                    -LENGTH(REPLACE(offer_type,\',\',\'\'))+1),\',\',-1),"DG","GB"),"CG","GB") as last_offer_region'), 'offer_type')
                ->get();

            $values = collect($data)->groupBy(function ($item) {
                return $item->code . "-" . $item->sex;
            })->toArray();

            foreach ($values as $value) {
                $value = collect($value)->sort(function ($a, $b) {
                    $id1 = +isset($a->id) ? $a->id : 0;
                    $id2 = +isset($b->id) ? $b->id : 0;
                    $d1 = isset($a->panel_date) ? Carbon::parse($a->panel_date) : Carbon::now();
                    $d2 = isset($b->panel_date) ? Carbon::parse($b->panel_date) : Carbon::now();
                    if ($d1->gt($d2)) {
                        return 1;
                    } else if ($d1->eq($d2) && $id1 > $id2) {
                        return 1;
                    } else {
                        return -1;
                    }
                })->values()->toArray();
                $i = 1;
                $query = "UPDATE tbl_panel_info SET go_panel_position = (CASE ansar_id ";

                foreach ($value as $p) {
                    $p = (array)$p;
                    $locked_region = "";

                    if ($p['locked'] && $p['last_offer_region']) {
                        $locked_region = " (" . $p['last_offer_region'] . ") ";
                    }

                    if ($p['offer_type'] == null || $p['offer_type'] == "") {
//                        offer type is null when first panel entry or empty string when last offer is ongoing.

                        if ($p['locked'] == 0) {
                            $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                           ////Log::info('UPDATE_GLOBAL_ANSAR: FIRST_PANEL_ENTRY ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current global position:' . $p['go_panel_position'] . ' future g position:' . $i);
                            $i++;
                        } elseif (in_array($p['district_id'], Config::get('app.offer'))) {
                            $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                           ////Log::info('UPDATE_GLOBAL_ANSAR:LAST_GB_OFFER ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current global position:' . $p['go_panel_position'] . ' future g position:' . $i);
                            $i++;
                        } elseif (in_array($p['offered_district'], Config::get('app.offer'))) {
                            $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                           ////Log::info('UPDATE_GLOBAL_ANSAR:LAST_GB_OFFER (ACCEPTED) ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current global position:' . $p['go_panel_position'] . ' future g position:' . $i);
                            $i++;
                        } else {
                            $query .= "WHEN " . $p['ansar_id'] . " THEN NULL ";
                           ////Log::info('UPDATE_GLOBAL_ANSAR:LAST_RE_OFFER ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current global position:' . $p['go_panel_position'] . ' future g position:null');
                        }

                    } elseif ((substr_count($p['offer_type'], 'GB') + substr_count($p['offer_type'], 'DG') + substr_count($p['offer_type'], 'CG')) < $go_offer_count) {
//                       global offer quota is not filled up yet. so, locked unlocked doesn't matter to update global position
                        $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                       ////Log::info('UPDATE_GLOBAL_ANSAR ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . $locked_region . ', current global position:' . $p['go_panel_position'] . ' future g position:' . $i);
                        $i++;
                    } else {
                        if ($p['last_offer_region'] == 'GB' && $p['locked'] == 1) {
                            $query .= "WHEN " . $p['ansar_id'] . " THEN $i ";
                           ////Log::info('UPDATE_GLOBAL_ANSAR ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . $locked_region . ', current global position:' . $p['go_panel_position'] . ' future g position:' . $i);
                            $i++;
                        } else {
//                            all global offer filled up. so, set position null
                            $query .= "WHEN " . $p['ansar_id'] . " THEN NULL ";
                           ////Log::info('UPDATE_GLOBAL_ANSAR ansar id:' . $p['ansar_id'] . ' locked-' . $p['locked'] . ', current global position:' . $p['go_panel_position'] . ' future g position:null');
                        }
                    }
                }
                $query .= "ELSE go_panel_position END) WHERE ansar_id IN (" . implode(",", array_column($value, 'ansar_id')) . ")";
                DB::statement($query);

            }
            DB::connection('hrm')->commit();
        } catch (\Exception $e) {
            //Log::info("global panel rearr:" . $e->getMessage());
            DB::connection('hrm')->rollback();
        }
        $this->delete();
    }
}
