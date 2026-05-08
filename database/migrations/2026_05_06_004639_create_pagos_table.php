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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->unsignedBigInteger('id_reserva');
            $table->decimal('monto', 10, 2);
            $table->datetime('fecha_pago')->useCurrent();
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'yape', 'plin']);

            $table->foreign('id_reserva')->references('id_reserva')->on('reservas')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
