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

        Mesa::factory()->count(12)->create();
        Mesero::factory()->count(6)->create();
        Cliente::factory()->count(40)->create();

        $this->call(PedidoSeeder::class);
    }
}