<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'tipo_documento' => 'dni',
            'documento' => fake()->unique()->numerify('########'),
            'pais' => 'Perú',
            'telefono' => fake()->numerify('9########'),
        ];
    }
}