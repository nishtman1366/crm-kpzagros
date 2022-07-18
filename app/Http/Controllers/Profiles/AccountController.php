<?php

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profiles\Accounts\CreateAccount;
use App\Http\Requests\Profiles\Accounts\UpdateAccount;
use App\Models\Profiles\Account;
use App\Models\Profiles\License;
use App\Models\Profiles\Profile;
use App\Models\Profiles\ProfilesAccount;
use App\Models\Variables\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountController extends Controller
{
    public function create(Profile $profile, Request $request)
    {
        $profile->load(['customer', 'business']);
        $customer = $profile->customer;
        if (is_null($customer)) return response()->json(['message' => 'اطلاعات مشتری یافت نشد'], 404);
        $business = $profile->business;
        if (is_null($business)) return redirect()->route('dashboard.profiles.businesses.create', ['profile' => $profile]);

        $banks = Bank::orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Profiles/CreateAccount', [
            'profileId' => $profile->id,
            'profile' => $profile,
            'customer' => $customer,
            'banks' => $banks
        ]);
    }

    public function store(Profile $profile, CreateAccount $request)
    {
        $accounts = $request->get('accounts');
        foreach ($accounts as $key => $account) {
            $CreatedAccount = Account::create($account);

            LicenseController::upload($request->accounts[$key]['sheba_file'], 'sheba_file', $profile->id, $CreatedAccount->id);

            ProfilesAccount::create([
                'profile_id' => $profile->id,
                'account_id' => $CreatedAccount->id
            ]);
        }

        if (count($accounts) > 1) {
            $profile->multi_account = 1;
            $profile->save();
        }

        return redirect()->route('dashboard.profiles.devices.create', ['profile' => $profile]);
    }

    public function edit(Profile $profile, Request $request)
    {
        $profile->load(['customer', 'business', 'accounts', 'accounts.account']);
        $customer = $profile->customer;
        if (is_null($customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد.');
        $accounts = $profile->accounts;
        $profile->load('deviceType');
        $banks = Bank::orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Profiles/EditAccount', [
            'profileId' => $profile->id,
            'profile' => $profile,
            'customer' => $customer,
            'banks' => $banks,
            'accounts' => $accounts
        ]);
    }

    public function update(Profile $profile, UpdateAccount $request)
    {
        $accounts = $request->get('accounts');
        ProfilesAccount::where('profile_id', $profile->id)->delete();

        foreach ($accounts as $key => $account) {
            if (key_exists('id', $account)) {
                $updatedAccount = Account::find($account['id']);
                $updatedAccount->fill($account);
                $updatedAccount->save();
            } else {
                $updatedAccount = Account::create($account);
            }

            if ($request->hasFile('accounts.' . $key . '.sheba_file')) {
                LicenseController::upload($request->accounts[$key]['sheba_file'], 'sheba_file', $profile->id, $updatedAccount->id);
            }

            ProfilesAccount::create([
                'profile_id' => $profile->id,
                'account_id' => $updatedAccount->id
            ]);
        }

        if (count($accounts) > 1) {
            $profile->multi_account = 1;
            $profile->save();
        }

        return redirect()->route('dashboard.profiles.view', ['profile' => $profile]);
    }
}
