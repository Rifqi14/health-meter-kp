<?php

namespace App\Http\Middleware\Custom;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use App\Role;
use App\Models\RoleMenu;
use Session;
class PageAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            $role = Role::find(Session::get('role_id'));
            if ($role) {
                $rolemenus = RoleMenu::select('menus.*')
                    ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                    ->where('role_id', '=', $role->id)
                    ->where('role_access', '=', 1)
                    ->orderBy('menus.menu_sort', 'asc')
                    ->get();
                    View::share('menuaccess', $rolemenus);
            }
            else{
                View::share('menuaccess', []);
            }
            View::share('rolesession', $role);
        }
        return $next($request);
    }
}