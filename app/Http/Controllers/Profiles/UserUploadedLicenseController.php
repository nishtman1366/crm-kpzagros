<?php

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Models\Profiles\UserUploadedLicense;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserUploadedLicenseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int)$request->query('perPage', 25);
        $status = (int)$request->query('status', 0);
        $list = UserUploadedLicense::with('profile')
            ->with('profile.customer')
            ->where('status', $status)
            ->paginate($perPage);
        $paginatedLinks = paginationLinks($list->appends($request->query->all()));

        return Inertia::render('Dashboard/Profiles/UploadedLicenses', [
            'licenses' => $list,
            'perPage' => $perPage,
            'status' => $status,
            'paginatedLinks' => $paginatedLinks,
        ]);
    }

    public function update(UserUploadedLicense $license)
    {
        $license->status=1;
        $license->save();

        return redirect()->back();
    }
}
