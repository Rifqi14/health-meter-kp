<?php

namespace App\Http\Middleware;

use Closure;
use App\Role;
use App\Models\Employee;
use App\Models\Title;
use App\Models\RoleMenu;
use App\Models\Workforce;
use App\RoleTitle;
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
            if(Auth::guard('admin')->user()->workforce){
                $workforce = Workforce::with(['site', 'title'])->where('id', Auth::guard('admin')->user()->workforce->id)->first();
                $accessmenu = [];
                $route = explode('.',Route::currentRouteName());
                if($workforce->title){
                    $role_id = [];
                    $roletitles = RoleTitle::where('title_id',$workforce->title->id)->get();
                    foreach($roletitles as $roletitle){
                        array_push($role_id,$roletitle->role_id);
                    }
                    if(count($role_id) > 0){
                        $rolemenus = RoleMenu::select('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                        ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                        ->whereIn('role_id',$role_id)
                        ->where('role_access', '=', 1)
                        ->orderBy('menus.menu_sort', 'asc')
                        ->groupBy('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                        ->get();
                        foreach($rolemenus as $rolemenu){
                            $accessmenu[] = $rolemenu->menu_route;
                        }
                    }
                    else{
                        $role = Role::where('guest',1)->first();
                        $rolemenus = RoleMenu::select('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                        ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                        ->where('role_id',$role->id)
                        ->where('role_access', '=', 1)
                        ->orderBy('menus.menu_sort', 'asc')
                        ->groupBy('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                        ->get();
                        foreach($rolemenus as $rolemenu){
                            $accessmenu[] = $rolemenu->menu_route;
                        } 
                    }
                }
                else{
                    $route = explode('.',Route::currentRouteName());
                    $role = Role::where('guest',1)->first();
                    $rolemenus = RoleMenu::select('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                    ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                    ->where('role_id',$role->id)
                    ->where('role_access', '=', 1)
                    ->orderBy('menus.menu_sort', 'asc')
                    ->groupBy('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                    ->get();
                    foreach($rolemenus as $rolemenu){
                        $accessmenu[] = $rolemenu->menu_route;
                    }
                }
            }
            else{
                $accessmenu = [];
            }
            if(!in_array($route[0],$accessmenu)){
                abort(403);
            }
            return $next($request);
        }
    }
}