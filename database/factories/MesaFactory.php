<?php

namespace Database\Factories;

use App\Models\Mesa;
use Illuminate\Database\Eloquent\Factories\Factory;

class MesaFactory extends Factory
{
    protected $model = Mesa::class;

    public function definition(): array
    {
        return [
            'numero' => fake()->unique()->numberBetween(1, 40),
            'capacidad' => fake()->randomElement([2, 4, 4, 6, 8]),
            'disponible' => true,
        ];
    }
}