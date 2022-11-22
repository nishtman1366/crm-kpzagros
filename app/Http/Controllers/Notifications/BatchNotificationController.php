<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Imports\Notifications\Receptions;
use App\Models\Config;
use App\Models\Notifications\BatchNotification;
use App\Models\Notifications\Reception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use IPPanel\Client;
use IPPanel\Errors\Error;
use IPPanel\Errors\HttpException;
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
        $sendAgain = $request->get('sendAgain',false);

        if ($batchNotification->type === 'pattern') {
            $i = 1;
            $batchNotification->receptions()->chunk(50, function ($receptions) use ($batchNotification, &$i, $sendAgain) {
                foreach ($receptions as $reception) {
                    if (!is_null($reception->reception) && $reception->reception != '') {
                        if ($reception->status === 1 && $sendAgain) {
                            $list[] = $reception->reception;
                            dispatch(new \App\Jobs\Notifications\SendNotification($batchNotification, $reception->reception))
                                ->onQueue('notificationsQueue')
                                ->delay(($i * 10));
                        } elseif ($reception->status === 0) {
                            $list[] = $reception->reception;
                            dispatch(new \App\Jobs\Notifications\SendNotification($batchNotification, $reception->reception))
                                ->onQueue('notificationsQueue')
                                ->delay(($i * 10));
                        }
                    }
                }
                $i++;
            });
        } elseif ($batchNotification->type === 'club') {
            $batchNotification->receptions()->chunk(10000, function ($receptions) use ($batchNotification) {
                $list = [];
                foreach ($receptions as $reception) {
                    $list[] = $reception->reception;
                }
                dispatch(new \App\Jobs\Notifications\SendNotification($batchNotification, '', $list, 'club'))
                    ->onQueue('notificationsQueue');
            });
        }
        $batchNotification->status = 1;
        $batchNotification->save();
        return redirect()->route('dashboard.notifications.list');
    }

    public function details(Request $request)
    {
        $id = (int)$request->route('id');
        $page = (int)$request->query('page', 0);
        $limit = (int)$request->query('limit', 10);

        $notification = BatchNotification::withCount('receptions')->find($id);
        if (is_null($notification)) throw new NotFoundHttpException('اعلان گروهی یافت نشد.');
        $receptions = Reception::where('batch_notification_id', $notification->id)->paginate(30);
        $paginatedLinks = paginationLinks($receptions->appends($request->query->all()));

        return Inertia::render('Dashboard/Notifications/Details', compact('notification', 'receptions', 'paginatedLinks'));
    }

    private function handleNotificationStatus(string $status)
    {
        switch ($status) {
            case 'active':
                return 1;
            case 'cancel':
                return 5;
            case 'interacting':
                return 3;
            case 'finish':
                return 4;
            default:
                return 99;
        }
    }
}
