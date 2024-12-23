<?php

namespace App\Http\Controllers\Profiles;

use App\Actions\Profiles\Businesses\CheckBusinessStatus;
use App\Actions\Profiles\Businesses\CreateBusinessData;
use App\Actions\Profiles\Businesses\HandleBusinessLicenses;
use App\Actions\Profiles\Businesses\UpdateBusinessData;
use App\Actions\Profiles\Customers\CheckCustomerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profiles\Business\CreateBusiness;
use App\Http\Requests\Profiles\Business\UpdateBusiness;
use App\Models\Profiles\Business;
use App\Models\Profiles\License;
use App\Models\Profiles\Profile;
use App\Models\Variables\BusinessCategory;
use App\Models\Variables\BusinessSubCategory;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BusinessController extends Controller
{
    public function create(Profile $profile, Request $request)
    {
        $customer = $profile->customer;
        if (is_null($customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد.');

        $ostans = DB::table('ostan')->select()->get(['id', 'name']);
        $shahrestans = DB::table('shahrestan')->select()->get(['id', 'name']);
        $bakhshs = DB::table('bakhsh')->select()->get(['id', 'name']);
        $shahrs = DB::table('shahr')->select()->get(['id', 'name']);
        $categories = BusinessCategory::orderBy('name', 'ASC')->get();
        $subCategories = BusinessSubCategory::with('parent')->orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Profiles/CreateBusiness', [
            'profileId' => $profile->id,
            'profile' => $profile,
            'customer' => $customer,
            'ostans' => $ostans,
            'shahrestans' => $shahrestans,
            'bakhshs' => $bakhshs,
            'shahrs' => $shahrs,
            'categories' => $categories,
            'subCategories' => $subCategories,
        ]);
    }

    public function store(Profile $profile, CreateBusiness $request)
    {
        $request->merge(['profile_id' => $profile->id, 'profile' => $profile]);
        app(Pipeline::class)
            ->send($request)
            ->through([
                CreateBusinessData::class,
                HandleBusinessLicenses::class
            ])
            ->thenReturn();

        return redirect()->route('dashboard.profiles.accounts.create', ['profile' => $profile]);
    }

    public function edit(Profile $profile)
    {
        $profile->load(['customer', 'business', 'accounts']);
        $customer = $profile->customer;
        if (is_null($customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد.');
        $business = $profile->business;
        if (is_null($business)) return redirect()->route('dashboard.profiles.businesses.create', ['profile' => $profile]);
        $profile->load('deviceType');
        $ostans = DB::table('ostan')->select()->get(['id', 'name']);
        $shahrestans = DB::table('shahrestan')->select()->get(['id', 'name']);
        $bakhshs = DB::table('bakhsh')->select()->get(['id', 'name']);
        $shahrs = DB::table('shahr')->select()->get(['id', 'name']);
        $categories = BusinessCategory::orderBy('name', 'ASC')->get();
        $subCategories = BusinessSubCategory::with('parent')->orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Profiles/EditBusiness', [
            'profileId' => $profile->id,
            'profile' => $profile,
            'customer' => $customer,
            'ostans' => $ostans,
            'shahrestans' => $shahrestans,
            'bakhshs' => $bakhshs,
            'shahrs' => $shahrs,
            'business' => $business,
            'categories' => $categories,
            'subCategories' => $subCategories,
        ]);
    }

    public function update(Profile $profile, UpdateBusiness $request)
    {
        $request->merge(['profile' => $profile]);
        app(Pipeline::class)
            ->send($request)
            ->through([
                CheckBusinessStatus::class,
                UpdateBusinessData::class,
                HandleBusinessLicenses::class
            ])
            ->thenReturn();

        return redirect()->route('dashboard.profiles.view', ['profile' => $profile]);
    }
}
