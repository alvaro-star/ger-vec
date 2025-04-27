<?php

namespace Database\Factories;

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
            'idade' => $this->faker->numberBetween(18, 70)
        ];
    }
}
