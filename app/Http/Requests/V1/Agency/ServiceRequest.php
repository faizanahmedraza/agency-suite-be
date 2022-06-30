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
            $all['name'] = preg_replace('/\s+/', ' ', trim($all['name']));
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
            'name' => 'required|string|max:150',
            'description' => 'required|string|max:10000',
            'image' => 'sometimes|string',
            'subscription_type' => 'required|in:' . implode(',', array_keys(Service::SUBSCRIPTION_TYPES)),
            'price' => 'required_if:subscription_type,'.array_keys(Service::SUBSCRIPTION_TYPES)[0].'|numeric',
            'purchase_limit' => 'sometimes|nullable|numeric|min:1',
            'weekly' => 'sometimes|nullable|numeric|min:1',
            'monthly' => 'sometimes|nullable|numeric|min:1',
            'quarterly' => 'sometimes|nullable|numeric|min:1',
            'biannually' => 'sometimes|nullable|numeric|min:1',
            'annually' => 'sometimes|nullable|numeric|min:1',
            'max_concurrent_requests' => 'sometimes|nullable|numeric|min:1|max:100',
            'max_requests_per_month' => 'sometimes|nullable|numeric|min:1|max:30',
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
