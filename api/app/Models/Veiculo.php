<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Veiculo extends BaseModel
{
    /** @use HasFactory<\Database\Factories\VehiculoFactory> */
    use HasFactory;
    protected $fillable = [
        'marca',
        'modelo',
        'placa',
        'renavam',
        'ano',
        'cor',
        'tipo_combustivel'
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }
}
