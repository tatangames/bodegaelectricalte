<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CIERRE DE PROYECTO
     */
    public function up(): void
    {
        Schema::create('transferencia', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_tipoproyecto')->unsigned();
            $table->date('fecha');
            $table->string('descripcion', 800)->nullable();
            $table->string('documento', 100)->nullable();

            $table->foreign('id_tipoproyecto')->references('id')->on('tipoproyecto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferencia');
    }
};
