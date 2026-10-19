<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('numero')->unique();
            $table->unsignedTinyInteger('capacidad');
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE mesas ADD CONSTRAINT mesas_capacidad_positiva CHECK (capacidad > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};