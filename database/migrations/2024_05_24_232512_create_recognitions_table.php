<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recognitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->text('tesseract_text')->nullable();
            $table->float('tesseract_time')->nullable();
            $table->float('tesseract_memory')->nullable();
            $table->float('tesseract_percentage')->nullable();
            $table->text('vision_text')->nullable();
            $table->float('vision_time')->nullable();
            $table->float('vision_memory')->nullable();
            $table->float('vision_percentage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recognitions');
    }
};
