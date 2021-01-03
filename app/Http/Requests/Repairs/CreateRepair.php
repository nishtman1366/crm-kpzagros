<?php

namespace App\Http\Requests\Repairs;

use Illuminate\Foundation\Http\FormRequest;

class CreateRepair extends FormRequest
{
    protected $errorBag = 'newRepairForm';

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
        return [
            'device_type_id' => 'required|exists:device_types,id',
            'psp_id' => 'required|exists:psps,id',
            'serial' => 'required',
            'name' => 'required',
            'mobile' => 'required',
            'national_code' => 'required',
            'repairTypeList' => 'required|array',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'mobile' => toEnglishNumbers($this->input('mobile')),
            'national_code' => toEnglishNumbers($this->input('national_code')),
            'serial' => toEnglishNumbers($this->input('serial')),
        ]);
    }
}
