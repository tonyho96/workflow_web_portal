<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Services\FormService;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Services\UserService;
use DB;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use App\Services\FileService;

class DashboardController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard/index');
    }

    /**
     * Show the change summary dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeSummary()
    {
        //$forms = Form::where("status",config('form.status.approved'))->get()->all();
	    $forms = Form::all();

        return view('dashboard/change-summary', compact("forms"));
    }

    public function removeUploadedFile(Request $request)
    { 
        $data = $request->all();
        DB::beginTransaction();
        try {
            $file = File::where('form_id',$data['file_form_id'])->where('file_name',$data['file_file_name'])->first();
            $file->delete();
            DB::commit();
            return back()->with('message', 'Delete Success!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', trans('Cannot delete the selected file'));
        }

    }

    public function changeSummaryDetail($id)
    {
        $users = User::all();
	    $form = Form::where("reference",$id)->first();
	    $files = File::where('form_id',$form->id)->get();

	    return view('dashboard/change-summary-detail', compact("form","users","files"));
    }

    public function editSummaryDetail(Request $request,$id)
    {
        $data = $request->all();
        DB::beginTransaction();
        try {
        
        $form = Form::where("reference",$id)->first();
        // $forms = Form::all();

        $formData = 
        [
            'raised_by_user_id'             => $data['raised_by_user_id'],
            'contact_no'                    => $data['contact_no'],
            'change_type'                   => $data['change_type'],
            'change_title'                  => $data['change_title'],
            'proposed_change_owner_user_id' => $data['proposed_change_owner_user_id'],
            'business_priority'             => $data['business_priority'],
            'change_start_date'             => $data['change_start_date'],
            'change_start_time'             => $data['change_start_time'],
            'change_end_date'               => $data['change_end_date'],
            'change_end_time'               => $data['change_end_time'],
            'proposed_change'               => $data['proposed_change'],
            'reason'                        => $data['reason'],
            'risk_assessment'               => $data['risk_assessment'],
            'rollback_strategy'             => $data['rollback_strategy'],
            'test_plan'                     => $data['test_plan'],
            'authorisation_signature'       => $data['authorisation_signature'],
	        'planned_date'                  => $data['planned_date']
        ];

        if (Auth::user()->role == config('user.role.CA')){
            $formData['status']  = $data['status'];
            $formData['complete_status'] = $data['complete_status'];
            if (!empty($data['complete_date']))
                $formData['complete_date'] = $data['complete_date'];
        }

        if ($data['change_type'] == config('form.change_type.self_approve')) {
            $formData['status'] = config('form.status.approved');
            $formData['approved_rejected_date'] = date('Y-m-d');
            $formData['approved_rejected_reason'] = '';
            //$formData['complete_status'] = config('form.complete_status.waiting_for_complete');
            $formData['approved_rejected_by'] = Auth::user()->id;
        }


        $form->update($formData);

        if ($request->file('files')) {
            foreach ( $request->file( 'files' ) as $file ) {
                $file = FileService::save( $form, $file );
            }
        }
        //return view('dashboard/change-summary-detail', compact("form"),compact('users'));
        DB::commit();

        return back()->with('message', 'Form Update Successfully!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->withInput($data)->with('error', 'Form Update Failed!');
        }
    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function forwardSchedule()
    {

        $data = FormService::buildForwardScheduleData();


        return view('dashboard/forward-schedule', compact('data'));
    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function importExport()
    {
	    $crUserData = User::where('role', config('user.role.CR'))->get();
	    $crUsers = ['0' => 'All'];
	    foreach ($crUserData as $user) {
		    $crUsers[$user->id] = $user->name;
	    }

	    $overviewUserId = isset($_GET['overview']) ? $_GET['overview'] : null;

	    $lastYear = date('Y-m-d H:i:s', strtotime("- 1 YEAR"));
        $lastQuarter = date('Y-m-d H:i:s', strtotime("- 3 MONTHS"));
        $lastMonth = date('Y-m-d H:i:s', strtotime("- 1 MONTHS"));
        $lastWeek = date('Y-m-d H:i:s', strtotime("- 1 WEEK"));

        $year = FormService::buildChartDataForRange($lastYear, null, $overviewUserId);
        $quarter = FormService::buildChartDataForRange($lastQuarter, null, $overviewUserId);
        $month = FormService::buildChartDataForRange($lastMonth, null, $overviewUserId);
        $week = FormService::buildChartDataForRange($lastWeek, null, $overviewUserId);

        $chartData = compact('year', 'quarter', 'month', 'week');
        $a = UserService::listAllUsers();
        $specified_user[-1] = 'All';
        
        foreach ($a as $user ){
          $specified_user[$user['id']] = $user['name'];
        }

        return view('dashboard/import-export', compact('chartData','specified_user', 'crUsers'));
    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function showApproveRejectForm()
    {
        $forms = Form::all();
        return view(
            'dashboard/approve-reject',
            compact('forms')
        );
    }

    /**
     * Show the complete change dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function completeChange()
    {
        $id = Auth::user()->id;

        // validate this CR user
        $user = User::where("id", $id)->first();
        if ($user != null) {
            $forms = Form::where("complete_status", config('form.complete_status.waiting_for_complete'))->where("proposed_change_owner_user_id", $id)->get()->all();
            return view('dashboard/complete-change', compact("forms"));
        } else {
            $forms = array();
            return view('dashboard/complete-change', compact("forms"));
        }
    }
}
