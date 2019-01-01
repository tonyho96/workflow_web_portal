<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\Form;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Gate;
use  DateTime;
use Mail;
class CronController extends Controller
{
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function sendEmailAskingCrUserToCompleteTheirChangeRequest()
	{
			//$emails=Form::where('status',config( 'form.status.approved' ))->where("complete_status",config( 'form.complete_status.waiting_for_complete' ))->where("change_end_date"+"change_end_time","<",time())->with("author:email")->where("role",config( 'user.role.CR'))->get();
		//$emails=Form::select(DB::raw("CONCAT(forms.change_end_date,' ',forms.change_end_time) as end_date_time"))->where("forms.end_date_time","<","2018-01-01 00:00:00")->where('status',config( 'form.status.approved' ))->where("complete_status",config( 'form.complete_status.waiting_for_complete' ))->with("author:email")->where("role",config( 'user.role.CR'))->get();
		//$emails=Form::where('status',config( 'form.status.approved' ))->where("complete_status",config( 'form.complete_status.waiting_for_complete' ))->with("author:email")->where("role",config( 'user.role.CR'))->get();
		//whereHas('comments', function ($query) {$query->where('content', 'like', 'foo%');})
		$emails=array();
		// get all forms which have been approved and be waiting for complete raised by CR users
		$cr_users=Form::where('status',config( 'form.status.approved' ))->where("complete_status",config( 'form.complete_status.waiting_for_complete' ))->whereHas('author', function ($query)
		{
			$query->where('role', '=', config( 'user.role.CR'));
		})->get();

		foreach ($cr_users as $key => $user) {
			$dtime = DateTime::createFromFormat("Y-m-d H:i:s", $user->change_end_date.' '.$user->change_end_time);
			$timestamp = $dtime->getTimestamp();
			if($timestamp<time())
			{
				$emails[]=$user->author->email;
			}
		}
		$data=array();
		// send email
		foreach ( $emails as $email ) {	
			Mail::send( 'emails.email', compact('email','data' ), function ( $message ) use ($data,$email ) {
				$message->to( $email);
				$message->subject( 'complete their change request' );
			} );
		}
		return response()
            ->json(['status' => '1']);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function sendEmailtoCAonceaweek()
    {
        $emails=array();
        $ca_users=User::where('role', 1)->get();
        $cr_users=Form::where("complete_status",config( 'form.complete_status.waiting_for_complete' ))->whereHas('author', function ($query)
        {
            $query->where('role', '=', config( 'user.role.CR'));
        })->get();
        foreach ($ca_users as $key => $user) {
            /*$dtime = DateTime::createFromFormat("Y-m-d H:i:s", $user->change_end_date.' '.$user->change_end_time);
            $timestamp = $dtime->getTimestamp();
            if($timestamp<time())
            {
                $emails[]=$user->author->email;
            }*/
            $emails[]=$user->email;
        }
        $data=array('cr_users' => $cr_users);
        foreach ( $emails as $email ) {
            Mail::send( 'emails.causeremail', compact('email','data' ), function ( $message ) use ($data,$email ) {
                $message->to( $email);
                $message->subject('All open CRs requiring completion');
            } );
        }
        return response()
            ->json(['status' => '1']);
    }
}