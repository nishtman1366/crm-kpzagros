<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Notifications\Event;
use App\Models\Notifications\Type;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationEventController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->query('query', null);
        $notificationEvents = Event::with('type')->where(function ($query) use ($searchQuery) {
            if (!is_null($searchQuery)) {
//                $query->where('title', 'LIKE', '%' . $searchQuery . '%');
//                $query->orWhere('pattern', 'LIKE', '%' . $searchQuery . '%');
//                $query->orWhere('body', 'LIKE', '%' . $searchQuery . '%');
            }
        })->orderBy('id', 'DESC')->get();

        $levels = [
            ['id' => 'SUPERUSER', 'name' => 'مدیرکل'],
            ['id' => 'ADMIN', 'name' => 'مدیر سیستم'],
            ['id' => 'AGENT', 'name' => 'نمایندگان'],
            ['id' => 'MARKETER', 'name' => 'بازاریابان'],
            ['id' => 'CUSTOMER', 'name' => 'مشتریان'],
            ['id' => 'TECHNICAL', 'name' => 'کارشناسان فنی'],
        ];
        $eventTypes = [
            ['id' => 'PROFILES', 'name' => 'پرونده ها'],
            ['id' => 'REPAIRS', 'name' => 'تعمیرات'],
            ['id' => 'DEVICES', 'name' => 'دستگاه ها'],
        ];
        $events = [
            ['id' => 0, 'type' => 'PROFILES', 'name' => 'ثبت موقت'],
            ['id' => 1, 'type' => 'PROFILES', 'name' => 'ثبت شده'],
            ['id' => 2, 'type' => 'PROFILES', 'name' => 'در انتظار بررسی مدارک'],
            ['id' => 3, 'type' => 'PROFILES', 'name' => 'تایید مدارک'],
            ['id' => 4, 'type' => 'PROFILES', 'name' => 'ثبت در PSP'],
            ['id' => 5, 'type' => 'PROFILES', 'name' => 'تایید شاپرک'],
            ['id' => 6, 'type' => 'PROFILES', 'name' => 'در انتظار تخصیص'],
            ['id' => 7, 'type' => 'PROFILES', 'name' => 'تخصیص داده شده'],
            ['id' => 8, 'type' => 'PROFILES', 'name' => 'نصب شده'],
            ['id' => 9, 'type' => 'PROFILES', 'name' => 'ابطال'],
            ['id' => 10, 'type' => 'PROFILES', 'name' => 'عدم تایید مدارک'],
            ['id' => 11, 'type' => 'PROFILES', 'name' => 'عدم تایید شاپرک'],
            ['id' => 12, 'type' => 'PROFILES', 'name' => 'درخواست ابطال'],
            ['id' => 13, 'type' => 'PROFILES', 'name' => 'عدم تایید سریال'],
            ['id' => 14, 'type' => 'PROFILES', 'name' => 'درخواست جابجایی'],
            ['id' => 15, 'type' => 'PROFILES', 'name' => 'اختصاص سریال جدید'],
            ['id' => 16, 'type' => 'PROFILES', 'name' => 'رد درخواست جابجایی'],
            ['id' => 1, 'type' => 'REPAIRS', 'name' => 'ثبت شده'],
            ['id' => 2, 'type' => 'REPAIRS', 'name' => 'دریافت شده توسط واحد فنی'],
            ['id' => 3, 'type' => 'REPAIRS', 'name' => 'در صف تعمیر'],
            ['id' => 4, 'type' => 'REPAIRS', 'name' => 'تعمیر شده'],
            ['id' => 5, 'type' => 'REPAIRS', 'name' => 'در انتظار پرداخت'],
            ['id' => 6, 'type' => 'REPAIRS', 'name' => 'پرداخت شده'],
            ['id' => 7, 'type' => 'REPAIRS', 'name' => 'عودت شده'],
        ];
        $notificationTypes = Type::orderBy('id', 'DESC')->get();
        return Inertia::render('Dashboard/Settings/NotificationEvents', [
            'searchQuery' => $searchQuery,
            'notificationEvents' => $notificationEvents,
            'levels' => $levels,
            'notificationTypes' => $notificationTypes,
            'events' => $events,
            'eventTypes' => $eventTypes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('notificationEventForm', [
            'event_type' => 'required',
            'notification_type_id' => 'required',
            'event' => 'required',
            'level' => 'required',
            'status' => 'required'
        ]);

        $notification = Event::create($request->all());

        return redirect()->route('dashboard.settings.notifications.events.list');
    }

    public function update(Request $request)
    {
        $id = $request->route('eventId');
        $request->validateWithBag('notificationEventForm', [
            'event_type' => 'required',
            'notification_type_id' => 'required',
            'level' => 'required',
            'status' => 'required'
        ]);
        $notification = Event::find($id);
        if (is_null($notification)) throw new NotFoundHttpException('اطلاعات اعلان یافت نشد.');
        $notification->fill($request->all());
        $notification->save();

        return redirect()->route('dashboard.settings.notifications.events.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('eventId');
        $notification = Event::find($id);
        if (is_null($notification)) throw new NotFoundHttpException('اطلاعات اعلان یافت نشد.');
        $notification->delete();
        return redirect()->route('dashboard.settings.notifications.events.list');
    }
}
