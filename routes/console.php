<?php

use App\Exceptions\NotificationException;
use App\Libraries\TemplateEngine;
use App\Models\User;
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
//    $notification = \App\Models\Notifications\BatchNotification::with('receptions')
//        ->find(3);
//    $x = $notification->receptions()->chunk(50, function ($receptions) {
//        print_r($receptions->count());
//        print(PHP_EOL);
//    });
//    $job = dispatch(new \App\Jobs\Notifications\SendNotification($notification, '', 'club'))
//        ->onQueue('notificationsQueue');
//    print_r($job);
    $client = new  Client('RwoB81G8VWdrZ4xc-GmNp96xPlk1rvdcYmUGnSCvWZY=');
    $d = $client->fetchStatuses('209076264');
    print_r($d);
});

Artisan::command('transfer', function () {
    $array = ['11123',
        '11121',
        '11119',
        '11118',
        '11117',
        '11116',
        '11114',
        '11106',
        '11112',
        '11109',
        '11111',
        '11113',
        '11107',
        '11105',
        '11103',
        '11104',
        '10871',
        '10627',
        '11097',
        '11098',
        '11095',
        '11093',
        '11096',
        '11094',
        '11091',
        '11092',
        '11090',
        '11089',
        '11088',
        '11087',
        '11084',
        '11085',
        '11078',
        '11081',
        '11032',
        '11079',
        '11076',
        '10889',
        '11075',
        '11074',
        '10973',
        '11072',
        '11073',
        '11065',
        '11071',
        '11033',
        '11070',
        '11068',
        '11067',
        '11066',
        '11038',
        '11063',
        '11037',
        '11062',
        '11060',
        '10978',
        '11058',
        '11057',
        '11056',
        '11055',
        '11053',
        '11052',
        '11051',
        '11050',
        '11049',
        '11048',
        '11047',
        '11046',
        '11045',
        '11044',
        '11043',
        '11042',
        '11028',
        '11041',
        '11040',
        '11039',
        '11036',
        '11035',
        '11034',
        '11029',
        '11031',
        '11027',
        '11023',
        '11025',
        '10926',
        '11021',
        '11020',
        '11018',
        '11017',
        '11016'];
    $directories = \Illuminate\Support\Facades\Storage::disk('licenses')->directories('profiles');
    foreach ($directories as $directory) {
        $directoryNameList = explode('/', $directory);
        $dirName = $directoryNameList[count($directoryNameList) - 1];
        if (is_array($dirName, $array)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);
            print(sprintf('Directory %s created', $directory) . PHP_EOL);
            $files = \Illuminate\Support\Facades\Storage::disk('licenses')->files($directory);
            foreach ($files as $file) {
                $stream = \Illuminate\Support\Facades\Storage::disk('licenses')->readStream($file);
                \Illuminate\Support\Facades\Storage::disk('public')->writeStream($file, $stream);
                print(sprintf('File %s created', $file) . PHP_EOL);
            }
            print(sprintf('directory %s fully coped to new disk', $directory) . PHP_EOL);
            print('#####################################################' . PHP_EOL);
        }
    }
    print('All directories successfully copied to new disk' . PHP_EOL);
});
