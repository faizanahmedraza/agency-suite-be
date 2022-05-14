<?php

namespace App\Http\Requests\V1\Customer;

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
            'service_id' => [
                'required',
                Rule::exists('services')->where(function ($query) {
                    $query->where('agency_id', app('agency')->id)->where('catalog_status', 1)->where('status', 1);
                })
            ],
            'recurring_type' => 'nullable|in:' . implode(",", CustomerServiceRequest::RECURRING_TYPE),
            'reference_no' => 'required',
            'intake_form' => "required|array",
            'intake_form.0.title' => "required|string",
            'intake_form.0.description' => "required|string",
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
            'intake_form.0.title.required' => "The intake form title field is required.",
            'intake_form.0.title.string' => "The intake form title must be a string.",
            'intake_form.0.description.required' => "The intake form description field is required.",
            'intake_form.0.description.string' => "The intake form description must be a string.",
        ];
    }
}
