<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user=Auth::user();

        return Inertia::render('Dashboard/Users/UsersList');
    }

    public function view()
    {

    }

    public function update(Request $request)
    {

    }
}
