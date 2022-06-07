<?php

namespace App\Http\Requests\V1\Agency;

use App\Models\PaymentGateway;
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'gateway' => 'nullable|in:'.implode(',',PaymentGateway::PAYMENT_GATEWAYS),
            'gateway_id' => 'required',
            'gateway_code' => 'required',
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
