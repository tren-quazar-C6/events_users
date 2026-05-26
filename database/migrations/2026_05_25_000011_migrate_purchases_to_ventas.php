<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('purchases');

        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staffs')->nullOnDelete();
            $table->enum('tipo_venta', ['ONLINE', 'TAQUILLA'])->default('ONLINE');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('cargo_servicio', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('moneda', 10)->default('COP');
            $table->enum('estado_pago', ['PENDING', 'APPROVED', 'DECLINED', 'VOIDED', 'ERROR', 'REFUNDED'])->default('PENDING');
            $table->string('metodo_pago', 50)->nullable();
            $table->string('referencia_interna', 255)->unique()->nullable();
            $table->string('referencia_wompi', 255)->nullable();
            $table->string('transaccion_wompi_id', 255)->nullable();
            $table->json('json_respuesta')->nullable();
            $table->datetime('fecha_pago')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
