<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $emails = Email::orderBy('UserId', 'ASC')
            ->where(function ($query) use ($search) {
                if (!is_null($search)) $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('DomainId', 'LIKE', '%' . $search . '%')
                        ->orWhere('Email', 'LIKE', '%' . $search . '%')
                        ->orWhere('owner', 'LIKE', '%' . $search . '%');
                });
            })
            ->get();
        return Inertia::render('Dashboard/Emails/List', compact('emails', 'search'));
    }

    public function store(Request $request)
    {
        $request->validateWithBag('emailForm', [
            'Email' => 'required|email',
            'password' => 'required|min:8',
            'owner' => 'nullable',
            'status' => 'required'
        ]);

        $salt = substr(Hash::make(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')), -16);
        $request->merge(['DomainId' => 1, 'password' => Hash::make($request->get('password'), ['salt' => $salt])]);
        Email::create($request->all());

        return redirect()->route('dashboard.emails.list');
    }

    public function update(Email $email, Request $request)
    {
        $request->validateWithBag('emailForm', [
            'Email' => 'required|email',
            'password' => 'nullable|min:8',
            'owner' => 'nullable',
            'status' => 'required'
        ]);
        if ($request->has('password') && !is_null($request->get('password'))) {
            $salt = substr(Hash::make(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')), -16);
            $request->merge(['password' => Hash::make($request->get('password'), ['salt' => $salt])]);
            $email->fill($request->all());
        } else {
            $email->fill($request->except(['password']));
        }
        $email->save();

        return redirect()->route('dashboard.emails.list');
    }

    public function destroy(Email $email)
    {
        $email->delete();
        return redirect()->route('dashboard.emails.list');
    }
}
