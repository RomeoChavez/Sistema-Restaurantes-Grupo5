<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('nombre', 100);
            $table->decimal('precio', 8, 2);
            $table->boolean('disponible')->default(true);
            $table->timestamps();

            $table->unique(['categoria_id', 'nombre']);
            $table->index('disponible');
        });

        DB::statement('ALTER TABLE platos ADD CONSTRAINT platos_precio_no_negativo CHECK (precio >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('platos');
    }
};