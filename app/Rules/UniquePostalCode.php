<?php

namespace App\Rules;

use App\Models\Profiles\Business;
use App\Models\Profiles\Customer;
use App\Models\Profiles\Profile;
use Illuminate\Contracts\Validation\Rule;

class UniquePostalCode implements Rule
{
    private $profileId;

    private $update;

    /**
     * Create a new rule instance.
     *
     * @param int $profileId
     * @param bool $update
     */
    public function __construct(int $profileId, bool $update = false)
    {
        if (is_null($profileId) || $profileId === 0) return false;
        $this->profileId = $profileId;
        $this->update = $update;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $profile = Profile::with('customer')->where('id', $this->profileId)->get()->first();

        $customers = Customer::where('national_code', $profile->customer->national_code)
            ->where('profile_id', '!=', $this->profileId)
            ->pluck('profile_id');

        if (count($customers) == 0) {
            if ($this->update) {
                $b = Business::where('postal_code', $value)
                    ->where('profile_id', '!=', $this->profileId)
                    ->exists();

                return !$b;
            } else {
                $b = Business::where('postal_code', $value)
                    ->exists();

                return !$b;
            }
        } else {
            $businesses = Business::whereIn('profile_id', $customers)
                ->pluck('id');

            foreach ($businesses as $business) {
                $b = Business::where('postal_code', $value)
                    ->where('id', '!=', $business)
                    ->exists();

                if ($b) return false;
            }

            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'کد پستی وارد شده تکراری است';
    }
}
