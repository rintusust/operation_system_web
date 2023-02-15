<?php

namespace App\modules\recruitment\Controllers;

use App\Http\Controllers\Controller;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\recruitment\Models\JobApplicantExamCenter;
use App\modules\recruitment\Models\JobCircular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicantExamCenter extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = JobApplicantExamCenter::with('circular');
            if ($request->circular) {
                $data->where('job_circular_id', $request->circular);
            }
            if ($request->range!='all') {
                $data->whereHas('units',function($q) use ($request){
                    $q->where('tbl_units.division_id',$request->range);
                });
            }
            if ($request->unit!='all') {
                $data->whereHas('units',function($q) use ($request){
                    $q->where('tbl_units.id',$request->unit);
                });
            }
//            return $data->get();
            return view('recruitment::exam_center.data', ['data' => $data->get()]);

        }
        return view('recruitment::exam_center.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $circulars = JobCircular::pluck('circular_name', 'id');
        $units = District::all();
        $division = Division::all();
        $division->prepend('--Select a division','');
        return view('recruitment::exam_center.create', compact('circulars', 'units','division'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return $request->all();
        $this->validate($request, JobApplicantExamCenter::rules());

        DB::beginTransaction();
        try{
            $input = $request->except(['search_unit','units','exam_roll_place']);
            $exam_roll_place = $request->exam_roll_place?json_encode(array_values($request->exam_roll_place)):null;
            $input['exam_place_roll_wise'] = $exam_roll_place;
            $jec = JobApplicantExamCenter::where('job_circular_id',$request->job_circular_id)
                ->whereHas('examUnits',function ($q) use($request){
                    $q->whereIn('unit_id',$request->units);
                })->exists();
            if($jec) throw new \Exception('Exam center already exists under this circular and units');
            $ec = JobApplicantExamCenter::create($input);
            $ec->units()->attach($request->units);
            DB::commit();
            return redirect()->route('recruitment.exam-center.index')->with('session_success','Exam center created successfully');
        }catch(\Throwable $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Error $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }
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
        $data = JobApplicantExamCenter::find($id);
        $circulars = JobCircular::pluck('circular_name', 'id');
        $units = District::all();
        $division = Division::pluck('division_name_bng','id');
        $division->prepend('--Select a division','');
        return view('recruitment::exam_center.edit', compact('circulars', 'units','data','division'));
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

        $this->validate($request, JobApplicantExamCenter::rules());

        DB::beginTransaction();
        try{
            $input = $request->except(['search_unit','units','exam_roll_place']);
            $exam_roll_place = $request->exam_roll_place?json_encode(array_values($request->exam_roll_place)):null;
            $input['exam_place_roll_wise'] = $exam_roll_place;
            $jec = JobApplicantExamCenter::where('job_circular_id',$request->job_circular_id)
                ->where('id','!=',$id)
                ->whereHas('examUnits',function ($q) use($request){
                    $q->whereIn('unit_id',$request->units);
                })->exists();
            if($jec) throw new \Exception('Exam center already exists under this circular and units');
            $exam_center = JobApplicantExamCenter::findOrFail($id);
            $exam_center->update($input);
            $exam_center->units()->detach();
            $exam_center->units()->attach($request->units);
            DB::commit();
            return redirect()->route('recruitment.exam-center.index')->with('session_success','Exam center Updated successfully');
        }catch(\Throwable $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Error $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }
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
        DB::beginTransaction();
        try{
            $exam_center = JobApplicantExamCenter::findOrFail($id);
            $exam_center->delete();
            DB::commit();
            return redirect()->route('recruitment.exam-center.index')->with('session_success','Exam center Deleted successfully');
        }catch(\Throwable $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Error $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('recruitment.exam-center.index')->with('session_error',$e->getMessage());
        }
    }
}
