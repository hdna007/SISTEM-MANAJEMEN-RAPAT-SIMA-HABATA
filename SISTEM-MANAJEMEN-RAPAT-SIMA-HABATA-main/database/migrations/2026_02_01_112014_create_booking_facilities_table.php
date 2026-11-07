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
        Schema::create('booking_facilities', function (Blueprint $table) {
            $table->foreignId('id_booking')->constrained('bookings', 'id_booking')->cascadeOnDelete();
            $table->foreignId('id_facility')->constrained('facilities', 'id_facility');
            $table->integer('jumlah');
            $table->timestamps();

            $table->primary(['id_booking', 'id_facility']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_facilities');
    }
};
