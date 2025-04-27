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
            'modelo' => 'required|string|max:255',
            'placa' => 'required|string|unique:veiculos,placa|max:7',
            'renavam' => 'required|string|max:11',
            'ano' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'cor' => 'required|string|max:30',
            'tipo_combustivel' => 'required|string|max:30',
            'pessoa_id' => 'required|exists:pessoas,id',
            'marca_id' => 'required|exists:marcas,id',
        ];
    }
}
