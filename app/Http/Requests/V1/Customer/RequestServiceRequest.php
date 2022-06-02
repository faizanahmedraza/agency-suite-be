<?php

namespace App\Http\Requests\V1\Customer;

use App\Models\CustomerServiceRequest;
use App\Models\Service;
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
                Rule::exists('services','id')->where(function ($query) {
                    $query->where('agency_id', app('agency')->id)->where('catalog_status', 1)->where('status', 1);
                })
            ],
            'recurring_type' => [
                Rule::requiredIf(function () {
                    $service = Service::where('id', $this->service_id)->where('agency_id', app('agency')->id)->first();
                    if (!empty($service) && $service->subscription_type == 1) {
                        return true;
                    }
                    return false;
                }),
                'in:' . implode(",", CustomerServiceRequest::RECURRING_TYPE)
            ],
            'intake_form' => "required|array",
            'intake_form.0.title' => "required|string",
            'intake_form.0.description' => "nullable|string",
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
            'intake_form.0.description.string' => "The intake form description must be a string.",
        ];
    }
}
