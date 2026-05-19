<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique(); // ORD-XXXXXX
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('event_id');
            $table->string('event_title');
            $table->string('event_date');
            $table->string('event_time');
            $table->string('venue');
            $table->string('city');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('service_fee');
            $table->unsignedInteger('total');
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
