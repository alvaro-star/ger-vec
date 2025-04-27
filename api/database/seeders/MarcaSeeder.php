<?php

namespace Database\Seeders;

use App\Models\Marca;
use App\Utils\Enums;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $size = count(Enums::getPredefinedBrands());
        Marca::factory($size)->create();
    }
}
