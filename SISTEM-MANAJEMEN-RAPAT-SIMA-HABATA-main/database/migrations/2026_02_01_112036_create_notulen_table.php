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
        Schema::create('notulen', function (Blueprint $table) {
            $table->id('id_notulen');
            $table->foreignId('id_booking')->constrained('bookings', 'id_booking');
            $table->string('judul_rapat');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->string('pemimpin_rapat');
            $table->text('peserta');
            $table->foreignId('id_notulis')->constrained('users', 'id_user');
            $table->text('hasil_materi');
            $table->text('deskripsi_rapat');
            $table->text('dokumentasi_kegiatan')->nullable();
            $table->string('pdf_file')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notulen');
    }
};
