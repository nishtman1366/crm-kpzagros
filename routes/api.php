<?php

use App\Models\Posts\Post;
use App\Models\Profiles\Profile;
use App\Models\Variables\Device;
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
    Route::get('devices/{terminal}', [\App\Http\Controllers\DeviceController::class, 'getDevicesListByAjax']);
    Route::get('newDevices/{profileId}', 'DeviceController@getNewDevicesListByAjax');
    Route::get('deviceTypes/{terminal}', [\App\Http\Controllers\DeviceController::class, 'getDeviceTypesListByAjax']);
    Route::get('devices/types/{type}', [\App\Http\Controllers\DeviceController::class, 'deviceTypes'])->name('dashboard.devices.types');
    Route::get('devices/{device}/view', [\App\Http\Controllers\DeviceController::class, 'view'])->name('api.dashboard.devices.view');

    Route::post('searchProfiles', [\App\Http\Controllers\DashboardController::class, 'searchProfiles']);
    Route::post('searchDevices', [\App\Http\Controllers\DashboardController::class, 'searchDevices']);
    Route::post('searchRepairs', [\App\Http\Controllers\DashboardController::class, 'searchRepairs']);

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

        Route::get('excel/status', [\App\Http\Controllers\Profiles\ProfileController::class, 'excelStatus']);
        Route::get('excel/cancel', [\App\Http\Controllers\Profiles\ProfileController::class, 'cancelExportJob']);
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
Route::get('profiles/{terminal}', [\App\Http\Controllers\ServiceController::class, 'getProfileByTerminal']);

Route::get('captcha', [\App\Http\Controllers\RegistrationController::class, 'refreshCaptcha'])->name('captcha.refresh');

Route::prefix('crm')->group(function () {
    Route::post('users/mobile', function (Request $request) {
        $user = \App\Models\Profiles\Customer::where('mobile', $request->get('mobile'))->get()->first();
        if (is_null($user)) {
            return response()->json(['message' => "اطلاعات کاربر یافت نشد."], 404);
        }
        return response()->json(['user' => $user]);
    });
});

