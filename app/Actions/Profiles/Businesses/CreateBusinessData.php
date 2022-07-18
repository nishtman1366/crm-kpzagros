<?php

namespace App\Actions\Profiles\Businesses;

use App\Models\Profiles\Business;
use Closure;
use Illuminate\Http\Request;

class CreateBusinessData
{
    public function handle(Request $request, Closure $next)
    {
        Business::create($request->except(['profile']));
        return $next($request);
    }
}
