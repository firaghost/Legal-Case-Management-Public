<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Using route/model binding for authorization in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Common rules for all message types
        $rules = [
            'body' => [
                'required_without:attachment',
                'nullable',
                'string',
                'max:5000',
            ],
            'type' => [
                'required',
                'string',
                Rule::in(['text', 'audio', 'image', 'document', 'video']),
            ],
            'reply_to' => [
                'nullable',
                'exists:messages,id',
            ],
        ];

        // Add file validation rules if attachments are present
        if ($this->hasFile('attachments.0')) {
            $maxSize = config('filesystems.upload_max_size', 10240); // 10MB default
            
            $rules['attachments.*'] = [
                'required',
                'file',
                'max:' . $maxSize,
            ];

            // Add mime type validation based on message type
            $mimeTypes = [
                'audio' => 'mimetypes:audio/*,video/*,application/octet-stream',
                'image' => 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml',
                'video' => 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/webm',
                'document' => 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/plain',
            ];

            if (isset($mimeTypes[$this->input('type')])) {
                $rules['attachments.*'][] = $mimeTypes[$this->input('type')];
            }
        }

        return $rules;
    }

    
    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'body.required_without' => 'Message text is required when no attachment is provided',
            'attachment.required' => 'Please select a file to upload',
            'attachment.mimetypes' => 'The selected file is not a valid file for this message type',
            'attachment.max' => 'The file size must not exceed ' . (config('filesystems.upload_max_size', 10240) / 1024) . 'MB',
        ];
    }
}






