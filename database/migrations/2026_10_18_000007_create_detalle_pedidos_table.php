<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('plato_id')->constrained('platos')->restrictOnDelete();
            $table->unsignedSmallInteger('cantidad')->default(1);
            $table->decimal('precio_unitario', 8, 2);
            $table->timestamps();

            $table->unique(['pedido_id', 'plato_id']);
        });

        DB::statement('ALTER TABLE detalle_pedidos ADD CONSTRAINT detalle_pedidos_cantidad_positiva CHECK (cantidad > 0)');
        DB::statement('ALTER TABLE detalle_pedidos ADD CONSTRAINT detalle_pedidos_precio_no_negativo CHECK (precio_unitario >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_pedidos');
    }
};