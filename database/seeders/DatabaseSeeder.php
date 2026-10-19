<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Mesa;
use App\Models\Mesero;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(MenuSeeder::class);

        Mesa::factory()->count(15)->create();
        Mesero::factory()->count(8)->create();
        Cliente::factory()->count(150)->create();

        $this->call(PedidoSeeder::class);
    }
}