<?php

namespace App\Http\Middleware\Custom;

use App\Models\Site;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use App\Role;
use App\Models\RoleMenu;
use Session;
class PageSite
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
        /*Check Existing Site On Sistem */
        $siteinfo = Site::where('code', $request->site)->first();
        if ($siteinfo) {
            View::share('siteinfo', $siteinfo);
            $request->merge(compact('siteinfo'));
            if (Auth::guard('site')->check()) {
                $site = \App\Models\Site::where('code',$request->site)->first();
                $siteuser = \App\Models\SiteUser::where('user_id',Auth::guard('site')->user()->id)->where('site_id',$site->id)->first();
                if($siteuser){
                    $role = Role::find(Session::get('site_role_id'));
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
                else{
                    abort(403);
                }
            }
            return $next($request);
        } else {
            /*Redirect to page 404 if site not exist */
            abort(404);
        }
    }
}