<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param mixed $user
     * @param array $input
     * @return void
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'current_password' => ['required', 'string'],
//            'password' => $this->passwordRules(),
            'password' => ['required', 'string', 'min:6'],
        ])->after(function ($validator) use ($user, $input) {
            if (!Hash::check($input['current_password'], $user->password)) {
                $validator->errors()->add('current_password', __('کلمه عبور وارد شده اشتباه است.'));
            }
        })->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => $input['password'],
        ])->save();
    }
}
