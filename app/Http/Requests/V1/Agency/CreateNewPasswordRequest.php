<?php

namespace App\Http\Requests\V1\Agency;

use Pearl\RequestValidate\RequestAbstract;

class CreateNewPasswordRequest extends RequestAbstract
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
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData(): array
    {
        $all = parent::validationData();
        if (isset($all['password'])) {
            $all['password'] = preg_replace('/\s+/', '', $all['password']);
        }
        return $all;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => 'required|string|max:35',
            'password' => 'required|string|min:6|max:100|confirmed'
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
            //
        ];
    }
}
