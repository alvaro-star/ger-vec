<?php

namespace Database\Seeders;

use App\Models\Marca;
use App\Models\Pessoa;
use App\Models\Revisao;
use App\Models\User;
use App\Models\Veiculo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Marca::truncate();
        User::truncate();
        Veiculo::truncate();
        Revisao::truncate();
        Pessoa::truncate();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);


        $this->call([
            MarcaSeeder::class,
            PessoaSeeder::class,
            VeiculoSeeder::class,
            RevisaoSeeder::class,
        ]);
    }
}
