<?php

namespace App\Http\Requests;

use App\Utils\Enums;
use App\Utils\PreValues;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMarcaRequest extends FormRequest
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
    public function messages()
    {
        return [
            'nome.required' => 'O campo Nome é obrigatório.',
            'nome.string' => 'O campo Nome deve ser uma sequência de caracteres.',
            'nome.max' => 'O Nome deve ter no máximo 20 caracteres.',
            'nome.regex' => 'O Nome deve conter apenas letras maiúsculas e espaços.',

            'ano_fundacao.required' => 'O campo Ano de Fundação é obrigatório.',
            'ano_fundacao.string' => 'O campo Ano de Fundação deve ser uma sequência de números.',
            'ano_fundacao.regex' => 'O Ano de Fundação deve conter exatamente 4 dígitos numéricos.',

            'pais.required' => 'O campo País é obrigatório.',
            'pais.string' => 'O campo País deve ser uma sequência de caracteres.',
            'pais.in' => 'O País selecionado não é válido.',
        ];
    }
}
