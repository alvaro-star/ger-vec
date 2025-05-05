<?php

namespace Database\Factories;

use App\Models\Marca;
use App\Utils\PreValues;

use App\Models\Pessoa;
use App\Models\Veiculo;
use App\Utils\Enums;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehiculo>
 */
class VeiculoFactory extends Factory
{
    private function generateValidRenavam(): string
    {
        do {
            $renavam = $this->createRenavam();
        } while (Veiculo::where('renavam', $renavam)->exists());

        return $renavam;
    }

    private function createRenavam(): string
    {
        $base = str_pad((string)random_int(100000000, 999999999), 11, '0', STR_PAD_LEFT);
        $base = substr($base, 0, 10);

        $soma = 0;
        $multiplicadores = [3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < 10; $i++) {
            $soma += $base[$i] * $multiplicadores[$i];
        }

        $resto = $soma % 11;
        $dv = ($resto < 2) ? 0 : 11 - $resto;

        return $base . $dv;
    }

    public function definition(): array
    {
        $pessoa = Pessoa::inRandomOrder()->first();
        $pessoa->increment('n_veiculos');

        return [
            'marca_id' =>  Marca::inRandomOrder()->first()->id,
            'modelo' => strtoupper($this->faker->word),
            'placa' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{1}[A-Z]{1}[0-9]{2}'),
            'renavam' => $this->generateValidRenavam(),
            'ano' => $this->faker->year,
            'cor' => $this->faker->randomElement(Enums::getRandomColors()),
            'tipo_combustivel' => $this->faker->randomElement(Enums::getFuelTypes()),
            'pessoa_id' => $pessoa->id,
        ];
    }
}
