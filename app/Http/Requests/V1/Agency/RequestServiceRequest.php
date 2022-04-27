<?php

namespace App\Http\Requests\V1\Agency;

use App\Models\CustomerServiceRequest;
use Illuminate\Validation\Rule;
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
            'customer_id' => 'required|exists:agency_customers,user_id',
            'recurring_type' => 'nullable|in:' . implode(",", CustomerServiceRequest::RECURRING_TYPE),
            'refrence_no' => 'required',
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
