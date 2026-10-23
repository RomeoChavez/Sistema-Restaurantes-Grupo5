<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platos', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('platos', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'imagen']);
        });
    }
};