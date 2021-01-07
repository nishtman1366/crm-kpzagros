<?php

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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('dashboard')->name('dashboard.')->namespace('App\\Http\\Controllers')->group(function () {
    Route::get('', 'DashboardController@index')->name('main');

    Route::prefix('devices')->name('devices.')->group(function () {
        Route::get('', 'DeviceController@index')->name('list');
        Route::get('new', 'DeviceController@create')->name('create');
        Route::post('', 'DeviceController@store')->name('store');
        Route::get('{id}', 'DeviceController@view')->name('view');
        Route::put('{id}', 'DeviceController@update')->name('update');
        Route::delete('{id}', 'DeviceController@destroy')->name('destroy');

        Route::get('excel', 'DeviceController@downloadExcel')->name('downloadExcel');
        Route::post('excel', 'DeviceController@uploadExcel')->name('uploadExcel');
    });

    Route::prefix('users/{type}')->name('users.')->group(function () {
        Route::get('', 'UserController@index')->name('list');
        Route::get('new', 'UserController@create')->name('create');
        Route::post('', 'UserController@store')->name('store');
        Route::get('{id}', 'UserController@view')->name('view');
        Route::put('{id}', 'UserController@update')->name('update');
        Route::delete('{id}', 'UserController@destroy')->name('destroy');
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

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('', 'ReportController@index')->name('list');
    });

    Route::prefix('payments')->name('payments.')->namespace('Payments')->group(function () {
        Route::get('{paymentId}/confirm', 'PaymentController@confirm')->name('confirm');
    });

    Route::prefix('repairs')->name('repairs.')->namespace('Repairs')->group(function () {
        Route::get('', 'RepairController@index')->name('list');
        Route::get('new', 'RepairController@create')->name('create');
        Route::post('', 'RepairController@store')->name('store');
        Route::get('{repairId}', 'RepairController@view')->name('view');
        Route::put('{repairId}', 'RepairController@update')->name('update');
        Route::get('excel', 'RepairController@downloadExcel')->name('downloadExcel');
    });

    Route::prefix('profiles')->name('profiles.')->namespace('Profiles')->group(function () {
        Route::get('', 'ProfileController@index')->name('list');
        Route::get('new', 'ProfileController@create')->name('create');
        Route::get('excel', 'ProfileController@downloadExcel')->name('downloadExcel');
        Route::post('excel', 'ProfileController@uploadExcel')->name('uploadExcel');

        Route::prefix('{profileId}')->group(function () {
            Route::get('view', 'ProfileController@view')->name('view');
            Route::put('', 'ProfileController@update')->name('update');
            Route::put('serial', 'ProfileController@updateSerial')->name('update.serial');
            Route::put('terminal', 'ProfileController@updateTerminal')->name('update.terminal');
            Route::put('cancelRequest', 'ProfileController@cancelRequest')->name('update.cancelRequest');
            Route::put('cancelConfirm', 'ProfileController@cancelConfirm')->name('update.cancelConfirm');
            Route::put('changeRequest', 'ProfileController@changeRequest')->name('update.changeRequest');
            Route::put('changeConfirm', 'ProfileController@changeConfirm')->name('update.changeConfirm');
            Route::put('newSerial', 'ProfileController@newSerial')->name('update.newSerial');


            Route::prefix('customers')->name('customers.')->group(function () {
                Route::get('', 'CustomerController@index')->name('list');
                Route::get('new', 'CustomerController@create')->name('create');
                Route::post('', 'CustomerController@store')->name('store');
                Route::get('edit', 'CustomerController@edit')->name('edit');
                Route::PUT('', 'CustomerController@update')->name('update');
            });

            Route::prefix('businesses')->name('businesses.')->group(function () {
                Route::get('', 'BusinessController@index')->name('list');
                Route::get('new', 'BusinessController@create')->name('create');
                Route::post('', 'BusinessController@store')->name('store');
                Route::get('edit', 'BusinessController@edit')->name('edit');
                Route::PUT('', 'BusinessController@update')->name('update');
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
                Route::post('', 'LicenseController@store')->name('store');
                Route::delete('{licenseId}', 'LicenseController@destroy')->name('destroy');
                Route::get('downloadZipArchive','LicenseController@downloadZipArchive')->name('downloadZipArchive');
            });
        });
    });
});
