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
     * Finalizar reservas vencidas automáticamente
     */
    private function finalizarReservasVencidas()
    {
        $reservas = Reserva::whereIn('estado', ['pendiente', 'confirmada'])
            ->whereDate('fecha_salida', '<', today())
            ->get();

        foreach ($reservas as $reserva) {

            // Cambiar estado reserva
            $reserva->update([
                'estado' => 'finalizada'
            ]);

            // Liberar habitaciones
            foreach ($reserva->habitaciones as $habitacion) {

                $habitacion->update([
                    'estado' => 'disponible'
                ]);
            }

            // Actualizar detalles
            $reserva->detalles()->update([
                'estado' => 'finalizada'
            ]);
        }
    }

    public function index()
    {
    // Ejecutar cierre automático
    $this->finalizarReservasVencidas();

    /*
    |--------------------------------------------------------------------------
    | FILTROS DEL DASHBOARD
    |--------------------------------------------------------------------------
    */
    $desde = request('desde');
    $hasta = request('hasta');
    $estadoFiltro = request('estado');

    /*
    |--------------------------------------------------------------------------
    | CONSULTA BASE RESERVAS
    |--------------------------------------------------------------------------
    */
    $reservasQuery = Reserva::query();

    if($desde && $hasta){

        $reservasQuery->whereBetween('created_at',[
            $desde.' 00:00:00',
            $hasta.' 23:59:59'
        ]);

    }
    if($estadoFiltro){

        $reservasQuery->where(
            'estado',
            $estadoFiltro
        );

    }
    /*
    |--------------------------------------------------------------------------
    | TOTALES
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
    | INGRESOS
    |--------------------------------------------------------------------------
    */

    $pagosQuery = Pago::query();

    if($desde && $hasta){

        $pagosQuery->whereBetween('fecha_pago',[
            $desde,
            $hasta
        ]);

    }else{

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

    $ingresosMes = $pagosQuery->sum('monto');

    /*
    |--------------------------------------------------------------------------
    | RESERVAS TABLA
    |--------------------------------------------------------------------------
    */

    $reservasActivas = (clone $reservasQuery)
        ->with([
            'cliente',
            'habitaciones'
        ])
        ->orderBy('fecha_entrada')
        ->take(10)
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

    return view('admin.dashboard', compact(

        'totalReservasHoy',

        'totalHabitaciones',

        'habitacionesOcupadas',

        'habitacionesDisponibles',

        'totalClientes',

        'ingresosMes',

        'reservasActivas',

        'ocupacionPorTipo',

        // filtros para Blade
        'desde',
        'hasta',
        'estadoFiltro'

    ));
}
}