<?php

namespace App\Http\Requests\V1\Admin;

use Pearl\RequestValidate\RequestAbstract;
use Spatie\Permission\Models\Permission;

class RoleRequest extends RequestAbstract
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
            'name' =>  ($this->isMethod('put')) ? "required|string|max:255|unique:roles,name,". $this->id : "required|string|max:255|unique:roles,name",
            'permissions' => 'sometimes|nullable|in:'.implode(',',Permission::pluck('id')->toArray())
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
