<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UploadLimit implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, int $value, Closure $fail): void
    {
        //check if uploadlimit has been exceeded
        if (request()->user()->getUploadLimit() < $value + request()->user()->getTotalUploadSize()) {
            $fail('Attachments total size exceeds upload limit.');
        }
    }
}
