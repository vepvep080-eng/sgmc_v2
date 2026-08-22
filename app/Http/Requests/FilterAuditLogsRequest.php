<?php

namespace App\Http\Requests;

use App\Models\AuditLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterAuditLogsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', AuditLog::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'action' => ['nullable', 'string', 'max:100'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', Rule::when($this->filled('date_from'), 'after_or_equal:date_from')],
            'numero' => ['nullable', 'string', 'max:100'],
        ];
    }
}
