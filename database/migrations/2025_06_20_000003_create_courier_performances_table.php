<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_id')->constrained('couriers');
            $table->string('pincode');
            $table->float('avg_delivery_speed');
            $table->float('rto_rate');
            $table->float('success_rate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_performances');
    }
};
