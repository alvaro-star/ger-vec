<?php

namespace Database\Factories;

use App\Utils\Enums;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Marca>
 */
class MarcaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => strtoupper($this->faker->unique()->randomElement(Enums::getPredefinedBrands())),
            'pais' => $this->faker->randomElement(Enums::getCountries()),
            'ano_fundacao' => $this->faker->numberBetween(2020, 2024),
        ];
    }
}
