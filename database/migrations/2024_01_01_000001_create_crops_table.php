<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('watering_frequency')->default(0);
            $table->enum('growth_stage', ['SEED', 'SPROUT', 'VEGETATIVE', 'FLOWERING', 'RIPENING'])->default('SEED');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};