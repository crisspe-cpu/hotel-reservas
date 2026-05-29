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
            Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reserva');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id');
            $table->date('fecha_entrada');
            $table->date('fecha_salida');
            $table->integer('num_huespedes')->default(1);
            $table->decimal('precio_total', 10, 2)->default(0.00);
            $table->enum('estado', ['pendiente','confirmada','cancelada','finalizada'])->default('pendiente');
            $table->datetime('fecha_registro')->nullable();
            $table->timestamps();

            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
