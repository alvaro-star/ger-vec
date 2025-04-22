<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\Revisao;
use App\Models\User;
use App\Models\Veiculo;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::truncate();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Revisao::truncate();
        Veiculo::truncate();
        Pessoa::truncate();

        //Pessoa::factory(15)->create();
        //Veiculo::factory(225)->create();
        //Revisao::factory(2250)->create();

        $this->call([
            PessoaSeeder::class,
            VeiculoSeeder::class,
            RevisaoSeeder::class
        ]);
    }
}
