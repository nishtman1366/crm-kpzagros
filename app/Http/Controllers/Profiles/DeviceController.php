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
        if (is_null($business)) return redirect()->route('dashboard.profiles.businesses.create', ['profileId' => $profileId]);
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
        $validateArray = collect([
            'psp_id' => 'required|exists:psps,id',
            'device_sell_type' => 'required',
            'device_physical_status' => 'required'
        ]);
        $deviceSellType = $request->get('device_sell_type');
        if ($deviceSellType === 'cash' || $deviceSellType === 'dept') {
            $validateArray = $validateArray->merge([
                'device_amount' => 'required'
            ]);
        } elseif ($deviceSellType === 'installment') {
            $validateArray = $validateArray->merge([
                'device_amount' => 'required',
                'device_dept_profile_id' => 'required',
            ]);
        }

        $request->validateWithBag('deviceTypeForm', $validateArray->toArray());

        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('شماره پرونده یافت نشد.');
        $profile->fill($request->all());
        $profile->save();

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

        $validateArray = collect([
            'psp_id' => 'required|exists:psps,id',
            'device_sell_type' => 'required',
            'device_physical_status' => 'required'
        ]);
        $deviceSellType = $request->get('device_sell_type');
        if ($deviceSellType === 'cash' || $deviceSellType === 'dept') {
            $validateArray = $validateArray->merge([
                'device_amount' => 'required'
            ]);
        } elseif ($deviceSellType === 'installment') {
            $validateArray = $validateArray->merge([
                'device_amount' => 'required',
                'device_dept_profile_id' => 'required',
            ]);
        }

        $request->validateWithBag('deviceTypeForm', $validateArray->toArray());

        $profile->fill($request->all());
        $profile->save();

        return redirect()->route('dashboard.profiles.view', ['profileId' => $profileId]);
    }
}
