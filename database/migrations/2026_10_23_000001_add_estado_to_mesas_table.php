<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->string('estado', 20)->default('libre')->after('capacidad');
        });

        DB::table('mesas')->where('disponible', false)->update(['estado' => 'ocupado']);
        DB::table('mesas')->where('disponible', true)->update(['estado' => 'libre']);
    }

    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
