<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\Agent;
use App\Models\Tickets\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $agents = Agent::with('type')
            ->orderBy('name', 'ASC')->get();
        $types = Type::orderBy('name', 'ASC')->get();
        $users = User::where(function ($query) {
            $query->where('level', 'OFFICE')->orWhere('level', 'SUPERUSER');
        })->get();
        return Inertia::render('Dashboard/Tickets/Agents', compact('agents', 'types', 'users'));
    }

    public function store(Request $request)
    {
        $request->validateWithBag('agentForm', [
            'user_id' => 'required|exists:users,id',
            'ticket_type_id' => 'required'
        ]);

        $user = User::find($request->get('user_id'));
        $request->merge(['name' => $user->name]);
        Agent::create($request->all());

        return redirect()->route('dashboard.tickets.agents.list');
    }

    public function update(Request $request)
    {
        $id = (int)$request->route('id');
        $agent = Agent::find($id);
        if (is_null($agent)) throw new NotFoundHttpException('اطلاعات کاربر یافت نشد.');
        $request->validateWithBag('agentForm', [
            'user_id' => 'required|exists:users,id',
            'ticket_type_id' => 'required'
        ]);

        $user = User::find($request->get('user_id'));
        $request->merge(['name' => $user->name]);
        $agent->fill($request->all());
        $agent->save();

        return redirect()->route('dashboard.tickets.agents.list');
    }

    public function destroy(Request $request)
    {
        $id = (int)$request->route('id');
        $agent = Agent::find($id);
        if (is_null($agent)) throw new NotFoundHttpException('اطلاعات کاربر یافت نشد.');

        $agent->delete();
        return redirect()->route('dashboard.tickets.agents.list');
    }

    public static function setTicketAgent(int $ticketTypeId)
    {
        $agents = Type::find($ticketTypeId)->agents()
            ->with(['openTickets' => function ($query) {
                $query->addSelect(['id', 'agent_id']);
            }])->get();
        $count = 0;
        $minimumTickets = 1000000;
        $admin = Agent::find(1);
        $selectedAgent = $admin;
        foreach ($agents as $agent) {
            if ($agent->id !== 1) {
                if ($count == 0) {
                    $minimumTickets = $agent->openTickets->count();
                    $selectedAgent = $agent;
                } else {
                    $tickets_count = $agent->openTickets->count();
                    if ($tickets_count < $minimumTickets) {
                        $minimumTickets = $tickets_count;
                        $selectedAgent = $agent;
                    }
                }
                $count++;
            }
        }

        return $selectedAgent;
    }
}
