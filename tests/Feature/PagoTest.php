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

class PagoTest extends TestCase
{
    use RefreshDatabase;

    private function crearReserva()
    {
        $user = User::factory()->create([
            'role' => 'recepcionista'
        ]);

        $cliente = Cliente::create([
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'tipo_documento' => 'dni',
            'documento' => '12345678',
            'telefono' => '999999999'
        ]);

        $tipo = TipoHabitacion::create([
            'nombre' => 'Simple',
            'capacidad' => 2,
            'precio_base' => 100
        ]);

        $habitacion = Habitacion::create([
            'numero' => '101',
            'piso' => 1,
            'estado' => 'disponible',
            'id_tipo_habitacion' => $tipo->id_tipo
        ]);

        $reserva = Reserva::create([
            'id_cliente' => $cliente->id_cliente,
            'id' => $user->id,
            'fecha_entrada' => now(),
            'fecha_salida' => now()->addDay(),
            'num_huespedes' => 1,
            'precio_total' => 100,
            'estado' => 'pendiente',
            'fecha_registro' => now(),
        ]);

        DetalleReserva::create([
            'id_reserva' => $reserva->id_reserva,
            'id_habitacion' => $habitacion->id_habitacion,
            'precio_aplicado' => 100,
            'estado' => 'activa'
        ]);

        return [$user, $reserva];
    }

    /** @test */
    public function se_puede_registrar_un_pago()
    {
        [$user, $reserva] = $this->crearReserva();

        $response = $this->actingAs($user)->post('/recepcionista/pagos', [
            'id_reserva' => $reserva->id_reserva,
            'monto' => 50,
            'metodo_pago' => 'yape'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('pagos', [
            'id_reserva' => $reserva->id_reserva,
            'monto' => 50
        ]);
    }

    /** @test */
    public function no_se_puede_pagar_mas_del_saldo()
    {
        [$user, $reserva] = $this->crearReserva();

        Pago::create([
            'id_reserva' => $reserva->id_reserva,
            'monto' => 80,
            'fecha_pago' => now(),
            'metodo_pago' => 'efectivo'
        ]);

        $response = $this->actingAs($user)->post('/recepcionista/pagos', [
            'id_reserva' => $reserva->id_reserva,
            'monto' => 50,
            'metodo_pago' => 'yape'
        ]);

        $response->assertSessionHasErrors('monto');
    }

    /** @test */
    public function reserva_se_confirma_cuando_se_paga_completo()
    {
        [$user, $reserva] = $this->crearReserva();

        $this->actingAs($user)->post('/recepcionista/pagos', [
            'id_reserva' => $reserva->id_reserva,
            'monto' => 100,
            'metodo_pago' => 'tarjeta'
        ]);

        $this->assertDatabaseHas('reservas', [
            'id_reserva' => $reserva->id_reserva,
            'estado' => 'confirmada'
        ]);
    }
}