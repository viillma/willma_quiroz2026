<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portafolios', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 255);
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('servicio_id')->nullable()->constrained()->nullOnDelete();
            $table->string('categoria', 100)->nullable()->default('Otros');
            $table->text('descripcion')->nullable();
            $table->string('imagen_principal', 500)->nullable();
            $table->date('fecha')->nullable();
            $table->boolean('publicar')->default(false);
            $table->timestamps();
        });

        Schema::create('portafolio_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portafolio_id')->constrained()->cascadeOnDelete();
            $table->string('imagen_path', 500);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portafolio_images');
        Schema::dropIfExists('portafolios');
    }
};
