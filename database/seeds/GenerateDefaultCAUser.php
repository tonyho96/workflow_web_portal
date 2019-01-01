<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class GenerateDefaultCAUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		try {
			DB::beginTransaction();
			$defaultUser = User::where('name', '=', 'admin')->first();
			if ($defaultUser) {
				$defaultUser->delete();
			}
			$defaultUserData = [
				'name' => 'admin',
				'email' => 'causer@mailinator.com',
				'password' => bcrypt('123456'),
				'role' => config('user.role.CA')
			];
			User::insert($defaultUserData);
			DB::commit();
			echo "Seeding user data has done.\n";
		}
		catch (Exception $e) {
			echo "Seeding Company data has fail.\n";
			$this->logError($e);
			DB::rollback();
		}
    }
}
