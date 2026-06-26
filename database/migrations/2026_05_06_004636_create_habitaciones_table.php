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
            $table->bigIncrements('id_habitacion');

            $table->string('numero', 10);
            $table->integer('piso');

            $table->enum('estado', ['disponible', 'no disponible', 'mantenimiento'])
                ->default('disponible');

            $table->unsignedBigInteger('id_tipo_habitacion');

            // Campos de mantenimiento
            $table->text('motivo_mantenimiento')->nullable();
            $table->date('mantenimiento_desde')->nullable();
            $table->date('mantenimiento_hasta')->nullable();

            $table->timestamps();

            // FK (si la usas)
            // $table->foreign('id_tipo_habitacion')
            //       ->references('id_tipo_habitacion')
            //       ->on('tipos_habitacion');
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
