<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Reserva;
use App\Models\TipoHabitacion;
use App\Models\Habitacion;
use App\Models\DetalleReserva;
use App\Models\Pago;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoletaTest extends TestCase
{
    use RefreshDatabase;

    private function crearReservaConfirmada()
    {
        $user = User::factory()->create([
            'role' => 'recepcionista'
        ]);

        $cliente = Cliente::create([
            'nombre' => 'Carlos',
            'apellido' => 'Lopez',
            'tipo_documento' => 'dni',
            'documento' => '87654321',
            'telefono' => '999999999'
        ]);

        $tipo = TipoHabitacion::create([
            'nombre' => 'Suite',
            'capacidad' => 2,
            'precio_base' => 200
        ]);

        $habitacion = Habitacion::create([
            'numero' => '201',
            'piso' => 2,
            'estado' => 'disponible',
            'id_tipo_habitacion' => $tipo->id_tipo
        ]);

        $reserva = Reserva::create([
            'id_cliente' => $cliente->id_cliente,
            'id' => $user->id,
            'fecha_entrada' => now(),
            'fecha_salida' => now()->addDay(),
            'num_huespedes' => 1,
            'precio_total' => 200,
            'estado' => 'confirmada',
            'fecha_registro' => now(),
        ]);

        DetalleReserva::create([
            'id_reserva' => $reserva->id_reserva,
            'id_habitacion' => $habitacion->id_habitacion,
            'precio_aplicado' => 200,
            'estado' => 'activa'
        ]);

        Pago::create([
            'id_reserva' => $reserva->id_reserva,
            'monto' => 200,
            'fecha_pago' => now(),
            'metodo_pago' => 'efectivo'
        ]);

        return [$user, $reserva];
    }

    /** @test */
    public function se_puede_emitir_boleta()
    {
        [$user, $reserva] = $this->crearReservaConfirmada();

        $response = $this->actingAs($user)->post('/recepcionista/boletas', [
            'id_reserva' => $reserva->id_reserva
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('boletas', [
            'id_reserva' => $reserva->id_reserva,
            'total' => 200
        ]);
    }

    /** @test */
    public function no_se_puede_emitir_boleta_si_no_hay_nuevos_pagos()
    {
        [$user, $reserva] = $this->crearReservaConfirmada();

        $this->actingAs($user)->post('/recepcionista/boletas', [
            'id_reserva' => $reserva->id_reserva
        ]);

        $response = $this->actingAs($user)->post('/recepcionista/boletas', [
            'id_reserva' => $reserva->id_reserva
        ]);

        $response->assertSessionHasErrors();
    }
}