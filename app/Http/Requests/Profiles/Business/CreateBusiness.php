<?php

namespace App\Http\Requests\Profiles\Business;

use Illuminate\Foundation\Http\FormRequest;

class CreateBusiness extends FormRequest
{
    protected $errorBag = 'businessForm';

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
            'profile_id' => 'required|exists:profiles,id',
            'ostan_id' => 'required|exists:ostan,id',
            'shahrestan_id' => 'required|exists:shahrestan,id',
            'bakhsh_id' => 'required|exists:bakhsh,id',
            'shahr_id' => 'nullable|exists:shahr,id',
            'name' => 'required',
            'name_english' => 'required',
            'senf' => 'required',
            'postal_code' => 'required|unique:businesses,postal_code',
            'address' => 'required',
            'phone_code' => 'required',
            'phone' => 'required',
            'has_license' => 'required|in:YES,NO',
        ];
        $hasLicense = $this->input('has_license');

        if ($hasLicense == 'YES') {
            $validationArray = array_merge($validationArray, [
                'license_code' => 'required',
                'license_date' => 'required|date',
                'license_file' => 'required|image',
            ]);
        } elseif ($hasLicense == 'NO') {
            $validationArray = array_merge($validationArray, [
                'esteshhad_file' => 'required|image',
            ]);
        }
        return $validationArray;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'postal_code' => toEnglishNumbers($this->input('postal_code')),
            'phone_code' => toEnglishNumbers($this->input('phone_code')),
            'phone' => toEnglishNumbers($this->input('phone')),
            'license_code' => toEnglishNumbers($this->input('license_code')),
            'address' => toEnglishNumbers(clearAddress($this->input('address')))
        ]);
    }
}
