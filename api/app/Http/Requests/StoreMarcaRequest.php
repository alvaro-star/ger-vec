<?php

namespace App\Http\Requests;

use App\Utils\Enums;
use App\Utils\PreValues;
use Illuminate\Foundation\Http\FormRequest;

class StoreMarcaRequest extends FormRequest
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
            'nome' => [
                'required',
                'string',
                'unique:marcas,nome',
                'max:20',
                'regex:/^[\p{Lu}\s]+$/u'
            ],
            'ano_fundacao' => [
                'required',
                'string',
                'regex:/^\d{4}$/'
            ],
            'pais' => 'required|string|in:' . implode(',', Enums::getCountries()),
        ];
    }
}
