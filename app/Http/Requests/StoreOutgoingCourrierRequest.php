<?php

namespace App\Http\Requests;

use App\Models\OutgoingCourrier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOutgoingCourrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', OutgoingCourrier::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'objet' => ['required', 'string', 'max:255'],
            'destinataire' => ['required', 'string', 'max:255'],
            'organisme_destinataire' => ['required', 'string', 'max:255'],
            'adresse_destinataire' => ['nullable', 'string', 'max:500'],
            'date_envoi' => ['required', 'date'],
            'mode_envoi' => ['required', Rule::in(OutgoingCourrier::MODES_ENVOI)],
            'reference_postale' => ['nullable', 'string', 'max:100'],
            'priorite' => ['required', Rule::in(array_keys(OutgoingCourrier::PRIORITES))],
            'description' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            if ($this->filled('section_id') && ! \App\Models\Section::whereKey($this->integer('section_id'))->where('service_id', $this->integer('service_id'))->exists()) $validator->errors()->add('section_id', __('app.validation.section_service_mismatch'));
            if ($this->filled('assigned_user_id') && ! \App\Models\User::whereKey($this->integer('assigned_user_id'))->where('section_id', $this->integer('section_id'))->exists()) $validator->errors()->add('assigned_user_id', __('app.validation.recipient_section_mismatch'));
        }];
    }
}
