<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\User;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		// $this->call(UserTableSeeder::class);
		DB::table('users')->delete();
		 $users = array(
				[
					'id' => '1',
					'name' => 'Skyvory',
					'username' => 'skyvory',
					'email' => 'skyvory@hotmail.com',
					'password' => Hash::make('skyvory')
				],
		);
		// Loop through each user above and create the record for them in the database
		foreach ($users as $user)
		{
			User::create($user);
		}

		Model::reguard();
	}
}
