<?php

namespace App\Services;


use App\Models\Form;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use League\Csv\Reader;
use League\Csv\Writer;
use RuntimeException;

class FormService
{
	/**
	 * validate form
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public static function validate($input)
    {
        $ruleValidates = [
            'raised_by_user_id' => 'required',
            'contact_no' => 'required',
            'change_type' => 'required',
            'change_title' => 'required',
            'proposed_change_owner_user_id' => 'required',
            'date_raised' => 'required',
            'business_priority' => 'required',
            'change_start_date' => 'required',
            'change_start_time' => 'required',
            'change_end_date' => 'required',
            'change_end_time' => 'required',
            'status' => 'required',
            'complete_status' => 'required',
            'proposed_change' => 'required',
            'reason' => 'required',
            'risk_assessment' => 'required',
            'rollback_strategy' => 'required',
            'test_plan' => 'required',
            'authorisation_signature' => 'required',
            /*'authorisation_signature_date'    => 'required',
            'completion_notes'                => 'required',
            'completion_signature'            => 'required',
            'completion_signature_date'       => 'required',*/
            'planned_date' => 'required',
//        	'approved_rejected_reason'		  => 'required',
//        	'approved_rejected_date'		  => 'required'
            /*'status'						  => 'required',
            'complete_status'				  => 'required',*/
        ];

        return Validator::make($input, $ruleValidates);
    }

	/**
	 * create form
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public static function create($input)
    {
        DB::beginTransaction();
        try {
            $input['author_user_id'] = Auth::user()->id;
            $input['reference'] = FormService::getNextReference();
            $form = Form::create($input);
            DB::commit();
            return $form;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

	public static function update($form, $input)
	{
		DB::beginTransaction();
		try {
			$form->update($input);
			DB::commit();
			return $form;
		} catch (\Exception $e) {
			DB::rollback();
			return false;
		}
	}

	/**
	 * get forms with conditions
	 *
	 * @param $from
	 * @param null $to
	 * @param array $conditions
	 *
	 * @return mixed
	 */
	public static function getForms($from, $to = null, $conditions = [])
    {
        $formQuery = Form::where('created_at', '>=', $from);

        if ($to) {
            $formQuery = $formQuery->where('created_at', '<=', $to);
        }

        foreach ($conditions as $condition) {
            if ($condition[1] == 'in') {
                $formQuery = $formQuery->whereIn($condition[0], $condition[2]);
            } else {
                $formQuery = $formQuery->where($condition[0], $condition[1], $condition[2]);
            }
        }

        return $formQuery->get();
    }

	/**
	 * Build chart data - map with chartjs
	 *
	 * @param $from
	 * @param null $to
	 *
	 * @return array
	 */
	public static function buildChartDataForRange($from, $to = null, $userId = null)
    {
        
		if (-1 != $userId)
			$CRUserIds = [$userId];
		else
			$CRUserIds = User::where('role', '=', config('user.role.CR'))->pluck('id')->toArray();

	    $allForms = self::getForms($from, $to)->count();
        $raisedByCRForms = self::getForms($from, $to, [['raised_by_user_id', 'in', $CRUserIds]])->count();
        $successByCRForms = self::getForms(
            $from,
            $to,
            [
                ['raised_by_user_id', 'in', $CRUserIds],
                ['complete_status', '=', config('form.complete_status.success')]
            ]
        )->count();

        $conditions = [
        	['status', '=', config('form.status.pending_CAB')],
        ];
        if ($userId)
        	$conditions[] = ['raised_by_user_id', '=', $userId];
        $pendingCABForms = self::getForms($from, $to, $conditions)->count();

	    $conditions = [
		    ['status', '=', config('form.status.rejected')],
	    ];
	    if ($userId)
		    $conditions[] = ['raised_by_user_id', '=', $userId];
        $rejectedForms = self::getForms($from, $to, $conditions)->count();

        return compact('allForms', 'raisedByCRForms', 'successByCRForms', 'pendingCABForms', 'rejectedForms');
    }

	/**
	 * @param $forms
	 * @param $statisticInfo
	 *
	 * @return \League\Csv\AbstractCsv|static
	 */
	public static function buildCSV($forms, $statisticInfo,$specified_user)
    {
        $records = [];
	      if (-1 == $specified_user ){
					$records[] = ['', 'Week', 'Month', 'Quarter', 'Year'];
					$records[] = ['Number of changes raised by users', $statisticInfo['week']['raisedByCRForms'], $statisticInfo['month']['raisedByCRForms'], $statisticInfo['quarter']['raisedByCRForms'], $statisticInfo['year']['raisedByCRForms']];
					$records[] = ['Number of completed successfully changes by users', $statisticInfo['week']['successByCRForms'], $statisticInfo['month']['successByCRForms'], $statisticInfo['quarter']['successByCRForms'], $statisticInfo['year']['successByCRForms']];
					$records[] = ['Total number of pending CAB changes', $statisticInfo['week']['pendingCABForms'], $statisticInfo['month']['pendingCABForms'], $statisticInfo['quarter']['pendingCABForms'], $statisticInfo['year']['pendingCABForms']];
					$records[] = ['Total number of rejected changes', $statisticInfo['week']['rejectedForms'], $statisticInfo['month']['rejectedForms'], $statisticInfo['quarter']['rejectedForms'], $statisticInfo['year']['rejectedForms']];
					$records[] = [''];
	      }else{
					$specified_user_name = User::find($specified_user)->name;
					$records[] = ['', 'Week', 'Month', 'Quarter', 'Year'];
					$records[] = ['Number of changes raised by '.$specified_user_name, $statisticInfo['week']['raisedByCRForms'], $statisticInfo['month']['raisedByCRForms'], $statisticInfo['quarter']['raisedByCRForms'], $statisticInfo['year']['raisedByCRForms']];
					$records[] = ['Number of completed successfully changes by '.$specified_user_name, $statisticInfo['week']['successByCRForms'], $statisticInfo['month']['successByCRForms'], $statisticInfo['quarter']['successByCRForms'], $statisticInfo['year']['successByCRForms']];
					$records[] = ['Total number of pending CAB changes', $statisticInfo['week']['pendingCABForms'], $statisticInfo['month']['pendingCABForms'], $statisticInfo['quarter']['pendingCABForms'], $statisticInfo['year']['pendingCABForms']];
					$records[] = ['Total number of rejected changes', $statisticInfo['week']['rejectedForms'], $statisticInfo['month']['rejectedForms'], $statisticInfo['quarter']['rejectedForms'], $statisticInfo['year']['rejectedForms']];
					$records[] = [''];
	      }
        // statistic
        // $records[] = ['', 'Week', 'Month', 'Quarter', 'Year'];
        // $records[] = ['Number of changes raised by particular user', $statisticInfo['week']['raisedByCRForms'], $statisticInfo['month']['raisedByCRForms'], $statisticInfo['quarter']['raisedByCRForms'], $statisticInfo['year']['raisedByCRForms']];
        // $records[] = ['Number of completed successfully changes by particular user', $statisticInfo['week']['successByCRForms'], $statisticInfo['month']['successByCRForms'], $statisticInfo['quarter']['successByCRForms'], $statisticInfo['year']['successByCRForms']];
        // $records[] = ['Total number of pending CAB changes', $statisticInfo['week']['pendingCABForms'], $statisticInfo['month']['pendingCABForms'], $statisticInfo['quarter']['pendingCABForms'], $statisticInfo['year']['pendingCABForms']];
        // $records[] = ['Total number of rejected changes', $statisticInfo['week']['rejectedForms'], $statisticInfo['month']['rejectedForms'], $statisticInfo['quarter']['rejectedForms'], $statisticInfo['year']['rejectedForms']];
        // $records[] = [''];

        // main data
        $records[] = [
            "id",
            "raised_by_user_name",
            "contact_no",
            "change_type",
            "change_title",
            "proposed_change_owner_user_name",
            "date_raised",
            "business_priority",
            "change_start_date",
            "change_start_time",
            "change_end_date",
            "change_end_time",
            "proposed_change",
            "reason",
            "risk_assessment",
            "rollback_strategy",
            "test_plan",
            "authorisation_signature",
            "authorisation_signature_date",
            "completion_notes",
            "completion_signature",
            "completion_signature_date",
            "author_user_name",
            "reference",
            "status",
            "complete_status",
            "planned_date",
            "approved_rejected_reason",
            "approved_rejected_date",
            "approved_rejected_by",
            "created_at",
            "updated_at"
        ];

        foreach ($forms as $form) {
            $records[] = [
                $form->id,
                $form->raisedBy ? $form->raisedBy->name : '',
                $form->contact_no,
                $form->changeType(),
                $form->change_title,
                $form->proposedChangeOwner ? $form->proposedChangeOwner->name : '',
                $form->date_raised,
                $form->businessPriority(),
                $form->change_start_date,
                $form->change_start_time,
                $form->change_end_date,
                $form->change_end_time,
                $form->proposed_change,
                $form->reason,
                $form->risk_assessment,
                $form->rollback_strategy,
                $form->test_plan,
                $form->authorisation_signature,
                $form->authorisation_signature_date,
                $form->completion_notes,
                $form->completion_signature,
                $form->completion_signature_date,
                $form->author ? $form->author->name : '',
                $form->reference,
                $form->status(),
                $form->completeStatus(),
                $form->planned_date,
                $form->approved_rejected_reason,
                $form->approved_rejected_date,
                $form->approved_rejected_by,
                $form->created_at,
                $form->updated_at,
            ];
        }

        // insert data
        $csv = Writer::createFromString('');
        $csv->insertAll($records);

        // return $csv object
        return $csv;
    }

	/**
	 * @param $file
	 *
	 * @return bool|string
	 */
	public static function saveCSVFile($file)
    {
        try {
            if (empty($file))
                return false;

            // generate file name
            $filename = str_random(20) . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = config('form.import_path');

            // save file
            $file->move($destinationPath, $filename);

            // return file path
            return $destinationPath . "/$filename";
        } catch (Exception $exception) {
            return false;
        }
    }

	/**
	 * @param $csvRow
	 *
	 * @return array
	 */
	public static function parseCSVRow($csvRow)
    {
        $raisedByUserName = $csvRow[config('form.imported_csv.raised_by_user_name')];
        $contactNo = $csvRow[config('form.imported_csv.contact_no')];
        $changeType = $csvRow[config('form.imported_csv.change_type')];
        $changeTitle = $csvRow[config('form.imported_csv.change_title')];
        $proposedChangeOwnerName = $csvRow[config('form.imported_csv.proposed_change_owner_user_name')];
        $dateRaised = date('Y-m-d', strtotime(strtr($csvRow[config('form.imported_csv.date_raised')], '/', '-')));
        $businessPriority = $csvRow[config('form.imported_csv.business_priority')];
        $changeStartDate = date('Y-m-d', strtotime(strtr($csvRow[config('form.imported_csv.change_start_date')], '/', '-')));
        $changeStartTime = $csvRow[config('form.imported_csv.change_start_time')];
        $changeEndDate = date('Y-m-d', strtotime(strtr($csvRow[config('form.imported_csv.change_end_date')], '/', '-')));
        $changeEndTime = $csvRow[config('form.imported_csv.change_end_time')];
        $proposedChange = $csvRow[config('form.imported_csv.proposed_change')];
        $reason = $csvRow[config('form.imported_csv.reason')];
        $riskAssessment = $csvRow[config('form.imported_csv.risk_assessment')];
        $rollBackStrategy = $csvRow[config('form.imported_csv.rollback_strategy')];
        $testPlans = $csvRow[config('form.imported_csv.test_plan')];
        $authorisationSignature = $csvRow[config('form.imported_csv.authorisation_signature')];

        // bind and check imported data
        $raisedByUser = User::where('name', '=', $raisedByUserName)->first();
        if (!$raisedByUser) {
            throw new RuntimeException("User $raisedByUserName not found");
        } else {
            $raisedByUser = $raisedByUser->id;
        }

        if (!in_array($changeType, array_keys(config('form.change_type')))) {
            throw new RuntimeException("Invalid change type: $changeType");
        } else {
            $changeType = config("form.change_type.$changeType");
        }

        $proposedChangeOwner = User::where('name', '=', $proposedChangeOwnerName)->first();
        if (!$proposedChangeOwner) {
            throw new RuntimeException("User $proposedChangeOwner not found");
        } else {
            $proposedChangeOwner = $proposedChangeOwner->id;
        }

        if (!in_array($businessPriority, array_keys(config('form.business_priority')))) {
            throw new RuntimeException("Invalid priority: $businessPriority");
        } else {
            $businessPriority = config("form.business_priority.$businessPriority");
        }

        // unset all default csv fields
        $csvRow = [];

        // replace fields
        $csvRow['raised_by_user_id'] = $raisedByUser;
        $csvRow['contact_no'] = $contactNo;
        $csvRow['change_type'] = $changeType;
        $csvRow['change_title'] = $changeTitle;
        $csvRow['proposed_change_owner_user_id'] = $proposedChangeOwner;
        $csvRow['date_raised'] = $dateRaised;
        $csvRow['business_priority'] = $businessPriority;
        $csvRow['change_start_date'] = $changeStartDate;
        $csvRow['change_start_time'] = $changeStartTime;
        $csvRow['change_end_date'] = $changeEndDate;
        $csvRow['change_end_time'] = $changeEndTime;
        $csvRow['proposed_change'] = $proposedChange;
        $csvRow['reason'] = $reason;
        $csvRow['risk_assessment'] = $riskAssessment;
        $csvRow['rollback_strategy'] = $rollBackStrategy;
        $csvRow['test_plan'] = $testPlans;
        $csvRow['authorisation_signature'] = $authorisationSignature;
        $csvRow['authorisation_signature_date'] = date('Y-m-d');
        $csvRow['planned_date'] = date('Y-m-d',strtotime('next wednesday'));
        $csvRow['status'] = config('form.status.pending_CAB');
        $csvRow['complete_status'] = config('form.complete_status.planned');
        $csvRow['author_user_id'] = Auth::user()->id;
        $csvRow['reference'] = FormService::getNextReference();

        return $csvRow;
    }

	/**
	 * @param $filePath
	 */
	public static function importForms($filePath)
    {
        // read csv file
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords(); //returns all the CSV records as an Iterator object

        //fetch first row
        $records->next();

        // if this row is valid
        while ($records->valid()) {
            // get current row data
            $currentData = $records->current();

            $form_id = $currentData['id'];
            $form = Form::find($form_id);
            if ($form_id > 0 && !$form) {
                throw new RuntimeException("Form not found: $form_id");
            }

            $parsedData = self::parseCSVRow($currentData);
            if (empty($form_id)) { // insert new form
                $form = Form::create($parsedData);
            } else { // update existing form
                $form->update($parsedData);
            }

            // fetch next row
            $records->next();
        }
    }

	/**
	 * @return array
	 */
	public static function buildForwardScheduleData() {
		$result = [];

		// get oldest form
		$oldestForm = Form::orderBy( 'created_at' )->first();
		if ( ! $oldestForm ) {
			return [];
		}

		// to get oldest wednesday
		if ( $oldestForm ) {
			$oldestMonday = date( 'Y-m-d', strtotime( 'previous monday', strtotime( $oldestForm->created_at ) ) );
		} else {
			$oldestMonday = date( 'Y-m-d', strtotime( 'previous monday' ) );
		}

		// get latest form
		$latestForm = Form::orderBy( 'updated_at', 'desc' )->first();

		$latestUpdatedDate = $latestForm->updated_at;
		$latestEndDate     = Form::orderBy( 'change_end_date', 'desc' )->first()->change_end_date;
		$latestDate        = $latestUpdatedDate > $latestEndDate ? $latestUpdatedDate : $latestEndDate;
		// to get latest wednesday
		if ( $latestForm ) {
			$latestSunday = date( 'Y-m-d', strtotime( "next sunday", strtotime( $latestDate ) ) );
		} else {
			$latestSunday = date( 'Y-m-d', strtotime( 'next sunday' ) );
		}

		$forms           = Form::all();
		$result['forms'] = [];
		foreach ( $forms as $form ) {
			$formData = [
				'id'               => $form->id,
				'reference'        => $form->reference,
				'title'            => $form->change_title,
				'planned'          => $form->planned_date,
				'start_date'       => $form->change_start_date,
				'end_date'         => $form->change_end_date,
				'started'          => $form->change_start_date,
				'completed'        => '',
				'failed'           => '',
				'non_change'       => '',
				'isOpened'         => true
			];

			// if this form was approved and has been completed
			if ( $form->status == config( 'form.status.approved' ) && $form->complete_status == config( 'form.complete_status.success' ) ) {
				$completedDate = date( 'Y-m-d', strtotime( $form->complete_date ) );
				$formData['completed'] = $completedDate;
				$formData['isOpened']  = false;

				if ($completedDate > $latestSunday)
					$latestSunday = date( 'Y-m-d', strtotime( "next sunday", strtotime( $completedDate ) ) );
				if ($form->change_type != config('form.change_type.bypass')) {
					$formData['start_date'] = '';
					$formData['end_date']   = '';
				}
			}
			// if this form was approved but un-success
			if ( $form->complete_status == config( 'form.complete_status.unsuccess' ) ) {
				$failedDate = date( 'Y-m-d', strtotime( $form->complete_date ) );

				$formData['failed']   = $failedDate;
				$formData['isOpened'] = false;

				if ($failedDate > $latestSunday)
					$latestSunday = date( 'Y-m-d', strtotime( "next sunday", strtotime( $failedDate ) ) );
				$formData['start_date'] = '';
				$formData['end_date'] = '';
			}
			// if this form was succeeded by CA
			if ( $form->change_type == config( 'form.change_type.bypass' ) ) {
				//$bypass_date = date( 'Y-m-d', strtotime( $form->date_raised ) );
				//$formData['non_change'] = $bypass_date;
                $formData['non_change'] = 'non_change';
				$formData['isOpened']   = false;

				//if ($bypass_date > $latestSunday)
				//	$latestSunday = date( 'Y-m-d', strtotime( "next sunday", strtotime( $bypass_date ) ) );
			}

			if ( $latestForm ){
				if(!empty($formData['start_date']) && $formData['start_date'] <  $oldestMonday ){
					$oldestMonday = date( 'Y-m-d', strtotime( 'previous monday', strtotime($formData['start_date']  ) ) );
				}
				if(!empty($formData['completed']) && $formData['completed'] <  $oldestMonday ){
					$oldestMonday = date( 'Y-m-d', strtotime( 'previous monday', strtotime($formData['completed']  ) ) );
				}
			}

			$result['forms'][] = $formData;
		}

		$result['oldestMonday'] = $oldestMonday;
		$result['latestSunday'] = $latestSunday;

		return $result;
	}


	/**
	 * @return string
	 */
	public static function getNextReference()
    {
    	// get latest form
        $latestForm = Form::orderBy('reference', 'desc')->first();
        if ($latestForm && !empty($latestForm->reference)) {
            $reference = $latestForm->reference;
        } else {
            $reference = 'RFC0300';
        }

        $number = intval(substr($reference, 3));
        // get next number
        $nextNumber = $number + 1;
        if ($nextNumber < 1000) {
            $nextNumber = '0' . $nextNumber;
        }

        // add prefix
        return 'RFC' . $nextNumber;

    }
}
