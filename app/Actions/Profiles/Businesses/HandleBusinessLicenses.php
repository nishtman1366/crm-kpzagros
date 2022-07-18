<?php

namespace App\Actions\Profiles\Businesses;

use App\Http\Controllers\Profiles\LicenseController;
use Closure;

class HandleBusinessLicenses
{
    public function handle($request, Closure $next)
    {
        $profile = $request->profile;
        if ($request->hasFile('license_file')) {
            LicenseController::upload($request->file('license_file'), 'license_file', $profile->id);
        }
        if ($request->hasFile('esteshhad_file')) {
            LicenseController::upload($request->file('esteshhad_file'), 'esteshhad_file', $profile->id);
        }

        return $next($request);
    }
}
