<?php

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profiles\Business\CreateBusiness;
use App\Http\Requests\Profiles\Business\UpdateBusiness;
use App\Models\Profiles\Business;
use App\Models\Profiles\License;
use App\Models\Profiles\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BusinessController extends Controller
{
    public function create(Request $request)
    {
        $profileId = $request->route('profileId');

        $profile = Profile::with('customer')->find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد.');
        $customer = $profile->customer;
        if (is_null($customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد.');


        $ostans = DB::table('ostan')->select()->get(['id', 'name']);
        $shahrestans = DB::table('shahrestan')->select()->get(['id', 'name']);
        $bakhshs = DB::table('bakhsh')->select()->get(['id', 'name']);
        $shahrs = DB::table('shahr')->select()->get(['id', 'name']);
        return Inertia::render('Dashboard/Profiles/CreateBusiness', [
            'profileId' => (int)$profileId,
            'profile' => $profile,
            'customer' => $customer,
            'ostans' => $ostans,
            'shahrestans' => $shahrestans,
            'bakhshs' => $bakhshs,
            'shahrs' => $shahrs,
        ]);
    }

    public function store(CreateBusiness $request)
    {
        $profileId = $request->route('profileId');

        Business::create($request->all());

        if ($request->hasFile('license_file')) {
            LicenseController::upload($request->file('license_file'), 'license_file', $profileId);
        }
        if ($request->hasFile('esteshhad_file')) {
            LicenseController::upload($request->file('esteshhad_file'), 'esteshhad_file', $profileId);
        }

        return redirect()->route('dashboard.profiles.accounts.create', ['profileId' => $profileId]);
    }

    public function edit(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::with('customer')->with('business')->with('accounts')->with('device')->find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد.');
        $customer = $profile->customer;
        if (is_null($customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد.');
        $business = $profile->business;
        if (is_null($business)) throw new NotFoundHttpException('اطلاعات کسب و کار یافت نشد.');
        $profile->load('deviceType');
        $ostans = DB::table('ostan')->select()->get(['id', 'name']);
        $shahrestans = DB::table('shahrestan')->select()->get(['id', 'name']);
        $bakhshs = DB::table('bakhsh')->select()->get(['id', 'name']);
        $shahrs = DB::table('shahr')->select()->get(['id', 'name']);

        return Inertia::render('Dashboard/Profiles/EditBusiness', [
            'profileId' => (int)$profileId,
            'profile' => $profile,
            'customer' => $customer,
            'ostans' => $ostans,
            'shahrestans' => $shahrestans,
            'bakhshs' => $bakhshs,
            'shahrs' => $shahrs,
            'business' => $business,
        ]);
    }

    public function update(UpdateBusiness $request)
    {
        $profileId = $request->route('profileId');

        $business = Business::where('profile_id', $profileId)->get()->first();
        if (is_null($business)) throw new NotFoundHttpException('اطلاعات کسب و کار یافت نشد.');



        $business->fill($request->all());
        $business->save();

        if ($request->hasFile('license_file')) {
            LicenseController::upload($request->file('license_file'), 'license_file', $profileId);
        }
        if ($request->hasFile('esteshhad_file')) {
            LicenseController::upload($request->file('esteshhad_file'), 'esteshhad_file', $profileId);
        }

        return redirect()->route('dashboard.profiles.view', ['profileId' => $profileId]);
    }
}
