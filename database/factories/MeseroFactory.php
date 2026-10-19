<?php

namespace Database\Factories;

use App\Models\Mesero;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeseroFactory extends Factory
{
    protected $model = Mesero::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'telefono' => fake()->unique()->numerify('7#######'),
        ];
    }
}