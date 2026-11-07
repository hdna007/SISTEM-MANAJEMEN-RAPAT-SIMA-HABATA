<?php

namespace App\Http\Controllers;


use App\Models\Booking;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Dashboard View
    public function index()
    {
        $month = request()->get('month', date('m'));
        $year  = request()->get('year', date('Y'));

        $userId = auth()->id();
        $userRole = auth()->user()->role;

        $query = Booking::where('status', 'approved_proktor')
                ->where('tanggal_mulai', '>', now()); // 🔥 HANYA yang belum mulai

        if ($userRole !== 'admin') {
            $query->where(function ($q) use ($userId) {
                $q->where('id_user', $userId)
                ->orWhereHas('participants', function ($p) use ($userId) {
                    $p->where('booking_participants.id_user', $userId);
                });
            });
        }

        $rapatMendatang = $query->orderBy('tanggal_mulai')
                ->with(['room', 'notulis', 'creator'])
                ->first();

        return view('user.index',
            compact('month', 'year', 'rapatMendatang'),
            [
                'title' => 'Dashboard',
                'active' => 'dashboard'
            ]
        );
    }

    // Get Bookings for Calendar
    public function getBookings(Request $request)
    {
        $userId = auth()->id();
        $userRole = auth()->user()->role;
        $month = $request->month;
        $year  = $request->year;

        $query = Booking::where('status', 'approved_proktor')
            ->whereMonth('tanggal_mulai', $month)
            ->whereYear('tanggal_mulai', $year);

        if ($userRole !== 'admin') {
            $query->where(function ($q) use ($userId) {
                $q->where('id_user', $userId)
                ->orWhereHas('participants', function ($p) use ($userId) {
                    $p->where('booking_participants.id_user', $userId);
                });
            });
        }

        $bookings = $query->with(['room', 'notulis', 'creator', 'notulen', 'participants'])->get();

        return response()->json(
            $bookings->map(function ($b) use ($userId) {

                switch ($b->status_rapat) {
                    case 'upcoming':
                        $color = 'warning';
                        $status = 'Mendatang';
                        break;
                    case 'ongoing':
                        $color = 'success';
                        $status = 'Sedang berlangsung';
                        break;
                    case 'completed':
                        $color = 'primary';
                        $status = 'Selesai';
                        break;
                    default:
                        $color = 'secondary';
                        $status = ucfirst(str_replace('_', ' ', $b->status_rapat));
                }

                $isParticipant = false;
                if ($b->id_user == $userId) {
                    $isParticipant = true;
                } else {
                    $isParticipant = $b->participants->contains('id_user', $userId);
                }

                return [
                    'judul_rapat'     => $b->judul_rapat,
                    'is_participant'  => $isParticipant,
                    'no_transaksi'    => $b->no_transaksi,
                    'tanggal_mulai'   => $b->tanggal_mulai->format('Y-m-d H:i'),
                    'tanggal_selesai' => $b->tanggal_selesai->format('Y-m-d H:i'),
                    'ruangan'         => $b->room->nama_ruangan ?? '-',
                    'notulis'         => $b->notulis->nama_lengkap ?? '-',
                    'creator'         => $b->creator->nama_lengkap ?? '-',
                    'status'          => $status,
                    'color'           => $color,
                    'pdf_file'        => $b->notulen && $b->notulen->pdf_file ? route('notulen.pdf', $b->no_transaksi) : null,
                    'pdf_download'    => $b->notulen && $b->notulen->pdf_file ? route('notulen.download', $b->no_transaksi) : null
                ];
            })
        );
    }
}
