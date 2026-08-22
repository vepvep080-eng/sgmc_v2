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
            'department_id' => ['nullable', 'exists:departments,id'],
            'service_destination_id' => ['required', 'exists:services,id'],
            'section_destination_id' => ['nullable', 'exists:sections,id'],
            'recipient_user_id' => ['nullable', 'exists:users,id'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            $departmentId = $this->integer('department_id');
            $serviceId = $this->integer('service_destination_id');

            if ($departmentId && ! \App\Models\Service::whereKey($serviceId)->where('department_id', $departmentId)->exists()) {
                $validator->errors()->add('service_destination_id', __('app.validation.service_department_mismatch'));
            }

            if ($this->filled('recipient_user_id') && ! \App\Models\User::whereKey($this->integer('recipient_user_id'))->where('service_id', $serviceId)->exists()) {
                $validator->errors()->add('recipient_user_id', __('app.validation.recipient_service_mismatch'));
            }

            if ($this->filled('section_destination_id') && ! \App\Models\Section::whereKey($this->integer('section_destination_id'))->where('service_id', $serviceId)->exists()) {
                $validator->errors()->add('section_destination_id', __('app.validation.section_service_mismatch'));
            }

            if ($this->filled('recipient_user_id') && $this->filled('section_destination_id') && ! \App\Models\User::whereKey($this->integer('recipient_user_id'))->where('section_id', $this->integer('section_destination_id'))->exists()) {
                $validator->errors()->add('recipient_user_id', __('app.validation.recipient_section_mismatch'));
            }

            $courrier = $this->route('courrier');
            $user = $this->user();
            if ($user?->hasRole(\App\Models\Role::CHEF_SERVICE) && ($courrier?->service_id !== $user->service_id || $serviceId !== $user->service_id)) {
                $validator->errors()->add('service_destination_id', __('app.validation.routing_scope_denied'));
            }
            if ($user?->hasRole(\App\Models\Role::CHEF_SECTION) && ($courrier?->section_id !== $user->section_id || $this->integer('section_destination_id') !== $user->section_id)) {
                $validator->errors()->add('section_destination_id', __('app.validation.routing_scope_denied'));
            }
        }];
    }

    public function attributes(): array
    {
        return [
            'service_destination_id' => 'service destinataire',
            'section_destination_id' => 'section destinataire',
            'commentaire' => 'commentaire',
        ];
    }
}
