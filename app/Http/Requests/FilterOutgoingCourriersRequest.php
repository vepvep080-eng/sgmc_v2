<?php

namespace App\Http\Requests;

use App\Models\OutgoingCourrier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterOutgoingCourriersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', OutgoingCourrier::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'statut' => ['nullable', Rule::in(array_keys(OutgoingCourrier::STATUTS))],
            'priorite' => ['nullable', Rule::in(array_keys(OutgoingCourrier::PRIORITES))],
            'mode_envoi' => ['nullable', Rule::in(OutgoingCourrier::MODES_ENVOI)],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort' => ['nullable', Rule::in(OutgoingCourrier::SORTABLE_COLUMNS)],
            'dir' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
