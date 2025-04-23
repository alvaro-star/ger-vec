<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('telefone');
            $table->string('cpf');
            $table->boolean('is_masculino');
            $table->integer('idade');
            $table->unsignedBigInteger('n_veiculos')->default(0);
            $table->unsignedBigInteger('n_revisoes')->default(0);
            $table->timestamps();

            $table->index('nome', 'pessoas_idx_nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
