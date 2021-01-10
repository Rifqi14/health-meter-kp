<?php

namespace App\Http\Middleware;

use Closure;
use App\Role;
use App\Models\RoleMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Session;
class AccessMenu
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
        if(Auth::guard('admin')->check()){
            $role = Role::find(Session::get('role_id'));
            $accessmenu = [];
            $route = explode('.',Route::currentRouteName());
            if($role){
                $rolemenus = RoleMenu::select('menus.*')
                ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                ->where('role_id','=',$role->id)
                ->where('role_access','=',1)
                ->orderBy('menus.menu_sort','asc')     
                ->get();
                foreach($rolemenus as $rolemenu){
                    $accessmenu[] = $rolemenu->menu_route;
                }
            }
            if(!in_array($route[0],$accessmenu)){
                abort(403);
            }
            return $next($request);
        }
    }
}
