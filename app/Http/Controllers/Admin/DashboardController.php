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
    public function index()
    {
        // Totales generales
        $totalReservasHoy      = Reserva::whereDate('created_at', today())->count();
        $totalHabitaciones     = Habitacion::count();
        $habitacionesOcupadas  = Habitacion::where('estado', 'no disponible')->count();
        $habitacionesDisponibles = Habitacion::where('estado', 'disponible')->count();
        $totalClientes         = Cliente::count();

        // Ingresos del mes actual
        $ingresosMes = Pago::whereMonth('fecha_pago', now()->month)
                           ->whereYear('fecha_pago', now()->year)
                           ->sum('monto');

        // Reservas activas
        $reservasActivas = Reserva::with(['cliente', 'habitaciones'])
                                  ->whereIn('estado', ['pendiente', 'confirmada'])
                                  ->orderBy('fecha_entrada')
                                  ->take(10)
                                  ->get();

        // Ocupación por tipo de habitación
        $ocupacionPorTipo = Habitacion::select('tipo_habitaciones.nombre', DB::raw('count(*) as total'))
                                      ->join('tipo_habitaciones', 'habitaciones.id_tipo_habitacion', '=', 'tipo_habitaciones.id_tipo')
                                      ->where('habitaciones.estado', 'no disponible')
                                      ->groupBy('tipo_habitaciones.nombre')
                                      ->get();

        return view('admin.dashboard', compact(
            'totalReservasHoy',
            'totalHabitaciones',
            'habitacionesOcupadas',
            'habitacionesDisponibles',
            'totalClientes',
            'ingresosMes',
            'reservasActivas',
            'ocupacionPorTipo'
        ));
    }
}