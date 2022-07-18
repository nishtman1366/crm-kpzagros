<?php

namespace App\Http\Controllers\Profiles;

use App\Actions\Profiles\Customers\CheckCustomerStatus;
use App\Actions\Profiles\Customers\CreateCustomerData;
use App\Actions\Profiles\Customers\HandleCustomerLicenses;
use App\Actions\Profiles\Customers\UpdateCustomerData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profiles\Customers\CreateCustomer;
use App\Http\Requests\Profiles\Customers\UpdateCustomer;
use App\Models\Profiles\Customer;
use App\Models\Profiles\Profile;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerController extends Controller
{
    public function create(Profile $profile, Request $request)
    {
        $countries = DB::table('countries')->get();

        return Inertia::render('Dashboard/Profiles/CreateCustomer', [
            'profile' => $profile,
            'profileId' => $profile->id,
            'countries' => $countries
        ]);
    }

    public function store(Profile $profile, CreateCustomer $request)
    {
        $user = Auth::user();
        $profileExisted = Customer::where('profile_id', $profile->id)->exists();
        if ($profileExisted) throw new NotFoundHttpException('شماره پرونده تکراری است.');
        $request->merge(['user_id' => $user->id, 'profile_id' => $profile->id, 'profile' => $profile]);
        app(Pipeline::class)
            ->send($request)
            ->through([
                CreateCustomerData::class,
                HandleCustomerLicenses::class
            ])
            ->thenReturn();
        return redirect()->route('dashboard.profiles.businesses.create', ['profile' => $profile]);
    }

    public function edit(Profile $profile, Request $request)
    {
        $profile->load(['accounts', 'business', 'device']);
        $customer = $profile->customer;
        if (is_null($customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد');
        $countries = DB::table('countries')->get();
        $profile->load('deviceType');
        return Inertia::render('Dashboard/Profiles/EditCustomer', [
            'customer' => $customer,
            'profileId' => $profile->id,
            'profile' => $profile,
            'countries' => $countries
        ]);
    }

    public function update(Profile $profile, UpdateCustomer $request)
    {
        $request->merge(['profile' => $profile]);
        app(Pipeline::class)
            ->send($request)
            ->through([
                CheckCustomerStatus::class,
                UpdateCustomerData::class,
                HandleCustomerLicenses::class
            ])
            ->thenReturn();

        return redirect()->route('dashboard.profiles.view', ['profile' => $profile->id]);
    }
}
