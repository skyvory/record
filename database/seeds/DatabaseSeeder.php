<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\User;
use App\Vn;
use App\Developer;
use App\Assessment;

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
					'name' => 'God',
					'username' => 'god',
					'email' => 'summerdream358@gmail.com',
					'password' => Hash::make('kamisama')
				],
				[
					'id' => '2',
					'name' => 'Skyvory',
					'username' => 'skyvory',
					'email' => 'skyvory@hotmail.com',
					'password' => Hash::make('svry')
				],
				[
					'id' => '3',
					'name' => 'sv',
					'username' => 'sv',
					'email' => 'sv@sv.com',
					'password' => Hash::make('sv')
				],
		);
		// Loop through each user above and create the record for them in the database
		foreach ($users as $user)
		{
			User::create($user);
		}

		DB::table('developers')->delete();
		$devs = array(
			[
				'id' => '1',
				'name_en' => 'grmblgrmbl',
				'name_jp' => 'mzmz'
			],
		);
		foreach($devs as $dev) {
			Developer::create($dev);
		}
	
		 DB::table('vn')->delete();
		 $vn = array(
		 	[
		 		'id' => '1',
		 		'title_en' => 'version test alpha',
		 		'title_jp' => 'alpha test verson',
		 		'hashtag' => 'alpha',
		 		'developer_id' => '1',
		 		'date_release' => '2015-06-06 13:23:03'
		 	],
		 	[
		 		'id' => '2',
		 		'title_en' => 'version test beta',
		 		'title_jp' => 'beta test verson',
		 		'hashtag' => 'beta',
		 		'developer_id' => '1',
		 		'date_release' => '2015-06-06 13:23:03'
		 	],
		 	[
		 		'id' => '3',
		 		'title_en' => 'version test charlie',
		 		'title_jp' => 'charlie test verson',
		 		'hashtag' => 'charlie',
		 		'developer_id' => '1',
		 		'date_release' => '2015-06-06 13:23:03'
		 	],
		 	[
		 		'id' => '4',
		 		'title_en' => 'version test delta',
		 		'title_jp' => 'delta test verson',
		 		'hashtag' => 'delta',
		 		'developer_id' => '1',
		 		'date_release' => '2015-06-06 13:23:03'
		 	],
		 	[
		 		'id' => '5',
		 		'title_en' => 'version test echo',
		 		'title_jp' => 'echo test verson',
		 		'hashtag' => 'echo',
		 		'developer_id' => '1',
		 		'date_release' => '2015-06-06 13:23:03'
		 	],
		 	[
		 		'id' => '6',
		 		'title_en' => 'version test foxtrot',
		 		'title_jp' => 'foxtrot test verson',
		 		'hashtag' => 'foxtrot',
		 		'developer_id' => '1',
		 		'date_release' => '2015-06-06 13:23:03'
		 	],
		 	[
		 		'id' => '7',
		 		'title_en' => 'version test golf',
		 		'title_jp' => 'golf test verson',
		 		'hashtag' => 'golf',
		 		'developer_id' => '1',
		 		'date_release' => '2015-06-06 13:23:03'
		 	],
		 	[
		 		'id' => '8',
		 		'title_en' => 'version test hotel',
		 		'title_jp' => 'hotel test verson',
		 		'hashtag' => 'hotel',
		 		'developer_id' => '1',
		 		'date_release' => '2015-06-06 13:23:03'
		 	],
		 	[
		 		'id' => '9',
		 		'title_en' => 'version test indo',
		 		'title_jp' => 'indo test verson',
		 		'hashtag' => 'indo',
		 		'developer_id' => '1',
		 		'date_release' => '2015-06-06 13:23:03'
		 	],
		 	[
		 		'id' => '10',
		 		'title_en' => 'version test juliet',
		 		'title_jp' => 'juliet test verson',
		 		'hashtag' => 'juliet',
		 		'developer_id' => '1',
		 		'date_release' => '2015-06-06 13:23:03'
		 	],
		 );
		 foreach($vn as $v) {
		 	Vn::create($v);
		 }

		 DB::table('assessments')->delete();
		 $assessments = array(
		 	[
		 		'id' => '1',
		 		'vn_id' => '1',
		 		'user_id' => '3',
		 		'date_start' => '2015-01-01 01:23:42',
		 		'date_end' => null,
		 	],
		 	[
		 		'id' => '2',
		 		'vn_id' => '1',
		 		'user_id' => '3',
		 		'date_start' => '2015-01-02 01:23:42',
		 		'date_end' => null,
		 	],
		 	[
		 		'id' => '3',
		 		'vn_id' => '1',
		 		'user_id' => '3',
		 		'date_start' => '2015-02-02 01:23:42',
		 		'date_end' => null,
		 	],
		 );
		 foreach($assessments as $assessment) {
		 	Assessment::create($assessment);
		 }

		Model::reguard();
	}
}
