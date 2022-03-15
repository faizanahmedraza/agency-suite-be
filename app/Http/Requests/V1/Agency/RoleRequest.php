<?php

namespace App\Http\Requests\V1\Agency;

use App\Models\Role;
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
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData(): array
    {
        $all = parent::validationData();
        //Remove extra white spaces
        if (isset($all['name'])) {
            $all['name'] = Role::ROLES_PREFIXES['agency'] . trim($all['name']);
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
            'name' => $this->isMethod('PUT') ? "required|string|max:255|unique:roles,name," . $this->id.',id,agency_id,'.app('agency')->id : "required|string|max:255|unique:roles,name,NULL,id,agency_id,".app('agency')->id,
            'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'sometimes|nullable|in:' . implode(',', Permission::where('name', 'like', Role::ROLES_PREFIXES['agency'].'%')->pluck('id')->toArray())
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
            'permissions.*.in' => 'The selected permissions are invalid.',
        ];
    }
}
