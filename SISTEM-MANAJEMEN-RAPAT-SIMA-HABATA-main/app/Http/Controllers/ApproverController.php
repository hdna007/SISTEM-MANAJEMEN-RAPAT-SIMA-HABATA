<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\RapatApprovedMail;
use Illuminate\Support\Facades\Mail;

class ApproverController extends Controller
{
    public function index()
    {
        // Ambil tanggal hari ini
        $today = Carbon::today('Asia/Jakarta');

        // Permintaan rapat yang masih pending
        $pendingMeetings = Booking::with(['room', 'user', 'participants', 'facilities'])
            ->where('status', 'approved_proktor') // Hanya yang sudah di-approve admin
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // Hitung jumlah pending
        $jumlahPending = $pendingMeetings->count();

        // Rapat hari ini (yang sudah di-approve)
        $rapatHariIni = Booking::whereDate('tanggal_mulai', $today)
            ->where('status', 'approved_sekretaris')
            ->count();

        return view('approver.index', [
            'title'          => 'Approver Dashboard',
            'active'         => 'approver',
            'pendingMeetings'=> $pendingMeetings,
            'jumlahPending'  => $jumlahPending,
            'rapatHariIni'   => $rapatHariIni,
        ]);
    }

    // Approve rapat
    public function approve($id)
    {
        $booking = Booking::with(['participants', 'room'])
            ->where('id_booking', $id)
            ->firstOrFail();

        $booking->update([
            'status' => 'approved_sekretaris'
        ]);

        // 🔥 Kirim email satu-satu ke semua peserta
        foreach ($booking->participants as $participant) {
            if ($participant->email) {
                Mail::to($participant->email)
                    ->send(new RapatApprovedMail($booking));
            }
        }

        // Mail::raw('Test email dari SIMA HABATA', function ($message) {
        //     $message->to('raisagadis2021@gmail.com')
        //             ->subject('Test Email');
        // });

        return back()->with('success', 'Rapat disetujui & email terkirim ke semua peserta');
    }


    public function reject(Request $request, $id)
    {
        $request->validate([
            'keterangan_tolak' => 'required|string'
        ]);

        Booking::where('id_booking', $id)->update([
            'status' => 'rejected',
            'keterangan_tolak' => $request->keterangan_tolak
        ]);

        return back()->with('error', 'Rapat ditolak');
    }


}
