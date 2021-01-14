<?php

namespace App\Http\Middleware\Custom;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use App\Role;
use App\Models\Employee;
use App\Models\RoleMenu;
use App\RoleTitle;
use App\Models\Title;
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
            $employee  = Employee::select('titles.*')
                                    ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                                    ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                                    ->whereNull('finish') 
                                    ->where('employees.id',Auth::guard('admin')->user()->employee->id)
                                    ->first();
            $title = Title::find($employee->id);
            if($title){

                $role_id = [];
                $roletitles = RoleTitle::where('title_id','=',$employee->id)->get();
                foreach($roletitles as $roletitle){
                    array_push($role_id,$roletitle->role_id);
                }
                $rolemenus = RoleMenu::select('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                    ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                    ->whereIn('role_id',$role_id)
                    ->where('role_access', '=', 1)
                    ->orderBy('menus.menu_sort', 'asc')
                    ->groupBy('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                    ->get();
                    View::share('menuaccess', $rolemenus);
            }
            else{
                View::share('menuaccess', []);
            }
            
        }
        return $next($request);
    }
}