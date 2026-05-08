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
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id('id_habitacion');
            $table->string('numero', 10)->unique();
            $table->integer('piso');
            $table->enum('estado', ['disponible', 'no disponible', 'mantenimiento'])->default('disponible');
            $table->unsignedBigInteger('id_tipo_habitacion');
            $table->timestamps();

            $table->foreign('id_tipo_habitacion')->references('id_tipo')->on('tipo_habitaciones')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};
