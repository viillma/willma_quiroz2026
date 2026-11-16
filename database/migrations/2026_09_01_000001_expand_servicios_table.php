<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->string('codigo', 50)->nullable()->after('id');
            $table->string('categoria', 100)->nullable()->after('codigo');
            $table->decimal('costo_estimado', 10, 2)->nullable()->after('precio');
            $table->string('unidad', 50)->nullable()->default('servicio')->after('costo_estimado');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn(['codigo', 'categoria', 'costo_estimado', 'unidad']);
        });
    }
};
