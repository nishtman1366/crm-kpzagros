<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\Event;
use App\Models\Tickets\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private Ticket $ticket;
    private User $user;

    /**
     * EventController constructor.
     * @param Ticket $ticket
     * @param User $user
     */
    public function __construct(Ticket $ticket, User $user)
    {
        $this->ticket = $ticket;
        $this->user = $user;
    }


    public static function store(Ticket $ticket, User $user)
    {
        $self = new EventController($ticket, $user);
        $status = $ticket->status;
        switch ($status) {
            case 0:
                return $self->ticketCreated();
            case 1:
                return $self->ticketProcessing();
            case 2:
                return $self->ticketAnswered();
            case 3:
                return $self->ticketWaiting();
            case 4:
                return $self->ticketTransferred();
            case 99:
                return $self->ticketClosed();
        }
    }

    private function ticketCreated()
    {
        Event::create([
            'ticket_id' => $this->ticket->id,
            'title' => 'ثبت درخواست',
            'body' => sprintf('درخواست توسط %s ثبت شد.', $this->user->name)
        ]);
    }

    private function ticketProcessing()
    {
        Event::create([
            'ticket_id' => $this->ticket->id,
            'title' => 'در حال بررسی',
            'body' => sprintf('%s وصعیت درخواست را به درحال بررسی تغییر داد', $this->user->name)
        ]);
    }

    private function ticketAnswered()
    {
        Event::create([
            'ticket_id' => $this->ticket->id,
            'title' => 'پاسخ داده شده',
            'body' => sprintf('%s به درخواست کاربر پاسخ داد.', $this->user->name)
        ]);
    }

    private function ticketWaiting()
    {
        Event::create([
            'ticket_id' => $this->ticket->id,
            'title' => 'پاسخ کاربر',
            'body' => sprintf('%s برای این درخواست پاسخ ارسال کرد.', $this->user->name)
        ]);
    }

    private function ticketTransferred()
    {
        Event::create([
            'ticket_id' => $this->ticket->id,
            'title' => 'منتقل شده',
            'body' => sprintf('درخواست توسط %s به %s منتقل شد.', $this->user->name, $this->ticket->type->name)
        ]);
    }

    private function ticketClosed()
    {
        Event::create([
            'ticket_id' => $this->ticket->id,
            'title' => 'بسته شده',
            'body' => sprintf('درخواست توسط %s بسته شد.', $this->user->name)
        ]);
    }
}
