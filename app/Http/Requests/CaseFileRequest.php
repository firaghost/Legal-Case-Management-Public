<?php

namespace App\Http\Requests;

use App\Models\CaseFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CaseFileRequest extends FormRequest
{
    /**
     * Case file instance for route model binding.
     */
    protected ?CaseFile $caseFile = null;

    /**
     * Get the case file instance from the route.
     */
    protected function getCaseFile(): ?CaseFile
    {
        if ($this->caseFile === null) {
            $this->caseFile = $this->route('case');
        }

        return $this->caseFile;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $caseFile = $this->getCaseFile();
        $user = $this->user();

        if ($this->isMethod('POST')) {
            return $user->can('case_files.create');
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return $user->can('update', $caseFile);
        }

        if ($this->isMethod('DELETE')) {
            return $user->can('delete', $caseFile);
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $caseFile = $this->getCaseFile();
        $caseFileId = $caseFile ? $caseFile->id : null;

        $rules = [
            'case_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('case_files', 'case_number')->ignore($caseFileId),
            ],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => [
                'required',
                'string',
                Rule::in([
                    'open',
                    'in_progress',
                    'pending',
                    'closed',
                    'reopened',
                    'dismissed',
                    'settled',
                ]),
            ],
            'filing_date' => 'required|date',
            'hearing_date' => 'nullable|date|after_or_equal:filing_date',
            'closing_date' => 'nullable|date|after_or_equal:filing_date',
            'case_type' => [
                'required',
                'string',
                Rule::in([
                    'civil',
                    'criminal',
                    'family',
                    'commercial',
                    'labor',
                    'administrative',
                    'constitutional',
                    'other',
                ]),
            ],
            'priority' => [
                'required',
                'string',
                Rule::in(['low', 'medium', 'high', 'urgent']),
            ],
            'lawyer_id' => 'required|exists:users,id',
            'court_id' => 'required|exists:courts,id',
            'plaintiffs' => 'nullable|array',
            'plaintiffs.*.name' => 'required_with:plaintiffs|string|max:255',
            'plaintiffs.*.type' => 'required_with:plaintiffs|string|in:individual,company,organization',
            'plaintiffs.*.contact_info' => 'nullable|string|max:500',
            'defendants' => 'nullable|array',
            'defendants.*.name' => 'required_with:defendants|string|max:255',
            'defendants.*.type' => 'required_with:defendants|string|in:individual,company,organization',
            'defendants.*.contact_info' => 'nullable|string|max:500',
            'documents' => 'nullable|array',
            'documents.*.title' => 'required_with:documents|string|max:255',
            'documents.*.description' => 'nullable|string',
            'documents.*.file' => 'required_with:documents|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ];

        // Additional rules for update
        if ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            $rules['case_number'] = [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('case_files', 'case_number')->ignore($caseFileId),
            ];
            
            // Make fields optional for update
            $optionalFields = [
                'title', 'description', 'status', 'filing_date', 'hearing_date',
                'closing_date', 'case_type', 'priority', 'lawyer_id', 'court_id'
            ];
            
            foreach ($optionalFields as $field) {
                if (isset($rules[$field])) {
                    $rules[$field] = 'sometimes|' . $rules[$field];
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'case_number.unique' => 'This case number is already in use.',
            'hearing_date.after_or_equal' => 'The hearing date must be on or after the filing date.',
            'closing_date.after_or_equal' => 'The closing date must be on or after the filing date.',
            'lawyer_id.exists' => 'The selected lawyer is invalid.',
            'court_id.exists' => 'The selected court is invalid.',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'case_number' => 'case number',
            'filing_date' => 'filing date',
            'hearing_date' => 'hearing date',
            'closing_date' => 'closing date',
            'case_type' => 'case type',
            'lawyer_id' => 'lawyer',
            'court_id' => 'court',
            'plaintiffs.*.name' => 'plaintiff name',
            'defendants.*.name' => 'defendant name',
            'documents.*.file' => 'document file',
        ];
    }
}






