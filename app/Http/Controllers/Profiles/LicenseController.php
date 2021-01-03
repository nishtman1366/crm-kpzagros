<?php

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Models\Profiles\License;
use App\Models\Profiles\LicenseType;
use App\Models\Profiles\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class LicenseController
 * @package App\Http\Controllers\Profiles
 */
class LicenseController extends Controller
{
    public static function checkProfileLicenses(Profile $profile)
    {
        $errors = [];
        $licenseTypes = LicenseType::where('required', 1)->get()->each(function ($type) use ($profile, &$errors) {
            $licenseExistence = License::where('license_type_id', $type->id)->where('profile_id', $profile->id)->exists();
            if (($type->key === 'asasname_file' || $type->key === 'agahi_file_1' || $type->key === 'agahi_file_2') && $profile->customer->type !== 'ORGANIZATION') {

            } elseif ($type->key === 'license_file' && $profile->business->has_license == 'NO') {

            } elseif ($type->key === 'esteshhad_file' && $profile->business->has_license == 'YES') {

            } else {
                if (!$licenseExistence) $errors[$type->key] = sprintf('تصویر %s ارسال نشده است.', $type->name);
            }
        });

        $shebaTypeId = LicenseType::where('key', 'sheba_file')->get()->first();
        foreach ($profile->accounts as $account) {
            $licenseExistence = License::where('license_type_id', $shebaTypeId->id)->where('account_id', $account->account_id)->exists();
            if (!$licenseExistence) $errors['sheba_file_' . $account->account_id] = sprintf('تصویر تاییدیه شبای حساب %s (%s) ارسال نشده است.', $account->account->account_number, $account->account->bank->name);
        }

        return $errors;
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @param $key
     * @param $profileId
     * @param null $accountId
     */
    public static function upload(\Illuminate\Http\UploadedFile $file, $key, $profileId, $accountId = null)
    {
        $type = LicenseType::where('key', $key)->get()->first();
        $extension = $file->getClientOriginalExtension();


        if (is_null($type->file_name)) {
            $fileName = $type->key . (is_null($accountId) ? '' : '-' . $accountId . '') . '.' . $extension;
        } else {
            $fileName = $type->file_name . (is_null($accountId) ? '' : '-' . $accountId . '') . '.' . $extension;
        }
        $file->storeAs('profiles/' . $profileId, $fileName, 'public');

        if (is_null($accountId)) {
            License::updateOrCreate(
                ['profile_id' => $profileId, 'license_type_id' => $type->id],
                ['file' => $fileName]
            );
        } else {
            License::updateOrCreate(
                ['profile_id' => $profileId, 'license_type_id' => $type->id, 'account_id' => $accountId],
                ['file' => $fileName]
            );
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validateWithBag('uploadLicenseForm', [
            'license_type_id' => 'required|exists:license_types,id',
            'file' => 'required'
        ]);

        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد.');
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isSuperuser()) {
            if ($profile->user_id !== $user->id) throw new UnauthorizedHttpException('', 'شما اجازه دسترسی به این پرونده را ندارید.');

            if ($profile->status !== 0 && $profile->status !== 10) throw new UnauthorizedHttpException('', 'در این مرحله امکان بارگزاری مدارک وجود ندارد.');
        }


        if ($request->hasFile('file')) {
            $licenseTypeId = $request->get('license_type_id');
            $accountId = $request->get('account_id', null);
            $type = LicenseType::where('id', $licenseTypeId)->get()->first();
            static::upload($request->file('file'), $type->key, $profileId, $accountId);
        }
        return redirect()->route('dashboard.profiles.view', ['profileId' => $profileId]);
    }

    /**
     * @param $key
     * @param $profileId
     * @param null $accountId
     * @return string
     */
    public static function view($key, $profileId, $accountId = null)
    {
        $type = LicenseType::where('key', $key)->get()->first();
        if (is_null($type)) return '';

        $license = License::where('license_type_id', $type->id)->where(function ($query) use ($profileId, $accountId) {
            $query->where('profile_id', $profileId);
            if (!is_null($accountId)) $query->where('account_Id', $accountId);
        })->get()->first();

        if (is_null($license)) return '';

        return $license->url;
    }

    /**
     * @param Request $request
     * This function delete the license file with the @param $licneseId in route
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $licenseId = (int)$request->route('licenseId');
        $profileId = (int)$request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد.');

        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isSuperuser()) {
            if ($profile->user_id !== $user->id) throw new UnauthorizedHttpException('', 'شما اجازه دسترسی به این پرونده را ندارید.');

            if ($profile->status !== 0 && $profile->status !== 10) throw new UnauthorizedHttpException('', 'در این مرحله امکان حذف مدارک وجود ندارد.');
        }

        $license = License::find($licenseId);
        if (!is_null($license)) {
            if ($license->profile_id !== $profileId) throw new UnprocessableEntityHttpException('شما اجازه دسترسی به این پرونده را ندارید');
            Storage::disk('public')->delete('profiles/' . $license->profile_id . '/' . $license->file);
            $license->delete();
        }

        return redirect()->route('dashboard.profiles.view', ['profileId' => $profileId]);
    }
}
