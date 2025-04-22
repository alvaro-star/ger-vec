<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRevisaoRequest extends FormRequest
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
            'quilometragem' => 'required|integer|min:0',
            'tipo' => 'required|string|max:50',
            'descricao' => 'required|string',
            'observacoes' => 'required|string',
            'valor_total' => 'required|numeric|min:0',
            'garantia_meses' => 'required|numeric|min:0',
        ];
    }
}
