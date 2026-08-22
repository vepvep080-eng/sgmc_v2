<?php

namespace App\Http\Requests;

use App\Models\Courrier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterCourriersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Courrier::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'statut' => ['nullable', Rule::in(array_keys(Courrier::STATUTS))],
            'priorite' => ['nullable', Rule::in(array_keys(Courrier::PRIORITES))],
            'type' => ['nullable', 'string', 'max:100'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => [
                'nullable',
                'date',
                Rule::when($this->filled('date_from'), 'after_or_equal:date_from'),
            ],
            'sort' => ['nullable', Rule::in(Courrier::SORTABLE_COLUMNS)],
            'dir' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}