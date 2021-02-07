<?php

namespace App\Http\Requests\Profiles\Customers;

use App\Rules\NationalCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CreateCustomer extends FormRequest
{
    protected $errorBag = 'customerForm';

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
            'type' => ['required', Rule::in(['PERSON', 'ORGANIZATION']),],
            'first_name' => 'required',
            'first_name_english' => 'nullable',
            'last_name' => 'required',
            'last_name_english' => 'nullable',
            'father' => 'required',
            'father_english' => 'nullable',
            'national_code' => ['required', 'numeric', 'digits:10', new NationalCode($this->get('first_name'), $this->get('last_name'))],
            'id_code' => 'required|digits_between:1,10',
            'birthday' => 'required|date',
            'gender' => ['required', Rule::in(['male', 'female']),],
            'mobile' => 'required|numeric|digits:11|starts_with:09',
            'national_card_file_1' => 'required|image|mimetypes:image/jpg,image/jpeg',
            'national_card_file_2' => 'required|image|mimetypes:image/jpg,image/jpeg',
            'id_file' => 'required|image|mimetypes:image/jpg,image/jpeg',
        ];
        $type = $this->input('type');

        if ($type == 'ORGANIZATION') {
            $validationArray = array_merge($validationArray, [
                'company_name' => 'required',
                'company_name_english' => 'required',
                'business_name' => 'required',
                'reg_date' => 'required|date',
                'reg_code' => 'required',
                'company_national_code' => 'required|digits:11',
                'asasname_file' => 'required|image|mimetypes:image/jpg,image/jpeg',
                'agahi_file_1' => 'required|image|mimetypes:image/jpg,image/jpeg',
                'agahi_file_2' => 'required|image|mimetypes:image/jpg,image/jpeg',
            ]);
        }

        return $validationArray;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'national_code' => toEnglishNumbers($this->input('national_code')),
            'id_code' => toEnglishNumbers($this->input('id_code')),
            'mobile' => toEnglishNumbers($this->input('mobile')),
            'reg_code' => toEnglishNumbers($this->input('reg_code')),
        ]);
    }
}
