<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Habitacion;
use App\Models\Cliente;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   /**
     * Finalizar reservas vencidas automáticamente sin romper la ocupación actual
     */
    private function finalizarReservasVencidas()
    {
        // 1. Obtener las reservas que ya pasaron su fecha de salida
        $reservasVencidas = Reserva::whereIn('estado', ['pendiente', 'confirmada'])
            ->whereDate('fecha_salida', '<', today())
            ->get();

        foreach ($reservasVencidas as $reserva) {
            
            // Finalizar la reserva expirada
            $reserva->update(['estado' => 'finalizada']);

            // Finalizar sus detalles correspondientes
            $reserva->detalles()->update(['estado' => 'finalizada']);

            // Liberar las habitaciones SOLO si no están ocupadas por otra reserva HOY
            foreach ($reserva->habitaciones as $habitacion) {
                
                // Verificamos si la habitación tiene otra reserva confirmada activa en este instante
                $estaOcupadaAhora = DB::table('detalle_reservas')
                    ->join('reservas', 'detalle_reservas.id_reserva', '=', 'reservas.id_reserva')
                    ->where('detalle_reservas.id_habitacion', $habitacion->id_habitacion)
                    ->where('reservas.estado', 'confirmada')
                    ->whereDate('reservas.fecha_entrada', '<=', today())
                    ->whereDate('reservas.fecha_salida', '>=', today())
                    ->exists();

                // Si no hay nadie más hospedado, la regresamos a disponible
                if (!$estaOcupadaAhora) {
                    $habitacion->update(['estado' => 'disponible']);
                }
            }
        }
    }

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Actualizar reservas vencidas
        |--------------------------------------------------------------------------
        */
        $this->finalizarReservasVencidas();

        /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        */
        $desde = request('desde');
        $hasta = request('hasta');
        $estadoFiltro = request('estado');

        /*
        |--------------------------------------------------------------------------
        | CONSULTA RESERVAS
        |--------------------------------------------------------------------------
        */
        $reservasQuery = Reserva::query();

        if($desde && $hasta){

            $reservasQuery->whereBetween(
                'fecha_registro',
                [
                    $desde.' 00:00:00',
                    $hasta.' 23:59:59'
                ]
            );
        }

        if($estadoFiltro){
            $reservasQuery->where(
                'estado',
                $estadoFiltro
            );

        }
        /*
        |--------------------------------------------------------------------------
        | TARJETAS
        |--------------------------------------------------------------------------
        */
        $totalReservasHoy = (clone $reservasQuery)
            ->count();

        $totalHabitaciones = Habitacion::count();
        $habitacionesOcupadas = Habitacion::where(
            'estado',
            'no disponible'
        )->count();

        $habitacionesDisponibles = Habitacion::where(
            'estado',
            'disponible'
        )->count();
        $totalClientes = Cliente::count();

            /*
        |--------------------------------------------------------------------------
        | INGRESOS (TOTAL COBRADO)
        |--------------------------------------------------------------------------
        */
        $pagosQuery = Pago::query();

        if ($desde && $hasta) {

            $pagosQuery->whereBetween(
                'fecha_pago',
                [
                    $desde . ' 00:00:00',
                    $hasta . ' 23:59:59'
                ]
            );

        } elseif ($desde) {

            $pagosQuery->where(
                'fecha_pago',
                '>=',
                $desde . ' 00:00:00'
            );

        } elseif ($hasta) {

            $pagosQuery->where(
                'fecha_pago',
                '<=',
                $hasta . ' 23:59:59'
            );

        } else {

            $pagosQuery
                ->whereMonth(
                    'fecha_pago',
                    now()->month
                )
                ->whereYear(
                    'fecha_pago',
                    now()->year
                );

        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL COBRADO
        |--------------------------------------------------------------------------
        */
        $ingresosMes = (clone $pagosQuery)->sum('monto');

        /*
        |--------------------------------------------------------------------------
        | PAGOS POR MÉTODO (para futuras gráficas)
        |--------------------------------------------------------------------------
        */
        $pagosPorMetodo = (clone $pagosQuery)
            ->select(
                'metodo_pago',
                DB::raw('SUM(monto) as total')
            )
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        /*
        |--------------------------------------------------------------------------
        | HABITACIONES MÁS USADAS (USANDO DETALLE_RESERVAS)
        |--------------------------------------------------------------------------
        */

        $habitacionesMasUsadasQuery = Habitacion::select(
                'habitaciones.id_habitacion',
                'habitaciones.numero',
                'habitaciones.estado',
                DB::raw('COUNT(detalle_reservas.id_detalle) as total_usos')
            )
            ->join('detalle_reservas', 'habitaciones.id_habitacion', '=', 'detalle_reservas.id_habitacion')
            ->join('reservas', 'detalle_reservas.id_reserva', '=', 'reservas.id_reserva');


        // FILTRO DE FECHAS (usa fecha de registro de la reserva)
        if ($desde && $hasta) {
            $habitacionesMasUsadasQuery->whereBetween('reservas.fecha_registro', [
                $desde . ' 00:00:00',
                $hasta . ' 23:59:59'
            ]);
        }

        // FILTRO DE ESTADO
        if ($estadoFiltro) {
            $habitacionesMasUsadasQuery->where('reservas.estado', $estadoFiltro);
        }

        $habitacionesMasUsadas = $habitacionesMasUsadasQuery
            ->groupBy(
                'habitaciones.id_habitacion',
                'habitaciones.numero',
                'habitaciones.estado'
            )
            ->orderByDesc('total_usos')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | OCUPACION POR TIPO
        |--------------------------------------------------------------------------
        */
        $ocupacionPorTipo = Habitacion::select(

                'tipo_habitaciones.nombre',

                DB::raw('count(*) as total')

            )

            ->join(
                'tipo_habitaciones',
                'habitaciones.id_tipo_habitacion',
                '=',
                'tipo_habitaciones.id_tipo'
            )
            ->where(
                'habitaciones.estado',
                'no disponible'
            )

            ->groupBy(
                'tipo_habitaciones.nombre'
            )

            ->get();

        return view(
            'admin.dashboard',
            compact(

                'totalReservasHoy',

                'totalHabitaciones',

                'habitacionesOcupadas',

                'habitacionesDisponibles',

                'totalClientes',

                'ingresosMes',

                'habitacionesMasUsadas',

                'ocupacionPorTipo',

                'desde',

                'hasta',

                'estadoFiltro'

            )
        );

    }
}