<?php

namespace App\Http\Requests\Profiles\Accounts;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccount extends FormRequest
{
    protected $errorBag = 'accountForm';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validationArray = [
            'accounts.*.bank_id' => 'required|exists:banks,id',
            'accounts.*.branch' => 'required|numeric',
            'accounts.*.account_number' => 'required|numeric',
            'accounts.*.sheba_code' => 'required|digits:24|numeric',
            'accounts.*.sheba_file' => 'required|image',
            'accounts.*.first_name' => 'required',
            'accounts.*.last_name' => 'required',
            'accounts.*.national_code' => 'required|digits:10|numeric',
            'accounts.*.birthday' => 'required|date',
            'accounts.*.mobile' => 'required|digits:11,starts_with:09|numeric',
        ];
        return $validationArray;
    }
}
