<?php

namespace App\Http\Middleware;
use App\Models\Builder as Builder;

use Closure;

class CheckRoutes
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
        
        //echo($request->id);
      /*
        $reqId = preg_match(( "/^[0-9]*$/"), $request->id, $matches); 
        echo $matches;
        */
        $builderId = Builder::where("id", $stringifyReqId)->exists();
       
        return $next($request);
    }
}
