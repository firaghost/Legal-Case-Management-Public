<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create cases');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'case_type_id' => 'required|exists:case_types,id',
            'court_name' => 'nullable|string|max:255',
            'lawyer_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'plaintiffs' => 'required|array|min:1',
            'plaintiffs.*.name' => 'required|string|max:255',
            'plaintiffs.*.contact_info' => 'nullable|string|max:255',
            'defendants' => 'required|array|min:1',
            'defendants.*.name' => 'required|string|max:255',
            'defendants.*.contact_info' => 'nullable|string|max:255',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Case title is required.',
            'case_type_id.required' => 'Please select a case type.',
            'case_type_id.exists' => 'Selected case type is invalid.',
            'plaintiffs.required' => 'At least one plaintiff is required.',
            'plaintiffs.*.name.required' => 'Plaintiff name is required.',
            'defendants.required' => 'At least one defendant is required.',
            'defendants.*.name.required' => 'Defendant name is required.',
            'documents.*.mimes' => 'Only PDF, DOC, DOCX, JPG, JPEG, PNG files are allowed.',
            'documents.*.max' => 'File size should not exceed 10MB.',
        ];
    }
}






