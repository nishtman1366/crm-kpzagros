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
        $search = $request->query('search');
        $type = strtoupper($type);
        $user = Auth::user();
        $users = User::with('parent')
            ->where(function ($query) use ($type, $user, $search) {
                $query->where('level', $type);
                if ($user->level != 'SUPERUSER') {
                    if ($type != 'ADMIN') {
                        $query->where('parent_id', $user->id);
                    }
                }
                if (!is_null($search)) $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('username', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'ASC')->paginate();
        $users->appends(request()->query())->links();
        $paginatedLinks = paginationLinks($users);

        $usersType = $this->returnUsersType($type);

        $type = strtolower($type);

        return Inertia::render('Dashboard/Users/UsersList', [
            'type' => $type,
            'usersType' => $usersType,
            'users' => $users,
            'search' => $search,
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
//        dd($request->all());
        User::create($request->all());

        return redirect()->route('dashboard.users.list', ['type' => $type]);
    }

    public function view(string $type, User $user)
    {
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

    public function update(string $type, User $user, Request $request)
    {
        $request->validateWithBag('userForm', [
            'parent_id' => 'required',
            'name' => 'required',
            'username' => 'required|unique:users,id,' . $user->id,
            'password' => 'nullable|min:6',
            'mobile' => 'required|digits:11',
            'status' => 'required',
        ]);

        $user->fill($request->all());
        $user->save();
        $type = $request->route('type');

        return redirect()->route('dashboard.users.list', ['type' => $type]);
    }

    public function destroy(string $type, User $user)
    {
        $user->delete();
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
            case 'TECHNICAL':
                return 'کارشناسان فنی';
            case 'OFFICE':
                return 'کارمندان دفتر';
            case 'ACCOUNTING':
                return 'حسابداری';
        }
    }
}
