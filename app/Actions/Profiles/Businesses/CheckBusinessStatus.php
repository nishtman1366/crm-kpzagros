<?php

namespace App\Actions\Profiles\Businesses;

use App\Models\Profiles\Business;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckBusinessStatus
{
    public function handle(Request $request, Closure $next)
    {
        $business = Business::where('profile_id', $request->profile->id)->get()->first();
        if (is_null($business)) throw new NotFoundHttpException('اطلاعات کسب و کار یافت نشد');
        $request->merge(['business' => $business]);
        return $next($request);
    }
}
