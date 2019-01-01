<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $users = UserService::listUser([
            // 'role'       => config( 'user.role.CA' )
        ]);
        return view('users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // get params and create validator
        $input = $request->all();
        $validate = UserService::validate($input);
        if ($validate->fails()) {
            return redirect()->action('UserController@create')
                ->withErrors($validate)->withInput($input)->with('message', $validate->errors()->first())->with('alert-class', 'alert-danger');

        }

        // create user
        if (UserService::create($input)) {
            return redirect()->action('UserController@index')
                ->with('message', trans('New User Created!'));
        }

        // if create fail
        return redirect()->action('UserController@index')
            ->with('error', trans('False to Create New User!'));
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
        $roles = UserService::getRoles();
        $user = User::find($id);
        return view('users.edit', ['user' => $user]);
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
        $user = User::find($id);
        return view('users.edit', ['user' => $user]);
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
        // get user
        $user = User::find($id);
        if (!$user) {
            return redirect()->action('UserController@index')
                ->with('error', trans('labels.update_false'));
        }

        // get params and validate
        $input = $request->all();
        $validate = UserService::validate($input, $user);
        if ($validate->fails()) {
            return redirect()->action('UserController@edit', $user->id)
                ->withErrors($validate)->withInput($input);
        }

        // update user
        if (UserService::update($input, $user)) {
            return redirect()->action('UserController@index')
                ->with('message', 'Update Success');
        }

        // unknown error
        return redirect()->action('UsersController@edit')
            ->with('error', trans('labels.update_false'));
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
        $user = User::find($id);
        if (UserService::delete($user)) {
            return back()->with('message', 'Delete Success!');
        }

        return back()->with('error', trans('labels.integrity_constraint_violation'));
    }

    public function approveUser($id)
    {
        //
  
        $user = User::find($id);
        if (UserService::approve($user)) {
            Mail::send('emails.new_user', ['user' => $user, 'subject' => 'New User Confirmation'], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('New User Confirmation');
            });
            return redirect()->action('UserController@index')->with('message', 'User Approved!');
        }
 
        return redirect()->action('UserController@edit')->with('error', trans('User Approve Failed!'));
    }
}
