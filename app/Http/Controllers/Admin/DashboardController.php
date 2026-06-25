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

        $reservas = Reserva::whereIn('estado', [
                'pendiente',
                'confirmada'
            ])
            ->whereDate('fecha_salida', '<', today())
            ->get();


        foreach ($reservas as $reserva) {


            // finalizar reserva

            $reserva->update([
                'estado'=>'finalizada'
            ]);



            // liberar habitaciones

            foreach($reserva->habitaciones as $habitacion){

                $habitacion->update([
                    'estado'=>'disponible'
                ]);

            }



            // finalizar detalle

            $reserva->detalles()->update([
                'estado'=>'finalizada'
            ]);

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
        | INGRESOS DEL MES
        |--------------------------------------------------------------------------
        */


        $pagosQuery = Pago::query();



        if($desde && $hasta){


            $pagosQuery->whereBetween(
                'fecha_pago',
                [
                    $desde,
                    $hasta
                ]
            );


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
        | RESERVAS ACTIVAS DASHBOARD
        |--------------------------------------------------------------------------
        */


        $reservasActivas = Reserva::with([
                'cliente',
                'habitaciones'
            ])

            ->whereIn('estado',[
                'pendiente',
                'confirmada'
            ])

            ->orderBy(
                'fecha_entrada',
                'desc'
            )

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







        return view(
            'admin.dashboard',
            compact(

                'totalReservasHoy',

                'totalHabitaciones',

                'habitacionesOcupadas',

                'habitacionesDisponibles',

                'totalClientes',

                'ingresosMes',

                'reservasActivas',

                'ocupacionPorTipo',

                'desde',

                'hasta',

                'estadoFiltro'

            )
        );

    }


}