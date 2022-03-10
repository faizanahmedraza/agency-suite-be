<?php

namespace App\Http\Requests\V1\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rule;
use Pearl\RequestValidate\RequestAbstract;

class UserRequest extends RequestAbstract
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
        if (isset($all['email'])) {
            $all['email'] = preg_replace('/\s+/', '', strtolower(trim($all['email'])));
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
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ($this->isMethod('put')) ? 'required|email:rfc,dns|max:50|email|unique:users,username,' . $this->id : 'required|email:rfc,dns|max:50|unique:users,username',
            'password' => ($this->isMethod('put')) ? '' : 'sometimes|nullable|string|min:6|max:100|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'required|string|in:' . implode(',', Role::userRoles()->pluck('name')->toArray()),
            'permissions' => 'nullable|array',
            'permissions.*' => 'nullable|string',
            'status' => 'required|string|' . Rule::in(array_keys(User::STATUS)),
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
            'roles.*.in' => 'One of these selected roles are invalid.',
        ];
    }
}
