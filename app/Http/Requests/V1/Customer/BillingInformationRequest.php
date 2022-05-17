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
            'holder_name' => 'required|max:100|regex:/^[a-zA-Z ]*$/i',
            'card_no' => 'required|regex:/^[0-9]{13,19}$/',
            'cvc' => 'required|regex:/^[0-9]{3,4}$/',
            'expiry_month' => 'required|regex:/\d*[1-9]/',
            'expiry_year' => 'required|regex:/^[0-9]{2}$/',
            'address' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|numeric|digits_between:5,20',
            'street' => 'sometimes|nullable|string|max:50',
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
            'holder_name.regex' => 'The holder name should contain alphabets.',
            'card_no.regex' => 'The card no should contain only 13 to 19 digits.',
            'cvc.regex' => 'The cvc should contain only 3 to 4 digits.',
            'expiry_month.regex' => 'The expiry month should contain only 1 to 2 digits.',
            'expiry_year.regex' => 'The expiry year should contain only 1 to 2 digits.',
            'zip_code.digits_between' => 'The zip code must be greater than 4 and less than 20 digits.'
        ];
    }
}
