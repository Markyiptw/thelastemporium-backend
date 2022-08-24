<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Obj;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('upload-media', function ($user, Obj $object) {
            if ($user->id === $object->user_id) {return true;};
        });

        Gate::define('update-location', function ($user, Obj $object) {
            if ($user->id === $object->user_id) {return true;};
        });

        Gate::after(function ($user, $ability) {
            return $user instanceof Admin;
        });
    }
}
