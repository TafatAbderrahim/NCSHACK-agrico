<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watering_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->enum('method', ['SPRINKLER', 'DRIP', 'FLOOD']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watering_logs');
    }
};