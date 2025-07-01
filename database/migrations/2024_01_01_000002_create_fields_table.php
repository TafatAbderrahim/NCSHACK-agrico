<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('surface');
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->string('crop_name');
            $table->decimal('temperature')->default(30.0);
            $table->decimal('moisture')->default(65.0);
            $table->string('condition');
            $table->string('valve_state');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};