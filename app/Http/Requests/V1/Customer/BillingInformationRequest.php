<?php

namespace App\Http\Requests\V1\Customer;

use Pearl\RequestValidate\RequestAbstract;

class BillingInformationRequest extends RequestAbstract
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
            'invoice_to' => 'required|string|max:100',
            'address' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|numeric|digits_between:5,20',
            'tax_code' => 'sometimes|nullable|string|max:30',
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
            'zip_code.digits_between' => 'The zip code must be greater than 4 and less than 20 digits.'
        ];
    }
}
