<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\User;
use App\Services\FileService;
use App\Services\FormService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;
use RuntimeException;
use Log;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();

        return view('forms.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        if (!Auth::user()->isCA()) { // CR can't touch this status fields, this will be set here
            $input['status'] = config('form.status.pending_CAB');
            $input['complete_status'] = config('form.complete_status.planned');
            $input['authorisation_signature_date'] = date('Y-m-d');
        } else { // CA can edit the statuses
            $input['authorisation_signature_date'] = date('Y-m-d');

            if (($input['complete_status'] == config('form.complete_status.success') || $input['complete_status'] == config('form.complete_status.unsuccess')) && empty($input['complete_date']))
                $input['complete_date'] = $input['change_end_date'];
        }

        // raise by = current user id
        if (!isset($input['raised_by_user_id'])) {
            $input['raised_by_user_id'] = Auth::user()->id;
        }

        // date raise = current date
        if (!isset($input['date_raised'])) {
            $input['date_raised'] = date('Y-m-d');
        }

        // create validator
        $validate = FormService::validate($input);

        // if validate fail
        if ($validate->fails()) {
            return redirect()->action('FormController@create')
                ->withErrors($validate)->withInput($input);
        }

        if ($input['change_type'] == config('form.change_type.self_approve')) {
	        $input['status'] = config('form.status.approved');
	        $input['approved_rejected_date'] = date('Y-m-d');
	        $input['approved_rejected_reason'] = '';
	        $input['complete_status'] = config('form.complete_status.waiting_for_complete');
	        $input['approved_rejected_by'] = Auth::user()->id;
        }

        // try to create
        if ($form_created = FormService::create($input)) { // if created successfully

	        if ($request->file('files')) {
		        foreach ( $request->file( 'files' ) as $file ) {
			        $file = FileService::save( $form_created, $file );
		        }
	        }

            // send email to all CA users
            $changeTypeSelect = [
                config('form.change_type.normal') => 'Normal',
                config('form.change_type.emergency') => 'Emergency',
                config('form.change_type.self_approve') => 'Minor/self approved',
				config('form.change_type.bypass') => 'By Pass'
            ];

            $businessPrioritySelect = [
                config('form.business_priority.business_critical') => 'Business critical: no work-around',
                config('form.business_priority.high') => 'High: e.g. replace work-around',
                config('form.business_priority.medium_problem_issue_resolution') => 'Medium: problem/issue resolution',
                config('form.business_priority.medium_essential_enhancement') => 'Medium: essential enhancement',
                config('form.business_priority.low') => 'Low: non-essential or cosmetic'
            ];

            $statusSelect = [
                config('form.status.pending_CAB') => 'Pending CAB',
                config('form.status.approved') => 'Approved',
                config('form.status.rejected') => 'Rejected',
            ];

            $scheduleStatusSelect = [
                config('form.schedule_status.planned') => 'Planned',
                config('form.schedule_status.wating_for_complete') => 'Waiting for complete',
                config('form.schedule_status.completed') => 'Completed',
            ];

            $completeStatusSelect = [
                config('form.complete_status.planned') => 'Planned',
                config('form.complete_status.waiting_for_complete') => 'Waiting for complete',
                config('form.complete_status.success') => 'Success',
                config('form.complete_status.unsuccess') => 'Unsuccess',
            ];

            // get all CA users
	        // TODO just send to working CA users
            $users = DB::table('users')->where('role', '=', config('user.role.CA'))->get();

            // bind data
            $raised_by_user = DB::table('users')->where('id', $input['raised_by_user_id'])->first()->name;
            $proposed_change_owner = DB::table('users')->where('id', $input['proposed_change_owner_user_id'])->first()->name;
            $change_type = $changeTypeSelect[$input['change_type']];
            $bussiness_priority = $businessPrioritySelect[$input['business_priority']];
            $status = $statusSelect[$input['status']];
            $complete_status = $completeStatusSelect[$input['complete_status']];

            $data = [
                ['field' => 'Reference', 'value' => $form_created->reference],
                ['field' => 'Raised by user', 'value' => $raised_by_user],
                ['field' => 'Contact no', 'value' => $input['contact_no']],
                ['field' => 'Change type', 'value' => $change_type],
                ['field' => 'Change title', 'value' => $input['change_title']],
                ['field' => 'Proposed change owner', 'value' => $proposed_change_owner],
                ['field' => 'Date raised', 'value' => $input['date_raised']],
                ['field' => 'Business priority', 'value' => $bussiness_priority],
                ['field' => 'Change start date', 'value' => $input['change_start_date']],
                ['field' => 'Change start time', 'value' => $input['change_start_time']],
                ['field' => 'Change end date', 'value' => $input['change_end_date']],
                ['field' => 'Change end time', 'value' => $input['change_end_time']],
                ['field' => 'Propsed change', 'value' => $input['proposed_change']],
                ['field' => 'Reason', 'value' => $input['reason']],
                ['field' => 'Risk assessment', 'value' => $input['risk_assessment']],
                ['field' => 'Rollback strategy', 'value' => $input['rollback_strategy']],
                ['field' => 'Test plan', 'value' => $input['test_plan']],
                ['field' => 'Authorisation signature date', 'value' => $input['authorisation_signature_date']],
                ['field' => 'Planned date', 'value' => $input['planned_date']],
                ['field' => 'Status', 'value' => $status],
                ['field' => 'Complete status', 'value' => $complete_status]
            ];

            // send mail
	        $subject = 'Request for change Form has been created';
            foreach ($users as $user) {
                if ($user->is_working) {
                    Mail::send('emails.email', compact('data', 'user', 'subject'), function ($message) use ($data, $user) {
                        $message->to($user->email);
                        $message->subject('Request for change Form has been created');
                    });
                }
            }

            $user = Auth::user();
	        Mail::send('emails.email', compact('data', 'user', 'subject'), function ($message) use ($data, $user) {
		        $message->to($user->email);
		        $message->subject('Request for change Form has been created');
	        });
			
			$proposed_owner = DB::table('users')->where('id', $input['proposed_change_owner_user_id'])->first();
			$raised_user = DB::table('users')->where('id', $input['raised_by_user_id'])->first();
			
	        if ($raised_user-> email != $user->email ){
				Mail::send('emails.email', compact('data', 'user', 'subject'), function ($message) use ($data, $raised_user) {
					$message->to($raised_user->email);
					$message->subject('Request for change Form has been created');
				});
			}			
			
	        if ($proposed_owner-> email != $user->email && $proposed_owner-> email != $raised_user->email ){
				Mail::send('emails.email', compact('data', 'user', 'subject'), function ($message) use ($data, $proposed_owner) {
					$message->to($proposed_owner->email);
					$message->subject('Request for change Form has been created');
				});
			}


			if ($input['smart_hub_impact']==config('form.smart_hub_impact.yes')){
	        	//send email if choose smart hub impact is Yes
				$subject = 'Smart Hub Impact Email Template';
				foreach ($users as $user) {	//send admin

					$data = [
						['smart_hub' => 'Hi '.$user->name.'!'.'.<br>'.'You raised change reference '.$form_created->reference
						.' and select YES to Smart Hub Impact. This change will need to be discussed at the next available OT CAB date which is '
						.$input['planned_date'].'<br>'
						.'Many Thanks!']
					];
					if ($user->is_working) {
						Mail::send('emails.email', compact('data', 'user', 'subject'), function ($message) use ($data, $user) {
							$message->to($user->email);
							$message->subject('Smart Hub Impact Email Template');
						});
					}
				}

				if ($raised_user-> email != $user->email ){
					$data = [
						['smart_hub' => 'Hi '.$raised_user->name.'!'.'.<br>'.'You raised change reference '.$form_created->reference
							.' and select YES to Smart Hub Impact. This change will need to be discussed at the next available OT CAB date which is '
							.$input['planned_date'].'<br>'
							.'Many Thanks!']
					];
					Mail::send('emails.email', compact('data', 'user', 'subject'), function ($message) use ($data, $raised_user) {
						$message->to($raised_user->email);
						$message->subject('Smart Hub Impact Email Template');
					});
				}

				if ($proposed_owner-> email != $user->email && $proposed_owner-> email != $raised_user->email ){
					$data = [
						['smart_hub' => 'Hi '.$proposed_owner->name.'!'.'.<br>'.'You raised change reference '.$form_created->reference
							.' and select YES to Smart Hub Impact. This change will need to be discussed at the next available OT CAB date which is '
							.$input['planned_date'].'<br>'
							.'Many Thanks!']
					];
					Mail::send('emails.email', compact('data', 'user', 'subject'), function ($message) use ($data, $proposed_owner) {
						$message->to($proposed_owner->email);
						$message->subject('Smart Hub Impact Email Template');
					});
				}

			}

            return redirect()->action('FormController@create')
                ->with('message', 'Create success');
        }

        return redirect()->action('FormController@create')
            ->withErrors(['error' => 'Create fail'])->withInput($input);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
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
     *
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
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	/**
	 * Approve waiting CAB forms
	 * @param Request $request
	 */
	public function form_approved(Request $request)
    {
        $data = $request->all();
        $form_id = $data['form_id'];
        $reason = $data['reason'];
        $approvedRejectedBy = $data['approved_rejected_by'];

        DB::table('forms')
            ->where('id', $form_id)
            ->update(
                [
                    'status' => config('form.status.approved'),
                    'approved_rejected_date' => date('Y-m-d'),
                    'approved_rejected_reason' => $reason,
                    'complete_status' => config('form.complete_status.waiting_for_complete'),
                    'approved_rejected_by' => $approvedRejectedBy
                ]
            );

        $form = Form::where('id', '=', $form_id)->first();
        //Log::info(var_dump($form));
        // send email to CR user
        //$user = DB::table('users')->where('id', '=', $form->author_user_id)->first();
        $user = $form->author;
        //var_dump($user->email);
        //exit();
        /*Mail::raw('', function($message) use($user){
            $message->to($user->email);
            $message->subject('Approved');
        });*/
        Mail::send('emails.approvedemail', array('user' => $user, 'subject' => 'Change Approved', 'reason' => $reason), function ($message) use ($data, $user) {
            $message->to($user->email);
            $message->subject('Change Approved');
        });
        echo json_encode(1);
    }

	/**
	 * Reject waiting CAB forms
	 * @param Request $request
	 */
	public function form_rejected(Request $request)
    {
        $data = $request->all();
        $form_id = $data['form_id'];
        $reason = $data['reason'];
	    $approvedRejectedBy = $data['approved_rejected_by'];

	    DB::table('forms')
            ->where('id', $form_id)
            ->update(
                [
                    'status' => config('form.status.rejected'),
                    'approved_rejected_date' => date('Y-m-d'),
                    'approved_rejected_reason' => $reason,
                    'complete_status' => config('form.complete_status.unsuccess'),
                    'approved_rejected_by' => $approvedRejectedBy
                ]
            );

        $form = Form::where('id', '=', $form_id)->first();

        // send email to CR user
        //$user = DB::table('users')->where('id', '=', $form->author_user_id)->first();
        $user = $form->author;
        Mail::send('emails.rejectemail', array('user' => $user, 'subject' => 'Change Rejected', 'reason' => $reason), function ($message) use ($data, $user) {
            $message->to($user->email);
            $message->subject('Change Rejected');
        });
        echo json_encode(1);
    }

	/**
	 * @param Request $request
	 *
	 * @return $this
	 */
	public function exportCSV(Request $request)
    {
        $specified_user = $request->input('specified_user');
        // parse date range string to $from and $to
        $rangeString = $request->get('range');
        $rangeArray = explode(" - ", $rangeString);

        $from = date('Y-m-d 00:00:00', strtotime($rangeArray[0]));
        $to = date('Y-m-d 23:59:59', strtotime($rangeArray[1]));

        // build statistic data
        $lastYear = date('Y-m-d H:i:s', strtotime("- 1 YEAR"));
        $lastQuarter = date('Y-m-d H:i:s', strtotime("- 3 MONTHS"));
        $lastMonth = date('Y-m-d H:i:s', strtotime("- 1 MONTHS"));
        $lastWeek = date('Y-m-d H:i:s', strtotime("- 1 WEEK"));

        // reuse build chart data function
        $year = FormService::buildChartDataForRange($lastYear, null, $specified_user);
        $quarter = FormService::buildChartDataForRange($lastQuarter, null, $specified_user);
        $month = FormService::buildChartDataForRange($lastMonth, null, $specified_user);
        $week = FormService::buildChartDataForRange($lastWeek, null, $specified_user);

        $statisticData = compact('year', 'quarter', 'month', 'week');

        if (-1 == $specified_user){
          $forms = FormService::getForms($from, $to);
        }else{
          $forms = FormService::getForms($from, $to, [['raised_by_user_id', '=', $specified_user]]);
        }
        // if ($request->input('export') == config('form.export_option.all')) {
        //     $forms = FormService::getForms($from, $to);
        // } else {
        //     $current_user_id = Auth::user()->id;
        //     $forms = FormService::getForms($from, $to, [['raised_by_user_id', '=', $current_user_id]]);
        // }

        // build csv string
        $csvString = FormService::buildCSV($forms, $statisticData,$specified_user);

        // write to file
        $fileName = md5(uniqid(rand(), true)) . ".csv";
        $bytes_written = File::put(config('form.export_path') . "/$fileName", $csvString);

        // return download file, then delete file
        $headers = ['Content-Type: text/csv'];

        return response()->download(config('form.export_path') . "/$fileName", $fileName, $headers)->deleteFileAfterSend(true);
    }

	/**
	 * @param Request $request
	 *
	 * @return $this|\Illuminate\Http\RedirectResponse
	 */
	public function importCSV(Request $request)
    {
        try {
            $file = $request->file('import_file');

            // if file not found, return with error message
            if (!$file) {
                return redirect()->action('DashboardController@importExport')
                    ->withErrors(['error' => 'File not found']);
            }

            $filePath = FormService::saveCSVFile($file);
            FormService::importForms($filePath);

            return redirect()->action('DashboardController@importExport')
                ->with(['message' => "Imported successfully"]);
        } catch (RuntimeException $exception) {
            return redirect()->action('DashboardController@importExport')
                ->withErrors(['error' => $exception->getMessage()]);
        }
    }

    /**
     * Store complete change form.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
	public function storeCompleteChangeForm( Request $request ) {
		DB::beginTransaction();
		try {
			// get parameters
			$input                              = $request->only( [ 'completion_notes', 'completion_signature', 'complete_status', 'complete_date' ] );
			$input['completion_signature_date'] = date( 'Y-m-d H:i:s' );
			$request->validate( [
				'completion_notes'     => 'required',
				'completion_signature' => 'required',
				'complete_status'      => 'required',
			] );
			$form_id            = $request->input( "form_id" );
			$input['is_closed'] = 1;
			$form               = Form::where( "id", $form_id )->first();
			$form->update( $input );
			DB::commit();

			$completeStatusSelect = [
				config( 'form.complete_status.planned' )              => 'Planned',
				config( 'form.complete_status.waiting_for_complete' ) => 'Waiting for complete',
				config( 'form.complete_status.success' )              => 'Success',
				config( 'form.complete_status.unsuccess' )            => 'Unsuccess',
				config( 'form.complete_status.bypass' )               => 'By pass',
			];

			$author     = $form->author;
			$detailData = [
				[ 'name' => 'Notes', 'value' => $input['completion_notes'] ],
				[ 'name' => 'Signature', 'value' => $input['completion_signature'] ],
				[ 'name' => 'Status', 'value' => $completeStatusSelect[ $input['complete_status'] ] ]
			];

			//send email to all CA user
			$CAUsers = User::where( 'role', config( 'user.role.CA' ) )->where( 'is_working', '=', 1 )->get();
			foreach ( $CAUsers as $CAUser ) {
				Mail::send( 'emails.complete_change_for_admin', array( 'form' => $form, 'receiver' => $CAUser, 'subject' => 'Change Completed', 'user' => $author, 'detailData' => $detailData ), function ( $message ) use ( $CAUser ) {
					$message->to( $CAUser->email );
					$message->subject( 'Change Completed' );
				} );
			}

			// Mail::send('emails.complete_change_for_user', array('form' => $form, 'subject' => 'Change Completed', 'user' => $author, 'detailData' => $detailData), function ($message) use ($author) {
			//   $message->to($author->email);
			//   $message->subject('Change Completed');
			// });

			//send email to proposed_change_owner_user_id
			$proposed_change_owner_user = User::where( 'role', config( 'user.role.CR' ) )
			                                  ->where( 'id', '=', $form->proposed_change_owner_user_id )->first();
			if ( $email_by_proposed = @$proposed_change_owner_user->email ) {
				Mail::send( 'emails.complete_change_for_user', array( 'form' => $form, 'subject' => 'Change Completed', 'user' => $proposed_change_owner_user, 'detailData' => $detailData ), function ( $message ) use ( $email_by_proposed ) {
					$message->to( $email_by_proposed );
					$message->subject( 'Change Completed' );
				} );
			}
			
			//send email to raised_by_user_id
			$raised_by_user = User::where( 'role', config( 'user.role.CR' ) )
			                      ->where( 'id', '=', $form->raised_by_user_id )->first();
			if ( $email_by_user = @$raised_by_user->email ) {
				Mail::send( 'emails.complete_change_for_user', array( 'form' => $form, 'subject' => 'Change Completed', 'user' => $proposed_change_owner_user, 'detailData' => $detailData ), function ( $message ) use ( $email_by_user ) {
					$message->to( $email_by_user );
					$message->subject( 'Change Completed' );
				} );
			}			
			
			if ( $form != null ) {
				return redirect()->action( 'DashboardController@completeChange' )->with( 'message', 'Change success' );
			} else {
				return redirect()->action( 'DashboardController@completeChange' )->with( 'error', 'Change fail' )->withInput( $input );
			}

		} catch ( \Exception $e ) {
			DB::rollback();

			return false;
		}
	}

    public function ajaxToggleNonChange(Request $request, $id) {
        $toggleDate = $request->get('date');
    	$form = Form::find($id);

		$nonChangeDates = $form->nonChangeDates();
		$nonChangeReverse = array_flip($nonChangeDates);
	    if (array_has($nonChangeReverse, $toggleDate)) {
	    	$index = $nonChangeReverse[$toggleDate];
	    	unset($nonChangeDates[$index]);
	    }
	    else {
	    	$nonChangeDates[] = $toggleDate;
	    }

	    FormService::update($form, ['none_change_dates' => json_encode($nonChangeDates)]);
	    return 1;
    }
}
