<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class LoginAsUserController extends Controller
{
    public function login(User $user,Request $request)
    {
        $admin = Auth::user();
        if ($admin->level === 'ADMIN' || $admin->level === 'SUPERUSER') {
            Auth::guard()->login($user);
            session()->put('loginByAdmin', true);
            session()->put('admin_id', $admin->id);
            session()->flash('message', sprintf('ورود به‌عنوان «%s» انجام‌شد.', $user->name));

            return redirect()->route('dashboard.main');
        }

        throw new UnauthorizedHttpException('', 'دسترسی غیرمجاز');
    }
}
