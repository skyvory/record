<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		'App\Model' => 'App\Policies\ModelPolicy',
	];

	/**
	 * Register any application authentication / authorization services.
	 *
	 * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
	 * @return void
	 */
	public function boot(GateContract $gate)
	{
		parent::registerPolicies($gate);

		//
		// $gate->before(function($user, $ability) {
		// 	if($user->isAdmin()) {
		// 		return true;
		// 	}
		// });
		$gate->define('add-vn', function($user, $vn) {
			return $user->role == "common";
		});
		$gate->define('index-assessment', function($user, $assessment) {
			return $user->role == "common";
		});
		$gate->define('show-assessment', function($user, $assessment) {
			return $user->role == "common";
		});
		$gate->define('store-assessment', function($user, $assessment) {
			return $user->role == "common";
		});
		$gate->define('update-assessment', function($user, $assessment) {
			if($user->role == "common" && $user->id == $assessment->user_id) {
				return true;
			}
			return false;
		});
		$gate->define('delete-assessment', function($user, $assessment) {
			return $user->role == "common";
		});
	}
}
