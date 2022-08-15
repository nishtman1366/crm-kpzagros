<?php

namespace App\Actions\Profiles\Customers;

use App\Models\Profiles\Customer;
use Closure;
use Illuminate\Http\Request;

class UpdateCustomerData
{
    public function handle(Request $request, Closure $next)
    {
        $customer = $request->customer;
        $customer->fill($request->except(['profile', 'customer']));
        $customer->save();
        return $next($request);
    }
}
