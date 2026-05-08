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
        Schema::create('detalle_reservas', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_reserva');
            $table->unsignedBigInteger('id_habitacion');
            $table->decimal('precio_aplicado', 10, 2);
            $table->enum('estado', ['activa', 'cancelada'])->default('activa');

            // Una habitación no puede aparecer dos veces en la misma reserva
            $table->unique(['id_reserva', 'id_habitacion']);

            $table->foreign('id_reserva')->references('id_reserva')->on('reservas')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('id_habitacion')->references('id_habitacion')->on('habitaciones')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_reservas');
    }
};
