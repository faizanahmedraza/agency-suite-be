<?php

namespace App\Http\Requests\V1\Agency;

use App\Rules\EmailFormatRule;
use Pearl\RequestValidate\RequestAbstract;

class CustomerRequest extends RequestAbstract
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
        if (isset($all['email'])) {
            $all['email'] = preg_replace('/\s+/', '', strtolower(trim($all['email'])));
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
            'first_name' => 'required|regex:/^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/i|string|max:100',
            'last_name' => 'required|regex:/^[a-zA-Z]+(?:\s[a-zA-Z]+)*$/i|string|max:100',
            'email' => ($this->isMethod('put')) ? '' : 'required|email:rfc,dns|max:50|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i|unique:users,username,NULL,id,agency_id,'.app('agency')->id.',deleted_at,NULL',
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
            'first_name.regex' => 'The first name should not contain numbers and special characters.',
            'last_name.regex' => 'The last name should not contain numbers and special characters.',
        ];
    }
}
