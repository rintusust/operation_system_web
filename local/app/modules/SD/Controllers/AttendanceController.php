<?php

namespace App\modules\SD\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\KpiGeneralModel;
use App\modules\HRM\Models\PersonalInfo;
use App\modules\HRM\Models\TransferAnsar;
use App\modules\SD\Models\Attendance;
use App\modules\SD\Models\SalarySheetHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rules = [
                "month" => 'required',
                "range" => 'required_if:ansar_id,' . null,
                "unit" => 'required_if:ansar_id,' . null,
                "thana" => 'required_if:ansar_id,' . null,
                "kpi" => 'required_if:ansar_id,' . null,
                "year" => 'required|regex:/^[0-9]{4}$/',
            ];
            $this->validate($request, $rules);
            $attendance = Attendance::with(['kpi'])
                ->whereHas('kpi', function ($q) use ($request) {
                    if ($request->range && $request->range != 'all') {
                        $q->where('division_id', $request->range);
                    }
                    if ($request->unit && $request->unit != 'all') {
                        $q->where('unit_id', $request->unit);
                    }
                    if ($request->thana && $request->thana != 'all') {
                        $q->where('thana_id', $request->thana);
                    }
                    if ($request->kpi && $request->kpi != 'all') {
                        $q->where('id', $request->kpi);
                    }
                });
            if ($request->ansar_id) {
                $attendance->where('ansar_id', $request->ansar_id);
            }
            if ($request->month) {
                $attendance->where('month', '=', $request->month);
            }
            if ($request->year) {
                $attendance->where('year', '=', $request->year);
            }
            $attendance->where('is_attendance_taken', '=', 1);
            if (!$request->ansar_id) {
                $type = "count";
                $data = collect($attendance->select(DB::raw("SUM(is_present=1) as total_present"), DB::raw("SUM(is_present=0 AND is_leave=0) as total_absent"), DB::raw("SUM(is_leave=1) as total_leave"), 'day')
                    ->groupBy('day')
                    ->get());
            } else {
                $personal_info = PersonalInfo::where('ansar_id', $request->ansar_id)->first();
                $type = "view";
                $data = $attendance->get();
                $ansar_id = $request->ansar_id;
                $ansar_name = $personal_info->ansar_name_bng;
                $father_name = $personal_info->father_name_bng;
            }
            $first_date = Carbon::parse("01-{$request->month}-{$request->year}");
            return view('SD::attendance.data', compact('first_date', 'data', 'type', 'ansar_id', 'ansar_name', 'father_name'));

        }
        return view('SD::attendance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $rules = [
                /*"range" => 'required',
                "unit" => 'required',
                "thana" => 'required',
                "kpi" => 'required',
                "month" => 'required',
                "year" => 'required',*/
                "month" => 'required',
                "range" => 'required',
                "unit" => 'required',
                "thana" => 'required',
                "kpi" => 'required',
                "year" => 'required|regex:/^[0-9]{4}$/',
            ];
            $this->validate($request, $rules);
            $m = $request->month;
            $y = $request->year;
            $ansar_id = $request->ansar_id;
            $attendance = KpiGeneralModel::with(['attendance' => function ($q) use ( $m, $y, $ansar_id) {

                if ($ansar_id) $q->where('ansar_id', $ansar_id);
                $q->where('month', $m);
                $q->where('year', $y);
                $q->where('is_attendance_taken', 0);
                $q->select('ansar_id', 'id', 'kpi_id', 'year','month','day');

            },'attendance.ansar'=>function($q){
                $q->select('ansar_id','ansar_name_bng','designation_id');
                $q->with('designation');
            }]);
            if ($request->range && $request->range != 'all') {
                $attendance->where('division_id', $request->range);
            }
            if ($request->unit && $request->unit != 'all') {
                $attendance->where('unit_id', $request->unit);
            }
            if ($request->thana && $request->thana != 'all') {
                $attendance->where('thana_id', $request->thana);
            }
            if ($request->kpi && $request->kpi != 'all') {
                $attendance->where('id', $request->kpi);
            }
            if ($request->ansar_id) {
                $attendance->whereHas('attendance', function ($q) use ($ansar_id) {
                    $q->where('ansar_id', $ansar_id);
                });
            }

            DB::enableQueryLog();
            $data = $attendance->first();
            if(!$data) return "false";
            $att = collect($data->attendance)->groupBy('ansar_id')->all();
            foreach ($att as $k=>$a){
                $ansar = $a[0]['ansar'];
                foreach ($a as $kk=>$v){
                    unset($a[$kk]['ansar']);
                }
                $mg = collect($a)->groupBy('month')->all();
                $att[$k] = ['data'=>$mg,'details'=>$ansar];
            }
            unset($data->attendance);
            $data['attendance'] = $att;
