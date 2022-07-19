<?php

use App\Models\Profiles\Profile;
use App\Models\Variables\Device;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::pattern('id', '[0-9]+');
Route::pattern('profileId', '[0-9]+');
Route::pattern('customerId', '[0-9]+');
Route::pattern('repairId', '[0-9]+');
Route::pattern('postId', '[0-9]+');
Route::pattern('returnId', '[0-9]+');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('dashboard')->name('dashboard.')->namespace('App\\Http\\Controllers')->group(function () {
    Route::get('', [\App\Http\Controllers\DashboardController::class, 'index'])->name('main');

    Route::prefix('devices')->name('devices.')->group(function () {
        Route::get('', [\App\Http\Controllers\DeviceController::class, 'index'])->name('list');
        Route::get('new', [\App\Http\Controllers\DeviceController::class, 'create'])->name('create');
        Route::post('', [\App\Http\Controllers\DeviceController::class, 'store'])->name('store');
        Route::get('{device}', [\App\Http\Controllers\DeviceController::class, 'view'])->name('view');
        Route::put('{device}', [\App\Http\Controllers\DeviceController::class, 'update'])->name('update');
        Route::delete('{device}', [\App\Http\Controllers\DeviceController::class, 'destroy'])->name('destroy');
        Route::get('excel', [\App\Http\Controllers\DeviceController::class, 'downloadExcel'])->name('downloadExcel');
        Route::post('excel', [\App\Http\Controllers\DeviceController::class, 'uploadExcel'])->name('uploadExcel');
        Route::post('batchJob', [\App\Http\Controllers\DeviceController::class, 'batchJob'])->name('batchJob');
    });

    Route::prefix('users/{type}')->name('users.')->group(function () {
        Route::get('', [\App\Http\Controllers\UserController::class, 'index'])->name('list');
        Route::get('new', [\App\Http\Controllers\UserController::class, 'create'])->name('create');
        Route::post('', [\App\Http\Controllers\UserController::class, 'store'])->name('store');
        Route::get('{user}', [\App\Http\Controllers\UserController::class, 'view'])->name('view');
        Route::put('{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
        Route::delete('{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('settings')->name('settings.')->namespace('Settings')->group(function () {
        Route::get('', 'SettingController@index')->name('main');
        Route::put('', 'SettingController@update')->name('update');

        Route::prefix('devices')->name('devices.')->group(function () {
            Route::get('', 'DeviceController@index')->name('list');
            Route::post('', 'DeviceController@store')->name('store');
            Route::put('{deviceId}', 'DeviceController@update')->name('update');
            Route::delete('{deviceId}', 'DeviceController@destroy')->name('destroy');
        });

        Route::prefix('banks')->name('banks.')->group(function () {
            Route::get('', 'BankController@index')->name('list');
            Route::post('', 'BankController@store')->name('store');
            Route::put('{bankId}', 'BankController@update')->name('update');
            Route::delete('{bankId}', 'BankController@destroy')->name('destroy');
        });

        Route::prefix('psps')->name('psps.')->group(function () {
            Route::get('', 'PspController@index')->name('list');
            Route::post('', 'PspController@store')->name('store');
            Route::put('{pspId}', 'PspController@update')->name('update');
            Route::delete('{pspId}', 'PspController@destroy')->name('destroy');
        });

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::prefix('types')->name('types.')->group(function () {
                Route::get('', 'NotificationTypeController@index')->name('list');
                Route::post('', 'NotificationTypeController@store')->name('store');
                Route::put('{typeId}', 'NotificationTypeController@update')->name('update');
                Route::delete('{typeId}', 'NotificationTypeController@destroy')->name('destroy');
            });

            Route::prefix('events')->name('events.')->group(function () {
                Route::get('', 'NotificationEventController@index')->name('list');
                Route::post('', 'NotificationEventController@store')->name('store');
                Route::put('{eventId}', 'NotificationEventController@update')->name('update');
                Route::delete('{eventId}', 'NotificationEventController@destroy')->name('destroy');
            });
        });

        Route::prefix('repairTypes')->name('repairTypes.')->group(function () {
            Route::get('', 'RepairTypeController@index')->name('list');
            Route::post('', 'RepairTypeController@store')->name('store');
            Route::put('{repairTypeId}', 'RepairTypeController@update')->name('update');
            Route::delete('{repairTypeId}', 'RepairTypeController@destroy')->name('destroy');
        });

        Route::prefix('repairLocations')->name('repairLocations.')->group(function () {
            Route::get('', 'RepairLocationController@index')->name('list');
            Route::post('', 'RepairLocationController@store')->name('store');
            Route::put('{repairLocationId}', 'RepairLocationController@update')->name('update');
            Route::delete('{repairLocationId}', 'RepairLocationController@destroy')->name('destroy');
        });

        Route::prefix('licenses')->name('licenses.')->group(function () {
            Route::get('', 'LicenseController@index')->name('list');
            Route::post('', 'LicenseController@store')->name('store');
            Route::put('{licenseId}', 'LicenseController@update')->name('update');
            Route::delete('{licenseId}', 'LicenseController@destroy')->name('destroy');
        });
    });

    Route::prefix('reports')->name('reports.')->namespace('Reports')->group(function () {
        Route::get('', 'ReportController@index')->name('main');
        Route::get('profiles', 'ProfileController@index')->name('profiles');
        Route::get('devices', 'DeviceController@index')->name('devices');
        Route::get('repairs', 'RepairController@index')->name('repairs');
        Route::get('list', 'ReportController@index2')->name('list');
    });

    Route::prefix('payments')->name('payments.')->namespace('Payments')->group(function () {
        Route::get('{paymentId}/confirm', 'PaymentController@confirm')->name('confirm');
    });

    Route::prefix('repairs')->name('repairs.')->namespace('Repairs')->group(function () {
        Route::get('', 'RepairController@index')->name('list');
        Route::get('new', 'RepairController@create')->name('create');
        Route::post('', 'RepairController@store')->name('store');
        Route::get('{repairId}', 'RepairController@view')->name('view');
        Route::get('{repairId}/print', 'RepairController@print')->name('print');
        Route::put('{repairId}', 'RepairController@update')->name('update');
        Route::put('{repairId}/updateStatusByAdmin', 'RepairController@updateStatusByAdmin')->name('updateStatusByAdmin');
        Route::get('excel', 'RepairController@downloadExcel')->name('downloadExcel');
    });

    Route::prefix('returns')->name('returns.')->namespace('Returns')->group(function () {
        Route::get('', 'ReturnController@index')->name('list');
        Route::get('new', 'ReturnController@create')->name('create');
        Route::post('', 'ReturnController@store')->name('store');
        Route::get('{returnId}', 'ReturnController@view')->name('view');
        Route::post('{returnId}', 'ReturnController@update')->name('update');
        Route::put('{returnId}/status', 'ReturnController@updateStatus')->name('updateStatus');
    });

    Route::prefix('profiles')->name('profiles.')->namespace('Profiles')->group(function () {
        Route::get('', 'ProfileController@index')->name('list');
        Route::get('new', 'ProfileController@create')->name('create');
        Route::get('excel', 'ProfileController@downloadExcel')->name('downloadExcel');
        Route::post('excel', 'ProfileController@uploadExcel')->name('uploadExcel');

        Route::prefix('{profile}')->group(function () {
            Route::prefix('update')->name('update.')->group(function () {
                Route::put('terminals/{terminal}/serial', [\App\Http\Controllers\Profiles\TerminalController::class, 'updateSerial'])->name('serial');
                Route::put('terminals/{terminal}/number', [\App\Http\Controllers\Profiles\TerminalController::class, 'terminalNumber'])->name('terminal');
                Route::put('terminals/{terminal}/serial/reject', [\App\Http\Controllers\Profiles\TerminalController::class, 'rejectSerial'])->name('reject_serial');
                Route::put('terminals/{terminal}/cancel', [\App\Http\Controllers\Profiles\TerminalController::class, 'cancelSerial'])->name('cancel_terminal');
                Route::put('terminals/{terminal}/confirmCancel', [\App\Http\Controllers\Profiles\TerminalController::class, 'confirmCancelSerial'])->name('confirm_cancel');
                Route::put('terminals/{terminal}/install', [\App\Http\Controllers\Profiles\TerminalController::class, 'install'])->name('install');
                Route::put('terminals/{terminal}/changeSerial', [\App\Http\Controllers\Profiles\TerminalController::class, 'changeSerial'])->name('change_serial');
                Route::put('terminals/{terminal}/confirmChange', [\App\Http\Controllers\Profiles\TerminalController::class, 'confirmChangeSerial'])->name('confirm_change');

                Route::put('merchant', [\App\Http\Controllers\Profiles\ProfileController::class, 'updateMerchant'])->name('merchant');

                Route::put('licenses/confirm', [\App\Http\Controllers\Profiles\ProfileController::class, 'confirmLicenses'])->name('licenses.confirm');

                Route::put('status', [\App\Http\Controllers\Profiles\ProfileController::class, 'updateStatus'])->name('status');
            });
        });


        Route::prefix('{profile}')->group(function () {
            Route::get('view', [\App\Http\Controllers\Profiles\ProfileController::class, 'view'])->name('view');
            Route::get('/deliveryForm', [\App\Http\Controllers\Profiles\ProfileController::class, 'deliveryForm'])->name('delivery.form');
            Route::put('', [\App\Http\Controllers\Profiles\ProfileController::class, 'update'])->name('update');
            Route::put('updateStatus', [App\Http\Controllers\Profiles\ProfileController::class, 'updateStatus'])->name('update.status');
            Route::put('setType', [\App\Http\Controllers\Profiles\ProfileController::class, 'setType'])->name('update.type');

            Route::prefix('customers')->name('customers.')->group(function () {
                Route::get('new', [\App\Http\Controllers\Profiles\CustomerController::class, 'create'])->name('create');
                Route::post('', [\App\Http\Controllers\Profiles\CustomerController::class, 'store'])->name('store');
                Route::get('edit', [\App\Http\Controllers\Profiles\CustomerController::class, 'edit'])->name('edit');
                Route::put('', [\App\Http\Controllers\Profiles\CustomerController::class, 'update'])->name('update');
            });

            Route::prefix('businesses')->name('businesses.')->group(function () {
                Route::get('new', [\App\Http\Controllers\Profiles\BusinessController::class, 'create'])->name('create');
                Route::post('', [\App\Http\Controllers\Profiles\BusinessController::class, 'store'])->name('store');
                Route::get('edit', [\App\Http\Controllers\Profiles\BusinessController::class, 'edit'])->name('edit');
                Route::put('', [\App\Http\Controllers\Profiles\BusinessController::class, 'update'])->name('update');
            });

            Route::prefix('accounts')->name('accounts.')->group(function () {
                Route::get('', 'AccountController@index')->name('list');
                Route::get('new', 'AccountController@create')->name('create');
                Route::post('', 'AccountController@store')->name('store');
                Route::get('edit', 'AccountController@edit')->name('edit');
                Route::PUT('', 'AccountController@update')->name('update');
            });

            Route::prefix('devices')->name('devices.')->group(function () {
                Route::get('', 'DeviceController@index')->name('list');
                Route::get('new', 'DeviceController@create')->name('create');
                Route::post('', 'DeviceController@store')->name('store');
                Route::get('edit', 'DeviceController@edit')->name('edit');
                Route::put('update', 'DeviceController@update')->name('update');
            });

            Route::prefix('licenses')->name('licenses.')->group(function () {
                Route::post('', [\App\Http\Controllers\Profiles\LicenseController::class, 'store'])->name('store');
                Route::delete('{licenseId}', [\App\Http\Controllers\Profiles\LicenseController::class, 'destroy'])->name('destroy');
                Route::get('downloadZipArchive', [\App\Http\Controllers\Profiles\LicenseController::class, 'downloadZipArchive'])->name('downloadZipArchive');
            });
        });
    });

    Route::prefix('posts')->name('posts.')->namespace('Posts')->group(function () {
        Route::get('', 'PostController@index')->name('list');
        Route::get('create', 'PostController@create')->name('create');
        Route::post('', 'PostController@store')->name('store');
        Route::get('{postId}', 'PostController@edit')->name('edit');
        Route::put('{postId}', 'PostController@update')->name('update');
        Route::delete('{postId}', 'PostController@destroy')->name('destroy');

        Route::get('archive', 'PostController@archive')->name('archive');
        Route::get('{postId}/view', 'PostController@view')->name('view');

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('', 'CategoryController@index')->name('list');
            Route::get('{categoryId}', 'CategoryController@view')->name('view');
            Route::post('', 'CategoryController@store')->name('store');
            Route::put('{categoryId}', 'CategoryController@update')->name('update');
            Route::delete('{categoryId}', 'CategoryController@destroy')->name('destroy');
        });
    });

    Route::prefix('tickets')->name('tickets.')->namespace('Tickets')->group(function () {
        Route::get('', 'TicketController@index')->name('list');
        Route::get('new', 'TicketController@create')->name('create');
        Route::post('', 'TicketController@store')->name('store');
        Route::get('{id}', 'TicketController@view')->name('view');
        Route::put('{id}', 'TicketController@update')->name('update');
        Route::delete('{id}', 'TicketController@destroy')->name('destroy');

        Route::post('{id}/reply', 'ReplyController@store')->name('reply.store');

        Route::prefix('types')->name('types.')->group(function () {
            Route::get('', 'TypeController@index')->name('list');
            Route::post('', 'TypeController@store')->name('store');
            Route::put('{id}', 'TypeController@update')->name('update');
            Route::delete('{id}', 'TypeController@destroy')->name('destroy');
        });

        Route::prefix('agents')->name('agents.')->group(function () {
            Route::get('', 'AgentController@index')->name('list');
            Route::post('', 'AgentController@store')->name('store');
            Route::put('{id}', 'AgentController@update')->name('update');
            Route::delete('{id}', 'AgentController@destroy')->name('destroy');
        });
    });

    Route::prefix('notifications')->name('notifications.')->namespace('Notifications')->group(function () {
        Route::get('', 'BatchNotificationController@index')->name('list');
        Route::post('', 'BatchNotificationController@store')->name('store');
        Route::put('{id}', 'BatchNotificationController@update')->name('update');
        Route::delete('{id}', 'BatchNotificationController@destroy')->name('destroy');

        Route::post('{id}/receptions', 'NotificationReceptionController@store')->name('receptions.store');
        Route::post('{id}/send', 'BatchNotificationController@send')->name('send');
        Route::get('{id}/details', 'BatchNotificationController@details')->name('details');
    });
});


