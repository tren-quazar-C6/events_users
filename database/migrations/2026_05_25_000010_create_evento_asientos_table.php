<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento_asientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->foreignId('asiento_id')->constrained('asientos');
            $table->decimal('precio', 10, 2);
            $table->enum('estado', ['DISPONIBLE', 'RESERVADO', 'VENDIDO', 'BLOQUEADO'])->default('DISPONIBLE');
            $table->datetime('fecha_reserva')->nullable();
            $table->datetime('reserva_expira')->nullable();
            $table->timestamps();

            $table->unique(['evento_id', 'asiento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_asientos');
    }
};
