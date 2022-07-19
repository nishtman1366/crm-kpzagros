<?php

use App\Exceptions\NotificationException;
use App\Libraries\ProgressBar;
use App\Libraries\TemplateEngine;
use App\Models\User;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use IPPanel\Client;
use IPPanel\Errors\Error;
use IPPanel\Errors\HttpException;

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

Artisan::command('transfer', function () {
    $directories = \Illuminate\Support\Facades\Storage::disk('public')->directories('profiles');
    foreach ($directories as $directory) {
        \Illuminate\Support\Facades\Storage::disk('licenses')->makeDirectory($directory);
        print(sprintf('Directory %s created', $directory) . PHP_EOL);
        $files = \Illuminate\Support\Facades\Storage::disk('public')->files($directory);
        foreach ($files as $file) {
            $stream = \Illuminate\Support\Facades\Storage::disk('public')->readStream($file);
            \Illuminate\Support\Facades\Storage::disk('licenses')->writeStream($file, $stream);
            print(sprintf('File %s created', $file) . PHP_EOL);
        }
        print(sprintf('directory %s fully coped to new disk', $directory) . PHP_EOL);
        print('#####################################################' . PHP_EOL);
    }
    print('All directories successfully copied to new disk' . PHP_EOL);
});

Artisan::command('categories', function () {
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\Businesses\CategoryImport, public_path('files/business_categories.xlsx'));
});

Artisan::command('changeTerminals', function () {
    \App\Models\Profiles\Profile::with('deviceType')
        ->orderBy('id', 'ASC')
        ->where('status', '!=', 0)
        ->get()
        ->each(function ($profile) {
            if (!is_null($profile->psp_id) && !is_null($profile->deviceType)) {
                \App\Models\Profiles\Terminal::create([
                    'profile_id' => $profile->id,
                    'type' => 'WPOS',
                    'terminal_number' => $profile->terminal_id,
                    'device_connection_type_id' => $profile->deviceType->device_connection_type_id,
                    'device_type_id' => $profile->device_type_id,
                    'device_id' => $profile->device_id,
                    'device_sell_type' => $profile->device_sell_type,
                    'device_amount' => $profile->device_amount,
                    'device_dept_profile_id' => $profile->device_dept_profile_id,
                    'device_physical_status' => $profile->device_physical_status,
                ]);
            }
        });
});

function terminalStatusBaseOnProfileStatus($status)
{
    switch ($status) {
        case 0:
        case 1:
        case 2:
        case 3:
        case 4:
        case 5:
            return 0;
        case 6:
            return 1;
        case 7:
            return 3;
        case 8:
            return 6;
        case 9:
            return 5;
        case 10:
        case 11:
        case 255:
            return false;
        case 12:
            return 4;
        case 13:
            return 2;
        case 14:
            return 7;
        case 15:
            return 8;
        case 16:
            return 9;
        case 17:
            return 10;
    }
}

Artisan::command('updateTerminalStatuses', function () {
    \App\Models\Profiles\Terminal::with('profile')
        ->chunk(1000, function ($terminals) {
            $terminals->each(function ($terminal) {
                $status = terminalStatusBaseOnProfileStatus($terminal->profile->status);
                if ($status) {
                    $terminal->status = $status;
                    $terminal->save();
                }
            });
        });
});

Artisan::command('migrateDB', function () {
    print PHP_EOL;
    print "Fetching Data From Server...";
    print PHP_EOL;
    \App\Libraries\TransferDB::run([
        User::class,
        \App\Models\Variables\Device::class,
        \App\Models\Profiles\Profile::class,
        \App\Models\Profiles\Customer::class,
        \App\Models\Profiles\Business::class,
        \App\Models\Profiles\Account::class,
        \App\Models\Profiles\ProfilesAccount::class,
        \App\Models\Profiles\ProfileMessage::class,
        \App\Models\Profiles\License::class,
    ]);
    print PHP_EOL;
});

Artisan::command('color', function () {
    print "\e[32mAll Customers Transferred Successfully\e[0m";
    print PHP_EOL;
    print "All Customers Transferred Successfully";
});
