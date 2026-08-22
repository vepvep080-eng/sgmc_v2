<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('courrier')) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:connected,disconnected'],
            'scanner_name' => ['nullable', 'string', 'max:255'],
            'error_message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
