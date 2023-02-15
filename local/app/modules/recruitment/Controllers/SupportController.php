<?php

namespace App\modules\recruitment\Controllers;

use App\Jobs\FeedbackSMS;
use App\modules\recruitment\Models\FeebBack;
use App\modules\recruitment\Models\JobAppliciant;
use App\modules\recruitment\Models\SmsQueue;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{


    //Updated BY Rintu on 18-10-2022
    public function problemReport(Request $request){
        if(!strcasecmp($request->method(),'post')){
            $applicants  = DB::table('job_feedback')
                ->select('job_feedback.id','job_feedback.comment','job_feedback.status', 'job_feedback.national_id_no', 'job_feedback.date_of_birth',  'job_feedback.problem_type', 'job_feedback.payment_option', 'job_feedback.sender_no', 'job_feedback.txid', 'job_feedback.created_at', 'job_feedback.mobile_no_self','job_circular.circular_name')
                ->join('job_circular', 'job_circular.id', '=', 'job_feedback.feed_circular_id')
                ->where('job_circular.status', 'active')
                ->where('job_feedback.status','pending');
            // ->where('job_feedback.created_at', '>', '2022-10-12');
            if($request->mobile_no_self != ''){
                $applicants =   $applicants->where('job_feedback.mobile_no_self',$request->mobile_no_self);
            }
            $applicants =   $applicants->orderBy('job_feedback.created_at','desc')->paginate(50);
        }
        else $applicants  = DB::table('job_feedback')
            ->select('job_feedback.id','job_feedback.comment','job_feedback.status', 'job_feedback.national_id_no', 'job_feedback.date_of_birth',  'job_feedback.problem_type', 'job_feedback.payment_option', 'job_feedback.sender_no', 'job_feedback.txid', 'job_feedback.created_at', 'job_feedback.mobile_no_self','job_circular.circular_name')
            ->join('job_circular', 'job_circular.id', '=', 'job_feedback.feed_circular_id')
            ->where('job_circular.status', 'active')
            ->where('job_feedback.status','pending')
            //->where('job_feedback.created_at', '>', '2022-10-12')
            ->orderBy('job_feedback.id','desc')->paginate(50);
        return view('recruitment::support.applicants_feedback',['applicants'=>$applicants]);
    }

    public function replyProblem(Request $request,$id){

        if(!$request->exists('type')){
            return redirect()->back()->with('error_message','Invalid Request');
        }
        if($request->type=='verify'){
            DB::beginTransaction();
            try {
                $feedBack = FeebBack::find($id);
                if($feedBack){
                    $applicant = $feedBack->applicant;
                    $payment = $applicant?$applicant->payment:null;
                    if ($payment) {
                        $payment->returntxID = $payment->txID;
                        $payment->bankTxID = $feedBack->txid;
                        $payment->bankTxStatus = 'SUCCESS';
                        $payment->txnAmount = 200;
                        $payment->spCode = '000';
                        $payment->spCodeDes = 'ApprovedManual';
                        $payment->paymentOption = $feedBack->payment_option;
                        $payment->save();
                        $applicant->status = $applicant->status=='initial'?'paid':'applied';
                        $applicant->save();
                        $feedBack->status='verify';
                        $feedBack->save();
                        $this->dispatch(new FeedbackSMS($request->message,$applicant->mobile_no_self));
                        DB::commit();
                        return redirect()->back()->with('success_message', 'verified successfully');
                    }
                }
                return redirect()->back()->with('error_message', 'This applicant not found');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error_message', $e->getMessage());
            }
        }
        elseif ($request->type=='reject'){
            DB::beginTransaction();
            try {
                $feedBack = FeebBack::find($id);
                if($feedBack){
                    $feedBack->status='verify';
                    $feedBack->save();
                    $this->dispatch(new FeedbackSMS($request->message,$feedBack->mobile_no_self));
                    DB::commit();
                    return redirect()->back()->with('success_message', 'rejected successfully');
                }
                return redirect()->back()->with('error_message', 'This applicant not found');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error_message', $e->getMessage());
            }
        }
        else{
            return redirect()->back()->with('error_message','Invalid Request');
        }
    }
    public function replyProblemDelete($id){
        $feedback = FeebBack::find($id);
        if($feedback){
            $feedback->delete();
            return redirect()->back()->with('success_message', 'deleted successfully');
        }
        return redirect()->back()->with('error_message', 'not found');
    }

    public function sendUserNamePassword(){
        /*
        $circular_id = 145;
        $paids = JobAppliciant::where('job_circular_id',$circular_id)->where('status','paid')->get();
        foreach ($paids as $paid){
            $body = "Your applicant id:{$paid->applicant_id}, password: {$paid->applicant_password} .";
            $to = $paid->mobile_no_self;
            $payload = json_encode(compact('to','body'));
            $try = 1;
            SmsQueue::create(compact('payload','circular_id','try'));
        }
        return "success";
        */
    }
}
