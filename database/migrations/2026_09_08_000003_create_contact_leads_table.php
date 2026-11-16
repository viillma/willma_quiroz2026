<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_leads', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('telefono', 50)->nullable();
            $table->string('correo', 255)->nullable();
            $table->string('servicio', 255)->nullable();
            $table->text('mensaje');
            $table->string('estado', 50)->default('nuevo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_leads');
    }
};
