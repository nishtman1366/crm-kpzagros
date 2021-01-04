<?php

use App\Libraries\TemplateEngine;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('createUsers', function () {
    $admins = User::factory()->count(20)->level('ADMIN')->create()->each(function ($admin) {
        $agents = User::factory()->count(2)->level('AGENT')->parent($admin->id)->create()->each(function ($agent) {
            User::factory()->count(10)->level('MARKETER')->parent($agent->id)->create();
        });
    });
});
Artisan::command('createDevices', function () {
    $devices = \App\Models\Variables\Device::factory()->count(1500)->create();
    echo count($devices);
});
Artisan::command('pass', function () {
    print \Illuminate\Support\Facades\Hash::make('Nil00f@r1869');
});

Artisan::command('checkDates', function () {
    $customers = \App\Models\Profiles\Customer::orderBy('id', 'ASC')->get()->each(function ($customer) {
        $birthday = $customer->birthday;
        if (substr($birthday, 0, 4) < 1900) {
            $newDate = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $birthday)->toCarbon();
            $customer->birthday = $newDate;
            $customer->save();
        }
    });

    $businesses = \App\Models\Profiles\Business::orderBy('id', 'ASC')->get()->each(function ($business) {
        $licenseDate = $business->license_date;
        if (substr($licenseDate, 0, 4) < 1900) {
            $newDate = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $licenseDate)->toCarbon();
            $business->license_date = $newDate;
            $business->save();
        }
    });

    $accounts = \App\Models\Profiles\Account::orderBy('id', 'ASC')->get()->each(function ($account) {
        $birthday = $account->birthday;
        if (substr($birthday, 0, 4) < 1900) {
            $newDate = \Morilog\Jalali\Jalalian::fromFormat('Y-m-d', $birthday)->toCarbon();
            $account->birthday = $newDate;
            $account->save();
        }
    });
});

Artisan::command('setProfileId', function () {
    $licenses = \App\Models\Profiles\License::get()->each(function ($license) {
        if (is_null($license->profile_id)) {
            $customer = \App\Models\Profiles\Customer::where('id', $license->customer_id)->get()->first();
            if (!is_null($customer)) {
                $license->profile_id = $customer->profile_id;
            }
        }

        switch ($license->name) {
            case 'national_card_file_1':
            case 'national_card_file_2':
            case 'id_file':
                $dir = 'customers/' . $license->customer_id . '/licenses/personal';
                break;
            case 'sheba_file':
                $dir = 'customers/' . $license->customer_id . '/licenses/accounts';
                break;
            case 'license_file':
            case 'esteshhad_file':
                $dir = 'profiles/' . $license->profile_id . '/licenses/business';
                break;
        }

        $type = \App\Models\Profiles\LicenseType::where('key', $license->name)->get()->first();

        $accountId = $license->account_id;
        $ext = pathinfo(storage_path('public/' . $dir . '/' . $license->file), PATHINFO_EXTENSION);
        if (!is_null($accountId)) {
            $fileName = $type->key . (is_null($accountId) ? '' : '-' . $accountId . '') . '.' . strtolower($ext);
        } else {
            $fileName = $type->key . '.' . strtolower($ext);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->copy($dir . '/' . $license->file, 'newProfiles/' . $license->profile_id . '/' . $fileName);

        $license->file = $fileName;
        $license->license_type_id = $type->id;

        $license->save();
    });
});

Artisan::command('sms', function () {
    $user = User::find(1);

    $user->notifyNow(new \App\Notifications\Profiles\AdminNotification(1, ['order_id' => '12334']));
});
