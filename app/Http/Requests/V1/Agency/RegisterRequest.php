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
            $all['email'] = preg_replace('/\s+/', '', strtolower(trim($all['email'])));
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
            'email' => 'required|email:rfc,dns|max:100|email|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i',
            'agency_name' => 'required|string|max:100|regex:/^[A-Za-z0-9]([\s_\.-]?\w+)+[A-Za-z0-9]$/i|unique:agencies,name',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
