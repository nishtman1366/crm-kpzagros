<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('id', 'ASC')->get();
        $pageTitle = $settings->where('key', 'PAGE_TITLE')->first();
        return Inertia::render('Dashboard/Settings/SettingsMain', compact('pageTitle'));
    }

    public function update(Request $request)
    {
        foreach ($request->except(['deleteLogo', 'COMPANY_LOGO']) as $item => $value) {
            $setting = Setting::where('key', $item)->get()->first();
            if (!is_null($setting)) $setting->update(['value' => $value]);
        }

        if ($request->hasFile('COMPANY_LOGO')) {
            $file = $request->file('COMPANY_LOGO')->store('settings/logo', 'public');
            $setting = Setting::where('key', 'COMPANY_LOGO')->get()->first();
            if (!is_null($setting)) $setting->update(['value' => url('storage') . '/' . $file]);
        }
        if ($request->has('deleteLogo') && $request->get('deleteLogo') == true) {
            $setting = Setting::where('key', 'COMPANY_LOGO')->get()->first();
            $file = explode('/', $setting->value);
            Storage::disk('public')->delete('settings/logo/' . $file[count($file) - 1]);
            if (!is_null($setting)) {
                $setting->update(['value' => null]);
            }
        }

        return redirect()->route('dashboard.settings.main');
    }
}
