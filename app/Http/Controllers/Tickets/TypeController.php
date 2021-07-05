<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\Type;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TypeController extends Controller
{
    public function index(Request $request)
    {
        $types = Type::orderBy('id', 'ASC')->get();
        return Inertia::render('Dashboard/Tickets/Types', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validateWithBag('typeForm', [
            'name' => 'required',
            'status' => 'required'
        ]);

        $type = Type::create($request->all());

        return redirect()->route('dashboard.tickets.types.list');
    }

    public function update(Request $request)
    {
        $id = $request->route('id');
        $request->validateWithBag('typeForm', [
            'name' => 'required',
            'status' => 'required'
        ]);
        $type = Type::find($id);
        if (is_null($type)) throw new NotFoundHttpException('اطلاعات واحد یافت نشد.');
        $type->fill($request->all());
        $type->save();

        return redirect()->route('dashboard.tickets.types.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('id');
        $type = Type::find($id);
        if (is_null($type)) throw new NotFoundHttpException('اطلاعات واحد یافت نشد.');
        $type->delete();
        return redirect()->route('dashboard.tickets.types.list');
    }
}
