<?php

namespace App\Providers;

use App\Models\Config;
use App\Models\RoleMenu;
use App\Models\Site;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Session;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('passcheck', function ($attribute, $value, $parameters) {
            return Hash::check($value, $parameters[0]);
        });
        Schema::defaultStringLength(191);
        if (Schema::hasTable('configs')) {
            config([
                'configs' => Config::all([
                    'option', 'value'
                ])
                    ->keyBy('option')
                    ->transform(function ($setting) {
                        return $setting->value;
                    })
                    ->toArray()
            ]);
        }
        view()->composer('*', function ($view) {
            if (Auth::guard('admin')->check()) {
                $role = Auth::guard('admin')->user()->roles()->first();
                if ($role) {
                    $rolemenus = RoleMenu::select('menus.*')
                        ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                        ->where('role_id', '=', $role->id)
                        ->where('role_access', '=', 1)
                        ->orderBy('menus.menu_sort', 'asc')
                        ->get();
                }
                $view->with('menuaccess', $rolemenus);
                $role = Role::find(Session::get('role_id'));
                $view->with('rolesession', $role);
            }
        });
    }
}