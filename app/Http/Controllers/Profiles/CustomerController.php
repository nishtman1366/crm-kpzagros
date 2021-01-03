<?php

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profiles\Customers\CreateCustomer;
use App\Http\Requests\Profiles\Customers\UpdateCustomer;
use App\Models\Profiles\Customer;
use App\Models\Profiles\License;
use App\Models\Profiles\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Dashboard/Customers/CustomersList');
    }

    public function create(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد');

        return Inertia::render('Dashboard/Profiles/CreateCustomer', [
            'profile' => $profile,
            'profileId' => (int)$profileId
        ]);
    }

    public function store(CreateCustomer $request)
    {
        $user = Auth::user();
        if (is_null($user)) return response()->json(['please log in'], 401);

        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد');

        $request->merge(['user_id' => $user->id]);

        $request->merge(['profile_id' => $profile->id]);
        $customer = Customer::create($request->all());

        if ($request->hasFile('national_card_file_1')) {
            LicenseController::upload($request->file('national_card_file_1'),'national_card_file_1', $profileId);
        }
        if ($request->hasFile('national_card_file_2')) {
            LicenseController::upload($request->file('national_card_file_2'),'national_card_file_2', $profileId);
        }
        if ($request->hasFile('id_file')) {
            LicenseController::upload($request->file('id_file'),'id_file', $profileId);
        }
        if ($request->hasFile('asasname_file')) {
            LicenseController::upload($request->file('asasname_file'),'asasname_file', $profileId);
        }
        if ($request->hasFile('agahi_file_1')) {
            LicenseController::upload($request->file('agahi_file_1'),'agahi_file_1', $profileId);
        }
        if ($request->hasFile('agahi_file_2')) {
            LicenseController::upload($request->file('agahi_file_2'),'agahi_file_2', $profileId);
        }

        return redirect()->route('dashboard.profiles.businesses.create', ['profileId' => $profile->id]);
    }

    public function edit(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::with('customer')
            ->with('accounts')
            ->with('business')
            ->with('device')
            ->find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد');
        $customer = $profile->customer;
        if (is_null($customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد');

        $profile->load('deviceType');
        return Inertia::render('Dashboard/Profiles/EditCustomer', [
            'customer' => $customer,
            'profileId' => (int)$profileId,
            'profile' => $profile,
        ]);
    }

    public function update(UpdateCustomer $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد');

        $customer = Customer::where('profile_id', $profileId)->get()->first();
        if (is_null($customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد');

        $customer->fill($request->all());
        $customer->save();

        if ($request->hasFile('national_card_file_1')) {
            LicenseController::upload($request->file('national_card_file_1'),'national_card_file_1', $profileId);
        }
        if ($request->hasFile('national_card_file_2')) {
            LicenseController::upload($request->file('national_card_file_2'),'national_card_file_2', $profileId);
        }
        if ($request->hasFile('id_file')) {
            LicenseController::upload($request->file('id_file'),'id_file', $profileId);
        }
        if ($request->hasFile('asasname_file')) {
            LicenseController::upload($request->file('asasname_file'),'asasname_file', $profileId);
        }
        if ($request->hasFile('agahi_file_1')) {
            LicenseController::upload($request->file('agahi_file_1'),'agahi_file_1', $profileId);
        }
        if ($request->hasFile('agahi_file_2')) {
            LicenseController::upload($request->file('agahi_file_2'),'agahi_file_2', $profileId);
        }

        return redirect()->route('dashboard.profiles.view', ['profileId' => $profileId]);
    }
}
