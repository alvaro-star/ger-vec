<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends BaseModel
{
    /** @use HasFactory<\Database\Factories\MarcaFactory> */
    use HasFactory;

    protected $fillable = [
        'nome',
        'pais',
        'ano_fundacao'
    ];
}
