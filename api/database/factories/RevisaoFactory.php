<?php

namespace Database\Factories;

use App\Models\Veiculo;
use App\Utils\Enums;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Revisao>
 */
class RevisaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $veiculo = Veiculo::inRandomOrder()->first();

        // Incrementa o número de revisões no veículo e na pessoa
        $veiculo->increment('n_revisoes');
        $veiculo->pessoa()->increment('n_revisoes');

        return [
            'data' => $this->faker->date,
            'quilometragem' => $this->faker->numberBetween(1000, 500000),
            'tipo' => $this->faker->randomElement(Enums::getRevisionTypes()),
            'descricao' => $this->faker->sentence,
            'observacoes' => $this->faker->paragraph,
            'valor_total' => $this->faker->randomFloat(2, 50, 1000),
            'garantia_meses' => $this->faker->numberBetween(1, 24),
            'veiculo_id' => $veiculo->id,
            'pessoa_id' => $veiculo->pessoa_id,
        ];
    }
}
