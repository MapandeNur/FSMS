<?php

namespace App\Http\Requests;

use App\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreStudentDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type' => [
                'required',
                new Enum(DocumentType::class),
            ],

            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'document_type.required' => 'Document type is required.',
            'document.required' => 'Please select a document.',
            'document.file' => 'The uploaded item must be a valid file.',
            'document.mimes' => 'Only PDF, JPG, JPEG, and PNG files are allowed.',
            'document.max' => 'The document must not exceed 2MB.',
        ];
    }
}