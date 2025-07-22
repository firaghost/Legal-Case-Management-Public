<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            'case_file_id' => 'required|exists:case_files,id',
            'doc_type' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'A file must be uploaded.',
            'file.mimes' => 'Only PDF, DOC, DOCX, JPG, JPEG, PNG files are allowed.',
            'file.max' => 'File size should not exceed 10MB.',
            'case_file_id.required' => 'Case file is required.',
            'case_file_id.exists' => 'Selected case file is invalid.',
        ];
    }
}






