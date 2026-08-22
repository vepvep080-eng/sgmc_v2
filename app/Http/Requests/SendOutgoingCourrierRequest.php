<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOutgoingCourrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('send', $this->route('outgoing_courrier')) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
