<?php

namespace Database\Factories;

use App\Models\CacheRevisao;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pessoa>
 */
class PessoaFactory extends Factory
{
    private function generateValidCpf(): string
    {
        do {
            $cpf = $this->createCpf();
        } while (Pessoa::where('cpf', $cpf)->exists());

        return $cpf;
    }

    private function createCpf(): string
    {
        $n = [];
        for ($i = 0; $i < 9; $i++) {
            $n[$i] = random_int(0, 9);
        }

        $n[9] = $this->calculateDigit($n, 10);
        $n[10] = $this->calculateDigit($n, 11);

        return implode('', $n);
    }

    private function calculateDigit(array $numbers, int $weight): int
    {
        $sum = 0;
        for ($i = 0; $i < $weight - 1; $i++) {
            $sum += $numbers[$i] * ($weight - $i);
        }

        $rest = $sum % 11;
        return ($rest < 2) ? 0 : 11 - $rest;
    }

    public function definition(): array
    {
        return [
            'nome' => $this->faker->name,
            'celular' => $this->faker->numerify('###########'),
            'cpf' => $this->generateValidCpf(),
            'is_masculino' => $this->faker->boolean,
            'nascimento' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
        ];
    }
}
