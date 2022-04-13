<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Imports\Notifications\Receptions;
use App\Models\Config;
use App\Models\Notifications\BatchNotification;
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

        if ($batchNotification->type === 'pattern') {
            $i = 1;
            $batchNotification->receptions()->chunk(50, function ($receptions) use ($batchNotification, &$i) {
                foreach ($receptions as $reception) {
                    if (!is_null($reception->reception) && $reception->reception != '') {
                        $list[] = $reception->reception;
                        dispatch(new \App\Jobs\Notifications\SendNotification($batchNotification, $reception->reception))
                            ->onQueue('notificationsQueue')
                            ->delay(($i * 10));
                    }
                }
                $i++;
//                Log::channel('notifications')->info('count: ' . count($list));
            });
        } elseif ($batchNotification->type === 'club') {
            $batchNotification->receptions()->chunk(10000, function ($receptions) use ($batchNotification) {
                $list = [];
                foreach ($receptions as $reception) {
                    $list[] = $reception->reception;
                }
//                Log::channel('notifications')->info('count: ' . count($list));
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
        $apiKey = 'RwoB81G8VWdrZ4xc-GmNp96xPlk1rvdcYmUGnSCvWZY=';
        $client = new Client($apiKey);
        try {
            $data = $client->getMessage($notification->bulk_id);
            $notification->valid_recipients = $data->validRecipientsCount;
            $notification->cost = $data->cost;
            $notification->payback_cost = $data->paybackCost;
            $notification->status = $this->handleNotificationStatus($data->status);
            $notification->save();

            $notification->cost = number_format($notification->cost);
            $notification->payback_cost = number_format($notification->payback_cost);
            $notification->valid_recipients = number_format($notification->valid_recipients);
            $notification->receptions_count = number_format($notification->receptions_count);
        } catch (Error $e) {
            dd($e);
        } catch (HttpException $e) {
            dd($e);
        }

        try {
            $list = $client->fetchStatuses($notification->bulk_id, ($page === 0 ? 0 : $page - 1), $limit);
            $receptions = $list[0];
            $pagination = $list[1];
        } catch (Error $e) {
            dd($e);
        } catch (HttpException $e) {
            dd($e);
        }

        return Inertia::render('Dashboard/Notifications/Details', compact('notification', 'receptions', 'pagination'));
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