Route::post('apiService/login', function (Request $request) {
    Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')]);
    $user = Auth::user();
    $data = $user->createToken('api_token');
    return response()->json($data);
});
Route::prefix('apiService')->middleware('auth:sanctum')->group(function () {

    Route::get('psp', function () {
        return \App\Models\Variables\Psp::orderBy('name', 'ASC')->whereStatus(1)->get();
    });
    Route::get('banks', function () {
        return \App\Models\Variables\Bank::orderBy('name', 'ASC')->whereStatus(1)->get();
    });

    Route::get('profiles', function (Request $request) {
        $profiles = Profile::with('customer')
            ->with('business')
            ->with('accounts')
            ->with('accounts.account')
            ->with('accounts.account.bank')
            ->with('psp')
            ->with('user')
            ->with('terminals')
            ->with('terminals.device')
            ->with('terminals.device.user')
            ->with('terminals.deviceType')
            ->with('terminals.deviceConnectionType')
            ->with('user.parent')
            ->with('messages')
            ->with('licenses')
            ->with('licenses.type')
            ->where(function ($query) use ($request) {
                if ($request->query->has('psp_id')) {
                    $query->where('psp_id', $request->query('psp_id'));
                }
            })
            ->paginate(100);

        return response()->json($profiles);
    });

    Route::get('devices', function (Request $request) {
        $profiles = Device::with('deviceType')
            ->with('user')
            ->with('user.parent')
            ->with('terminal')
            ->paginate(100);

        return response()->json($profiles);
    });

    Route::get('profiles/{profile}', function (Profile $profile) {
        $profile->load([
            'customer',
            'business',
            'business.city',
            'business.province',
            'accounts',
            'accounts.account',
            'psp',
            'terminals',
            'terminals.device',
            'terminals.deviceType',
            'terminals.deviceConnectionType'
        ]);
        return response()->json($profile);
    });
    Route::post('profiles/search', function (Request $request) {
//        sleep(3);
        $profiles = Profile::with('customer')
            ->with('business')
            ->with('business.subCategory')
            ->with('business.city')
            ->with('terminals')
            ->with('accounts')
            ->with('accounts.account')
            ->whereHas('customer', function ($query) use ($request) {
                $query->where('mobile', $request->get('mobile'));
            })
            ->get();
        return response()->json($profiles);
//        https://jamservice.pna.co.ir/services/api/RequestService/AddNewRequest
        if (is_null($profile)) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('مشتری با مشخصات مورد نظر یافت نشد.');
//        $profile->load([
//            'customer',
//            'business',
//            'business.city',
//            'business.province',
//            'accounts',
//            'accounts.account',
//            'psp',
//            'terminals',
//            'terminals.device',
//            'terminals.deviceType',
//            'terminals.deviceConnectionType'
//        ]);
        return response()->json($profile);
    });

    Route::get('licenses', function () {
        $types = \App\Models\Profiles\LicenseType::orderBy('id')->get();
        return response()->json($types);
    });

    Route::put('profiles/{profile}', [\App\Http\Controllers\Profiles\ProfileController::class, 'update']);

    Route::get('users', function () {
        $user = \App\Models\User::orderBy('id')
            ->get();
        return response()->json($user);
    });

    Route::get('posts', function () {
        $posts = Post::with('files', 'category', 'levels', 'videos')->orderBy('id')->get();

        return response()->json($posts);
    });

    Route::get('tickets', function () {
        $posts = \App\Models\Tickets\Ticket::with('files', 'category', 'levels', 'videos')->orderBy('id')->get();

        return response()->json($posts);
    });

    Route::post('customers', function (Request $request) {
        $list = collect([]);
        $username = $request->get('username');
        $agent = \App\Models\User::withCount('profiles')->where('username', $username)->first();

        if ($agent) {
            $agent_id = $agent->id;
            $agent_name = $agent->name;
            Profile::with('terminals')
                ->where('user_id', $agent->id)
                ->offset($request->get('page') * 500)
                ->limit(10000)
                ->get()
                ->each(function ($profile) use (&$list) {
                    $profile->terminals->each(function ($terminal) use (&$list) {
                        if(!is_null($terminal['terminal_number'])) {
                            $list->push([
                                'profile_id' => $terminal['profile_id'],
                                'terminal' => $terminal['terminal_number'],
                            ]);
                        }
                    });
                });
        }
//
//
//        \App\Models\Profiles\Terminal::with('profile')
//            ->with('profile.user')
//            ->with('profile.customer')
//            ->with('profile.customer.user')
//            ->paginate(1000)
//            ->each(function ($terminal) use (&$list) {
//                if ($terminal->profile->user) {
//                    $list->push([
//                        'agent_id' => $terminal->profile->user->username,
//                        'terminal' => $terminal['terminal_number'],
//                    ]);
//                } else {
//                    if ($terminal->profile->customer && $terminal->profile->customer->user) {
//                        $list->push([
//                            'agent_id' => $terminal->profile->customer->user->username,
//                            'terminal' => $terminal['terminal_number'],
//                        ]);
//                    }
//                }
//
//            });
//        \App\Models\Profiles\Customer::with('profile')
//            ->with('profile.user')
//            ->with('profile.terminals')
//            ->paginate(500)
//            ->each(function ($crmCustomer) use (&$list) {
//                foreach ($crmCustomer->profile->terminals as $terminal) {
//                    $list->push([
//                        'agent_id' => $crmCustomer->profile->user->username,
//                        'national_code' => $crmCustomer['national_code'],
//                        'terminal' => $terminal['terminal_number'],
//                    ]);
//                }
//            });


//        $request->collect('customers')->each(function ($customer) use ($list) {
//            \App\Models\Profiles\Customer::with('profile')
//                ->with('profile.user')
//                ->with('profile.terminals')
//                ->where('national_code', $customer['national_code'])
//                ->get()
//                ->each(function ($crmCustomer) use (&$list, $customer) {
//                    if ($customer['terminal']) {
//                        $terminal = $crmCustomer->profile->terminals->where('terminal', $customer['terminal'])->first();
//                        if (!is_null($terminal)) {
//                            $list->push([
//                                'agent_id' => $crmCustomer->profile->user->username,
//                                'national_code' => $customer['national_code'],
//                                'terminal' => $customer['terminal'],
//                            ]);
//                        }
//                    }
//                });
//        });

        return response()->json($list);
    });
});
