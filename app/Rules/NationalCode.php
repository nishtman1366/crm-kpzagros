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
            if ($this->manipulateCustomerName($customer->first_name) !== trim($this->firstName) || $this->manipulateCustomerName($customer->last_name) !== trim($this->lastName)) {
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
        return checkNationalCode($value);
    }

    private function manipulateCustomerName(string $name)
    {
        $name = str_replace('ي', 'ی', $name);
        $name = str_replace('ك', 'ک', $name);
        $name = trim($name);

        return $name;
    }
}
