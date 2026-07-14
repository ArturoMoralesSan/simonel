<?php

namespace App\Providers;

use App\Http\ViewComposers\DashboardMenuComposer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Role;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $view->with('authUser', Auth::user());
        });

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('rolesSelect', Role::orderBy('name')->pluck('name', 'id'));
            }
        });

        View::composer(
            'layout.dashboard-menu', DashboardMenuComposer::class
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

