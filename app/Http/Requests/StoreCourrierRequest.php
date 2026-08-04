<?php

namespace App\Http\Requests;

use App\Models\Courrier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Courrier::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'objet' => ['required', 'string', 'max:255'],
            'expediteur' => ['required', 'string', 'max:255'],
            'date_reception' => ['required', 'date', 'before_or_equal:today'],
            'reference' => ['nullable', 'string', 'max:100'],
            'priorite' => ['required', Rule::in(array_keys(Courrier::PRIORITES))],
            'type' => ['required', 'string', 'max:100'],
            'service_id' => ['required', 'exists:services,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'piece_jointe' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function attributes(): array
    {
        return [
            'objet' => 'objet',
            'expediteur' => 'expéditeur',
            'date_reception' => 'date de réception',
            'reference' => 'référence',
            'priorite' => 'priorité',
            'service_id' => 'service destinataire',
            'piece_jointe' => 'pièce jointe',
        ];
    }

    public function messages(): array
    {
        return [
            'piece_jointe.mimes' => 'La pièce jointe doit être un fichier PDF, DOCX ou une image (JPG/PNG).',
            'piece_jointe.max' => 'La pièce jointe ne doit pas dépasser 10 Mo.',
            'date_reception.before_or_equal' => 'La date de réception ne peut pas être dans le futur.',
        ];
    }
}
