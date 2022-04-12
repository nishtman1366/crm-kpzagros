<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Imports\Notifications\Receptions;
use App\Models\Notifications\BatchNotification;
use App\Models\Notifications\Reception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationReceptionController extends Controller
{
    public function store(Request $request)
    {
        $id = (int)$request->route('id');
        $batchNotification = BatchNotification::find($id);
        if (is_null($batchNotification)) throw new NotFoundHttpException('اعلام گروهی یافت نشد.');

        if ($request->has('delete_old_numbers') && $request->get('delete_old_numbers') == true) {
            Reception::where('batch_notification_id', $batchNotification->id)->delete();
        }

        if ($request->has('body') && trim($request->get('body') != '')) {
            $body = $request->get('body');
            $numbers = explode(',', $body);
            foreach ($numbers as $number) {
                Reception::firstOrCreate([
                    'batch_notification_id' => $batchNotification->id,
                    'reception' => $this->clearMobileNumber($number)
                ]);
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file')->store('temp/notifications/batch');
            Excel::import(new Receptions($id), $file);
        }

        return redirect()->route('dashboard.notifications.list');
    }


    private function clearMobileNumber($number)
    {
        $number = str_replace(' ', '', $number);
        $number = str_replace('-', '', $number);
        $number = str_replace('_', '', $number);
        $number = str_replace('.', '', $number);

        return $number;
    }
}
