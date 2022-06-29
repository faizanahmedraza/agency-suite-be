<?php

namespace App\Http\Requests\V1\Agency;

use App\Models\CustomerInvoice;
use App\Models\CustomerServiceRequest;
use App\Models\Service;
use Illuminate\Validation\Rule;
use Pearl\RequestValidate\RequestAbstract;

class InvoiceRequest extends RequestAbstract
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
            'invoice_type' => 'required|in:' . implode(',', CustomerInvoice::TYPES),
            'service_id' => [
                Rule::requiredIf(function () {
                    $type = $this->invoice_type;
                    if (!empty($type)) {
                        if ($type == CustomerInvoice::TYPES[0]) {
                            return true;
                        }
                    }
                    return false;
                }),
                Rule::exists('services', 'id')->where(function ($query) {
                    $query->where('agency_id', app('agency')->id);
                })
            ],
            'customer_id' => [
                'required',
                Rule::exists('agency_customers', 'user_id')->where(function ($query) {
                    $query->where('agency_id', app('agency')->id);
                })
            ],
            'recurring_type' => [
                Rule::requiredIf(function () {
                    if (!empty($this->service_id)) {
                        $service = Service::where('id', $this->service_id)->first();
                        if (!empty($service) && $service->subscription_type == 1) {
                            return true;
                        }
                    }
                    return false;
                }),
            ],
            'quantity' => 'sometimes|nullable|numeric|min:1',
            'intake_form' => [
                Rule::requiredIf(function () {
                    $type = $this->invoice_type;
                    if (!empty($type)) {
                        if ($type == CustomerInvoice::TYPES[0]) {
                            return true;
                        }
                    }
                    return false;
                }),
                'array'
            ],
            'intake_form.0.title' => [
                Rule::requiredIf(function () {
                    $type = $this->invoice_type;
                    if (!empty($type)) {
                        if ($type == CustomerInvoice::TYPES[0]) {
                            return true;
                        }
                    }
                    return false;
                }),
                "string"
            ],
            'intake_form.0.description' => "nullable|string",
            'invoice_items' => [
                Rule::requiredIf(function () {
                    $type = $this->invoice_type;
                    if (!empty($type)) {
                        if ($type == CustomerInvoice::TYPES[1]) {
                            return true;
                        }
                    }
                    return false;
                }),
                'array'
            ],
            'invoice_items.*.name' => [
                Rule::requiredIf(function () {
                $type = $this->invoice_type;
                if (!empty($type)) {
                    if ($type == CustomerInvoice::TYPES[1]) {
                        return true;
                    }
                }
                return false;
            }), 'string'],
            'invoice_items.*.rate' => [
                Rule::requiredIf(function () {
                    $type = $this->invoice_type;
                    if (!empty($type)) {
                        if ($type == CustomerInvoice::TYPES[1]) {
                            return true;
                        }
                    }
                    return false;
                }), 'numeric','min:1'],
            'invoice_items.*.quantity' => [
                Rule::requiredIf(function () {
                    $type = $this->invoice_type;
                    if (!empty($type)) {
                        if ($type == CustomerInvoice::TYPES[1]) {
                            return true;
                        }
                    }
                    return false;
                }), 'numeric','min:1'],
            'invoice_items.*.discount' => 'sometimes|nullable|numeric|min:0',
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
