<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('staff_id')->constrained('staffs');
            $table->enum('resultado', ['VALIDO', 'DUPLICADO', 'INVALIDO', 'FALSO']);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
