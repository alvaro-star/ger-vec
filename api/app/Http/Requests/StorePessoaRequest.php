<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePessoaRequest extends FormRequest
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
            'nome' => 'required|string|max:60',
            'celular' => 'required|string|regex:/^\d{11}$/',
            'cpf' => 'required|string|regex:/^\d{11}$/|unique:pessoas,cpf',
            'sexo' => 'required|string|in:F,M',
            'idade' => 'required|integer|between:18,100',
        ];
    }

    public function messages()
{
    return [
        'nome.required' => 'O campo Nome é obrigatório.',
        'nome.string' => 'O campo Nome deve ser uma sequência de caracteres.',
        'nome.max' => 'O Nome deve ter no máximo 60 caracteres.',

        'celular.required' => 'O campo Celular é obrigatório.',
        'celular.string' => 'O campo Celular deve ser uma sequência de caracteres.',
        'celular.regex' => 'O Celular deve conter apenas números e ter entre 11 dígitos.',

        'cpf.required' => 'O campo CPF é obrigatório.',
        'cpf.string' => 'O campo CPF deve ser uma sequência de caracteres.',
        'cpf.regex' => 'O CPF deve conter exatamente 11 dígitos numéricos.',
        'cpf.unique' => 'Já existe uma pessoa cadastrada com este CPF.',

        'sexo.required' => 'O campo Sexo é obrigatório.',
        'sexo.string' => 'O campo Sexo deve ser uma sequência de caracteres.',
        'sexo.in' => 'O Sexo deve ser M (Masculino) ou F (Feminino).',

        'idade.required' => 'O campo Idade é obrigatório.',
        'idade.integer' => 'O campo Idade deve ser um número inteiro.',
        'idade.between' => 'A Idade deve estar entre 18 e 200 anos.',
    ];
}
}
