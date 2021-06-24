<?php

namespace App\Http\Middleware;

use App\Models\Form;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class HandleInertiaRequests
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('message')) {
            Inertia::share('message', $request->session()->get('message'));
        }

        $configs = [];
        $settings = Setting::orderBy('id', 'ASC')->get()->each(function ($item) use (&$configs) {
            $configs[Str::camel(strtolower($item->key))] = $item->value;
        });
        Inertia::share('configs', $configs);

        $user = Auth::user();
        if (!is_null($user)) {
            $notifications = $user->notifications->take(5)->each(function ($notification) {
                $notification->date = $notification->created_at->diffForHumans();
            });
            $unreadNotifications = $user->unreadNotifications;

            Inertia::share('notifications', $notifications);
            Inertia::share('unreadNotifications', $unreadNotifications);
        }

        return $next($request);
    }
}
