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
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $typeId = $request->query('typeId');
        $statusId = $request->query('statusId', 255);
        $agentId = $request->query('agentId');
        $searchQuery = $request->query('query');

        $tickets = Ticket::orderBy('id', 'DESC')
            ->with('user')
            ->with('type')
            ->with('agent')
            ->where(function ($query) use ($user) {
                if ($user->isAdmin() || $user->isSuperUser()) {

                } else {
                    if ($user->isSupportAgent()) {
                        $query->where('agent_id', $user->agent_id)
                            ->orWhere('user_id', $user->id);
                    } else {
                        $query->where('user_id', $user->id);
                    }
                }
            })
            ->where(function ($query) use ($typeId, $statusId, $agentId, $searchQuery) {
                if (!is_null($typeId) && $typeId != 0) $query->where('ticket_type_id', $typeId);
                if (!is_null($agentId) && $agentId != 0) $query->where('user_id', $agentId);
                if ($statusId != 255) $query->where('status', $statusId);
                if (!is_null($searchQuery)) $query->where('code', 'LIKE', '%' . $searchQuery . '%');
            })
            ->paginate(30);
        $paginatedLinks = paginationLinks($tickets->appends($request->query->all()));

        $types = Type::where('status', true)->get();
        $agents = Agent::where('status', true)->orderBy('id', 'ASC')->get();
        $userTypes = [
            ['id' => 'ADMIN', 'name' => 'مدیر سیستم'],
            ['id' => 'AGENT', 'name' => 'نمایندگان'],
            ['id' => 'MARKETER', 'name' => 'بازاریابان'],
            ['id' => 'OFFICE', 'name' => 'کاربران اداری'],
            ['id' => 'TECHNICAL', 'name' => 'کاربران فنی'],
            ['id' => 'ACCOUNTING', 'name' => 'حسابداری'],
        ];
        $users = User::orderBy('name', 'ASC')->get();
        $statuses = [
            0 => 'ثبت شده',
            1 => 'در حال بررسی',
            2 => 'پاسخ داده شده',
            3 => 'پاسخ کاربر',
            4 => 'منتقل شده',
            99 => 'پایان یافته',
        ];
        $agentUsers = User::where('level', 'AGENT')->get();
        return Inertia::render('Dashboard/Tickets/List', [
            'tickets' => $tickets,
            'types' => $types,
            'statuses' => $statuses,
            'agentUsers' => $agentUsers,

            'typeId' => (int)$typeId,
            'statusId' => (int)$statusId,
            'agentId' => (int)$agentId,
            'searchQuery' => $searchQuery,

            'agents' => $agents,
            'userTypes' => $userTypes,
            'users' => $users,
            'paginatedLinks' => $paginatedLinks,
        ]);
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
            $reception = User::where('id', $request->get('user_id'))->get()->first();
            if($reception->isSuperUser() || $reception->isAdmin() || $reception->isSupportAgent()){
                $request->merge(['status' => 0]);
            }else{
                $request->merge(['status' => 2]);
            }
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

    public function authorizeForPassword(Ticket $ticket, Request $request)
    {
        $request->validateWithBag('passwordForm', [
            'password' => 'required'
        ]);
        $password = $request->get('password');
        if (Hash::check($password, $ticket->password)) {
            $request->session()->flash('userAuthorizedForTicket', true);
            return redirect()->route('dashboard.tickets.view', ['id' => $ticket]);
        }

        throw new UnauthorizedHttpException('', 'کلمه عبور وارد شده اشتباه است');
    }
}
