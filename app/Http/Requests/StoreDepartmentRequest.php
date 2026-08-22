<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('create', Department::class) ?? false; }
    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:20', Rule::unique('departments', 'code')], 'manager_id' => ['nullable', 'exists:users,id'], 'description' => ['nullable', 'string', 'max:1000'], 'is_active' => ['boolean']];
    }
}
