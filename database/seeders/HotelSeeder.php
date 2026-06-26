<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cliente;
use App\Models\TipoHabitacion;
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\DetalleReserva;
use App\Models\Pago;
use App\Models\Boleta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. USUARIO RECEPCIONISTA
        $recepcionista = User::updateOrCreate(
            ['email' => 'recepcion@hotel.com'],
            [
                'name' => 'Recepcionista',
                'password' => Hash::make('123456'),
                'role' => 'recepcionista',
                'estado' => 'activo'
            ]
        );

        // 2. TIPOS HABITACION
        $tipos = [
            'Simple' => ['capacidad' => 1, 'precio_base' => 100, 'descripcion' => 'Habitación simple'],
            'Doble'  => ['capacidad' => 2, 'precio_base' => 180, 'descripcion' => 'Habitación doble'],
            'Suite'  => ['capacidad' => 2, 'precio_base' => 350, 'descripcion' => 'Habitación suite']
        ];

        foreach ($tipos as $nombre => $data) {
            TipoHabitacion::updateOrCreate(['nombre' => $nombre], $data);
        }

        // 3. HABITACIONES (30) - Todas nacen disponibles por defecto
        for ($i = 1; $i <= 30; $i++) {
            Habitacion::updateOrCreate(
                ['numero' => $i],
                [
                    'piso' => ceil($i / 10),
                    'estado' => 'disponible', // Estado inicial limpio
                    'id_tipo_habitacion' => fake()->numberBetween(1, 3)
                ]
            );
        }

        // 4. CLIENTES
        $paises = ['Perú', 'Colombia', 'Chile', 'Argentina', 'Bolivia', 'Ecuador', 'Venezuela', 'México'];
        $clientes = [];
        for ($i = 1; $i <= 200; $i++) {
            $clientes[] = Cliente::create([
                'nombre' => fake()->firstName(),
                'apellido' => fake()->lastName(),
                'tipo_documento' => fake()->randomElement(['dni', 'pasaporte']),
                'documento' => fake()->unique()->numerify('########'),
                'pais' => fake()->randomElement($paises),
                'telefono' => fake()->phoneNumber()
            ]);
        }

        // 5. GENERACIÓN DE RESERVAS HISTÓRICAS
        $reservasMes = [1 => 60, 2 => 70, 3 => 80, 4 => 90, 5 => 100, 6 => 100];

        foreach ($reservasMes as $mes => $cantidad) {
            for ($i = 0; $i < $cantidad; $i++) {
                
                $dia = fake()->numberBetween(1, 28);
                $entrada = Carbon::create(2026, $mes, $dia);
                $salida = $entrada->copy()->addDays(fake()->numberBetween(1, 5));
                
                $estado = fake()->randomElement(['confirmada', 'confirmada', 'finalizada']);
                $num_huespedes = fake()->numberBetween(1, 4);

                $habitacion = Habitacion::inRandomOrder()->first();
                $tipo = TipoHabitacion::find($habitacion->id_tipo_habitacion);

                $precio_base = match ($tipo->nombre) {
                    'Simple' => 100,
                    'Doble'  => 180,
                    'Suite'  => 350,
                };

                $noches = max(1, $entrada->diffInDays($salida));
                $precio_total = $precio_base * $noches;

                $reserva = Reserva::create([
                    'id_cliente'     => fake()->randomElement($clientes)->id_cliente,
                    'id'             => $recepcionista->id,
                    'fecha_entrada'  => $entrada,
                    'fecha_salida'   => $salida,
                    'num_huespedes'  => $num_huespedes,
                    'precio_total'   => $precio_total,
                    'estado'         => $estado,
                    'fecha_registro' => $entrada
                ]);

                DetalleReserva::create([
                    'id_reserva'      => $reserva->id_reserva,
                    'id_habitacion'   => $habitacion->id_habitacion,
                    'precio_aplicado' => $precio_base
                ]);

                if (in_array($estado, ['confirmada', 'finalizada'])) {
                    Pago::create([
                        'id_reserva'  => $reserva->id_reserva,
                        'monto'       => $precio_total,
                        'metodo_pago' => fake()->randomElement(['efectivo', 'tarjeta', 'yape', 'plin']),
                        'fecha_pago'  => $entrada
                    ]);

                    $totalRealPagado = $reserva->pagos()->sum('monto');

                    Boleta::create([
                        'id_reserva'      => $reserva->id_reserva,
                        'id'              => $recepcionista->id,
                        'fecha_emision'   => $entrada,
                        'total'           => $totalRealPagado,
                        'total_acumulado' => $totalRealPagado
                    ]);
                }
            }
        }

       // =========================================================================
        // 🔥 PASO 6 CORREGIDO: ACTUALIZACIÓN DE HABITACIONES HOY (26 JUNIO 2026)
        // =========================================================================
        
        $hoy = \Carbon\Carbon::today()->toDateString();

        // 1. Buscamos los IDs de habitaciones que tienen reservas confirmadas ACTIVAS HOY
        $habitacionesOcupadasIds = \App\Models\DetalleReserva::whereHas('reserva', function($query) use ($hoy) {
            $query->where('estado', 'confirmada')
                  ->whereDate('fecha_entrada', '<=', $hoy)
                  ->whereDate('fecha_salida', '>=', $hoy);
        })->pluck('id_habitacion')->unique()->toArray();

        // 2. Apagamos las que están ocupadas hoy
        if (!empty($habitacionesOcupadasIds)) {
            \App\Models\Habitacion::whereIn('id_habitacion', $habitacionesOcupadasIds)
                ->update(['estado' => 'no disponible']);
        }}
}