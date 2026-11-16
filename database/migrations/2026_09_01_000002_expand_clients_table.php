<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('code', 50)->nullable()->after('id');
            $table->string('tipo_cliente', 50)->nullable()->default('natural')->after('name');
            $table->string('ci_nit', 30)->nullable()->after('tipo_cliente');
            $table->string('whatsapp', 20)->nullable()->after('phone');
            $table->string('direccion', 255)->nullable()->after('email');
            $table->boolean('activo')->default(true)->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['code', 'tipo_cliente', 'ci_nit', 'whatsapp', 'direccion', 'activo']);
        });
    }
};
