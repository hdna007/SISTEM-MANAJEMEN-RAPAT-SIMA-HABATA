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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('id_booking');
            $table->uuid('no_transaksi')->unique();
            $table->foreignId('id_user')->constrained('users', 'id_user');
            $table->foreignId('id_room')->constrained('rooms', 'id_room');

            $table->string('judul_rapat');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');

            $table->foreignId('id_notulis')->nullable()->constrained('users', 'id_user');

            $table->enum('status', [
                            'pending_admin',

                            'approved_admin',
                            'rejected_admin',

                            'approved_proktor',
                            'rejected_proktor',
                            
                            'canceled'
                        ])->default('pending_admin');

            $table->text('keterangan_tolak')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
