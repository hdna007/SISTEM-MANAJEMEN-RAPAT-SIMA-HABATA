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
        Schema::create('booking_participants', function (Blueprint $table) {
            $table->foreignId('id_booking')->constrained('bookings', 'id_booking')->cascadeOnDelete();
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('peran_peserta', 20);
            $table->timestamps();


            $table->primary(['id_booking', 'id_user']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_participants');
    }
};
