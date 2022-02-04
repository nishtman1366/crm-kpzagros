<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    Route::post('searchProfiles', 'DashboardController@searchProfiles');
    Route::post('searchDevices', 'DashboardController@searchDevices');
    Route::post('searchRepairs', 'DashboardController@searchRepairs');

    /*
     * وب سرویس های مربوط به نرم افزار موبایل
     */
    Route::prefix('profiles')->namespace('Profiles')->group(function () {
        Route::get('', 'ProfileController@index');
        Route::post('create', 'ProfileController@create');

        Route::post('{profileId}/customers/store', 'CustomerController@store');

        Route::get('{profileId}/businesses/create', 'BusinessController@create');
        Route::post('{profileId}/businesses/store', 'BusinessController@store');

        Route::get('{profileId}/accounts', 'AccountController@index');
        Route::get('{profileId}/accounts/create', 'AccountController@create');
        Route::post('{profileId}/accounts/store', 'AccountController@store');

    });
});

Route::post('login', function (Request $request) {
    if (Auth::attempt($request->only(['username', 'password']))) {
        $user = Auth::user();
        $token = $user->createToken($request->get('loginType'));

        return response()->json([
            'success' => true,
            'token' => $token->plainTextToken,
            'user' => $user
        ]);
    }
    return response()->json([
        'success' => false,
        'message' => 'login error'
    ]);
});

Route::middleware('auth:sanctum')->get('logout', function (Request $request) {
    Auth::user()->currentAccessToken()->delete();
    return response()->json(['success' => true]);
});

Route::get('domain', function () {
    $domain = request()->getHttpHost();
    return response()->json($domain);
});


Route::get('devices/{serial}', [\App\Http\Controllers\ServiceController::class, 'getDeviceBySerial']);
Route::get('profile/{terminal}', [\App\Http\Controllers\ServiceController::class, 'getProfileByTerminal']);
