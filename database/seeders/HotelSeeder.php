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

        // USUARIOS

        $recepcionista = User::create([
            'name'=>'Recepcionista',
            'email'=>'recepcion@hotel.com',
            'password'=>Hash::make('123456'),
            'role'=>'recepcionista',
            'estado'=>'activo'
        ]);



        // TIPOS HABITACION

        TipoHabitacion::insert([

            [
                'nombre'=>'Simple',
                'capacidad'=>1,
                'precio_base'=>100,
                'descripcion'=>'Habitación individual'
            ],

            [
                'nombre'=>'Doble',
                'capacidad'=>2,
                'precio_base'=>180,
                'descripcion'=>'Habitación matrimonial'
            ],

            [
                'nombre'=>'Suite',
                'capacidad'=>4,
                'precio_base'=>350,
                'descripcion'=>'Habitación premium'
            ]

        ]);



        // HABITACIONES

        for($i=1;$i<=30;$i++){

            Habitacion::create([

                'numero'=>$i,

                'piso'=>ceil($i/10),

                'estado'=>fake()->randomElement([
                    'disponible',
                    'disponible',
                    'mantenimiento'
                ]),

                'id_tipo_habitacion'=>fake()->numberBetween(1,3)

            ]);
        }




        // CLIENTES PERU / VENEZUELA

        $clientes=[];


        for($i=1;$i<=120;$i++){

            $clientes[] = Cliente::create([

                'nombre'=>fake()->firstName(),

                'apellido'=>fake()->lastName(),

                'tipo_documento'=>fake()->randomElement([
                    'dni',
                    'pasaporte'
                ]),

                'documento'=>fake()->unique()->numerify('########'),

                'pais'=>fake()->randomElement([
                    'Perú',
                    'Venezuela'
                ]),

                'telefono'=>fake()->phoneNumber()

            ]);

        }





        // RESERVAS POR MES 2026
        // enero - junio 19

        $reservasMes = [

            1 => 20,
            2 => 25,
            3 => 30,
            4 => 35,
            5 => 45,
            6 => 50

        ];



        foreach($reservasMes as $mes=>$cantidad){


            for($i=0;$i<$cantidad;$i++){


                $dia = fake()->numberBetween(
                    1,
                    $mes == 6 ? 19 : 28
                );


                $entrada = Carbon::create(
                    2026,
                    $mes,
                    $dia
                );


                $salida = $entrada->copy()
                    ->addDays(
                        fake()->numberBetween(1,5)
                    );



                $estado = fake()->randomElement([

                    'confirmada',
                    'confirmada',
                    'finalizada',
                    'pendiente',
                    'cancelada'

                ]);




                $reserva = Reserva::create([


                    'id_cliente'=>
                    fake()->randomElement($clientes)->id_cliente,


                    'id'=>
                    $recepcionista->id,


                    'fecha_entrada'=>$entrada,


                    'fecha_salida'=>$salida,


                    'num_huespedes'=>
                    fake()->numberBetween(1,4),



                    'precio_total'=>
                    fake()->randomElement([
                        150,
                        250,
                        400,
                        700,
                        1000
                    ]),



                    'estado'=>$estado,


                    'fecha_registro'=>$entrada

                ]);





                $habitacion =
                    Habitacion::inRandomOrder()->first();



                DetalleReserva::create([

                    'id_reserva'=>
                    $reserva->id_reserva,


                    'id_habitacion'=>
                    $habitacion->id_habitacion,


                    'precio_aplicado'=>
                    fake()->numberBetween(100,500)

                ]);






                if(
                    in_array(
                        $estado,
                        ['confirmada','finalizada']
                    )
                ){


                    Pago::create([

                        'id_reserva'=>
                        $reserva->id_reserva,


                        'monto'=>
                        $reserva->precio_total,


                        'metodo_pago'=>
                        fake()->randomElement([

                            'efectivo',
                            'tarjeta',
                            'yape',
                            'plin'

                        ]),


                        'fecha_pago'=>$entrada

                    ]);

                }





                if($estado=='finalizada'){


                    Boleta::create([

                        'id_reserva'=>
                        $reserva->id_reserva,


                        'id'=>
                        $recepcionista->id,


                        'total'=>
                        $reserva->precio_total,


                        'total_acumulado'=>
                        $reserva->precio_total

                    ]);

                }



            }

        }

    }
}