<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (is_null($user) || $user->status !== 1) {
            auth()->guard('web')->logout();
            return redirect()->route('login')->withErrors(['username' => sprintf('حساب کاربری شما %s می‌باشد.', $user->statusText)]);
        }

        return $next($request);
    }
}
