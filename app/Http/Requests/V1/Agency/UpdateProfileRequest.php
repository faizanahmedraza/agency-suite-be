<?php

namespace App\Http\Requests\V1\Agency;

use Pearl\RequestValidate\RequestAbstract;

class UpdateProfileRequest extends RequestAbstract
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|regex:/^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/i|max:100',
            'last_name' => 'required|regex:/^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/i|max:100',
            'image' => 'sometimes|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'The name should not contain numbers and special characters.'
        ];
    }
}
