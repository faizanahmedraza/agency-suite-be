<?php

namespace App\Http\Requests\V1\Agency;

use App\Models\User;
use Pearl\RequestValidate\RequestAbstract;

use Illuminate\Validation\Rule;

class UserListRequest extends RequestAbstract
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
            'from_date' => 'nullable|date_format:Y-m-d|date',
            'to_date' => 'nullable|date_format:Y-m-d|date',
            'status' => 'string|'.Rule::in(array_keys(User::STATUS)),
            'order_by' => 'string|'. Rule::in(['asc','desc']),
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
            //
        ];
    }
}
