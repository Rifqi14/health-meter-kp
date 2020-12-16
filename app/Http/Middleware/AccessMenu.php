<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\RoleMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
        $role = Auth::guard('admin')->user()->roles()->first();
        $accessmenu = ['sessionsite'];
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
