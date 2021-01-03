<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::pattern('id', '[0-9]+');
Route::pattern('profileId', '[0-9]+');
Route::pattern('customerId', '[0-9]+');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->prefix('dashboard')->namespace('App\\Http\\Controllers')->group(function () {
    Route::get('devices/{profileId}', 'DeviceController@getDevicesListByAjax');
    Route::get('newDevices/{profileId}', 'DeviceController@getNewDevicesListByAjax');
    Route::get('deviceTypes/{profileId}', 'DeviceController@getDeviceTypesListByAjax');

    Route::get('profiles/{profileId}/newDevice', 'Profiles\\ProfileController@getNewDeviceByAjax')->name('getNewDeviceByAjax');
    Route::get('profiles/{profileId}/newDeviceType', 'Profiles\\ProfileController@getNewDeviceTypeByAjax')->name('getNewDeviceTypeByAjax');
});
