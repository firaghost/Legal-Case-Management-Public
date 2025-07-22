<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('update cases');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'case_type_id' => 'required|exists:case_types,id',
            'court_name' => 'nullable|string|max:255',
            'lawyer_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Open,Closed,Suspended',
            'branch_id' => 'nullable|exists:branches,id',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'closed_at' => 'nullable|date|after:opened_at',
            'plaintiffs' => 'required|array|min:1',
            'plaintiffs.*.id' => 'nullable|exists:plaintiffs,id',
            'plaintiffs.*.name' => 'required|string|max:255',
            'plaintiffs.*.contact_info' => 'nullable|string|max:255',
            'defendants' => 'required|array|min:1',
            'defendants.*.id' => 'nullable|exists:defendants,id',
            'defendants.*.name' => 'required|string|max:255',
            'defendants.*.contact_info' => 'nullable|string|max:255',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'progress_note' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Case title is required.',
            'status.in' => 'Status must be Open, Closed, or Suspended.',
            'closed_at.after' => 'Closed date must be after opened date.',
            'plaintiffs.required' => 'At least one plaintiff is required.',
            'defendants.required' => 'At least one defendant is required.',
            'documents.*.mimes' => 'Only PDF, DOC, DOCX, JPG, JPEG, PNG files are allowed.',
            'documents.*.max' => 'File size should not exceed 10MB.',
        ];
    }
}






