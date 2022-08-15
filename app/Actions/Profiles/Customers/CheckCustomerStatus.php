<?php

namespace App\Actions\Profiles\Customers;

use App\Models\Profiles\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckCustomerStatus
{
    public function handle(Request $request, Closure $next)
    {
        $customer = Customer::where('profile_id', $request->profile->id)->get()->first();
        if (is_null($customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد');
        $request->merge(['customer' => $customer]);
        return $next($request);
    }
}
