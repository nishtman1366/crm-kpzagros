<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notifications\BatchNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BatchNotificationController extends Controller
{
    public function index(Request $request)
    {
        $i = 1;
        $list = BatchNotification::with('receptions')
            ->orderBy('id', 'DESC')
            ->get()
            ->each(function ($item) use (&$i) {
                $item->no = $i;
                $i++;
            });

        return Inertia::render('Dashboard/Notifications/List', compact('list'));
    }

    public function store(Request $request)
    {
        $request->validateWithBag('notificationForm', [
            'title' => 'required',
            'pattern' => 'required',
            'parameters' => 'required',
        ]);

        BatchNotification::create($request->all());

        return redirect()->route('dashboard.notifications.list');
    }

    public function update(Request $request)
    {
        $id = (int)$request->route('id');
        $batchNotification = BatchNotification::with('receptions')
            ->find($id);
        if (is_null($batchNotification)) throw new NotFoundHttpException('اعلان گروهی یافت نشد.');

        $batchNotification->fill($request->all());
        $batchNotification->save();
        return redirect()->route('dashboard.notifications.list');
    }

    public function destroy(Request $request)
    {
        $id = (int)$request->route('id');
        $batchNotification = BatchNotification::with('receptions')
            ->find($id);
        if (is_null($batchNotification)) throw new NotFoundHttpException('اعلان گروهی یافت نشد.');

        $batchNotification->delete();

        return redirect()->route('dashboard.notifications.list');
    }

    public function send(Request $request)
    {
        $id = (int)$request->route('id');
        $batchNotification = BatchNotification::with('receptions')
            ->find($id);
        if (is_null($batchNotification)) throw new NotFoundHttpException('اعلان گروهی یافت نشد.');

        foreach ($batchNotification->receptions as $reception) {
            if (!is_null($reception->reception) && $reception->reception != '') {
                dispatch(new \App\Jobs\Notifications\SendNotification($batchNotification, $reception->reception))
                    ->onQueue('notificationsQueue');
            }
        }

        return redirect()->route('dashboard.notifications.list');
    }
}
