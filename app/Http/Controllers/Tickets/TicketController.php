<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\Agent;
use App\Models\Tickets\File;
use App\Models\Tickets\Reply;
use App\Models\Tickets\Ticket;
use App\Models\Tickets\Type;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $i = 1;
        $tickets = Ticket::orderBy('id', 'DESC')
            ->with('user')
            ->with('type')
            ->with('agent')
            ->where(function ($query) use ($user) {
                if ($user->isAdmin() || $user->isSuperUser()) {

                } else {
                    if ($user->isSupportAgent()) {
                        $query->where('agent_id', $user->agent_id)->orWhere('user_id', $user->id);
                    } else {
                        $query->where('user_id', $user->id);
                    }
                }
            })
            ->get()
            ->each(function ($ticket) use (&$i) {
                $ticket->no = $i;
                $i++;
            });

        $types = Type::where('status', true)->get();
        $agents = Agent::orderBy('id', 'ASC')->get();
        $userTypes = [
            ['id' => 'ADMIN', 'name' => 'مدیر سیستم'],
            ['id' => 'AGENT', 'name' => 'نمایندگان'],
            ['id' => 'MARKETER', 'name' => 'بازاریابان'],
            ['id' => 'OFFICE', 'name' => 'کاربران اداری'],
            ['id' => 'TECHNICAL', 'name' => 'کاربران فنی'],
        ];
        $users = User::orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Tickets/List', compact('tickets', 'types', 'agents', 'userTypes', 'users'));
    }

    public function store(Request $request)
    {
        $request->validateWithBag('ticketForm', [
            'title' => 'required',
            'ticket_type_id' => 'required',
            'agent_id' => 'required',
            'body' => 'required'
        ]);

        $user = Auth::user();
        if ($user->isSuperUser() || $user->isAdmin() || $user->isSupportAgent()) {
            $request->merge(['status' => 2]);
        } else {
            $request->merge(['user_id' => $user->id]);
        }
//        $ticketTypeId = (int)$request->get('ticket_type_id');
//        $agent = AgentController::setTicketAgent($ticketTypeId);
//
//        if (!is_null($agent)) {
//            $request->merge(['agent_id' => $agent->id]);
//        }

        $request->merge(['code' => $this->createTicketCode()]);
        $ticket = Ticket::create($request->all());
        if ($request->hasFile('files')) {
            $files = $request->file('files', []);
            FileController::upload($files, $ticket->id, null);
        }
        EventController::store($ticket, $user);
        return redirect()->route('dashboard.tickets.list');
    }

    public function view(Request $request)
    {
        $id = (int)$request->route('id');
        $ticket = Ticket::with('user')
            ->with('type')
            ->with('files')
            ->with('events')
            ->with('agent')
            ->with('replies')
            ->with('replies.user')
            ->with('replies.files')
            ->find($id);
        if (is_null($ticket)) throw new NotFoundHttpException('درخواست مورد نظر یافت نشد.');

        $types = Type::where('status', true)->orderBy('id', 'ASC')->get();
        $agents = Agent::orderBy('id', 'ASC')->get();
        return Inertia::render('Dashboard/Tickets/ViewTicket', compact('ticket', 'types', 'agents'));
    }

    public function update(Request $request)
    {
        $id = (int)$request->route('id');
        $ticket = Ticket::with('replies')->find($id);
        if (is_null($ticket)) throw new NotFoundHttpException('درخواست مورد نظر یافت نشد.');
        $user = Auth::user();
        $status = (int)$request->get('status');
        if ($status == 4) {
            $request->validateWithBag('ticketForm', [
                'ticket_type_id' => 'required',
                'agent_id' => 'required'
            ]);
        }
        $ticketTypeId = (int)$request->get('ticket_type_id');
//        if ($status === 4) {
//            $agent = AgentController::setTicketAgent($ticketTypeId);
//
//            if (!is_null($agent)) {
//                $request->merge(['agent_id' => $agent->id]);
//            }
//        }

        $ticket->fill($request->all());
        $ticket->save();
        EventController::store($ticket, $user);
        return redirect()->route('dashboard.tickets.list');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdmin() || $user->isSuperUser()) {
            $id = (int)$request->route('id');
            $ticket = Ticket::with('replies')->find($id);
            if (is_null($ticket)) throw new NotFoundHttpException('درخواست مورد نظر یافت نشد.');
            $ticket->replies()->delete();
            $ticket->delete();

            return redirect()->route('dashboard.tickets.list');
        } else {
            throw new UnauthorizedHttpException('شما اجازه دسترسی به این بخش را ندارید.');
        }
    }

    private function createTicketCode()
    {
        $code = rand(111111, 999999);
        $codeExistence = Ticket::where('code', $code)->exists();
        if ($codeExistence) return $this->createTicketCode();

        return $code;
    }
}
