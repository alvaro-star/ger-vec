<?php

namespace Database\Factories;

use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehiculo>
 */
class VeiculoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'marca' => $this->faker->company,
            'modelo' => $this->faker->word,
            'placa' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{4}'),
            'renavam' => $this->faker->numberBetween(10000000000, 99999999999),
            'ano' => $this->faker->year,
            'cor' => $this->faker->colorName,
            'tipo_combustivel' => $this->faker->randomElement(['Gasolina', 'Álcool', 'Diesel', 'Elétrico', 'Flex']),
            'pessoa_id' => Pessoa::inRandomOrder()->first()->id,
        ];
    }
}
