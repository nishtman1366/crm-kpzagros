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

        return $next($request);
    }
}
