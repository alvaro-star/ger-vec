<?php

namespace Database\Seeders;

use App\Models\CacheRevisao;
use App\Models\Pessoa;
use Illuminate\Database\Seeder;

class PessoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pessoas = Pessoa::factory(96)->create();
        foreach ($pessoas as $pessoa) {
            $cache = new CacheRevisao();
            $cache->pessoa_id = $pessoa->id;
            $cache->save();
        }
    }
}
