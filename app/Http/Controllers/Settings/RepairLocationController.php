<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Repairs\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RepairLocationController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->query('query', null);
        $repairLocations = Location::where(function ($query) use ($searchQuery) {
            if (!is_null($searchQuery)) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%');
            }
        })->orderBy('name')->get();

        return Inertia::render('Dashboard/Settings/RepairLocations', [
            'searchQuery' => $searchQuery,
            'repairLocations' => $repairLocations,
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('repairLocationForm', [
            'name' => 'required',
            'status' => 'required'
        ]);

        $repairLocation = Location::create($request->all());

        return redirect()->route('dashboard.settings.repairLocations.list');
    }

    public function update(Request $request)
    {
        $id = $request->route('repairLocationId');
        $request->validateWithBag('repairLocationForm', [
            'name' => 'required',
            'status' => 'required'
        ]);
        $repairLocation = Location::find($id);
        if (is_null($repairLocation)) throw new NotFoundHttpException('اطلاعات محل تعمیرات یافت نشد.');
        $repairLocation->fill($request->all());
        $repairLocation->save();

        return redirect()->route('dashboard.settings.repairLocations.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('repairLocationId');
        $repairLocation = Location::find($id);
        if (is_null($repairLocation)) throw new NotFoundHttpException('اطلاعات محل تعمیرات یافت نشد.');
        $repairLocation->delete();
        return redirect()->route('dashboard.settings.repairLocations.list');
    }
}
