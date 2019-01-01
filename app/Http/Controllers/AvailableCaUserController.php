<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Gate;

class AvailableCaUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            if ($this->user->isCA() == false) {
                exit();
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $users = UserService::listUser([
            'role' => config('user.role.CA'),
            'is_working' => true
        ]);
        return view('available-ca-users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = array();

        $users_list = UserService::listNotWorkingCaUsers();
        foreach ($users_list as $key => $value) {
            $users[$value->id] = $value->name;
        }
        return view('available-ca-users.create', compact('users'));
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
        $input = $request->all();
        //dd($input);
        //$app->end();

        $validate = UserService::validateUpdateUserToWorking($input);

        if ($validate->fails()) {
            return redirect()->action('AvailableCaUserController@create')
                ->withErrors($validate)->withInput($input)->with('message', $validate->errors()->first())->with('alert-class', 'alert-danger');

        }
        $id = $input['user_ca_not_working'];
        if (UserService::updateUserWorking($id, 1)) {
            return redirect()->action('AvailableCaUserController@index')
                ->with('message', trans('message.create_success'));
        }

        return redirect()->action('AvailableCaUserController@index')
            ->with('error', trans('message.create_fail'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    	// Working Ca Users Quantity Min Is One
        if (UserService::listWorkingCaUsers()->count() == 1) {
            return back()->withErrors(['error' => trans('message.constraint_min_as_delete_user_ca_working')]);
        }

        // set this CA user as working user
        if (UserService::updateUserWorking($id, 0)) {
            return back()->with('message', trans('message.destroy_success'));
        }

        return back()->with('error', trans('message.destroy_fail'));
    }
}
