<?php

namespace App\Http\Requests\V1\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rule;
use Pearl\RequestValidate\RequestAbstract;
use Spatie\Permission\Models\Permission;

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
            'email' => ($this->isMethod('put')) ? 'sometimes|nullable|email:rfc,dns|max:50|email|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i|unique:users,username,' . $this->id.',id,agency_id,NULL' : 'required|email:rfc,dns|max:50|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i|unique:users,username,NULL,id,agency_id,NULL',
            'password' => ($this->isMethod('put')) ? '' : 'sometimes|nullable|string|min:6|max:100|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'required|in:' . implode(',', Role::adminRoles()->pluck('id')->toArray()),
            'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'sometimes|nullable|in:' . implode(',', Permission::where('name', 'not like', Role::ROLES_PREFIXES['agency'] . '%')->pluck('id')->toArray()),
            'status' => ($this->isMethod('put')) ? 'required|string|' . Rule::in(array_keys(User::STATUS)) : 'sometimes|nullable|string|' . Rule::in(array_keys(User::STATUS)),
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
            'roles.*.in' => 'The selected roles are invalid.',
            'permissions.*.in' => 'The selected permissions are invalid.',
            'password.confirmed' => "Password did not matched.",
        ];
    }
}
