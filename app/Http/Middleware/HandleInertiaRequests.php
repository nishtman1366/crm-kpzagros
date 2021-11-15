<?php

namespace App\Http\Middleware;

use App\Models\Form;
use App\Models\Setting;
use App\Models\Tickets\Ticket;
use Carbon\Carbon;
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
        $domain = request()->getHttpHost();
        $settings = Setting::orderBy('id', 'ASC')
            ->where(function ($query)use($domain){
                $query->where('domain', $domain)
                    ->orWhere('domain','kpzagros-crm.com');
            })
            ->get()
            ->each(function ($item) use (&$configs) {
            $configs[Str::camel(strtolower($item->key))] = $item->value;
        });
        Inertia::share('configs', $configs);

        $user = Auth::user();
        if (!is_null($user)) {
            $tickets = Ticket::with('events')->where(function ($query) use ($user) {
                if ($user->isSupportAgent()) {
                    $query->where('agent_id', $user->agent_id)
                        ->whereIn('status', [0, 3, 4]);
                } else {
                    $query->where('user_id', $user->id)
                        ->where('status', 2);
                }
            })
                ->orderBy('id', 'DESC')
                ->get()
                ->each(function ($ticket) {
                    $ticket->date = $ticket->updated_at->diffForHumans();
                });

            Inertia::share('userTickets', $tickets);
        }

        return $next($request);
    }
}
