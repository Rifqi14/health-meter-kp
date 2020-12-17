<?php

namespace App\Http\Middleware\Custom;

use App\Models\Site;
use Closure;
use Illuminate\Support\Facades\View;

class ExistSite
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
            return $next($request);
        } else {
            /*Redirect to page 404 if site not exist */
            abort(404);
        }
    }
}