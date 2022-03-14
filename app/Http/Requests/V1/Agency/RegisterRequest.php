<?php

namespace App\Http\Requests\V1\Agency;

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
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData(): array
    {
        $all = parent::validationData();
        //Convert request value to lowercase
        if (isset($all['email']) && isset($all['agency_name'])) {
            $all['email'] = strtolower(preg_replace('/\s+/', '', $all['email']));
            $all['agency_name'] = preg_replace('/\s+/', ' ', $all['agency_name']);
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
            'first_name' => 'required|alpha|max:100',
            'last_name' => 'required|alpha|max:100',
            'email' => 'required|email:rfc,dns|max:50|email|unique:users,username',
            'password' => 'required|string|min:6|max:100|confirmed',
            'agency_name' => 'required|string|max:100|unique:agencies,name',
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
