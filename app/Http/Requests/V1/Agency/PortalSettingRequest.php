<?php

namespace App\Http\Requests\V1\Agency;

use App\Models\AgencyDomain;
use App\Rules\NotAllowedDomainRule;
use Pearl\RequestValidate\RequestAbstract;


class PortalSettingRequest extends RequestAbstract
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
            $all['name'] = preg_replace('/\s+/', ' ', $all['name']);
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
            'name' => 'sometimes|nullable|string|max:100|regex:/^[A-Za-z0-9]([\s_\.-]?\w+)+[A-Za-z0-9]$/i|unique:agencies,name,'.app('agency')->id,',deleted_at,NULL',
            'domain' => ['required', 'string', 'max:100', 'regex:/^((?!http))[^*|\":<>[\]{}`\/\\()\';\s@&$#+]+$/i', new NotAllowedDomainRule],
            'logo' => 'sometimes|nullable|string',
            'favicon' => 'sometimes|nullable|string',
            'primary_color' => 'sometimes|nullable|string'
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
