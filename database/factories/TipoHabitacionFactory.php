<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TipoHabitacionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => 'Simple',
            'capacidad' => 2,
            'precio_base' => 100,
            'descripcion' => 'Habitación simple',
        ];
    }
}