<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'applicationId' => [
                'required',
                'integer',
                'exists:applications,id'
            ],
            'questionId' => [
                'required',
                'integer',
                'exists:questions,id'
            ],
            'files.*' => [
                'required',
                File::types(['pdf'])
                    ->max('5mb')
            ]
        ];
    }
}
