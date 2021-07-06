<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\File;
use App\Models\Tickets\Reply;
use App\Models\Tickets\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReplyController extends Controller
{
    public function store(Request $request)
    {
        $request->validateWithBag('replyForm', [
            'body' => 'required'
        ]);

        $id = $request->route('id');
        $ticket = Ticket::find($id);
        if (is_null($ticket)) throw new NotFoundHttpException('درخواست مورد نظر یافت نشد.');

        $user = Auth::user();
        $reply = Reply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => $request->get('body'),
        ]);

        if ($request->hasFile('files')) {
            $files = $request->file('files', []);
            FileController::upload($files, $ticket->id, $reply->id);
        }

        $status = 2;
        if ($user->id === $ticket->user_id) $status = 3;
        $ticket->status = $status;
        $ticket->save();
        EventController::store($ticket, $user);
        return redirect()->route('dashboard.tickets.view', ['id' => $ticket->id]);
    }
}
