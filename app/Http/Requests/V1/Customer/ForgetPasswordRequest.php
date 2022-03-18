<?php

namespace App\Http\Requests\V1\Customer;

use App\Rules\EmailFormatRule;
use Pearl\RequestValidate\RequestAbstract;

class ForgetPasswordRequest extends RequestAbstract
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
            'email' => ['required','email:rfc,dns','max:100',new EmailFormatRule(),'exists:users,username,agency_id,'.app('agency')->id]
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
            'email.exists' => "Email doesn't exist."
        ];
    }
}
