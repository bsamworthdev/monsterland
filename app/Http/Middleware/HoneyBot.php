<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HoneyBot
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
        if($request->isMethod('POST') && $request->fakeField && $request->fakeField != ''){
            return redirect('register');
        }
        return $next($request);
    }
}
