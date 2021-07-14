<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notifications\BatchNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BatchNotificationController extends Controller
{
    public function index(Request $request)
    {
        $i = 1;
        $list = BatchNotification::withCount('receptions')->orderBy('id', 'DESC')
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
        ]);
        $type = $request->get('type');
        if ($type === 'pattern') {
            $request->validateWithBag('notificationForm', [
                'pattern' => 'required',
                'parameters' => 'required',
            ]);
        } elseif ($type === 'club') {
            $request->validateWithBag('notificationForm', [
                'body' => 'required',
            ]);
        }
        BatchNotification::create($request->all());

        return redirect()->route('dashboard.notifications.list');
    }

    public function update(Request $request)
    {
        $request->validateWithBag('notificationForm', [
            'title' => 'required',
        ]);
        $type = $request->get('type');
        if ($type === 'pattern') {
            $request->validateWithBag('notificationForm', [
                'pattern' => 'required',
                'parameters' => 'required',
            ]);
        } elseif ($type === 'club') {
            $request->validateWithBag('notificationForm', [
                'body' => 'required',
            ]);
        }

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
        $batchNotification = BatchNotification::find($id);
        if (is_null($batchNotification)) throw new NotFoundHttpException('اعلان گروهی یافت نشد.');

        if ($batchNotification->type === 'pattern') {
            $receptions = $batchNotification->receptions;
            foreach ($batchNotification->receptions as $reception) {
                if (!is_null($reception->reception) && $reception->reception != '') {
                    dispatch(new \App\Jobs\Notifications\SendNotification($batchNotification, $reception->reception))
                        ->onQueue('notificationsQueue');
                }
            }
        } elseif ($batchNotification->type === 'club') {
            $batchNotification->receptions()->chunk(500, function ($receptions) use ($batchNotification) {
                $list = [];
                foreach ($receptions as $reception) {
                    $list[] = $reception->reception;
                }
                dispatch(new \App\Jobs\Notifications\SendNotification($batchNotification, '', $list, 'club'))
                    ->onQueue('notificationsQueue');
            });
        }

        return redirect()->route('dashboard.notifications.list');
    }
}
