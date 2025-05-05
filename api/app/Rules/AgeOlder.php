<?php

namespace App\Rules;

use App\Utils\TransformData;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AgeOlder implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $nascimento = TransformData::stringToDateTime($value);
        if ($nascimento == null) {
            $fail('O :atribute informado não é válido');
        }
        $idade = TransformData::nascimentoStringToIdade($value);
        if ($idade < 18)
            $fail('A idade deve ser maior que 18 anos');
    }
}
