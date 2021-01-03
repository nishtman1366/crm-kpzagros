<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Variables\Bank;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->query('query', null);
        $banks = Bank::where(function ($query) use ($searchQuery) {
            if (!is_null($searchQuery)) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%');
            }
        })->orderBy('name')->get();

        return Inertia::render('Dashboard/Settings/Banks', [
            'searchQuery' => $searchQuery,
            'banks' => $banks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('bankForm', [
            'name' => 'required',
            'status' => 'required'
        ]);

        $bank = Bank::create($request->all());

        return redirect()->route('dashboard.settings.banks.list');
    }

    public function update(Request $request)
    {
        $id = $request->route('bankId');
        $request->validateWithBag('bankForm', [
            'name' => 'required',
            'status' => 'required'
        ]);
        $bank = Bank::find($id);
        if (is_null($bank)) throw new NotFoundHttpException('اطلاعات بانک یافت نشد.');
        $bank->fill($request->all());
        $bank->save();

        return redirect()->route('dashboard.settings.banks.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('bankId');
        $bank = Bank::find($id);
        if (is_null($bank)) throw new NotFoundHttpException('اطلاعات بانک یافت نشد.');
        $bank->delete();
        return redirect()->route('dashboard.settings.banks.list');
    }
}
