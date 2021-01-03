<?php

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Models\Profiles\Profile;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceConnectionType;
use App\Models\Variables\DevicePsp;
use App\Models\Variables\DeviceType;
use App\Models\Variables\Psp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeviceController extends Controller
{
    public function create(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::with('customer')->with('business')->with('accounts')->find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);
        $customer = $profile->customer;
        if (is_null($customer)) return response()->json(['message' => 'اطلاعات مشتری یافت نشد'], 404);
        $business = $profile->business;
        if (is_null($business)) return response()->json(['message' => 'اطلاعات کسب و کار یافت نشد'], 404);
        $accounts = $profile->accounts;
        if (count($accounts) === 0) return response()->json(['message' => 'اطلاعات حساب های بانکی یافت نشد'], 404);
        $user = Auth::User();

        $connectionTypes = DeviceConnectionType::orderBy('id', 'ASC')->get();

        $deviceTypes = DeviceType::where('status', 1)->get();

        $psps = Psp::orderBy('name', 'ASC')->get();

        $devicePsps = DevicePsp::all();
        return Inertia::render('Dashboard/Profiles/CreateDevice', [
            'profileId' => (int)$profileId,
            'profile' => $profile,
            'customer' => $customer,
            'connectionTypes' => $connectionTypes,
            'deviceTypes' => $deviceTypes,
            'psps' => $psps,
            'devicePsps' => $devicePsps,
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('deviceTypeForm', [
            'psp_id' => 'required|exists:psps,id'
        ]);

        $profileId = $request->route('profileId');

        Profile::find($profileId)->update(
            [
                'psp_id' => $request->get('psp_id'),
                'device_type_id' => $request->get('device_type_id'),
            ]);

        return redirect()->route('dashboard.profiles.view', ['profileId' => $profileId]);
    }

    public function edit(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::with('customer')->find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد.');
        if (is_null($profile->customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد.');
        $deviceType = $profile->deviceType;
        if (is_null($deviceType)) throw new NotFoundHttpException('اطلاعات دستگاه یافت نشد.');

        $user = Auth::User();

        $connectionTypes = DeviceConnectionType::orderBy('id', 'ASC')->get();

        $deviceTypes = DeviceType::where('status', 1)->get();

        $psps = Psp::orderBy('name', 'ASC')->get();

        $devicePsps = DevicePsp::all();
        return Inertia::render('Dashboard/Profiles/EditDevice', [
            'profileId' => (int)$profileId,
            'profile' => $profile,
            'customer' => $profile->customer,
            'connectionTypes' => $connectionTypes,
            'deviceTypes' => $deviceTypes,
            'deviceType' => $deviceType,
            'psps' => $psps,
            'devicePsps' => $devicePsps,
        ]);
    }

    public function update(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::with('customer')->find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد.');
        if (is_null($profile->customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد.');
        $deviceType = $profile->deviceType;
        if (is_null($deviceType)) throw new NotFoundHttpException('اطلاعات دستگاه یافت نشد.');
        Profile::find($profileId)->update([
            'psp_id' => $request->get('psp_id'),
            'device_type_id' => $request->get('device_type_id'),
        ]);

        return redirect()->route('dashboard.profiles.view', ['profileId' => $profileId]);
    }
}
