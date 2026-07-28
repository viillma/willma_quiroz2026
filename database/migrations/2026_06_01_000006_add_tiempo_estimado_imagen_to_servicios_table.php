<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->integer('tiempo_estimado')->nullable()->after('precio')->comment('Días estimados de entrega');
            $table->string('imagen')->nullable()->after('tiempo_estimado');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn(['tiempo_estimado', 'imagen']);
        });
    }
};
