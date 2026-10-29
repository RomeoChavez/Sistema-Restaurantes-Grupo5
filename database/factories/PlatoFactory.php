<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Plato;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatoFactory extends Factory
{
    protected $model = Plato::class;

    public function definition(): array
    {
        return [
            'categoria_id' => Categoria::factory(),
            'nombre' => fake()->randomElement([
                'Silpancho',
                'Pique macho',
                'Sajta de pollo',
                'Fricase',
                'Charquekan',
                'Milanesa de pollo',
                'Sopa de mani',
                'Salteña',
                'Api con pastel',
                'Helado de canela',
                'Refresco de mocochinchi',
                'Jugo de naranja',
            ]) . ' ' . fake()->numerify('###'),
            'descripcion' => fake()->sentence(12),
            'imagen' => null,
            'precio' => fake()->randomFloat(2, 8, 90),
            'disponible' => fake()->boolean(90),
        ];
    }
}