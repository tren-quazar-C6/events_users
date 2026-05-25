<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('estado_ticket_id')->constrained('estado_tickets');
            $table->foreignId('evento_asiento_id')->constrained('evento_asientos');
            $table->string('codigo_unico', 20)->unique();
            $table->string('qr_token', 255)->unique();
            $table->timestamps();

            $table->unique('evento_asiento_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
