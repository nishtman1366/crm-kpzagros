<?php

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Models\Profiles\Profile;
use App\Models\Profiles\Terminal;
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
    public function create(Profile $profile, Request $request)
    {
        $profile->load(['customer', 'business', 'accounts']);
        $customer = $profile->customer;
        if (is_null($customer)) return response()->json(['message' => 'اطلاعات مشتری یافت نشد'], 404);
        $business = $profile->business;
        if (is_null($business)) return redirect()->route('dashboard.profiles.businesses.create', ['profile' => $profile]);
        $accounts = $profile->accounts;
        if (count($accounts) === 0) return response()->json(['message' => 'اطلاعات حساب های بانکی یافت نشد'], 404);
        $user = Auth::User();

        $connectionTypes = DeviceConnectionType::orderBy('id', 'ASC')->get();

        $deviceTypes = DeviceType::where('status', 1)->get();

        $psps = Psp::orderBy('name', 'ASC')->get();

        $devicePsps = DevicePsp::all();
        return Inertia::render('Dashboard/Profiles/CreateDevice', [
            'profileId' => $profile->id,
            'profile' => $profile,
            'customer' => $customer,
            'connectionTypes' => $connectionTypes,
            'deviceTypes' => $deviceTypes,
            'psps' => $psps,
            'devicePsps' => $devicePsps,
        ]);
    }

    public function store(Profile $profile, Request $request)
    {
        $validateArray = collect([
            'type' => 'required|in:POS,WPOS,IPG',
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


        $request->merge(['profile_id' => $profile->id]);
        Terminal::create($request->all());

        $profile->psp_id = $request->get('psp_id');
        $profile->save();

        return redirect()->route('dashboard.profiles.view', ['profile' => $profile]);
    }

    public function edit(Profile $profile, Request $request)
    {
        if (is_null($profile->customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد.');
        $terminals = $profile->terminals;
        if (count($terminals)===0) throw new NotFoundHttpException('اطلاعات دستگاه یافت نشد.');
        $connectionTypes = DeviceConnectionType::orderBy('id', 'ASC')->get();
        $deviceTypes = DeviceType::where('status', 1)->get();
        $psps = Psp::orderBy('name', 'ASC')->get();
        $devicePsps = DevicePsp::all();
        return Inertia::render('Dashboard/Profiles/EditDevice', [
            'profileId' => $profile->id,
            'profile' => $profile,
            'customer' => $profile->customer,
            'connectionTypes' => $connectionTypes,
            'deviceTypes' => $deviceTypes,
            'deviceType' => null,
            'psps' => $psps,
            'devicePsps' => $devicePsps,
        ]);
    }

    public function update(Profile $profile,Request $request)
    {
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
