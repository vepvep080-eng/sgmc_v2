<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectRoutingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reject', $this->route('routing')) ?? false;
    }

    public function rules(): array
    {
        return [
            'commentaire' => ['required', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'commentaire' => 'motif du refus',
        ];
    }

    public function messages(): array
    {
        return [
            'commentaire.required' => 'Le motif du refus est obligatoire.',
        ];
    }
}
