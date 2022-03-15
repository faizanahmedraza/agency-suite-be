<?php

namespace App\Http\Requests\V1\Agency;

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
        if(isset($all['roles']))
        {
            $all['roles'] = array_map(array($this, 'addPrefix'),$all['roles']);
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
            'email' => ($this->isMethod('put')) ? 'sometimes|nullable|email:rfc,dns|max:50|email|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i|unique:users,username,' . $this->id.',id,agency_id,'.app('agency')->id : 'required|email:rfc,dns|max:50|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i|unique:users,username,NULL,id,agency_id,'.app('agency')->id,
            'password' => ($this->isMethod('put')) ? '' : 'sometimes|nullable|string|min:6|max:100|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'required|string|in:' . implode(',', Role::agencyRoles()->pluck('id')->toArray()),
            'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'sometimes|nullable|in:' . implode(',', Permission::where('name','like',Role::ROLES_PREFIXES['agency'].'%')->pluck('id')->toArray()),
            'status' => ($this->isMethod('put')) ? 'required|string|' . Rule::in(array_keys(User::STATUS)) : 'sometimes|nullable|string|' . Rule::in(array_keys(User::STATUS)),
        ];
    }

    public function addPrefix($v)
    {
        return Role::ROLES_PREFIXES['agency'].$v;
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
        ];
    }
}
