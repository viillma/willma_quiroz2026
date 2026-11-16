<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->text('texto');
            $table->unsignedTinyInteger('calificacion')->default(5);
            $table->string('servicio', 255)->nullable();
            $table->string('foto', 500)->nullable();
            $table->boolean('publicado')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonios');
    }
};
