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
        Schema::create('revisoes', function (Blueprint $table) {
            $table->id();

            $table->date('data');
            $table->index('data', 'revisoes_idx_data');
            $table->integer('quilometragem');
            $table->string('tipo', 50)->nullable(); // preventiva, corretiva, etc.
            $table->text('descricao')->nullable();
            $table->text('observacoes')->nullable();
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->decimal('garantia_meses', 10, 2)->nullable(); // Em tempo

            $table->unsignedBigInteger('veiculo_id');
            $table->foreign('veiculo_id')->references('id')->on('veiculos');
            $table->index('veiculo_id', 'revisoes_idx_veiculo_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revisoes');
    }
};
