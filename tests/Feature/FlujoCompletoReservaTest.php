<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Reserva;
use App\Models\Habitacion;
use App\Models\TipoHabitacion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FlujoCompletoReservaTest extends TestCase
{
    use RefreshDatabase;

    public function test_flujo_completo_reserva_pago_boleta()
    {
        // ================================
        // USUARIO RECEPCIONISTA
        // ================================
        $user = User::factory()->create([
            'role' => 'recepcionista'
        ]);

        // ================================
        // CLIENTE
        // ================================
        $cliente = Cliente::create([
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'tipo_documento' => 'dni',
            'documento' => '12345678',
            'telefono' => '999999999',
        ]);

        // ================================
        // TIPO HABITACION
        // ================================
        $tipo = TipoHabitacion::create([
            'nombre' => 'Simple',
            'capacidad' => 2,
            'precio_base' => 100,
            'descripcion' => 'Habitación simple'
        ]);

        // ================================
        // HABITACION
        // ================================
        $habitacion = Habitacion::create([
            'numero' => '101',
            'piso' => 1,
            'estado' => 'disponible',
            'id_tipo_habitacion' => $tipo->id_tipo,
        ]);

        // ================================
        // LOGIN
        // ================================
        $this->actingAs($user);

        // ================================
        // CREAR RESERVA
        // ================================
        $responseReserva = $this->post(
            route('recepcionista.reservas.store'),
            [
                'id_cliente' => $cliente->id_cliente,
                'id_habitacion' => $habitacion->id_habitacion,
                'fecha_entrada' => now()->addDay()->format('Y-m-d'),
                'fecha_salida' => now()->addDays(2)->format('Y-m-d'),
                'num_huespedes' => 2,
            ]
        );

        $responseReserva->assertRedirect();

        // ================================
        // VERIFICAR RESERVA
        // ================================
        $this->assertDatabaseHas('reservas', [
            'id_cliente' => $cliente->id_cliente,
            'estado' => 'pendiente',
        ]);

        $reserva = Reserva::first();

        // ================================
        // REGISTRAR PAGO
        // ================================
        $responsePago = $this->post(
            route('recepcionista.pagos.store'),
            [
                'id_reserva' => $reserva->id_reserva,
                'monto' => 100,
                'metodo_pago' => 'efectivo',
            ]
        );

        $responsePago->assertRedirect();

        // ================================
        // VERIFICAR PAGO
        // ================================
        $this->assertDatabaseHas('pagos', [
            'id_reserva' => $reserva->id_reserva,
            'monto' => 100,
        ]);

        // ================================
        // RESERVA CONFIRMADA
        // ================================
        $this->assertDatabaseHas('reservas', [
            'id_reserva' => $reserva->id_reserva,
            'estado' => 'confirmada',
        ]);

        // ================================
        // EMITIR BOLETA
        // ================================
        $responseBoleta = $this->post(
            route('recepcionista.boletas.store'),
            [
                'id_reserva' => $reserva->id_reserva,
            ]
        );

        $responseBoleta->assertRedirect();

        // ================================
        // VERIFICAR BOLETA
        // ================================
        $this->assertDatabaseHas('boletas', [
            'id_reserva' => $reserva->id_reserva,
            'total' => 100,
        ]);

        // ================================
        // HABITACION SIGUE OCUPADA
        // ================================
        $this->assertDatabaseHas('habitaciones', [
            'id_habitacion' => $habitacion->id_habitacion,
            'estado' => 'no disponible',
        ]);
    }
}