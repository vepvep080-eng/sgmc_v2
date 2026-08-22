<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadScanDocumentRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['token' => ['required', 'string', 'size:64'], 'document' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240']]; }
}
