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
        Schema::create('informacion_general', function (Blueprint $table) {
            $table->id();

            // REPORTE PX DISTANCIA PARA REPORTE DE SOBRANTES
            $table->integer('px_sobrantes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informacion_general');
    }
};
