<?php

namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;

class UserService
{
    /**
     * Show list users CA working.
     *
     * @return Illuminate\Database\Query\Builder
     */
    public static function listWorkingCaUsers()
    {
        return User::select("id", "name","email")->where("role", config('user.role.CA'))->where("is_working", true)->get();
    }

    /**
     * Show list users CA not working.
     *
     * @return Illuminate\Database\Query\Builder
     */
    public static function listNotWorkingCaUsers()
    {
        return User::select("id", "name")->where("role", config('user.role.CA'))->where("is_working", false)->get();
    }

    /**
     * change CA user to CA user working.
     * @param id is user id
     * @param is_working is working or not working
     * @return boolean
     */
    public static function updateUserWorking($id, $is_working)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            $userData = [
                'is_working' => $is_working,
            ];
            $user->update($userData);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();

            return false;
        }
    }

    /**
     * validate add CA user working
     * @param  input
     * @return boolean
     */
    public static function validateUpdateUserToWorking($input)
    {
        $ruleValdates = [
            'user_ca_not_working' => 'required',
        ];
        return Validator::make($input, $ruleValdates);
    }

	/**
	 * @return array
	 */
	public static function getRoles()
    {
        $result = [];
        foreach (config('user.roles') as $key => $value) {
            $result[$value] = 'users.roles.' . $key;
        }

        return $result;
    }

    /**
  	 * @return array
  	 */
  	public static function listAllUsers()
      {
          $users = User::orderBy('id', 'desc');
          return $users->get();
      }

	/**
	 * @param $conditions
	 * @param int $paginate
	 *
	 * @return mixed
	 */
	public static function listUser($conditions, $paginate = 10)
    {
        $users = User::orderBy('id', 'desc');
        foreach ($conditions as $key => $value) {
            $users->where($key, $value);
        }

        if ($paginate)
            return $users->paginate(10);
        return $users->get();
    }

	/**
	 * @param $input
	 * @param null $user
	 *
	 * @return mixed
	 */
	public static function validate($input, $user = null)
    {
        $ruleValdates = [
            'email' => 'required|email|max:255|unique:users',
            'name' => 'required',
//            'role' => 'in:' . implode(',', config('user.roles')),
        ];

        if ($user) {
            $ruleValdates['email'] .= ',id,' . $user->id;
        } else {
//            $ruleValdates['password'] = 'required|confirmed';
        }

        return Validator::make($input, $ruleValdates);
    }

	/**
	 * @param $input
	 *
	 * @return bool
	 */
	public static function create($input)
    {
        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => bcrypt($input['password']),
                'role' => $input['role'],
                'team' => $input['team'],
                'remember_token' => null,
                'is_approved' => 0,
            ]);

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            die($e->getMessage());
            return false;
        }
    }

	/**
	 * @param $input
	 * @param $user
	 *
	 * @return bool
	 */
	public static function update($input, $user)
    {
        DB::beginTransaction();
        try {
            $userData = [
                'name' => $input['name'],
                'email' => $input['email'],
                'role' => $input['role'],
                'team' => $input['team'],
            ];
            if (strlen($input['password'])) {
                $userData['password'] = bcrypt($input['password']);
            }

            $user->update($userData);

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollback();

            return false;
        }
    }

	/**
	 * @param $user
	 * @param null $mes
	 *
	 * @return bool
	 */
	public static function delete($user, $mes = null)
    {
        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();

            return false;
        }
    }

    public static function approve($user, $mes = null)
    {
        DB::beginTransaction();
        try {

             $userData = [
                'is_approved' => 1
            ];
            $user->update($userData);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();

            return false;
        }
    }
}
