<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Habitacion;
use App\Models\TipoHabitacion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservaTest extends TestCase
{
    use RefreshDatabase;

    public function test_recepcionista_puede_crear_reserva()
    {
        $user = User::factory()->create([
            'role' => 'recepcionista'
        ]);

        $cliente = Cliente::factory()->create();

        $tipo = TipoHabitacion::factory()->create([
            'precio_base' => 100
        ]);

        $habitacion = Habitacion::factory()->create([
            'estado' => 'disponible',
            'id_tipo_habitacion' => $tipo->id_tipo
        ]);

        $response = $this->actingAs($user)->post('/recepcionista/reservas', [
            'id_cliente' => $cliente->id_cliente,
            'id_habitacion' => $habitacion->id_habitacion,
            'fecha_entrada' => now()->addDay()->format('Y-m-d'),
            'fecha_salida' => now()->addDays(3)->format('Y-m-d'),
            'num_huespedes' => 2,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reservas', [
            'id_cliente' => $cliente->id_cliente,
            'estado' => 'pendiente'
        ]);

        $this->assertDatabaseHas('habitaciones', [
            'id_habitacion' => $habitacion->id_habitacion,
            'estado' => 'no disponible'
        ]);
    }

    public function test_no_se_puede_reservar_habitacion_ocupada()
    {
        $user = User::factory()->create([
            'role' => 'recepcionista'
        ]);

        $cliente1 = Cliente::factory()->create();
        $cliente2 = Cliente::factory()->create();

        $tipo = TipoHabitacion::factory()->create([
            'precio_base' => 100
        ]);

        $habitacion = Habitacion::factory()->create([
            'estado' => 'disponible',
            'id_tipo_habitacion' => $tipo->id_tipo
        ]);

        // Primera reserva
        $this->actingAs($user)->post('/recepcionista/reservas', [
            'id_cliente' => $cliente1->id_cliente,
            'id_habitacion' => $habitacion->id_habitacion,
            'fecha_entrada' => now()->addDay()->format('Y-m-d'),
            'fecha_salida' => now()->addDays(3)->format('Y-m-d'),
            'num_huespedes' => 2,
        ]);

        // Segunda reserva MISMAS FECHAS
        $response = $this->actingAs($user)->post('/recepcionista/reservas', [
            'id_cliente' => $cliente2->id_cliente,
            'id_habitacion' => $habitacion->id_habitacion,
            'fecha_entrada' => now()->addDay()->format('Y-m-d'),
            'fecha_salida' => now()->addDays(3)->format('Y-m-d'),
            'num_huespedes' => 2,
        ]);

        $response->assertSessionHasErrors('id_habitacion');

        $this->assertDatabaseCount('reservas', 1);
    }

    public function test_cancelar_reserva_libera_habitacion()
    {
        $user = User::factory()->create([
            'role' => 'recepcionista'
        ]);

        $cliente = Cliente::factory()->create();

        $tipo = TipoHabitacion::factory()->create([
            'precio_base' => 100
        ]);

        $habitacion = Habitacion::factory()->create([
            'estado' => 'disponible',
            'id_tipo_habitacion' => $tipo->id_tipo
        ]);

        // Crear reserva
        $this->actingAs($user)->post('/recepcionista/reservas', [
            'id_cliente' => $cliente->id_cliente,
            'id_habitacion' => $habitacion->id_habitacion,
            'fecha_entrada' => now()->addDay()->format('Y-m-d'),
            'fecha_salida' => now()->addDays(3)->format('Y-m-d'),
            'num_huespedes' => 2,
        ]);

        $reserva = \App\Models\Reserva::first();

        // Cancelar reserva
        $response = $this->actingAs($user)->put(
            "/recepcionista/reservas/{$reserva->id_reserva}",
            [
                'fecha_entrada' => $reserva->fecha_entrada->format('Y-m-d'),
                'fecha_salida' => $reserva->fecha_salida->format('Y-m-d'),
                'num_huespedes' => 2,
                'estado' => 'cancelada',
            ]
        );

        $response->assertRedirect();

        // Reserva cancelada
        $this->assertDatabaseHas('reservas', [
            'id_reserva' => $reserva->id_reserva,
            'estado' => 'cancelada'
        ]);

        // Habitación liberada
        $this->assertDatabaseHas('habitaciones', [
            'id_habitacion' => $habitacion->id_habitacion,
            'estado' => 'disponible'
        ]);

        // Detalle cancelado
        $this->assertDatabaseHas('detalle_reservas', [
            'id_reserva' => $reserva->id_reserva,
            'estado' => 'cancelada'
        ]);
    }
}