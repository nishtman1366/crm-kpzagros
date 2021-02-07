<?php

namespace App\Rules;

use App\Models\Profiles\Customer;
use Illuminate\Contracts\Validation\Rule;

class NationalCode implements Rule
{
    private $messageType;
    private $firstName;
    private $lastName;

    /**
     * NationalCode constructor.
     * @param $firstName
     * @param $lastName
     */
    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
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
        $customers = Customer::where('national_code', $value)->get();
        if (count($customers) === 0) return $this->checkNationalCodeAlgorithm($value);
        foreach ($customers as $customer) {
            if (trim($customer->first_name) !== trim($this->firstName) || trim($customer->last_name) !== trim($this->lastName)) {
                $this->messageType = 2;
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        switch ($this->messageType) {
            default:
            case 1:
                return 'کد ملی وارد شده اشتباه است';
            case 2:
                return 'کد ملی وارد شده متعلق به شخص دیگری می باشد';
        }
    }

    private function checkNationalCodeAlgorithm($value)
    {
        $this->messageType = 1;
        if (!preg_match('/^[0-9]{10}$/', $value))
            return false;
        for ($i = 0; $i < 10; $i++)
            if (preg_match('/^' . $i . '{10}$/', $value))
                return false;
        for ($i = 0, $sum = 0; $i < 9; $i++)
            $sum += ((10 - $i) * intval(substr($value, $i, 1)));
        $ret = $sum % 11;
        $parity = intval(substr($value, 9, 1));
        if (($ret < 2 && $ret == $parity) || ($ret >= 2 && $ret == 11 - $parity))
            return true;
        return false;
    }
}
