<?php

namespace App\Actions\Profiles\Businesses;

use Closure;
use Illuminate\Http\Request;

class UpdateBusinessData
{
    public function handle(Request $request, Closure $next)
    {
        $business = $request->business;
        $business->fill($request->except(['profile', 'business']));
        $business->save();
        return $next($request);
    }
}
