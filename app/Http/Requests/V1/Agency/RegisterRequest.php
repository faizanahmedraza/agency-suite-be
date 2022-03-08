<?php

namespace App\Http\Requests\V1;

use App\Rules\EmailFormatRule;
use Pearl\RequestValidate\RequestAbstract;


class RegisterRequest extends RequestAbstract
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
            'first_name' => 'required|alpha|max:100',
            'last_name' => 'required|alpha|max:100',
            'email' => ['required','email:rfc,dns',new EmailFormatRule(),'max:100','unique:users,username'],
            'password' => 'required|confirmed|min:6||max:100|string',
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
            'email.unique' => "Email already exist.",
            'password.min' => "Password length must be greater than 5 characters.",
            'password.confirmed' => "Password not matched.",
        ];
    }
}
