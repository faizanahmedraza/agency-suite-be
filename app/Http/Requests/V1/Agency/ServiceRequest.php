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
            'price' => 'required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[0].'|numeric',
            'purchase_limit' => 'sometimes|nullable|numeric',
            'weekly' => 'required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[1].'|numeric',
            'monthly' => 'required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[1].'|numeric',
            'quarterly' => 'required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[1].'|numeric',
            'biannually' => 'required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[1].'|numeric',
            'annually' => 'required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[1].'|numeric',
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
