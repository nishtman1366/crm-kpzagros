<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Repairs\Type;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RepairTypeController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->query('query', null);
        $repairTypes = Type::where(function ($query) use ($searchQuery) {
            if (!is_null($searchQuery)) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%');
            }
        })->orderBy('name')->get();

        return Inertia::render('Dashboard/Settings/RepairTypes', [
            'searchQuery' => $searchQuery,
            'repairTypes' => $repairTypes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('repairTypeForm', [
            'name' => 'required',
            'status' => 'required'
        ]);

        $repairType = Type::create($request->all());

        return redirect()->route('dashboard.settings.repairTypes.list');
    }

    public function update(Request $request)
    {
        $id = $request->route('repairTypeId');
        $request->validateWithBag('repairTypeForm', [
            'name' => 'required',
            'status' => 'required'
        ]);
        $repairType = Type::find($id);
        if (is_null($repairType)) throw new NotFoundHttpException('اطلاعات علت خرابی یافت نشد.');
        $repairType->fill($request->all());
        $repairType->save();

        return redirect()->route('dashboard.settings.repairTypes.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('repairTypeId');
        $repairType = Type::find($id);
        if (is_null($repairType)) throw new NotFoundHttpException('اطلاعات علت خرابی یافت نشد.');
        $repairType->delete();
        return redirect()->route('dashboard.settings.repairTypes.list');
    }
}
