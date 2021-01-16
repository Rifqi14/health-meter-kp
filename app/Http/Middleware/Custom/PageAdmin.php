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
use App\Models\Workforce;
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
            if(Auth::guard('admin')->user()->workforce){
                // $employee  = Employee::with('site')->select('titles.*')
                //                         ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                //                         ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                //                         ->whereNull('finish') 
                //                         ->where('employees.id',Auth::guard('admin')->user()->employee->id)
                //                         ->first();
                $workforce = Workforce::with(['site', 'title'])->where('id', Auth::guard('admin')->user()->workforce->id)->first();
                $title = Title::find($workforce->title->id);
                if($title){

                    $role_id = [];
                    $roletitles = RoleTitle::with('role')->where('title_id','=',$workforce->title->id)->get();
                    $data_manager = 0;
                    foreach($roletitles as $roletitle){
                        if($roletitle->role->data_manager){
                            $data_manager = 1;
                        }
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
                        if(Auth::guard('admin')->user()->workforce->site){
                            View::share('menuaccess', $rolemenus);
                            View::share('accesssite', $data_manager);
                            View::share('siteinfo', Auth::guard('admin')->user()->workforce->site);
                        }
                        else{
                            return redirect('/admin/error');
                        }
                    }
                    else{
                        $role = Role::where('guest',1)->first();
                        if($role){
                            $rolemenus = RoleMenu::select('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                            ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                            ->where('role_id',$role->id)
                            ->where('role_access', '=', 1)
                            ->orderBy('menus.menu_sort', 'asc')
                            ->groupBy('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                            ->get();
                            if( Auth::guard('admin')->user()->workforce->site){
                                View::share('menuaccess', $rolemenus);
                                View::share('accesssite', $role->data_manager);
                                View::share('siteinfo',  Auth::guard('admin')->user()->workforce->site);
                            }
                            else{
                                return redirect('/admin/error');
                            }
                        }
                        else{
                            return redirect('/admin/error');
                        }
                    }
                   
                }
                else{
                    $role = Role::where('guest',1)->first();
                    if($role){
                        $rolemenus = RoleMenu::select('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                        ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                        ->where('role_id',$role->id)
                        ->where('role_access', '=', 1)
                        ->orderBy('menus.menu_sort', 'asc')
                        ->groupBy('menus.id','menus.parent_id','menus.menu_name','menus.menu_route','menus.menu_icon','menus.menu_sort')
                        ->get();
                        if( Auth::guard('admin')->user()->workforce->site){
                            View::share('menuaccess', $rolemenus);
                            View::share('accesssite', $role->data_manager);
                            View::share('siteinfo',  Auth::guard('admin')->user()->workforce->site);
                        }
                        else{
                            return redirect('/admin/error');
                        }
                    }
                    else{
                        return redirect('/admin/error');
                    }
                }  
            }
            else{
                return redirect('/admin/error');
            }  
        }
        return $next($request);
    }
}