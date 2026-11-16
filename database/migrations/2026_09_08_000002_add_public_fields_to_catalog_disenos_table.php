<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_disenos', function (Blueprint $table) {
            $table->boolean('publicado')->default(true)->after('estado');
            $table->boolean('destacado')->default(false)->after('publicado');
        });
    }

    public function down(): void
    {
        Schema::table('catalog_disenos', function (Blueprint $table) {
            $table->dropColumn(['publicado', 'destacado']);
        });
    }
};
