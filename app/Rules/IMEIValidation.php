<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IMEIValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $value = toEnglishNumbers($value);
        $s = (string)$value;
        $len = strlen($s);

        if ($len != 15)
            return false;

        $sum = 0;
        for ($i = $len; $i >= 1; $i--) {
            $d = ($value % 10);

            // Doubling every alternate digit
            if ($i % 2 == 0)
                $d = 2 * $d;

            // Finding sum of the digits
            $sum += $this->sumDigits($d);
            $value = intval($value / 10, 10);
        }

        return ($sum % 10 == 0);
    }

    private function sumDigits($char)
    {
        $a = 0;
        while ($char > 0) {
            $a = $a + $char % 10;
            $char = intval($char / 10, 10);
        }
        return $a;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'کد IMEI وارد شده اشتباه است.';
    }
}