Route::get('listSerials', function () {
    $profiles = Profile::with('customer')
        ->with('device')
        ->where('new_device_id', '!=', null)
        ->get()->each(function ($profile) {
            $newDevice = Device::where('id', $profile->new_device_id)->get()->first();
            echo '<p style="direction: rtl">' . $profile->id . ',' . $profile->customer->fullName . ',' . $profile->customer->national_code . ',' . $profile->device_id . ',' . (!is_null($profile->device) ? $profile->device->serial : '') . ',' . $profile->new_device_id . ',' . (!is_null($newDevice) ? $newDevice->serial : '') . '</p>';
        });
});

Route::get('ipg', function (\Illuminate\Http\Request $request) {
    $referenceCode = $request->query('referenceCode');
    return view('ipg', ['referenceCode' => $referenceCode]);
});

Route::get('duplicates', function (\Illuminate\Http\Request $request) {
    $duplicates = collect();

    $pspId = $request->query('psp');
    if ($pspId) {
        $profiles = Profile::with('customer')
            ->with('psp')
            ->with('business')
            ->where('psp_id', $pspId)
            ->whereIn('status', [5, 6, 7, 8, 10, 11, 12, 13, 14, 15, 16])
            ->selectRaw('`merchant_id`, COUNT(`merchant_id`) as m ')
            ->groupBy('merchant_id')
            ->havingRaw('m > 1')
            ->orderBy('id', 'ASC')
            ->get();

        foreach ($profiles as $profile) {
            $profiles = Profile::with('customer')
                ->with('psp')
                ->with('business')
                ->where('psp_id', $pspId)
                ->whereIn('status', [5, 6, 7, 8, 10, 11, 12, 13, 14, 15, 16])
                ->where('merchant_id', $profile->merchant_id)
                ->get()
                ->each(function ($p) use (&$duplicates) {
                    $duplicates->push($p);
                });
        }
    }

    $psps = \App\Models\Variables\Psp::orderBy('name', 'ASC')->get();
    return view('Temp.duplicates', compact('duplicates', 'psps', 'pspId'));
})->name('duplicates');

Route::get('duplicates/{profileId}', function (\Illuminate\Http\Request $request) {
    $parent = Profile::find((int)$request->route('profileId'));
    if (is_null($parent)) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('پروفایل یافت نشد.');

    $children = Profile::with('terminals')
        ->where('merchant_id', $parent->merchant_id)
        ->where('id', '!=', $parent->id)
        ->get()
        ->each(function ($profile) use ($parent) {
            $profile->terminals->each(function ($terminal) use ($parent) {
                $terminal->profile_id = $parent->id;
                $terminal->save();
            });
            $profile->status = 255;
            $profile->parent_profile_id = $profile->id;
            $profile->save();
        });

    return redirect()->back();
})->name('duplicateProfiles');


