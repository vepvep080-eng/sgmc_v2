<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\User::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', Rule::exists('roles', 'id')],
            'service_id' => [
                Rule::requiredIf(fn () => $this->roleIsChefService()),
                'nullable',
                Rule::exists('services', 'id'),
            ],
            'is_active' => ['boolean'],
        ];
    }

    protected function roleIsChefService(): bool
    {
        $chefServiceRoleId = Role::where('slug', Role::CHEF_SERVICE)->value('id');

        return $chefServiceRoleId !== null && (int) $this->input('role_id') === (int) $chefServiceRoleId;
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'email' => 'adresse e-mail',
            'password' => 'mot de passe',
            'role_id' => 'rôle',
            'service_id' => 'service',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Un service doit être sélectionné pour un Chef de Service.',
        ];
    }
}
