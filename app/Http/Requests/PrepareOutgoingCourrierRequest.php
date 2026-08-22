<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrepareOutgoingCourrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('prepare', $this->route('outgoing_courrier')) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
