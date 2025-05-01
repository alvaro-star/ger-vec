<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pessoa extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PessoaFactory> */
    use HasFactory;
    protected $fillable = [
        'nome',
        'celular',
        'cpf',
        'nascimento'
    ];
}
