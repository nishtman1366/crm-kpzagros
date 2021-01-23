<?php

namespace App\Http\Requests\Profiles\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTerminal extends FormRequest
{
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
            'terminal_id' => 'required',
            'merchant_id' => 'required'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'terminal_id' => toEnglishNumbers($this->input('terminal_id')),
            'merchant_id' => toEnglishNumbers($this->input('merchant_id')),
        ]);
    }
}
