<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Variables\Bank;
use App\Models\Variables\Psp;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PspController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->query('query', null);
        $psps = Psp::where(function ($query) use ($searchQuery) {
            if (!is_null($searchQuery)) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%');
            }
        })->orderBy('name')->get();

        return Inertia::render('Dashboard/Settings/Psps', [
            'searchQuery' => $searchQuery,
            'psps' => $psps,
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('pspForm', [
            'name' => 'required',
            'status' => 'required'
        ]);

        $psp = Psp::create($request->all());

        return redirect()->route('dashboard.settings.psps.list');
    }

    public function update(Request $request)
    {
        $id = $request->route('pspId');
        $request->validateWithBag('pspForm', [
            'name' => 'required',
            'status' => 'required'
        ]);
        $psp = Psp::find($id);
        if (is_null($psp)) throw new NotFoundHttpException('اطلاعات سرویس دهنده یافت نشد.');
        $psp->fill($request->all());
        $psp->save();

        return redirect()->route('dashboard.settings.psps.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('pspId');
        $psp = Psp::find($id);
        if (is_null($psp)) throw new NotFoundHttpException('اطلاعات سرویس دهنده یافت نشد.');
        $psp->delete();
        return redirect()->route('dashboard.settings.psps.list');
    }
}
