<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 4/3/2018
 * Time: 1:31 PM
 */

namespace App\modules\operation\Repositories\VDPInfo;


use App\Http\Requests\Request;
use App\modules\operation\Models\UserActionLog;
use App\modules\operation\Models\VDPAnsarInfo;
use App\modules\operation\Requests\OperationVDPInfoRequest;
use App\modules\operation\Models\District;
use App\modules\operation\Models\Division;
use App\modules\operation\Models\Thana;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class OperationVDPInfoRepository implements OperationVDPInfoInterface
{
    public $info;

    /**
     * VDPInfoRepository constructor.
     * @param VDPAnsarInfo $info
     */
    public function __construct(VDPAnsarInfo $info)
    {
        $this->info = $info;
    }


    /**
     * @param OperationVDPInfoRequest $request
     * @param string $user_id
     * @return mixed
     */
    public function create($data, $user_id = '')
    {
        DB::connection('operation')->beginTransaction();
        try {
            if(!is_array($data)){
                $data = $data->all();
            }
            $count = $this->getLastPartOfGeoID($data);

            $division_code = sprintf("%02d", Division::find($data['division_id'])->division_code);
            $unit_code = sprintf("%02d", District::find($data['unit_id'])->unit_code);
            $thana_code = sprintf("%02d", Thana::find($data['thana_id'])->thana_code);
            $geo_id = $division_code . $unit_code . $thana_code .$count;

            $data['geo_id'] = $geo_id;


            $info = $this->info->create($data);
            //$info->status()->create([]);



            $user = auth()->user();
            $now = Carbon::now()->format('d-M-Y h:i:s A');
            UserActionLog::create([
                'action_user_id' => $user->id,
                'action_description' => "VDP ID({$geo_id}) has been created by {$user->user_name} at {$now}",
                'action_type' => 'Entry',
                'action_id' => $info->id,
            ]);
            DB::connection('operation')->commit();
        } catch (\Exception $e) {
            DB::connection('operation')->rollback();
            Log::info($e->getTraceAsString());
            return ['data' => ['message' => $e->getMessage()], 'status' => false];
        }
        return ['data' => ['message' => "data created successfully"], 'status' => true];
    }

    /**
     * @param $id
     * @param string $user_id
     * @return mixed
     */
    public function getInfo($id, $user_id = '')
    {
        $info = $this->info->with(['division', 'unit', 'thana', 'bloodGroup','designationData'])->where('id', $id)->userQuery($user_id);
        return $info->first();
    }

    /**
     * @param array $param
     * @param int $paginate
     * @param string $user_id
     * @param bool $is_api
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getInfos($param = [], $paginate = 30, $user_id = '', $is_api = false)
    {
//        return $param;
        $range = isset($param['range']) && $param['range'] ? $param['range'] : 'all';
        $unit = isset($param['unit']) && $param['unit'] ? $param['unit'] : 'all';
        $thana = isset($param['thana']) && $param['thana'] ? $param['thana'] : 'all';

        if ($is_api) {
            $vdp_infos = $this->info;
            $vdp_infos = $vdp_infos->with(['division' => function ($q) {
                $q->select('division_name_bng as division_name', 'id');
            }, 'unit' => function ($q) {
                $q->select('unit_name_bng as unit_name', 'id');
            }, 'thana' => function ($q) {
                $q->select('thana_name_bng as thana_name', 'id');
            }])->select('ansar_name_bng', 'geo_id', 'id', 'designation', 'division_id', 'unit_id', 'thana_id','union_word_text', 'status');
        } else {
            $vdp_infos = $this->info->with(['division', 'unit', 'thana']);
        }
        if ($range != 'all') {
            $vdp_infos = $vdp_infos->where('division_id', $range);
        }
        if ($unit != 'all') {
            $vdp_infos = $vdp_infos->where('unit_id', $unit);
        }
        if ($thana != 'all') {
            $vdp_infos = $vdp_infos->where('thana_id', $thana);
        }

        if (isset($param['vdpdesignation'])) $vdp_infos = $vdp_infos->where('designation',$param['vdpdesignation']);
        $vdp_infos = $vdp_infos->userQuery($user_id);
        if (isset($param['q'])) $vdp_infos = $vdp_infos->searchQuery($param['q']);
        if ($paginate > 0) {
            return $vdp_infos->paginate($paginate);
        }
        if (!$is_api) return $vdp_infos->get();
        else return ['data' => $vdp_infos->get()];
    }

    /**
     * @param Request $request
     * @param $id
     * @param string $user_id
     * @return mixed
     * @internal param Request $input
     */
    public function update($request, $id, $user_id = '')
    {
        DB::connection('operation')->beginTransaction();
        try {

            $info = $this->info->findOrFail($id);


            $serial = substr($info->geo_id, -6);


            $division_code = sprintf("%02d", Division::find($request->division_id)->division_code);
            $unit_code = sprintf("%02d", District::find($request->unit_id)->unit_code);
            $thana_code = sprintf("%02d", Thana::find($request->thana_id)->thana_code);
            $geo_id = $division_code . $unit_code . $thana_code .$serial;
            #$e_geo_id = substr($info->geo_id, 0, 12);
            if ($geo_id == $info->geo_id) {
                $geo_id = $info->geo_id;
            } else {
                //$count = $this->getLastPartOfGeoID($request);
                //$geo_id .= $count;
            }
            Log::info($request->profile_pic);
            if ($request->hasFile('profile_pic') && !$request->is('operation/api/*')) {
                $file = $request->file('profile_pic');
                $path = storage_path('operation/profile_pic');
                if (!File::exists($path)) File::makeDirectory($path, 777, true);
                $image_name = $geo_id . '.' . $file->clientExtension();
                Image::make($file)->save($path . '/' . $image_name);
            } else if ($request->profile_pic && $request->is('operation/api/*')) {
                $path = storage_path('operation/profile_pic');
                $image = Image::make(base64_decode($request->profile_pic));;
                $extension = 'png';
                $mime = $image->mime();
                if ($mime == 'image/jpeg')
                    $extension = 'jpg';
                elseif ($mime == 'image/png')
                    $extension = 'png';
                elseif ($mime == 'image/gif')
                    $extension = 'gif';
                if (!File::exists($path)) File::makeDirectory($path, 777, true);
                $image_name = $geo_id . '.' . $extension;
                $image->save($path . '/' . $image_name);
            }


            $data = $request->except(['status', 'action_user_id', 'division', 'thana', 'unit', 'designation_data']);
            $data['geo_id'] = $geo_id;
            if (isset($path) && isset($image_name)) $data['profile_pic'] = $image_name;
            else if ($request->hasFile('profile_pic')) $data['profile_pic'] = '';

            $info->update($data);

            $user = auth()->user();
            $now = Carbon::now()->format('d-M-Y h:i:s A');
            UserActionLog::create([
                'action_user_id' => $user->id,
                'action_description' => "VDP ID({$geo_id}) has been updated by {$user->user_name} at {$now}",
                'action_type' => 'Edit',
                'action_id' => $info->id,
            ]);
            DB::connection('operation')->commit();
        } catch (\Exception $e) {
            DB::connection('operation')->rollback();
            if (isset($path) && isset($image_name)) {
                if (File::exists($path . '/' . $image_name)) {
                    File::delete($path . '/' . $image_name);
                }
            }
            Log::info($e->getTraceAsString());
            return ['data' => ['message' => $e->getMessage()], 'status' => false];
        }
        return ['data' => ['message' => "data updated successfully"], 'status' => true];
    }

    /**
     * @param $id
     * @param string $user_id
     * @return mixed
     */
    public function getInfoForEdit($id, $user_id = '')
    {
        $info = $this->info->with(["division", "unit", "thana", "designationData"])->where('id', $id)->userQuery($user_id);
        return $info->first();
    }

    /**
     * @param id $
     * @return mixed
     */
    public function verifyVDP($id)
    {
        $type = auth()->user()->usertype->type_code;
        if ($type == 44 || $type == 22 || $type == 66 || $type == 11) {
            DB::connection('operation')->beginTransaction();
            try {
                $info = $this->info->findOrFail($id);
                if ($info->status != 'new') throw new \Exception("Member is already {
                $info->status}");
                $info->update(['status' => 'verified']);
                $user = auth()->user();
                $now = Carbon::now()->format('d-M-Y h:i:s A');
                UserActionLog::create([
                    'action_user_id' => $user->id,
                    'action_description' => "VDP ID({$info->geo_id}) has been verified by {$user->user_name} at {$now}",
                    'action_type' => 'Verify',
                    'action_id' => $info->id,
                ]);
                DB::connection('operation')->commit();
            } catch (\Exception $e) {
                DB::connection('operation')->rollback();
                return ['data' => ['message' => $e->getMessage()], 'status' => false];
            }
            return ['data' => ['message' => "VDP verified successfully"], 'status' => true];
        }
        return ['data' => ['message' => "You don`t have access to perform this action"], 'status' => false];
    }

    public function approveVDP($id)
    {
        $type = auth()->user()->usertype->type_code;
        if ($type == 22 || $type == 66 || $type == 11) {
            DB::connection('operation')->beginTransaction();
            try {
                $info = $this->info->findOrFail($id);
                if ($info->status != 'verified') throw new \Exception("Member status is  {
                $info->status}");
                $info->update(['status' => 'approved']);
                $user = auth()->user();
                $now = Carbon::now()->format('d-M-Y h:i:s A');
                UserActionLog::create([
                    'action_user_id' => $user->id,
                    'action_description' => "VDP ID({$info->geo_id}) has been approve by {$user->user_name} at {$now}",
                    'action_type' => 'Approve',
                    'action_id' => $info->id,
                ]);
                DB::connection('operation')->commit();
            } catch (\Exception $e) {
                DB::connection('operation')->rollback();
                return ['data' => ['message' => $e->getMessage()], 'status' => false];
            }
            return ['data' => ['message' => "VDP approved successfully"], 'status' => true];
        }
        return ['data' => ['message' => "You don`t have access to perform this action"], 'status' => false];
    }

    public function verifyAndApproveVDP($id)
    {
        $type = auth()->user()->usertype->type_code;
        if ($type == 22 || $type == 66 || $type == 11) {
            DB::connection('operation')->beginTransaction();
            try {
                $info = $this->info->findOrFail($id);
                if ($info->status != 'new') throw new \Exception("He/She is already {$info->status}");
                $info->update(['status' => 'approved']);
                DB::connection('operation')->commit();
            } catch (\Exception $e) {
                DB::connection('operation')->rollback();
                return ['data' => ['message' => $e->getMessage()], 'status' => false];
            }
            return ['data' => ['message' => "VDP approved successfully"], 'status' => true];
        }
        return ['data' => ['message' => "You don`t have access to perform this action"], 'status' => false];
    }

    private function getLastPartOfGeoID($request)
    {   //$count = 1;
        $count = $this->info->count();
        $count += 1;

        $count = sprintf("%06d", $count);
        return $count;
        //echo '<pre>';print_r($request); exit;
        switch ($entry_unit) {
            case 1:
                $count = $this->info->where($request->only(['division_id', 'thana_id', 'unit_id', 'gender']))
                    ->whereRaw("SUBSTRING(geo_id,12,1)=".$entry_unit)->count();
                $total =  115;
                $platoon = 1;
                $count += 1;
                break;
            case 2:
                if (strcasecmp($request->gender, "female")) throw new \Exception("Gender doesn`t match with  unit selected");
                $count = $this->info->where($request->only(['division_id', 'thana_id', 'unit_id', "gender", 'union_id']))
                    ->whereRaw("SUBSTRING(geo_id,12,1)=".$entry_unit)->count();
                $total = 32;
                $platoon = 1;
                $count += 1;
                break;
            case 3:
                if (strcasecmp($request->gender, "male")) throw new \Exception("Gender doesn`t match with  unit selected");
                $count = $this->info->where($request->only(['division_id', 'thana_id', 'unit_id', "gender", 'union_id']))
                    ->whereRaw("SUBSTRING(geo_id,12,1)=".$entry_unit)->count();
                $total = 32;
                $platoon = 1;
                $count += 1;
                break;
            case 4:
                $count = $this->info->where($request->only(['division_id', 'thana_id', 'unit_id', 'gender', 'union_id']))
                    ->whereRaw("SUBSTRING(geo_id,12,1)=".$entry_unit)->count();
                $total = 32;
                $platoon = 1;
                $count += ($request->gender == 'Male' ? 1 : 33);
                break;
            case 5:
                $count = $this->info->where($request->only(['division_id', 'thana_id', 'unit_id', 'gender', 'union_word_id']))
                    ->whereRaw("SUBSTRING(geo_id,12,1)=".$entry_unit)->count();

                $platoon = $count - 32 >= 0 ? 2 + intval(($count - 32) / 30) : 1;
                $total = $platoon==1?32:30;
                $count = $count - 32 >= 0 ? intval(($count - 32) % 30) : $count;
                $count += ($request->gender == 'Male' ? 1 : 33);
                break;
            case 6:
                $count = $this->info->where($request->only(['division_id', 'thana_id', 'unit_id', 'gender', 'union_word_id']))
                    ->whereRaw("SUBSTRING(geo_id,12,1)=".$entry_unit)->count();

                $platoon = $count - 32 >= 0 ? 2 + intval(($count - 32) / 30) : 1;
                $total = $platoon==1?32:30;
                $count = $count - 32 >= 0 ? intval(($count - 32) % 30) : $count;
                $count += ($request->gender == 'Male' ? 1 : 33);
                break;
            default :
                throw new \Exception("Invalid unit selected");
        }
        // $count = $this->info->where($request->only(['division_id', 'thana_id', 'unit_id', 'union_id', 'union_word_id', 'gender']))->count();
        if ($count > $total) {
            throw new \Exception("$total {$request->gender} already register in this selected unit");
        }
        $count = sprintf("%03d", $count);
        $platoon = sprintf("%02d", $platoon);
        return $platoon . $count;
    }
}