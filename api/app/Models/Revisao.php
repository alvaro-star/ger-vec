<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revisao extends BaseModel
{
    /** @use HasFactory<\Database\Factories\RevisaoFactory> */
    use HasFactory;
    protected $fillable = [
        'data',
        'quilometragem',
        'tipo',
        'descricao',
        'observacoes',
        'valor_total',
        'garantia_meses'
    ];
}
