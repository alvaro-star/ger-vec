<?php

namespace Database\Factories;

use App\Models\CacheRevisao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pessoa>
 */
class PessoaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->name,
            'celular' => $this->faker->numerify('###########'),
            'cpf' => $this->faker->unique()->numerify('###########'),
            'is_masculino' => $this->faker->boolean,
            'nascimento' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
        ];
    }
}
