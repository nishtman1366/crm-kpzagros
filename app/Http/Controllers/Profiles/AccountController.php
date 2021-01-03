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
    public function create(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::with('customer')->with('business')->find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);
        $customer = $profile->customer;
        if (is_null($customer)) return response()->json(['message' => 'اطلاعات مشتری یافت نشد'], 404);
        $business = $profile->business;
        if (is_null($business)) return response()->json(['message' => 'اطلاعات کسب و کار یافت نشد'], 404);

        $banks = Bank::orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Profiles/CreateAccount', [
            'profileId' => (int)$profileId,
            'profile' => $profile,
            'customer' => $customer,
            'banks' => $banks
        ]);
    }

    public function store(CreateAccount $request)
    {
        $profileId = $request->route('profileId');

        $accounts = $request->get('accounts');
        foreach ($accounts as $key => $account) {
            $CreatedAccount = Account::create($account);

            LicenseController::upload($request->accounts[$key]['sheba_file'],'sheba_file', $profileId, $CreatedAccount->id);

            ProfilesAccount::create([
                'profile_id' => $profileId,
                'account_id' => $CreatedAccount->id
            ]);

            if (count($accounts) > 1) {
                Profile::find($profileId)->update(['multi_account' => 1]);
            }
        }
        return redirect()->route('dashboard.profiles.devices.create', ['profileId' => $profileId]);
    }

    public function edit(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::with('business')->with('accounts')->with('accounts.account')->find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد.');
        $customer = $profile->customer;
        if (is_null($customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد.');
        $accounts = $profile->accounts;
        $profile->load('deviceType');
        $banks = Bank::orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Profiles/EditAccount', [
            'profileId' => (int)$profileId,
            'profile' => $profile,
            'customer' => $customer,
            'banks' => $banks,
            'accounts' => $accounts
        ]);
    }

    public function update(UpdateAccount $request)
    {
        $profileId = $request->route('profileId');

        $accounts = $request->get('accounts');
        ProfilesAccount::where('profile_id', $profileId)->delete();

        foreach ($accounts as $key => $account) {
            if (key_exists('id', $account)) {
                $updatedAccount = Account::find($account['id']);
                $updatedAccount->fill($account);
                $updatedAccount->save();
            } else {
                $updatedAccount = Account::create($account);
            }

            if ($request->hasFile('accounts.' . $key . '.sheba_file')) {
                LicenseController::upload($request->accounts[$key]['sheba_file'],'sheba_file', $profileId, $updatedAccount->id);
            }

            ProfilesAccount::create([
                'profile_id' => $profileId,
                'account_id' => $updatedAccount->id
            ]);

            if (count($accounts) > 1) {
                Profile::find($profileId)->update(['multi_account' => 1]);
            }
        }
        return redirect()->route('dashboard.profiles.view', ['profileId' => $profileId]);
    }
}
