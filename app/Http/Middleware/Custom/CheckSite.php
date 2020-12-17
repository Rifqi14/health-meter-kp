<?php

namespace App\Http\Middleware\Custom;

use App\Models\RoleMenu;
use App\Models\RoleUser;
use App\User;
use Closure;
use Illuminate\Support\Facades\View;

class CheckSite
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
        if (!$request->session()->get('site')) {
            return redirect($request->site);
        } else {
            $session = $request->session()->get('site');
            /* Info User Login */
            $user = User::select('users.*')
                ->leftJoin('site_users', 'site_users.user_id', '=', 'users.id')
                ->where('site_users.site_id', '=', $session->site_id)
                ->where('users.id', '=', $session->id)
                ->get()
                ->first();
            if ($user) {
                View::share('usersession', $user);

                /*List User Role*/
                $userroles = RoleUser::with('role')->where('user_id', '=', $session->id)->get();
                View::share('userroles', $userroles);

                /**List Menu Item On Sidebar */
                $rolemenus = RoleMenu::select('menus.*')
                    ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                    ->where('role_id', '=', $session->role_id)
                    ->where('role_access', '=', 1)
                    ->orderBy('menus.menu_sort', 'asc')
                    ->get();
                View::share('menuaccess', $rolemenus);

                if ($session->role_id) {
                    /**Check access menu */
                    $access = false;
                    foreach ($rolemenus as $rolemenu) {
                        if ($rolemenu->menu_route) {
                            if (strpos($request->path(), $rolemenu->menu_route) !== false) {
                                $access = true;
                            }
                        }
                    }
                    if ($access) {
                        return $next($request);
                    } else {
                        abort(404);
                    }
                } else {
                    return $next($request);
                }
            } else {
                abort(404);
            }
        }
    }
}