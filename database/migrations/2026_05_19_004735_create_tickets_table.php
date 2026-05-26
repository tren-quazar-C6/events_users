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
            $table->string('unique_code', 20)->unique(); // BTC-XXXXXX
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('event_id');
            $table->string('event_title');
            $table->string('event_date');
            $table->string('event_time');
            $table->string('venue');
            $table->string('city');
            $table->string('seat_row', 5);
            $table->unsignedSmallInteger('seat_number');
            $table->string('seat_section');
            $table->unsignedInteger('price');
            $table->enum('status', ['confirmed', 'used', 'cancelled'])->default('confirmed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
