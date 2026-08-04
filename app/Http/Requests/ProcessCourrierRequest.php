<?php

namespace App\Http\Requests;

use App\Models\Routing;
use Illuminate\Foundation\Http\FormRequest;

class ProcessCourrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        $routing = $this->route('routing');

        return $routing instanceof Routing
            && ($this->user()?->can('process', $routing) ?? false);
    }

    public function rules(): array
    {
        return [
            'commentaire' => ['required', 'string', 'max:5000'],
            'piece_jointe_traitement' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
                'max:10240',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'commentaire' => 'commentaire de traitement',
            'piece_jointe_traitement' => 'pièce jointe',
        ];
    }

    public function messages(): array
    {
        return [
            'piece_jointe_traitement.mimes' => 'La pièce jointe doit être un fichier PDF, DOC, DOCX ou une image (JPG/PNG).',
            'piece_jointe_traitement.max' => 'La pièce jointe ne doit pas dépasser 10 Mo.',
        ];
    }
}
