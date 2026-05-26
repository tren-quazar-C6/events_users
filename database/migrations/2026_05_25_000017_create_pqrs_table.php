<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pqrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staffs')->nullOnDelete();
            $table->enum('tipo', ['PREGUNTA', 'QUEJA', 'RECLAMO', 'SUGERENCIA']);
            $table->string('asunto', 255);
            $table->text('mensaje');
            $table->enum('estado', ['ABIERTO', 'EN_PROCESO', 'RESPONDIDO', 'CERRADO'])->default('ABIERTO');
            $table->text('respuesta')->nullable();
            $table->datetime('fecha_respuesta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pqrs');
    }
};
