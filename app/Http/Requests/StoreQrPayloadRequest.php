<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQrPayloadRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('update', $this->route('courrier')) ?? false; }
    public function rules(): array { return ['payload' => ['required', 'string', 'max:2048']]; }
}
