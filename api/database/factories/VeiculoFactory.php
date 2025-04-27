<?php

namespace Database\Factories;

use App\Models\Marca;
use App\Utils\PreValues;

use App\Models\Pessoa;
use App\Utils\Enums;
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
        $pessoa = Pessoa::inRandomOrder()->first();
        $pessoa->increment('n_veiculos');

        return [
            'marca_id' =>  Marca::inRandomOrder()->first()->id,
            'modelo' => strtoupper($this->faker->word),
            'placa' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{1}[A-Z]{1}[0-9]{2}'),
            'renavam' => $this->faker->unique()->numerify('###########'),
            'ano' => $this->faker->year,
            'cor' => $this->faker->randomElement(Enums::getRandomColors()),
            'tipo_combustivel' => $this->faker->randomElement(Enums::getFuelTypes()),
            'pessoa_id' => $pessoa->id,
        ];
    }
}
