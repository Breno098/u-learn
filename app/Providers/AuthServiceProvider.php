<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

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

        $this->registerUserGates();
    }

    private function registerUserGates(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        foreach(Permission::all() as $permission){
            Gate::define($permission->key, function (User $user) use ($permission) {
                foreach ($user->roles as $role) {
                    if ($role->permissions->contains('key', $permission->key))
                        return true;
                }

                return false;
            });
        }
    }
}
