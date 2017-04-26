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

		// $gate->before(function($user, $ability) {
		// 	if($user->isAdmin()) {
		// 		return true;
		// 	}
		// });

		$gate->define('read-vn', function($user) {
			return in_array($user->role, ['administrator', 'common', 'guest']);
		});
		$gate->define('create-vn', function($user) {
			return in_array($user->role, ['administrator']);
		});
		$gate->define('update-vn', function($user) {
			return in_array($user->role, ['administrator']);
		});
		$gate->define('delete-vn', function($user) {
			return in_array($user->role, ['administrator']);
		});
		$gate->define('relate-vn', function($user) {
			return in_array($user->role, ['administrator']);
		});
		$gate->define('remove-vn-relation', function($user) {
			return in_array($user->role, ['administrator']);
		});
		$gate->define('refresh-vn-cover', function($user) {
			return in_array($user->role, ['administrator']);
		});
		$gate->define('store-screenshot', function($user) {
			return in_array($user->role, ['administrator']);
		});
		$gate->define('read-screenshot', function($user) {
			return in_array($user->role, ['administrator', 'common']);
		});
		$gate->define('remove-screenshot', function($user) {
			return in_array($user->role, ['administrator']);
		});
		$gate->define('update-screenshot-property', function($user) {
			return in_array($user->role, ['administrator']);
		});
		$gate->define('search-game', function($user) {
			return in_array($user->role, ['administrator']);
		});


		$gate->define('read-assessment', function($user) {
			return in_array($user->role, ['administrator', 'common']);
		});
		$gate->define('create-assessment', function($user) {
			return in_array($user->role, ['administrator', 'common']);
		});
		$gate->define('update-assessment', function($user, $assessment) {
			if(in_array($user->role, ['administrator', 'common']) && $user->id == $assessment->user_id) {
				return true;
			}
			return false;
		});
		$gate->define('delete-assessment', function($user, $assessment) {
			if(in_array($user->role, ['administrator', 'common']) && $user->id == $assessment->user_id) {
				return true;
			}
			return false;
		});
		
		
		// character define
		$gate->define('store-character', function($user, $character) {
			return $user->role == "common";
		});
		$gate->define('update-character', function($user, $character) {
			return $user->role == "common";
		});
		$gate->define('delete-character', function($user, $character) {
			return $user->role == "common";
		});

		// developer define
		$gate->define('store-developer', function($user, $developer) {
			return $user->role == "common";
		});
		$gate->define('update-developer', function($user, $developer) {
			return $user->role == "common";
		});
		$gate->define('delete-developer', function($user, $developer) {
			return $user->role == "common";
		});

		// note define
		$gate->define('index-note', function($user, $note) {
			return $user->role == "common";
		});
		$gate->define('show-note', function($user, $note) {
			return $user->role == "common";
		});
		$gate->define('store-note', function($user, $note) {
			return $user->role == "common";
		});
		$gate->define('update-note', function($user, $note) {
			if($user->role == "common" && $user->id == $note->user_id) {
				return true;
			}
			return false;
		});
		$gate->define('delete-note', function($user, $note) {
			if($user->role == "common" && $user->id == $note->user_id) {
				return true;
			}
			return false;
		});

		// stock define
		$gate->define('index-stock', function($user, $stock) {
			return $user->role == "common";
		});
		$gate->define('show-stock', function($user, $stock) {
			return $user->role == "common";
		});
		$gate->define('store-stock', function($user, $stock) {
			return $user->role == "common";
		});
		$gate->define('update-stock', function($user, $stock) {
			if($user->role == "common" && $user->id == $stock->user_id) {
				return true;
			}
			return false;
		});
		$gate->define('delete-stock', function($user, $stock) {
			if($user->role == "common" && $user->id == $stock->user_id) {
				return true;
			}
			return false;
		});

		// lineament define
		$gate->define('index-lineament', function($user, $lineament) {
			return $user->role == "common";
		});
		$gate->define('show-lineament', function($user, $lineament) {
			return $user->role == "common";
		});
		$gate->define('store-lineament', function($user, $lineament) {
			return $user->role == "common";
		});
		$gate->define('update-lineament', function($user, $lineament) {
			if($user->role == "common" && $user->id == $lineament->user_id) {
				return true;
			}
			return false;
		});
		$gate->define('delete-lineament', function($user, $lineament) {
			if($user->role == "common" && $user->id == $lineament->user_id) {
				return true;
			}
			return false;
		});
	}
}
