<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;

class UpdateCompletedBookings extends Command
{
    protected $signature = 'bookings:update-completed';
    protected $description = 'Update booking status to completed and reactivate room';

    public function handle()
    {
        $now = Carbon::now();

        // 🔥 1. NONAKTIFKAN RUANGAN SAAT RAPAT BERLANGSUNG
        $ongoingBookings = Booking::where('status', 'approved_proktor')
            ->where('tanggal_mulai', '<=', $now)
            ->where('tanggal_selesai', '>=', $now)
            ->get();

        foreach ($ongoingBookings as $booking) {
            Room::where('id_room', $booking->id_room)
                ->update(['is_active' => 0]);
        }

        // 🔥 2. AKTIFKAN KEMBALI SETELAH SELESAI
        $finishedBookings = Booking::where('status', 'approved_proktor')
            ->where('tanggal_selesai', '<', $now)
            ->get();

        foreach ($finishedBookings as $booking) {

            Room::where('id_room', $booking->id_room)
                ->update(['is_active' => 1]);

            $booking->update([
                'status' => 'completed'
            ]);
        }

        $this->info('Rooms updated successfully.');
    }
}