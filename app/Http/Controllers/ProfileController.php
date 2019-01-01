<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Models\User;
use Hash;
use Session;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private function validator_changepass($data)
    {
        return Validator::make($data, [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_pass' => 'required|min:6|same:new_password',
        ]);
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        return view('dashboard/profile/index', ['user' => $user]);

    }

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function changePasswordForm()
    {
        return view('dashboard/change-password/index');
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function update(Request $request)
    {
        //
        $user = Auth::user();
        $inputs = $request->all();
        $user_id = $user->id;
        $obj_user = User::find($user_id);
        $obj_user->name = $inputs['name'];
        $obj_user->email = $inputs['email'];
        $obj_user->save();

        return redirect('dashboard/profile');
    }

	/**
	 * @param Request $request
	 *
	 * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function changePassword(Request $request)
    {
        $user = Auth::user();
        $inputs = $request->all();
        $validator = $this->validator_changepass($inputs);

        if ($validator->fails()) {
            return redirect()->action('UserController@changePasswordForm')
                ->withErrors($validator)->withInput($inputs);
        }
        $cur_password = $inputs['old_password'];
        $new_password = $inputs['new_password'];
        $confirm_new_password = $inputs['confirm_pass'];

        // validate password
        if ($new_password === $confirm_new_password) {
            if (Hash::check($cur_password, $user->password)) {
                $user_id = $user->id;
                $obj_user = User::find($user_id);
                $obj_user->password = Hash::make($new_password);
                $obj_user->save();
                //return response()->json(["result"=>true]);
                Session::flash('message', 'Password changed successfully!');
                Session::flash('alert-class', 'alert-success');
                return redirect('dashboard/change-password');
            } else {
                //return response()->json(["result"=>false]);
                Session::flash('message', 'Current password is incorrect!');
                Session::flash('alert-class', 'alert-danger');
                return redirect('dashboard/change-password');
            }
        }

    }


}
