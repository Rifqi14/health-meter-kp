<?php

namespace App\Http\Middleware\Custom;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

use App\Role;
use App\Models\Employee;
use App\Models\Menu;
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
                $route = explode('.',Route::currentRouteName());
                $workforce = Workforce::with(['site', 'title'])->where('id', Auth::guard('admin')->user()->workforce->id)->first();
                if($workforce->title){
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
                            $menu = Menu::where('menu_route',$route[0])->first();
                            $actionmenu = [];
                            if($menu){
                                $actions = RoleMenu::where('menu_id',$menu->id)->whereIn('role_id',$role_id)->get();
                                foreach($actions as $action){
                                    if($action->create){
                                        if(!in_array('create',$actionmenu)){
                                            array_push($actionmenu,'create');
                                        }
                                    }
                                    if($action->read){
                                        if(!in_array('read',$actionmenu)){
                                            array_push($actionmenu,'read');
                                        }
                                    }
                                    if($action->update){
                                        if(!in_array('update',$actionmenu)){
                                            array_push($actionmenu,'update');
                                        }
                                    }
                                    if($action->delete){
                                        if(!in_array('delete',$actionmenu)){
                                            array_push($actionmenu,'delete');
                                        }
                                    }
                                    if($action->import){
                                        if(!in_array('import',$actionmenu)){
                                            array_push($actionmenu,'import');
                                        }
                                    }
                                    if($action->export){
                                        if(!in_array('export',$actionmenu)){
                                            array_push($actionmenu,'export');
                                        }
                                    }
                                    if($action->print){
                                        if(!in_array('print',$actionmenu)){
                                            array_push($actionmenu,'print');
                                        }
                                    }
                                    if($action->sync){
                                        if(!in_array('sync',$actionmenu)){
                                            array_push($actionmenu,'sync');
                                        }
                                    }
                                }
                            }
                            View::share('actionmenu', $actionmenu);
                            request()->merge(['actionmenu' => $actionmenu]);
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
                                $menu = Menu::where('menu_route',$route[0])->first();
                                $actionmenu = [];
                                if($menu){
                                    $actions = RoleMenu::where('menu_id',$menu->id)->where('role_id',$role->id)->get();
                                    foreach($actions as $action){
                                        if($action->create){
                                            if(!in_array('create',$actionmenu)){
                                                array_push($actionmenu,'create');
                                            }
                                        }
                                        if($action->read){
                                            if(!in_array('read',$actionmenu)){
                                                array_push($actionmenu,'read');
                                            }
                                        }
                                        if($action->update){
                                            if(!in_array('update',$actionmenu)){
                                                array_push($actionmenu,'update');
                                            }
                                        }
                                        if($action->delete){
                                            if(!in_array('delete',$actionmenu)){
                                                array_push($actionmenu,'delete');
                                            }
                                        }
                                        if($action->import){
                                            if(!in_array('import',$actionmenu)){
                                                array_push($actionmenu,'import');
                                            }
                                        }
                                        if($action->export){
                                            if(!in_array('export',$actionmenu)){
                                                array_push($actionmenu,'export');
                                            }
                                        }
                                        if($action->print){
                                            if(!in_array('print',$actionmenu)){
                                                array_push($actionmenu,'print');
                                            }
                                        }
                                        if($action->sync){
                                            if(!in_array('sync',$actionmenu)){
                                                array_push($actionmenu,'sync');
                                            }
                                        }
                                    }
                                }
                                View::share('actionmenu', $actionmenu);
                                request()->merge(['actionmenu' => $actionmenu]);
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
                            $menu = Menu::where('menu_route',$route[0])->first();
                            $actionmenu = [];
                            if($menu){
                                $actions = RoleMenu::where('menu_id',$menu->id)->where('role_id',$role->id)->get();
                                foreach($actions as $action){
                                    if($action->create){
                                        if(!in_array('create',$actionmenu)){
                                            array_push($actionmenu,'create');
                                        }
                                    }
                                    if($action->read){
                                        if(!in_array('read',$actionmenu)){
                                            array_push($actionmenu,'read');
                                        }
                                    }
                                    if($action->update){
                                        if(!in_array('update',$actionmenu)){
                                            array_push($actionmenu,'update');
                                        }
                                    }
                                    if($action->delete){
                                        if(!in_array('delete',$actionmenu)){
                                            array_push($actionmenu,'delete');
                                        }
                                    }
                                    if($action->import){
                                        if(!in_array('import',$actionmenu)){
                                            array_push($actionmenu,'import');
                                        }
                                    }
                                    if($action->export){
                                        if(!in_array('export',$actionmenu)){
                                            array_push($actionmenu,'export');
                                        }
                                    }
                                    if($action->print){
                                        if(!in_array('print',$actionmenu)){
                                            array_push($actionmenu,'print');
                                        }
                                    }
                                    if($action->sync){
                                        if(!in_array('sync',$actionmenu)){
                                            array_push($actionmenu,'sync');
                                        }
                                    }
                                }
                            }
                            View::share('actionmenu', $actionmenu);
                            request()->merge(['actionmenu' => $actionmenu]);
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