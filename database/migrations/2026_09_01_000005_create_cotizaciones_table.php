<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->date('fecha');
            $table->date('fecha_validez')->nullable();
            $table->decimal('descuento', 10, 2)->nullable()->default(0);
            $table->decimal('total', 12, 2)->nullable()->default(0);
            $table->text('observaciones')->nullable();
            $table->text('condiciones')->nullable();
            $table->string('estado', 30)->default('borrador');
            $table->timestamps();
        });

        Schema::create('cotizacion_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->constrained('cotizaciones')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_service');
        Schema::dropIfExists('cotizaciones');
    }
};
