<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        if (!$request->user()->hasRole($roles)) {
            Auth::logout();
            return response(view("auth.login")->with("error" , "Access Denied: This area is for Admins only"), 403);
        }
        
        return $next($request);
    }
}
