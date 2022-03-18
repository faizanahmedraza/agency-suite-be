<?php

namespace App\Http\Requests\V1\Agency;

use App\Models\Service;
use Pearl\RequestValidate\RequestAbstract;


class ServiceRequest extends RequestAbstract
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
        if (isset($all['name'])) {
            $all['name'] = preg_replace('/\s+/', ' ', trim(strtolower($all['name'])));
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
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'image' => 'sometimes|string',
            'subscription_type' => 'required|in:' . implode(',', array_keys(Service::SUBSCRIPTION_TYPES)),
            'status' => 'required|in:' . implode(',', array_keys(Service::STATUS)),
            'price' => 'numeric|required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[0],
            'purchase_limit' => 'sometimes|nullable|numeric',
            'weekly' => 'numeric|required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[1],
            'monthly' => 'numeric|required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[1],
            'quarterly' => 'numeric|required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[1],
            'biannually' => 'numeric|required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[1],
            'annually' => 'numeric|required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[1],
            'max_concurrent_requests' => 'sometimes|nullable|numeric',
            'max_requests_per_month' => 'sometimes|nullable|numeric',
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
