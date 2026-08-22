<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArchiveOutgoingCourrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('archive', $this->route('outgoing_courrier')) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
