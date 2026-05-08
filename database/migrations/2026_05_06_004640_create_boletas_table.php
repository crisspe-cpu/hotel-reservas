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
        Schema::create('boletas', function (Blueprint $table) {
            $table->id('id_boleta');
            $table->unsignedBigInteger('id_reserva')->unique(); // 1 boleta por reserva
            $table->unsignedBigInteger('id_user');
            $table->datetime('fecha_emision')->useCurrent();
            $table->decimal('total', 10, 2);

            $table->foreign('id_reserva')->references('id_reserva')->on('reservas')->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_user')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletas');
    }
};
