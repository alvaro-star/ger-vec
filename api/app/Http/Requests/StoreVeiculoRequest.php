<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVeiculoRequest extends FormRequest
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
            'modelo' => 'required|string|max:50',
            'placa' => 'required|string|unique:veiculos,placa|max:7',
            'renavam' => 'required|string|regex:/^\d{11}$/',
            'ano' => 'required|integer|min:1800|max:' . (date('Y') + 1),
            'cor' => 'required|string|max:50',
            'tipo_combustivel' => 'required|string|max:50',
            'pessoa_id' => 'required|exists:pessoas,id',
            'marca_id' => 'required|exists:marcas,id',
        ];
    }
    public function messages()
    {
        return [
            'modelo.required' => 'O campo Modelo é obrigatório.',
            'modelo.string' => 'O campo Modelo deve ser uma sequência de caracteres.',
            'modelo.max' => 'O Modelo deve ter no máximo 255 caracteres.',

            'placa.required' => 'O campo Placa é obrigatório.',
            'placa.string' => 'O campo Placa deve ser uma sequência de caracteres.',
            'placa.unique' => 'Já existe um veículo cadastrado com esta Placa.',
            'placa.max' => 'A Placa deve ter no máximo 7 caracteres.',

            'renavam.required' => 'O campo Renavam é obrigatório.',
            'renavam.string' => 'O campo Renavam deve ser uma sequência de caracteres.',
            'renavam.max' => 'O Renavam deve ter no máximo 11 caracteres.',

            'ano.required' => 'O campo Ano é obrigatório.',
            'ano.integer' => 'O campo Ano deve ser um número inteiro.',
            'ano.min' => 'O Ano deve ser no mínimo 1800.',
            'ano.max' => 'O Ano não pode ser maior que o próximo ano.',

            'cor.required' => 'O campo Cor é obrigatório.',
            'cor.string' => 'O campo Cor deve ser uma sequência de caracteres.',
            'cor.max' => 'A Cor deve ter no máximo 30 caracteres.',

            'tipo_combustivel.required' => 'O campo Tipo de Combustível é obrigatório.',
            'tipo_combustivel.string' => 'O campo Tipo de Combustível deve ser uma sequência de caracteres.',
            'tipo_combustivel.max' => 'O Tipo de Combustível deve ter no máximo 30 caracteres.',

            'pessoa_id.required' => 'O campo Pessoa (Proprietário) é obrigatório.',
            'pessoa_id.exists' => 'A Pessoa (Proprietário) informada não foi encontrada.',

            'marca_id.required' => 'O campo Marca é obrigatório.',
            'marca_id.exists' => 'A Marca informada não foi encontrada.',
        ];
    }
}
