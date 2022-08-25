<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (is_null($user)) throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('', 'دسترسی به این بخش امکان‌پذیر نمی‌باشد.');
        if ($user->isSuperuser() || $user->isAdmin()) {
            return $next($request);
        }
        throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('', 'دسترسی به این بخش امکان‌پذیر نمی‌باشد.');
    }
}
