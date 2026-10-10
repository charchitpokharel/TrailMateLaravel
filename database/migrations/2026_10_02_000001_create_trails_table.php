<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trails', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('location', 120);
            $table->enum('difficulty', ['Easy', 'Moderate', 'Hard']);
            $table->decimal('distance_km', 6, 2);
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trails');
    }
};
