<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_evento_id')->constrained('tipo_eventos');
            $table->foreignId('staff_id')->nullable()->constrained('staffs')->nullOnDelete();
            $table->string('slug', 100)->unique();
            $table->string('nombre_evento', 150);
            $table->json('synopsis')->nullable();
            $table->string('author', 150)->nullable();
            $table->string('duration', 50)->nullable();
            $table->string('poster_color', 20)->nullable();
            $table->string('venue', 150)->nullable();
            $table->string('city', 100)->nullable();
            $table->decimal('price_from', 10, 2)->default(0);
            $table->datetime('fecha_evento');
            $table->datetime('fecha_inicio_ventas');
            $table->datetime('fecha_fin_ventas');
            $table->unsignedInteger('capacidad_total')->default(0);
            $table->boolean('publicado')->default(true);
            $table->boolean('activo')->default(true);
            $table->datetime('fecha_cancelacion')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
