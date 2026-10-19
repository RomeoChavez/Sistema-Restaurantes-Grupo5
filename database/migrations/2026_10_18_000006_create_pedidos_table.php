<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('mesa_id')->constrained('mesas')->restrictOnDelete();
            $table->foreignId('mesero_id')->constrained('meseros')->restrictOnDelete();
            $table->string('estado', 20)->default('pendiente');
            $table->timestamp('fecha_hora')->useCurrent();
            $table->timestamps();

            $table->index('estado');
            $table->index('fecha_hora');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};