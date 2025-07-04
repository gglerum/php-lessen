<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UploadLimit;

class StoreFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', [$this->application, $this->user()->organisation_id]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'total_file_size' => array_reduce($this->file('files'), fn(int $carry, $file) => $carry + $file->getSize(), 0),
        ]);
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
            'files' => 'required|array',
            'files.*' => 'required|mimes:pdf|min:1|max:5120',
            'total_file_size' => ['required', 'integer', 'min:1', new UploadLimit],
        ];
    }
}
