<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->route('type', null);
        $type = strtoupper($type);
        $user = Auth::user();
        $users = User::with('parent')->where(function ($query) use ($type, $user) {
            $query->where('level', $type);
            if ($user->level != 'SUPERUSER') {
                if ($type != 'ADMIN') {
                    $query->where('parent_id', $user->id);
                }
            }
        })
            ->orderBy('id', 'ASC')->paginate();

        $paginatedLinks = paginationLinks($users);

        $usersType = $this->returnUsersType($type);

        $type = strtolower($type);

        return Inertia::render('Dashboard/Users/UsersList', [
            'type' => $type,
            'usersType' => $usersType,
            'users' => $users,
            'paginatedLinks' => $paginatedLinks
        ]);
    }

    public function create(Request $request)
    {
        $type = $request->route('type', null);
        $type = strtoupper($type);
        $user = Auth::user();

        $usersType = $this->returnUsersType(strtoupper($type));

        $statuses = [
            ['id' => 0, 'name' => 'ثبت شده'],
            ['id' => 1, 'name' => 'تایید شده'],
            ['id' => 2, 'name' => 'معلق'],
        ];
        $agents = [];
        if ($type == 'marketer') {
            $agents = User::where('parent_id', $user->id)->where('level', 'AGENT')->get();
        }

        return Inertia::render('Dashboard/Users/CreateUser', [
            'type' => $type,
            'userType' => $usersType,
            'statuses' => $statuses,
            'agents' => $agents
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('userForm', [
            'parent_id' => 'required',
            'name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'mobile' => 'required|digits:11',
            'status' => 'required',
        ]);
        $type = $request->route('type');
        $request->merge(['level' => strtoupper($type)]);
        User::create($request->all());

        return redirect()->route('dashboard.users.list', ['type' => $type]);
    }

    public function view(Request $request)
    {
        $userId = $request->route('id');
        $user = User::find($userId);
        if (is_null($user)) return response()->json('not found', 404);
        $type = $request->route('type', null);
        $usersType = $this->returnUsersType(strtoupper($type));
        $statuses = [
            ['id' => 0, 'name' => 'ثبت شده'],
            ['id' => 1, 'name' => 'تایید شده'],
            ['id' => 2, 'name' => 'معلق'],
        ];
        $agents = [];
        if ($type == 'marketer') {
            $agents = User::where('parent_id', $user->id)->where('level', 'AGENT')->get();
        }

        return Inertia::render('Dashboard/Users/EditUser', [
            'selectedUser' => $user,
            'type' => $type,
            'userType' => $usersType,
            'statuses' => $statuses,
            'agents' => $agents
        ]);
    }

    public function update(Request $request)
    {
        $userId = $request->route('id');
        $request->validateWithBag('userForm', [
            'parent_id' => 'required',
            'name' => 'required',
            'username' => 'required|unique:users,id,' . $userId,
            'password' => 'nullable|min:6',
            'mobile' => 'required|digits:11',
            'status' => 'required',
        ]);

        $user = User::find($userId);
        if (is_null($user)) return response()->json('not found', 404);

        $user->fill($request->all());
        $user->save();
        $type = $request->route('type');

        return redirect()->route('dashboard.users.list', ['type' => $type]);
    }

    public function destroy(Request $request)
    {
        $userId = $request->route('id');
        $type = $request->route('type');

        User::destroy($userId);

        return redirect()->route('dashboard.users.list', ['type' => $type]);
    }

    function returnUsersType($type)
    {
        switch ($type) {
            case 'ADMIN':
                return 'مدیر سیستم';
            case 'AGENT':
                return 'نماینده';
            case 'MARKETER':
                return 'بازاریاب';
        }
    }
}
