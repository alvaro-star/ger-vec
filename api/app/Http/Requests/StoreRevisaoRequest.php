<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRevisaoRequest extends FormRequest
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
            'data' => 'required|date',
            'quilometragem' => 'required|numeric|min:0',
            'tipo' => 'required|string|max:50',
            'valor_total' => 'required|numeric|min:0',
            'descricao' => 'string',
            'observacoes' => 'string',
            'garantia_meses' => 'required|numeric|min:0',
            'placa' => 'required|exists:veiculos,placa',
        ];
    }

    public function messages()
    {
        return [
            'data.required' => 'O campo Data é obrigatório.',
            'data.date' => 'O campo Data deve ser uma data válida.',

            'quilometragem.required' => 'O campo Quilometragem é obrigatório.',
            'quilometragem.numeric' => 'O campo Quilometragem deve ser um número.',
            'quilometragem.min' => 'A Quilometragem deve ser maior ou igual a zero.',

            'tipo.required' => 'O campo Tipo é obrigatório.',
            'tipo.string' => 'O campo Tipo deve ser uma sequência de caracteres.',
            'tipo.max' => 'O campo Tipo deve ter no máximo 50 caracteres.',

            'valor_total.required' => 'O campo Valor Total é obrigatório.',
            'valor_total.numeric' => 'O campo Valor Total deve ser um número.',
            'valor_total.min' => 'O Valor Total deve ser maior ou igual a zero.',

            'garantia_meses.required' => 'O campo Garantia (Meses) é obrigatório.',
            'garantia_meses.numeric' => 'O campo Garantia (Meses) deve ser um número.',
            'garantia_meses.min' => 'A Garantia (Meses) deve ser maior ou igual a zero.',

            'placa.required' => 'O campo Placa é obrigatório.',
            'placa.exists' => 'A Placa informada não foi encontrada entre os veículos cadastrados.',
        ];
    }
}
