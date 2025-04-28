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
        Schema::create('cache_revisaos', function (Blueprint $table) {
            $table->id();
            $table->date('last_revisao')->nullable(true);
            $table->decimal('avg_revisoes', 20, 2)->nullable(true);

            $table->unsignedBigInteger('pessoa_id')->nullable();
            $table->foreign('pessoa_id')->references('id')->on('pessoas');
            $table->index('pessoa_id', 'cache_revisaos_idx_pessoa_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache_revisaos');
    }
};
