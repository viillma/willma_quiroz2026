<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->string('slug', 255)->nullable()->unique()->after('nombre');
            $table->boolean('publicado')->default(true)->after('estado');
            $table->boolean('destacado')->default(false)->after('publicado');
            $table->integer('orden')->default(0)->after('destacado');
            $table->string('seo_title', 255)->nullable()->after('orden');
            $table->string('seo_description', 500)->nullable()->after('seo_title');
            $table->json('caracteristicas')->nullable()->after('imagen');
            $table->json('incluye')->nullable()->after('caracteristicas');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug',
                'publicado',
                'destacado',
                'orden',
                'seo_title',
                'seo_description',
                'caracteristicas',
                'incluye',
            ]);
        });
    }
};
