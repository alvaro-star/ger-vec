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
            $table->decimal('quilometragem', 20, 2);
            $table->string('tipo', 50)->nullable();
            $table->text('descricao')->nullable();
            $table->text('observacoes')->nullable();
            $table->decimal('valor_total', 20, 2)->nullable();
            $table->decimal('garantia_meses', 10, 2)->nullable();

            $table->unsignedBigInteger('veiculo_id');
            $table->foreign('veiculo_id')->references('id')->on('veiculos');
            $table->index('veiculo_id', 'revisoes_idx_veiculo_id');

            $table->unsignedBigInteger('pessoa_id')->nullable();
            $table->foreign('pessoa_id')->references('id')->on('pessoas');
            $table->index(['pessoa_id', 'data'], 'revisoes_idx_pessoa_id');

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
