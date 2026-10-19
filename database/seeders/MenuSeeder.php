<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Plato;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menu = [
            'Entradas' => [
                'Salteña' => 8.00,
                'Tucumana de pollo' => 9.00,
            ],
            'Sopas' => [
                'Sopa de mani' => 25.00,
                'Chairo paceño' => 28.00,
            ],
            'Platos principales' => [
                'Silpancho' => 40.00,
                'Pique macho' => 65.00,
                'Sajta de pollo' => 38.00,
                'Fricase' => 45.00,
                'Charquekan' => 50.00,
            ],
            'Postres' => [
                'Api con pastel' => 15.00,
                'Helado de canela' => 12.00,
            ],
            'Bebidas' => [
                'Refresco de mocochinchi' => 8.00,
                'Jugo de naranja' => 10.00,
                'Gaseosa' => 7.00,
            ],
        ];

        foreach ($menu as $nombreCategoria => $platos) {
            $categoria = Categoria::factory()->create(['nombre' => $nombreCategoria]);

            foreach ($platos as $nombrePlato => $precio) {
            Plato::factory()->for($categoria)->create([
            'nombre' => $nombrePlato,
            'precio' => $precio,
            'disponible' => true,
             ]);
            }
        }
    }
}