//            if($request->ansar_id){
//                $type = "ansar_wise";
//            } else if($request->day){
//                $type = "day_wise";
//            }else if($request->month){
//                $type = "month_wise";
//            }
//            return DB::getQueryLog();
//    return $data;
            $date = $request->only(['day', 'month', 'year', 'ansar_id']);
            return compact('date', 'data','type');
//            return view('SD::attendance.create_data', compact('date', 'data'));

        }
        return view('SD::attendance.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
//        return $request->all();
        $attendance_datas = collect($request->get("attendance_data"))->toArray();
//        return var_dump($attendance_datas);
//        $type = $request->type;
        DB::connection('sd')->beginTransaction();
        try {
            $kpi_id = $request->kpi_id;
            $month = intval($request->month);
            $year = $request->year;

            foreach ($attendance_datas as $attendance_data) {
                $ansar_id = $attendance_data['ansar_id'];

                $present_dates = isset($attendance_data['present_dates'])?$attendance_data['present_dates']:[];
                $leave_dates = isset($attendance_data['leave_dates'])?$attendance_data['leave_dates']:[];
                if (count($present_dates) <= 0 && count($leave_dates) <= 0) continue;
                Attendance::where(compact('kpi_id', 'month','year','ansar_id'))->update(['is_attendance_taken' => 1]);
                foreach ($present_dates as $present_date) {
                    $d = Carbon::create($present_date['year'],$present_date['month']+1,$present_date['day']);
                    $day = $d->day;
                    $month = $d->month;
                    $year = $d->year;
                    $attendance = Attendance::where(compact('ansar_id', 'kpi_id', 'day', 'month', 'year'))->first();
                    $attendance->update(['is_present' => 1, 'is_attendance_taken' => 1, 'is_leave' => 0]);
                }
                foreach ($leave_dates as $leave_date) {
                    $d = Carbon::create($leave_date['year'],$leave_date['month']+1,$leave_date['day']);;
                    $day = $d->day;
                    $month = $d->month;
                    $year = $d->year;
                    $attendance = Attendance::where(compact('ansar_id', 'kpi_id', 'day', 'month', 'year'))->first();
                    $attendance->update(['is_leave' => 1, 'is_attendance_taken' => 1, 'is_present' => 0]);
                }

                //$attendance->update($attendance_data);
            }
            DB::connection('sd')->commit();
        } catch (\Exception $e) {
            DB::connection('sd')->rollback();
//            return redirect()->route("SD.attendance.create")->with('error_message', "An error occur while submitting attendance. please try again later or contact with system admin");
            return redirect()->route("SD.attendance.create")->with('error_message', $e->getMessage());
        }catch (\Throwable $e) {
            DB::connection('sd')->rollback();
//            return redirect()->route("SD.attendance.create")->with('error_message', "An error occur while submitting attendance. please try again later or contact with system admin");
            return redirect()->route("SD.attendance.create")->with('error_message', $e->getMessage());
        }catch (\Error $e) {
            DB::connection('sd')->rollback();
//            return redirect()->route("SD.attendance.create")->with('error_message', "An error occur while submitting attendance. please try again later or contact with system admin");
            return redirect()->route("SD.attendance.create")->with('error_message', $e->getMessage());
        }
        return redirect()->route("SD.attendance.create")->with('success_message', "Attendance taken successfully");

    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::connection("sd")->beginTransaction();
        try {
            $attendance = Attendance::findOrFail($id);
            $data = [];
            if ($request->status == "present" && ((!$attendance->is_present && !$attendance->is_leave) || (!$attendance->is_present && $attendance->is_leave))) {
                $data["is_present"] = 1;
                $data["is_leave"] = 0;
            } else if ($request->status == "absent" && ($attendance->is_present || $attendance->is_leave)) {
                $data["is_present"] = 0;
                $data["is_leave"] = 0;
            } else if ($request->status == "leave" && ((!$attendance->is_present && !$attendance->is_leave) || ($attendance->is_present && !$attendance->is_leave))) {
                $data["is_present"] = 0;
                $data["is_leave"] = 1;
            }
            $attendance->update($data);
            DB::connection("sd")->commit();
        } catch (\Exception $e) {
            DB::connection("sd")->rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
        return response()->json(['status' => true, 'message' => "Attendance updated successfully"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function viewAttendance(Request $request)
    {
//        return "sadasd";
        if ($request->ajax()) {
            $rules = [
                "date" => 'required|regex:/^[0-9]{1,2}$/',
                "month" => 'required',
                "range" => 'required',
                "unit" => 'required',
                "thana" => 'required',
                "kpi" => 'required',
                "year" => 'required|regex:/^[0-9]{4}$/',
            ];
            $valid = Validator::make($request->all(), $rules);
            if ($valid->fails()) {
                return "";
            }
            $generated_for_month = Carbon::create($request->year,$request->month)->format('F, Y');
            $generated_type = 'salary';
            $disburst_status = 'done';
            $kpi_id = $request->kpi;
            $salary_disburst_status = SalarySheetHistory::where(compact('generated_type','generated_for_month','disburst_status','kpi_id'))->exists();
//            return $salary_disburst_status?"true":'false';
            $kpi = KpiGeneralModel::find($request->kpi);
            $present_list = Attendance::with(['ansar' => function ($q) {
                $q->with(["embodiment" => function ($qq) {
                    $qq->with("kpi")->select("ansar_id", "kpi_id");
                }])->select('ansar_id', 'ansar_name_bng', 'division_id', 'unit_id', 'thana_id');
            }])->where('day', $request->date)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('kpi_id', $request->kpi)
                ->where('is_attendance_taken', 1)
                ->where('is_leave', 0)
                ->where('is_present', 1)
                ->get();
            $absent_list = Attendance::with(['ansar' => function ($q) {
                $q->with(["embodiment" => function ($qq) {
                    $qq->with("kpi")->select("ansar_id", "kpi_id");
                }])->select('ansar_id', 'ansar_name_bng', 'division_id', 'unit_id', 'thana_id');
            }])->where('day', $request->date)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('kpi_id', $request->kpi)
                ->where('is_attendance_taken', 1)
                ->where('is_leave', 0)
                ->where('is_present', 0)
                ->get();
            $leave_list = Attendance::with(['ansar' => function ($q) {
                $q->with(["embodiment" => function ($qq) {
                    $qq->with("kpi")->select("ansar_id", "kpi_id");
                }])->select('ansar_id', 'ansar_name_bng', 'division_id', 'unit_id', 'thana_id');
            }])->where('day', $request->date)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('kpi_id', $request->kpi)
                ->where('is_attendance_taken', 1)
                ->where('is_leave', 1)
                ->where('is_present', 0)
                ->get();
            $title = "Attendance of <br>" . $kpi->kpi_name . "<br> Date: " . Carbon::create($request->year, $request->month, $request->date)->format('d-M-Y');
            return view('SD::attendance.view', compact('title', 'present_list', 'absent_list', 'leave_list','kpi','salary_disburst_status'));
        }
        abort(401);

    }

    public function loadDataForPlanB(Request $request)
    {
        if (!$request->ajax()) return abort(403);
        $ansar_id = $request->ansar_id;
        $personalDetails = PersonalInfo::with(['embodiment.kpi', 'designation'])->whereHas('status', function ($q) {
            $q->where('embodied_status', 1);
            $q->where('block_list_status', 0);
            $q->where('black_list_status', 0);
        })->whereHas('embodiment', function ($q) {
            $q->where(function ($qq) {
                $qq->whereMonth('joining_date', '<=', 7);
                $qq->orWhere(DB::raw('YEAR(joining_date)'), '<=', 2018);
            });
        })->where('ansar_id', $ansar_id)->first();
        $freezeDetails = PersonalInfo::with(['freezing_info.embodiment.kpi', 'freezing_info.freezedAnsarEmbodiment.kpi', 'designation'])->whereHas('status', function ($q) {
            $q->where('freezing_status', 1);
            $q->where('block_list_status', 0);
            $q->where('black_list_status', 0);
        })->where(function($q){
            $q->orWhereHas('freezing_info.embodiment', function ($q) {
                $q->where(function ($qq) {
                    $qq->whereMonth('joining_date', '<=', 7);
                    $qq->orWhere(DB::raw('YEAR(joining_date)'), '<=', 2018);
                });
            })->orWhereHas('freezing_info.freezedAnsarEmbodiment', function ($q) {
                $q->where(function ($qq) {
                    $qq->whereMonth('embodied_date', '<=', 7);
                    $qq->orWhere(DB::raw('YEAR(embodied_date)'), '<=', 2018);
                });
            });
        })->where('ansar_id', $ansar_id)->first();
        $rest_details = PersonalInfo::with(['rest.embodimentLog.kpi', 'designation'])->whereHas('status', function ($q) {
            $q->where('rest_status', 1);
            $q->where('block_list_status', 0);
            $q->where('black_list_status', 0);
        })->whereHas('rest', function ($q) {
            $q->whereMonth('rest_date', '>=', 7);
            $q->whereYear('rest_date', '=', 2018);
        })->where('ansar_id', $ansar_id)->first();
//        return $personalDetails;
        $data = [];
        if ($personalDetails) {

            if ($personalDetails->embodiment->joining_date != $personalDetails->embodiment->transfered_date) {


                $t_date = Carbon::parse($personalDetails->embodiment->transfered_date);
//                return ['s'=>$t_date->month < 7];
                if ($t_date->month < 7 || $t_date->year < 2018) {
                    $row = [];
                    $row["kpi_id"] = $personalDetails->embodiment->kpi->id;
                    $row["kpi_name"] = $personalDetails->embodiment->kpi->kpi_name;
                    $row["dates"] = [];
                    $t_date = Carbon::create(2018, 7, 1);
                    for ($i = 0; $i < $t_date->daysInMonth; $i++) {
                        $modified_date = $t_date->addDays($i == 0 ? 0 : 1);
                        array_push($row['dates'], [
                            'day' => $modified_date->day,
                            'month' => $modified_date->month - 1,
                            'year' => $modified_date->year,
                        ]);
                    }
                    array_push($data, $row);
                } else if ($t_date->month == 7) {
                    $row = [];
                    $row["kpi_id"] = $personalDetails->embodiment->kpi->id;
                    $row["kpi_name"] = $personalDetails->embodiment->kpi->kpi_name;
                    $row["dates"] = [];
                    $modified_date = clone $t_date;
                    for ($i = 0; $i <= $t_date->daysInMonth - $t_date->day; $i++) {
                        $modified_date->addDays($i == 0 ? 0 : 1);
                        array_push($row['dates'], [
                            'day' => $modified_date->day,
                            'month' => $modified_date->month - 1,
                            'year' => $modified_date->year,
                        ]);
                    }
                    array_push($data, $row);
                    $transfer_history = TransferAnsar::where('ansar_id', $ansar_id)
                        ->where('embodiment_id', $personalDetails->embodiment->id)
                        ->whereMonth('transfered_kpi_join_date', '=', 7)
                        ->whereYear('transfered_kpi_join_date', '=', 2018)
                        ->orderBy('present_kpi_join_date', 'desc')
                        ->get();
                } else {
                    $transfer_history = TransferAnsar::where('ansar_id', $ansar_id)
                        ->where('embodiment_id', $personalDetails->embodiment->id)
                        ->where(function ($qq) {
                            $qq->whereMonth('present_kpi_join_date', '<=', 7);
                            $qq->orWhere(DB::raw('YEAR(present_kpi_join_date)'), '<=', 2018);
                        })
                        ->orderBy('present_kpi_join_date', 'desc')
                        ->get();
//                    return $transfer_history;
                }


//                return $transfer_history;
                if (!isset($transfer_history)) $transfer_history = [];
                foreach ($transfer_history as $th) {
                    $row = [];
                    $row["kpi_id"] = $th->present_kpi_id;
                    $row["kpi_name"] = $th->presentKpi->kpi_name;
                    $row['dates'] = [];
                    $pd = Carbon::parse($th->present_kpi_join_date);
                    $tdd = Carbon::parse($th->transfered_kpi_join_date);
                    $total_days = $tdd->month == 7 ? $tdd->day : 32;
                    for ($i = $pd->month < 7 || $pd->year < 2018 ? 1 : $pd->day; $i < $total_days; $i++) {
                        array_push($row['dates'], [
                            'day' => $i,
                            'month' => 6,
                            'year' => 2018,
                        ]);
                    }
                    if (count($row['dates'])) array_push($data, $row);
                }
            }
            else {
                $row = [];
                $row["kpi_id"] = $personalDetails->embodiment->kpi->id;
                $row["kpi_name"] = $personalDetails->embodiment->kpi->kpi_name;
                $row["dates"] = [];
                $jd = Carbon::parse($personalDetails->embodiment->joining_date);
                $first_date = Carbon::create(2018, 7, 1);
                for ($i = $jd->month==7&&$jd->year==2018?$jd->day:1; $i <= $first_date->daysInMonth; $i++) {
                    array_push($row['dates'], [
                        'day' => $i,
                        'month' => 6,
                        'year' => 2018,
                    ]);
                }
                array_push($data, $row);
            }
        }
        else if ($rest_details) {
//            return $rest_details;
            $t_date = Carbon::parse($rest_details->rest->rest_date);
            $e_date = Carbon::parse($rest_details->rest->embodimentLog->transfered_date);
//                return ['s'=>$t_date->month < 7];
            if ($t_date->month > 7 && ($e_date->month < 7 || $e_date->year < 2018)) {
                $row = [];
                $row["kpi_id"] = $rest_details->rest->embodimentLog->kpi->id;
                $row["kpi_name"] = $rest_details->rest->embodimentLog->kpi->kpi_name;
                $row["dates"] = [];
                $t_date = Carbon::create(2018, 7, 1);
                for ($i = 0; $i < $t_date->daysInMonth; $i++) {
                    $modified_date = $t_date->addDays($i == 0 ? 0 : 1);
                    array_push($row['dates'], [
                        'day' => $modified_date->day,
                        'month' => $modified_date->month - 1,
                        'year' => $modified_date->year,
                    ]);
                }
                array_push($data, $row);
            } else if ($t_date->month > 7 && $e_date->month == 7 && $e_date->year == 2018) {
                $row = [];
                $row["kpi_id"] = $rest_details->rest->embodimentLog->kpi->id;
                $row["kpi_name"] = $rest_details->rest->embodimentLog->kpi->kpi_name;
                $row["dates"] = [];
                $t_date = Carbon::create(2018, 7, 1);
                for ($i = $e_date->day; $i <= $e_date->daysInMonth; $i++) {
                    $modified_date = $t_date->addDays($i == 0 ? 0 : 1);
                    array_push($row['dates'], [
                        'day' => $modified_date->day,
                        'month' => $modified_date->month - 1,
                        'year' => $modified_date->year,
                    ]);
                }
                array_push($data, $row);
                $transfer_history = TransferAnsar::where('ansar_id', $ansar_id)
                    ->where('embodiment_id', $rest_details->rest->old_embodiment_id)
                    ->whereMonth('transfered_kpi_join_date', '=', 7)
                    ->whereYear('transfered_kpi_join_date', '=', 2018)
                    ->orderBy('present_kpi_join_date', 'desc')
                    ->get();
            } else if ($t_date->month > 7 && $e_date->month > 7) {
                $transfer_history = TransferAnsar::where('ansar_id', $ansar_id)
                    ->where('embodiment_id', $rest_details->rest->old_embodiment_id)
                    ->where(function ($qq) {
                        $qq->whereMonth('present_kpi_join_date', '<=', 7);
                        $qq->orWhere(DB::raw('YEAR(present_kpi_join_date)'), '<=', 2018);
                    })
                    ->orderBy('present_kpi_join_date', 'desc')
                    ->get();
//                return $transfer_history;
            } else if ($t_date->month == 7 && ($e_date->month < 7 || $e_date->year < 2018)) {
                $row = [];
                $row["kpi_id"] = $rest_details->rest->embodimentLog->kpi->id;
                $row["kpi_name"] = $rest_details->rest->embodimentLog->kpi->kpi_name;
                $row["dates"] = [];
                $modified_date = Carbon::create(2018, 7, 1);
                for ($i = 0; $i < $t_date->day; $i++) {
                    $modified_date->addDays($i == 0 ? 0 : 1);
                    array_push($row['dates'], [
                        'day' => $modified_date->day,
                        'month' => $modified_date->month - 1,
                        'year' => $modified_date->year,
                    ]);
                }
                array_push($data, $row);
            } else if ($t_date->month == 7 && ($e_date->month == 7 && $e_date->year == 2018)) {
                $row = [];
                $row["kpi_id"] = $rest_details->rest->embodimentLog->kpi->id;
                $row["kpi_name"] = $rest_details->rest->embodimentLog->kpi->kpi_name;
                $row["dates"] = [];
                for ($i = $e_date->day; $i <= $t_date->day; $i++) {

                    array_push($row['dates'], [
                        'day' => $i,
                        'month' => 6,
                        'year' => 2018,
                    ]);
                }
                array_push($data, $row);
                $transfer_history = TransferAnsar::where('ansar_id', $ansar_id)
                    ->where('embodiment_id', $rest_details->rest->old_embodiment_id)
                    ->whereMonth('transfered_kpi_join_date', '=', 7)
                    ->whereYear('transfered_kpi_join_date', '=', 2018)
                    ->orderBy('present_kpi_join_date', 'desc')
                    ->get();
            }
            if (!isset($transfer_history)) $transfer_history = [];
            foreach ($transfer_history as $th) {
                $row = [];
                $row["kpi_id"] = $th->present_kpi_id;
                $row["kpi_name"] = $th->presentKpi->kpi_name;
                $row['dates'] = [];
                $pd = Carbon::parse($th->present_kpi_join_date);
                $tdd = Carbon::parse($th->transfered_kpi_join_date);
                $total_days = $tdd->month == 7 ? $tdd->day : 32;
                for ($i = $pd->month < 7 || $pd->year < 2018 ? 1 : $pd->day; $i < $total_days; $i++) {
                    array_push($row['dates'], [
                        'day' => $i,
                        'month' => 6,
                        'year' => 2018,
                    ]);
                }
                if (count($row['dates'])) array_push($data, $row);
            }
            $personalDetails = $rest_details;
        }
        else if ($freezeDetails) {

            $t_date = Carbon::parse($freezeDetails->freezing_info->freez_date);
            if($freezeDetails->freezing_info->embodiment){
                $em = $freezeDetails->freezing_info->embodiment;
                $e_date = Carbon::parse($freezeDetails->freezing_info->embodiment->transfered_date);
                $em_id = $em->id;
            }
            else {
                $em = $freezeDetails->freezing_info->freezedAnsarEmbodiment;
                $e_date = Carbon::parse($freezeDetails->freezing_info->freezedAnsarEmbodiment->transfer_date);
                $em_id = $em->embodiment_id;
            }
            if ($t_date->month > 7 && $t_date->year==2018&&($e_date->month<7||$e_date->year<2018)) {
                $row = [];
                $row["kpi_id"] = $em->kpi->id;
                $row["kpi_name"] = $em->kpi->kpi_name;
                $row["dates"] = [];
                $t_date = Carbon::create(2018, 7, 1);
                for ($i = 0; $i < $t_date->daysInMonth; $i++) {
                    $modified_date = $t_date->addDays($i == 0 ? 0 : 1);
                    array_push($row['dates'], [
                        'day' => $modified_date->day,
                        'month' => $modified_date->month - 1,
                        'year' => $modified_date->year,
                    ]);
                }
                array_push($data, $row);
            }
            else if ($t_date->month == 7 && $t_date->year==2018&&($e_date->month<7||$e_date->year<2018)) {
                $row = [];
                $row["kpi_id"] = $em->kpi->id;
                $row["kpi_name"] = $em->kpi->kpi_name;
                $row["dates"] = [];
                $modified_date = Carbon::create(2018,7,1);
                for ($i = 0; $i < $t_date->day; $i++) {
                    $modified_date->addDays($i == 0 ? 0 : 1);
                    array_push($row['dates'], [
                        'day' => $modified_date->day,
                        'month' => $modified_date->month - 1,
                        'year' => $modified_date->year,
                    ]);
                }
                array_push($data, $row);
            }
            else if ($t_date->month == 7 && $t_date->year==2018&&$e_date->month==7) {
                $row = [];
                $row["kpi_id"] = $em->kpi->id;
                $row["kpi_name"] = $em->kpi->kpi_name;
                $row["dates"] = [];
                for ($i = $e_date->day; $i < $t_date->day; $i++) {
                    array_push($row['dates'], [
                        'day' => $i,
                        'month' => 6,
                        'year' => 2018,
                    ]);
                }
                array_push($data, $row);
                $transfer_history = TransferAnsar::where('ansar_id', $ansar_id)
                    ->where('embodiment_id', $em_id)
                    ->whereMonth('transfered_kpi_join_date', '=', 7)
                    ->whereYear('transfered_kpi_join_date', '=', 2018)
                    ->orderBy('present_kpi_join_date', 'desc')
                    ->get();
            }
            else if ($t_date->month > 7 && $t_date->year==2018&&$e_date->month==7) {
                $row = [];
                $row["kpi_id"] = $em->kpi->id;
                $row["kpi_name"] = $em->kpi->kpi_name;
                $row["dates"] = [];
                for ($i = $e_date->day; $i <= $e_date->daysInMonth; $i++) {
                    array_push($row['dates'], [
                        'day' => $i,
                        'month' => 6,
                        'year' => 2018,
                    ]);
                }
                array_push($data, $row);
                $transfer_history = TransferAnsar::where('ansar_id', $ansar_id)
                    ->where('embodiment_id', $em_id)
                    ->whereMonth('transfered_kpi_join_date', '=', 7)
                    ->whereYear('transfered_kpi_join_date', '=', 2018)
                    ->orderBy('present_kpi_join_date', 'desc')
                    ->get();
            }
            if (!isset($transfer_history)) $transfer_history = [];
            foreach ($transfer_history as $th) {
                $row = [];
                $row["kpi_id"] = $th->present_kpi_id;
                $row["kpi_name"] = $th->presentKpi->kpi_name;
                $row['dates'] = [];
                $pd = Carbon::parse($th->present_kpi_join_date);
                $tdd = Carbon::parse($th->transfered_kpi_join_date);
                $total_days = $tdd->month == 7 ? $tdd->day : 32;
                for ($i = $pd->month < 7 || $pd->year < 2018 ? 1 : $pd->day; $i < $total_days; $i++) {
                    array_push($row['dates'], [
                        'day' => $i,
                        'month' => 6,
                        'year' => 2018,
                    ]);
                }
                if (count($row['dates'])) array_push($data, $row);
            }
            $personalDetails = $freezeDetails;
        }
        return response()->json(compact('personalDetails', 'data'));


    }

    public function storePlanB(Request $request)
    {
        $rules = [
            "ansar_id" => "required",
            "selectedDates" => "required",
            "selectedDates.*.kpi_id" => "required|exists:hrm.tbl_kpi_info,id"
        ];
        $this->validate($request, $rules);
        $data = [];
        DB::connection('sd')->beginTransaction();
        try {
            foreach ($request->selectedDates as $date) {

                foreach ($date["present"] as $present) {
                    $row = $query = [];
                    $row['ansar_id'] = $query['ansar_id'] = $request->ansar_id;
                    $row['kpi_id'] = $query['kpi_id'] = $date["kpi_id"];
                    $row['day'] = $query['day'] = $present["day"];
                    $row['month'] = $query['month'] = intval($present["month"]) + 1;
                    $row['year'] = $query['year'] = intval($present["year"]);
                    $row['is_present'] = 1;
                    $row['is_leave'] = 0;
                    $row['is_attendance_taken'] = 1;
                    $att = Attendance::where($query)->first();
                    if (!$att) array_push($data, $row);
                    else if (!$att->is_attendance_taken) {
                        $att->update($row);
                    }
                }
                foreach ($date["absent"] as $absent) {
                    $row = $query = [];
                    $row['ansar_id'] = $query['ansar_id'] = $request->ansar_id;
                    $row['kpi_id'] = $query['kpi_id'] = $date["kpi_id"];
                    $row['day'] = $query['day'] = $absent["day"];
                    $row['month'] = $query['month'] = intval($absent["month"]) + 1;
                    $row['year'] = $query['year'] = intval($absent["year"]);
                    $row['is_present'] = 0;
                    $row['is_leave'] = 0;
                    $row['is_attendance_taken'] = 1;
                    $att = Attendance::where($query)->first();
                    if (!$att) array_push($data, $row);
                    else if (!$att->is_attendance_taken) {
                        $att->update($row);
                    }
                }
                foreach ($date["leave"] as $leave) {
                    $row = $query = [];
                    $row['ansar_id'] = $query['ansar_id'] = $request->ansar_id;
                    $row['kpi_id'] = $query['kpi_id'] = $date["kpi_id"];
                    $row['day'] = $query['day'] = $leave["day"];
                    $row['month'] = $query['month'] = intval($leave["month"]) + 1;
                    $row['year'] = $query['year'] = intval($leave["year"]);
                    $row['is_present'] = 0;
                    $row['is_leave'] = 1;
                    $row['is_attendance_taken'] = 1;
                    $att = Attendance::where($query)->first();
                    if (!$att) array_push($data, $row);
                    else if (!$att->is_attendance_taken) {
                        $att->update($row);
                    }
                }
            }
            Attendance::insert($data);
            DB::connection('sd')->commit();
        } catch (\Exception $e) {
            DB::connection('sd')->rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
        if (count($data) == 0) {
            return response()->json(['status' => false, 'message' => 'Attendance already taken']);
        }
        return response()->json(['status' => true, 'message' => 'Attendance taken successfully']);
    }


}
