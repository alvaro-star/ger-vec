<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\Revisao;
use App\Models\User;
use App\Models\Veiculo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::truncate();
        Veiculo::truncate();
        Revisao::truncate();
        Pessoa::truncate();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->createVeiculoRevisoes();
        $this->createVeiculoRevisoesComIntervalo(2);
        $this->call([
            PessoaSeeder::class,
            VeiculoSeeder::class,
            RevisaoSeeder::class,
        ]);
    }

    public function createVeiculoRevisoes(): void
    {
        $pessoa = Pessoa::factory()->create();

        $veiculo = Veiculo::factory()->create([
            'pessoa_id' => $pessoa->id,
        ]);

        $dataInicial = now();

        for ($i = 0; $i < 10; $i++) {
            Revisao::factory()->create([
                'veiculo_id' => $veiculo->id,
                'pessoa_id' => $veiculo->pessoa_id,
                'data' => $dataInicial->addDays(1),
            ]);
        }
    }

    public function createVeiculoRevisoesComIntervalo(int $intervaloEmDias): void
    {
        $pessoa = Pessoa::factory()->create();

        $veiculo = Veiculo::factory()->create([
            'pessoa_id' => $pessoa->id,
        ]);

        $dataInicial = now();

        for ($i = 0; $i < 10; $i++) {
            Revisao::factory()->create([
                'veiculo_id' => $veiculo->id,
                'pessoa_id' => $veiculo->pessoa_id,
                'data' => $dataInicial->addDays($i * $intervaloEmDias),
            ]);
        }
    }
}
