<?php

namespace App\Http\Requests;

use App\Models\Routing;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoutingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('redirect', Routing::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'service_destination_id' => ['required', 'exists:services,id'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'service_destination_id' => 'service destinataire',
            'commentaire' => 'commentaire',
        ];
    }
}
