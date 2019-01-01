<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use App\Services\UserService;



class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/forms/create';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => config('user.role.CR'),
            'team' => $data['team'],
            'is_approved' => false,
        ]);
    }

    protected function registered( Request $request, $user ) {
	    // Mail::send('emails.new_user', ['user' => $user, 'tmp_password' => $_SESSION['tmp_password'], 'subject' => 'New User Confirmation'], function ($message) use ($user) {
		//     $message->to($user->email);
		//     $message->subject('New User Confirmation');
	    // });
        //unset($_SESSION['tmp_password']);
        $working_ca_users = UserService::listWorkingCaUsers();
        $ca_emails = [];
        $ca_names = [];
        foreach($working_ca_users as $working_ca){
            array_push($ca_emails,$working_ca->email);
           
        }
        Mail::send('emails.inform_ca', ['user' => $user, 'approve_link' => action('UserController@approveUser',['id' => $user->id]), 'subject' => 'New User Confirmation'], function ($message) use ($ca_emails) {
		    $message->to($ca_emails);
		    $message->subject('New User Confirmation');
	    });
       
        // $_SESSION['tmp_password'] = $data['password'];
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
 
        event(new Registered($user = $this->create($request->all())));
 
        // $this->guard()->login($user);
        
        return $this->registered($request, $user)
            ?: redirect('/login')->with('message', trans('Thank you for registering, please await admin approval for signing up'));
    }
}
