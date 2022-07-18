<?php

namespace App\Actions\Profiles\Customers;

use App\Http\Controllers\Profiles\LicenseController;
use Closure;

class HandleCustomerLicenses
{
    public function handle($request, Closure $next)
    {
        $profile = $request->get('profile');
        if ($request->hasFile('national_card_file_1')) {
            LicenseController::upload($request->file('national_card_file_1'), 'national_card_file_1', $profile->id);
        }
        if ($request->hasFile('national_card_file_2')) {
            LicenseController::upload($request->file('national_card_file_2'), 'national_card_file_2', $profile->id);
        }
        if ($request->hasFile('id_file')) {
            LicenseController::upload($request->file('id_file'), 'id_file', $profile->id);
        }
        if ($request->hasFile('asasname_file')) {
            LicenseController::upload($request->file('asasname_file'), 'asasname_file', $profile->id);
        }
        if ($request->hasFile('agahi_file_1')) {
            LicenseController::upload($request->file('agahi_file_1'), 'agahi_file_1', $profile->id);
        }
        if ($request->hasFile('agahi_file_2')) {
            LicenseController::upload($request->file('agahi_file_2'), 'agahi_file_2', $profile->id);
        }

        return $next($request);
    }
}
