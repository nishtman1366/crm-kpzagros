<?php

namespace App\Http\Controllers;

use App\Models\Profiles\Customer;
use App\Models\Profiles\Terminal;
use App\Models\Variables\Device;
use App\Rules\IMEIValidation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Mews\Captcha\Facades\Captcha;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $resultsType = null;
        $results = [];
        if ($request->routeIs('*search*')) {
            $searchType = $request->route('type');
            if ($searchType === 'nationalCode') {
                $resultsType = 'customers';
                $results = $this->searchCustomers($request->route('query'));
            } elseif ($searchType === 'serial') {
                $resultsType = 'devices';
                $results = $this->searchSerials($request->route('query'));
            }
        }
        return Inertia::render('Registration/index', compact('resultsType', 'results'));
    }

    public function store(Device $device, Request $request)
    {
        $request->merge([
            'sim_number' => toEnglishNumbers($request->get('sim_number')),
            'captcha' => toEnglishNumbers($request->get('captcha'))
        ]);
        $request->validate([
            'imei' => ['required', new IMEIValidation, 'unique:devices,imei,' . $device->id],
            'sim_number' => 'required|starts_with:09',
            'captcha' => 'required|captcha'
        ], [
            'captcha.captcha' => 'عبارت امنیتی وارد شده اشتباه است.'
        ]);

        $device->fill($request->only(['imei', 'sim_number']));
        $device->save();

        session()->flash('message', 'ثبت اطلاعات با موفقیت انجام شد.');
        return redirect()->back();
    }

    private function searchCustomers(string $nationalCode)
    {
        return Customer::with('profile')
            ->with('profile.psp')
            ->with('profile.terminals')
            ->with('profile.terminals.device')
            ->with('profile.terminals.device.deviceType')
            ->where('national_code', $nationalCode)
            ->get();
    }

    private function searchSerials(string $serial)
    {
        return Device::with('deviceType')
            ->with('terminal')
            ->with('terminal.profile')
            ->with('terminal.profile.psp')
            ->with('terminal.profile.customer')
            ->where('serial', $serial)
            ->get();
    }

    public function refreshCaptcha()
    {
        return response()->json([
            'src' => Captcha::src('math')
        ]);
    }
}
