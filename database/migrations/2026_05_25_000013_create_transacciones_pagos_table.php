<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacciones_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->enum('proveedor_pago', ['WOMPI'])->default('WOMPI');
            $table->string('transaccion_ext_id', 255)->nullable();
            $table->enum('estado', ['PENDING', 'APPROVED', 'DECLINED', 'VOIDED', 'ERROR', 'REFUNDED'])->default('PENDING');
            $table->string('metodo_pago', 50)->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('moneda', 10)->default('COP');
            $table->string('referencia', 255)->nullable();
            $table->json('respuesta_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacciones_pagos');
    }
};
