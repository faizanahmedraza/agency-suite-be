<?php

namespace App\Http\Requests\V1\Agency;

use App\Models\CustomerServiceRequest;
use App\Models\Service;
use App\Models\User;
use Illuminate\Validation\Rule;
use Pearl\RequestValidate\RequestAbstract;

class CustomerToggleStatusRequest extends RequestAbstract
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
            'status' => 'required|in:'.implode(',',array_flip(User::STATUS)),
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
