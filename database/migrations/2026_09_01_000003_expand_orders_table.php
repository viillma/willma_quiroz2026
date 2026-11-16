<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('descuento', 10, 2)->nullable()->default(0)->after('total');
            $table->decimal('adelanto', 10, 2)->nullable()->default(0)->after('descuento');
            $table->string('forma_pago', 50)->nullable()->default('efectivo')->after('adelanto');
            $table->string('responsable', 100)->nullable()->after('forma_pago');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['descuento', 'adelanto', 'forma_pago', 'responsable']);
        });
    }
};
