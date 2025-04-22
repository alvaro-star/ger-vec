<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePessoaRequest extends FormRequest
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
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:15',
            'cpf' => 'required|string|size:11|regex:/^\d{11}$/',
            'sexo' => 'required|string|in:F,M',
            'idade' => 'required|integer|min:0|max:150',
        ];
    }
}
