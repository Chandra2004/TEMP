<?php

namespace TheFramework\Http\Requests;

use TheFramework\App\FormRequest;

class RequirementParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parameter_key' => 'required',
            'display_name' => 'required',
            'input_type' => 'required|in:text,number,select,date',
            'input_options' => 'nullable',
            'allowed_operators' => 'required',
            'description' => 'nullable',
            'is_active' => 'nullable'
        ];
    }

    public function labels(): array
    {
        return [
            'parameter_key' => 'key parameter',
            'display_name' => 'nama tampilan',
            'input_type' => 'tipe input',
            'allowed_operators' => 'operator yang dibolehkan'
        ];
    }
}
