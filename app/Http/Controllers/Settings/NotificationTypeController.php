<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Notifications\Type;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationTypeController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->query('query', null);
        $notificationTypes = Type::where(function ($query) use ($searchQuery) {
            if (!is_null($searchQuery)) {
                $query->where('title', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('pattern', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('body', 'LIKE', '%' . $searchQuery . '%');
            }
        })->orderBy('id','DESC')->get();

        return Inertia::render('Dashboard/Settings/NotificationTypes', [
            'searchQuery' => $searchQuery,
            'notificationTypes' => $notificationTypes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('notificationTypeForm', [
            'title' => 'required',
            'pattern' => 'required',
            'body' => 'required'
        ]);

        $notification = Type::create($request->all());

        return redirect()->route('dashboard.settings.notifications.types.list');
    }

    public function update(Request $request)
    {
        $id = $request->route('typeId');
        $request->validateWithBag('notificationTypeForm', [
            'title' => 'required',
            'pattern' => 'required',
            'body' => 'required'
        ]);
        $notification = Type::find($id);
        if (is_null($notification)) throw new NotFoundHttpException('اطلاعات اعلان یافت نشد.');
        $notification->fill($request->all());
        $notification->save();

        return redirect()->route('dashboard.settings.notifications.types.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('typeId');
        $notification = Type::find($id);
        if (is_null($notification)) throw new NotFoundHttpException('اطلاعات اعلان یافت نشد.');
        $notification->delete();
        return redirect()->route('dashboard.settings.notifications.types.list');
    }
}
