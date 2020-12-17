<?php
   
namespace App\Http\Middleware;

use App\Models\BlockedIP;
use Closure;
use Illuminate\Http\Request;
   
class IpMiddleware
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
        $restrictIps = BlockedIP::pluck('ip_address')->toArray();
        if (in_array($request->ip(), $restrictIps)) {
            return response()->json(['you don\'t have permission to access this application.']);
        }
    
        return $next($request);
    }
}