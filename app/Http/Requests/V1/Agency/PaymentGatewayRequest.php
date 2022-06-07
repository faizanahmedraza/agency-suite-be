<?php

namespace App\Http\Requests\V1\Agency;

use Pearl\RequestValidate\RequestAbstract;

class PaymentGatewayRequest extends RequestAbstract
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
            $all['gateway'] = preg_replace('/\s+/', '', strtolower(trim($all['gateway'])));
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
            'gateway' => 'required',
            'gateway_id' => 'required',
            'gateway_code' => '',
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

        ];
    }
}
