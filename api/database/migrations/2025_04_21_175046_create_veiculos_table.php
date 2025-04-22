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
            $table->string('marca');
            $table->index('marca', 'veiculos_idx_marca');
            $table->string('modelo');

            $table->string('placa')->unique();
            $table->string('renavam')->nullable();
            $table->integer('ano');

            $table->string('cor')->nullable();
            $table->string('tipo_combustivel')->nullable();

            $table->unsignedBigInteger('pessoa_id')->nullable();
            $table->foreign('pessoa_id')->references('id')->on('pessoas');
            $table->index('pessoa_id', 'veiculos_idx_pessoa_id');

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
