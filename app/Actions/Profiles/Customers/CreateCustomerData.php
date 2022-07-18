<?php

namespace App\Actions\Profiles\Customers;

use App\Models\Profiles\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateCustomerData
{
    public function handle(Request $request, Closure $next)
    {
        Customer::create($request->except(['profile']));
        return $next($request);
    }
}
