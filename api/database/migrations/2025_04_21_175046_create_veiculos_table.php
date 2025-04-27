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
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();


            $table->string('modelo', 50);

            $table->string('placa', 7)->unique();
            $table->string('renavam', 11)->unique();
            $table->string('ano', 4);

            $table->unsignedBigInteger('n_revisoes')->default(0);

            $table->string('cor', 50)->nullable();
            $table->string('tipo_combustivel', 50)->nullable();

            $table->unsignedBigInteger('pessoa_id')->nullable();
            $table->foreign('pessoa_id')->references('id')->on('pessoas');
            $table->index('pessoa_id', 'veiculos_idx_pessoa_id');

            $table->unsignedBigInteger('marca_id');
            $table->foreign('marca_id')->references('id')->on('marcas');
            $table->index('marca_id', 'veiculos_idx_marca_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
