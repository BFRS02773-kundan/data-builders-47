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
        Schema::create('dataset_build_logs', function (Blueprint $table) {
            $table->id();
            $table->string('dataset_name');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->string('file_path')->nullable();
            $table->integer('record_count')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataset_build_logs');
    }
};
