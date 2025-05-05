<?php

namespace App\Rules;

use App\Utils\Validators;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CPF implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Validators::isValidCpf($value)) {
            $fail('O CPF informado não é válido');
        }
    }
}
