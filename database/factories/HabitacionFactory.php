<?php

namespace Database\Factories;

use App\Models\TipoHabitacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class HabitacionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'numero' => fake()->unique()->numberBetween(100, 999),
            'piso' => 1,
            'estado' => 'disponible',
            'id_tipo_habitacion' => TipoHabitacion::factory(),
        ];
    }
}