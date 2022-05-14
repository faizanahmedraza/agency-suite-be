<?php

namespace App\Http\Requests\V1\Customer;

use App\Models\CustomerServiceRequest;
use Pearl\RequestValidate\RequestAbstract;

class RequestServiceRequest extends RequestAbstract
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
            'service_id' => 'required|exists:services,id',
            'recurring_type' => 'nullable|in:'.implode(",",CustomerServiceRequest::RECURRING_TYPE),
            'intake_form' => "required|array",
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
